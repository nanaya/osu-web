<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class MigrateFreshAllCommand extends Command
{
    protected $name = 'migrate:fresh-real';
    protected $description = 'Drop all tables and re-run all migrations (actually useful version)';

    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return 1;
        }

        $connections = $GLOBALS['cfg']['database']['connections'];

        $this->warn('This will drop tables in the following databases:');

        foreach ($connections as $name => $config) {
            $this->warn("{$name} => {$config['database']}");
        }

        $continue = $this->option('no-interaction') || $this->confirm('continue?', true);
        if (!$continue) {
            $this->error('User aborted!');
            return 1;
        }

        foreach (array_keys($connections) as $database) {
            $this->warn($database);
            $this->call('db:wipe', [
                '--database' => $database,
                '--drop-views' => true,
            ]);
        }

        $this->info('Dropped all tables successfully.');

        $this->call('migrate', [
            '--path' => $this->input->getOption('path'),
        ]);

        $this->info('Setup elasticsearch indices.');

        $this->call('es:index-documents', [
            '--cleanup' => true,
            '--no-interaction' => $this->option('no-interaction'),
        ]);

        $this->call('es:index-wiki', [
            '--cleanup' => true,
            '--create-only' => true,
            '--no-interaction' => $this->option('no-interaction'),
        ]);

        if ($this->option('seed') || $this->option('seeder')) {
            $this->call('db:seed', [
                '--class' => $this->option('seeder') ?? DatabaseSeeder::class,
                '--force' => true,
            ]);
        }

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'This option is ignored.'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.'],
        ];
    }
}
