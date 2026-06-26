{{-- WireCharts Alpine glue for Apache ECharts. Rendered once via @wirechartsScripts. --}}
<script>
document.addEventListener('alpine:init', () => {
    // Revive "@@...@@" markers back into real functions (JSON can't carry functions).
    function reviveFunctions(node) {
        if (Array.isArray(node)) {
            return node.map(reviveFunctions);
        }
        if (node && typeof node === 'object') {
            for (const key in node) {
                node[key] = reviveFunctions(node[key]);
            }
            return node;
        }
        if (typeof node === 'string' && node.startsWith('@@') && node.endsWith('@@')) {
            return new Function('return (' + node.slice(2, -2) + ')')();
        }
        return node;
    }

    // The chart engine may load via a deferred <script>, which can resolve after Alpine
    // boots. Wait for it before initialising so charts always render.
    function whenEngine(callback, tries = 0) {
        if (typeof echarts !== 'undefined') {
            callback();
        } else if (tries < 200) {
            setTimeout(() => whenEngine(callback, tries + 1), 25);
        } else {
            console.error('[WireCharts] chart engine failed to load.');
        }
    }

    // Shared ECharts lifecycle reused by every WireCharts component.
    function core(option, themeMode) {
        return {
            chart: null,
            option: reviveFunctions(option),
            themeMode: themeMode,
            _observer: null,
            _resize: null,

            init() {
                this.chart = echarts.init(this.$refs.canvas, this.isDark() ? 'dark' : null, { renderer: 'canvas' });
                this.chart.setOption({ backgroundColor: 'transparent', ...this.option });
            },

            mountChart() {
                this.init();

                this._resize = () => this.chart?.resize();
                window.addEventListener('resize', this._resize);

                if (this.themeMode === 'auto') {
                    this._observer = new MutationObserver(() => this.retheme());
                    this._observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class'],
                    });
                }
            },

            update() {
                this.chart?.setOption({ backgroundColor: 'transparent', ...this.option }, { notMerge: true });
            },

            retheme() {
                if (! this.chart) {
                    return;
                }
                const current = this.chart.getOption();
                this.chart.dispose();
                this.chart = echarts.init(this.$refs.canvas, this.isDark() ? 'dark' : null, { renderer: 'canvas' });
                this.chart.setOption({ ...current, backgroundColor: 'transparent' });
            },

            isDark() {
                return this.themeMode === 'dark'
                    || (this.themeMode === 'auto' && document.documentElement.classList.contains('dark'));
            },

            teardownChart() {
                this._observer?.disconnect();
                window.removeEventListener('resize', this._resize);
                this.chart?.dispose();
            },
        };
    }

    Alpine.data('wireChart', (option, themeMode = 'auto', model = null) => ({
        ...core(option, themeMode),

        render() {
            whenEngine(() => {
                this.mountChart();

                // Live-bind to a Livewire property holding the series (optional).
                if (model && this.$wire) {
                    this.$wire.$watch(model, (value) => {
                        this.option.series = reviveFunctions(value);
                        this.update();
                    });
                }
            });
        },

        destroy() {
            this.teardownChart();
        },
    }));

    // Audio charts: sonify a data series with the Web Audio API.
    // Pitch maps to value, time maps to the x-axis.
    Alpine.data('wireChartAudio', (option, audio = {}, themeMode = 'auto') => ({
        ...core(option, themeMode),
        audio: audio,
        playing: false,
        ctx: null,
        _timer: null,

        render() {
            whenEngine(() => this.mountChart());
        },

        values() {
            const series = this.option.series?.[this.audio.track || 0]?.data || [];
            return series.map((d) => {
                if (Array.isArray(d)) return d[1] ?? d[0];
                if (d && typeof d === 'object') return d.value;
                return d;
            }).filter((v) => typeof v === 'number');
        },

        summary() {
            const v = this.values();
            if (! v.length) return 'Empty chart.';
            const min = Math.min(...v), max = Math.max(...v);
            const trend = v[v.length - 1] > v[0] ? 'rising' : (v[v.length - 1] < v[0] ? 'falling' : 'flat');
            return `Sonified line chart with ${v.length} points, values from ${min} to ${max}, overall ${trend}. Press to play as audio.`;
        },

        toggle() {
            this.playing ? this.stop() : this.play();
        },

        play() {
            const v = this.values();
            if (! v.length) return;

            this.ctx = this.ctx || new (window.AudioContext || window.webkitAudioContext)();
            if (this.ctx.state === 'suspended') this.ctx.resume();
            this.playing = true;

            const duration = this.audio.duration ?? 5000;
            const step = duration / v.length;
            const min = Math.min(...v), max = Math.max(...v);
            const fMin = this.audio.minFreq ?? 220;
            const fMax = this.audio.maxFreq ?? 880;
            const start = this.ctx.currentTime + 0.05;

            v.forEach((value, i) => {
                const t = start + (i * step) / 1000;
                const norm = max === min ? 0.5 : (value - min) / (max - min);
                const freq = fMin + norm * (fMax - fMin);
                const noteLen = Math.min(step / 1000, 0.3);

                const osc = this.ctx.createOscillator();
                const gain = this.ctx.createGain();
                osc.type = this.audio.instrument || 'sine';
                osc.frequency.value = freq;
                osc.connect(gain);
                gain.connect(this.ctx.destination);

                gain.gain.setValueAtTime(0.0001, t);
                gain.gain.exponentialRampToValueAtTime(0.2, t + 0.012);
                gain.gain.exponentialRampToValueAtTime(0.0001, t + noteLen);
                osc.start(t);
                osc.stop(t + noteLen + 0.02);
            });

            // Move the tooltip along the chart in sync with playback.
            let i = 0;
            this._timer = setInterval(() => {
                if (i >= v.length) {
                    this.stop();
                    return;
                }
                this.chart?.dispatchAction({ type: 'showTip', seriesIndex: this.audio.track || 0, dataIndex: i });
                i++;
            }, step);
        },

        stop() {
            this.playing = false;
            clearInterval(this._timer);
            this._timer = null;
            this.chart?.dispatchAction({ type: 'hideTip' });
        },

        destroy() {
            this.stop();
            this.ctx?.close?.();
            this.teardownChart();
        },
    }));

    // Analog clock: advances the hour, minute and second gauges every second.
    Alpine.data('wireChartClock', (option, themeMode = 'auto') => ({
        ...core(option, themeMode),
        _clock: null,

        render() {
            whenEngine(() => {
                this.mountChart();
                this.tick();
                this._clock = setInterval(() => this.tick(), 1000);
            });
        },

        tick() {
            const now = new Date();
            const seconds = now.getSeconds();
            const minutes = now.getMinutes() + seconds / 60;
            const hours = (now.getHours() % 12) + minutes / 60;

            this.chart?.setOption({
                series: [
                    { data: [{ value: hours }] },
                    { data: [{ value: minutes }] },
                    { data: [{ value: seconds }] },
                ],
            });
        },

        destroy() {
            clearInterval(this._clock);
            this.teardownChart();
        },
    }));

    // Line race: reveal each series' points progressively for an animated draw.
    Alpine.data('wireChartRace', (option, themeMode = 'auto', config = {}) => ({
        ...core(option, themeMode),
        _timer: null,
        _full: [],
        _max: 0,
        _interval: config.interval || 400,

        render() {
            whenEngine(() => {
                this._full = (this.option.series || []).map((s) => Array.isArray(s.data) ? s.data.slice() : []);
                this._max = this._full.reduce((m, d) => Math.max(m, d.length), 0);
                this.reveal(1);
                this.mountChart();
                this.play();
            });
        },

        reveal(step) {
            (this.option.series || []).forEach((s, i) => {
                s.data = this._full[i].slice(0, step);
            });
        },

        play() {
            clearInterval(this._timer);
            let step = 1;
            this.reveal(step);
            this.chart?.setOption({ series: this.option.series });

            this._timer = setInterval(() => {
                step++;
                this.reveal(step);
                this.chart?.setOption({ series: this.option.series });
                if (step >= this._max) {
                    clearInterval(this._timer);
                }
            }, this._interval);
        },

        destroy() {
            clearInterval(this._timer);
            this.teardownChart();
        },
    }));
});
</script>
