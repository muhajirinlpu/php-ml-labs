<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Classifiers;
use League\Csv as LeagueCsv;

class NaiveBayesCommand extends Command
{
    protected $signature = 'demo:naive-bayes
                            {dataset_path : The path to the file to be classified}
                            {test_path : The path to the test file}
                            {--name= : The name of the dataset}';

    protected $description = 'Naive Bayes Demo';

    /**
     * @throws \League\Csv\Exception
     */
    public function handle(): void
    {
        $datasetPath = $this->argument('dataset_path');
        $testPath = $this->argument('test_path');

        if (!file_exists($datasetPath)) {
            $this->error("Dataset path not found: $datasetPath");
            return;
        }

        if (!is_null($testPath) && !file_exists($testPath)) {
            $this->error("Test path not found: $testPath");
            return;
        }

        $dataset = Labeled::fromIterator(new CSV($datasetPath, true));

        $test = Labeled::fromIterator(new CSV($testPath, true));

        $estimator = new Classifiers\NaiveBayes();
        $estimator->train($dataset);
        $this->info("Classifier trained. Count probabilities: ");

        $this->table(collect($dataset->labels())->unique()->values()->toArray(), $estimator->proba($test));
        $predictions = $estimator->predict($test);

        $csv = LeagueCsv\Reader::createFromPath($testPath, 'r');
        $csv->setHeaderOffset(0);

        $subject = collect($csv->getHeader())->last();

        if ($this->option('name')) {
            $this->info("Prediction for {$this->option('name')}:");
            $this->table($csv->getHeader(), collect($csv->getRecords())
                ->values()
                ->map(function (array $row, int $index) use ($subject, $predictions) {
                    $row[$subject] = $predictions[$index];

                    return $row;
                })->toArray());
        }
    }
}
