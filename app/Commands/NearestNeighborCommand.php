<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use League\Csv as LeagueCsv;

class NearestNeighborCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'demo:nearest-neighbor {dataset : The dataset to use}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'KNN demo';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \League\Csv\Exception
     */
    public function handle()
    {
        $csv = LeagueCsv\Reader::createFromPath(base_path($this->argument('dataset')));
        $csv->setHeaderOffset(0);

        $headers = $csv->getHeader();

        $subjectKey = $headers[count($headers) - 1];

        $dataset = collect($csv->getRecords())->values();

        $dataset;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
