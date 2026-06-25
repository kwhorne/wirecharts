<?php

namespace WireCharts\Console;

use Illuminate\Console\Command;
use WireCharts\Licensing\License;

class ActivateCommand extends Command
{
    protected $signature = 'wirecharts:activate {key? : Your WireCharts Pro license key}';

    protected $description = 'Activate WireCharts Pro with a license key';

    public function handle(): int
    {
        $key = $this->argument('key') ?: $this->secret('Paste your WireCharts Pro license key');

        if (! $key) {
            $this->components->error('No license key provided.');

            return self::FAILURE;
        }

        // Verify signature + expiry only (domain is checked at render time).
        $license = new License(key: $key, host: null);
        $claims = $license->claims();

        if ($claims === null) {
            $this->components->error('That license key is invalid or expired.');

            return self::FAILURE;
        }

        if (! $this->writeToEnv($key)) {
            $this->components->warn('Could not update .env automatically. Add this line manually:');
            $this->line('  WIRECHARTS_LICENSE_KEY='.$key);

            return self::FAILURE;
        }

        $name = $claims['name'] ?? 'your license';
        $this->components->info("WireCharts Pro activated for {$name}. All Pro components are now unlocked.");

        if (! empty($claims['domains'])) {
            $this->components->info('Licensed domains: '.implode(', ', $claims['domains']));
        }

        return self::SUCCESS;
    }

    protected function writeToEnv(string $key): bool
    {
        $path = $this->laravel->environmentFilePath();

        if (! is_writable($path) && ! is_writable(dirname($path))) {
            return false;
        }

        $contents = file_exists($path) ? file_get_contents($path) : '';
        $line = 'WIRECHARTS_LICENSE_KEY="'.$key.'"';

        if (preg_match('/^WIRECHARTS_LICENSE_KEY=.*$/m', $contents)) {
            $contents = preg_replace('/^WIRECHARTS_LICENSE_KEY=.*$/m', $line, $contents);
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        return file_put_contents($path, $contents) !== false;
    }
}
