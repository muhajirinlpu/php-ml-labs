<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Kernels\Distance\Minkowski;

class SevenSegmentRecognition extends Command
{
    public const DIGITS_LOOKUP = [
        0 => [1, 1, 1, 1, 1, 1, 0],
        1 => [1, 1, 0, 0, 0, 0, 0],
        2 => [1, 0, 1, 1, 0, 1, 1],
        3 => [1, 1, 1, 0, 0, 1, 1],
        4 => [1, 1, 0, 0, 1, 0, 1],
        5 => [0, 1, 1, 0, 1, 1, 1],
        6 => [0, 1, 1, 1, 1, 1, 1],
        7 => [1, 1, 0, 0, 0, 1, 0],
        8 => [1, 1, 1, 1, 1, 1, 1],
        9 => [1, 1, 1, 0, 1, 1, 1],
        '-' => [0, 0, 0, 0, 0, 1, 1],
    ];

    protected $signature = 'demo:7s-ocr';

    protected $description = '7 Segment Recognition Demo';

    public function handle(): int
    {
        $this->info('7 Segment Recognition Demo');

        $estimator = new KNearestNeighbors(10, false, new Minkowski(2.5));

        return 0;
    }
}
