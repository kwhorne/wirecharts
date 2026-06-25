<?php

namespace WireCharts\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ChangelogCommand extends Command
{
    protected $signature = 'wirecharts:changelog';

    protected $description = 'Verify that every Git tag has a matching CHANGELOG.md entry';

    public function handle(): int
    {
        $changelog = dirname(__DIR__, 2).'/CHANGELOG.md';

        if (! is_file($changelog)) {
            $this->components->error('CHANGELOG.md not found.');

            return self::FAILURE;
        }

        $documented = $this->documentedVersions((string) file_get_contents($changelog));
        $tags = $this->gitTags(dirname($changelog));

        if ($tags === null) {
            $this->components->warn('Git is not available here; listing documented versions only.');
            $this->listing('Documented', $documented);

            return self::SUCCESS;
        }

        $missing = array_values(array_diff($tags, $documented));

        $this->listing('Tags', $tags);
        $this->listing('Documented', $documented);

        if ($missing !== []) {
            $this->components->error('Tags without a CHANGELOG entry: '.implode(', ', $missing));

            return self::FAILURE;
        }

        $this->components->info('Every Git tag has a matching CHANGELOG entry.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    protected function documentedVersions(string $markdown): array
    {
        preg_match_all('/^##\s+\[([^\]]+)\]/m', $markdown, $matches);

        return array_values(array_filter(
            array_map('trim', $matches[1]),
            fn (string $v): bool => strtolower($v) !== 'unreleased',
        ));
    }

    /**
     * @return list<string>|null  Null when Git is unavailable.
     */
    protected function gitTags(string $directory): ?array
    {
        if (! is_dir($directory.'/.git')) {
            return null;
        }

        $result = Process::path($directory)->run('git tag --list');

        if (! $result->successful()) {
            return null;
        }

        return array_values(array_filter(array_map(
            fn (string $tag): string => ltrim(trim($tag), 'v'),
            preg_split('/\r?\n/', $result->output()) ?: [],
        )));
    }

    /**
     * @param  list<string>  $items
     */
    protected function listing(string $label, array $items): void
    {
        $this->components->twoColumnDetail($label, $items === [] ? '—' : implode(', ', $items));
    }
}
