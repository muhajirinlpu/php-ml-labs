<?php

namespace App\Commands;

use App\Models\Titanic;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use League\Csv as LeagueCsv;
use function Termwind\render;

class TitanicNormalization extends Command
{
    protected $signature = 'test:norm:titanic';

    protected $description = 'Normalize titanic data.';

    private static function standardDeviation(array $data): float
    {
        $mean = array_sum($data) / count($data);

        $sum = 0;
        foreach ($data as $value) {
            $sum += ($value - $mean) ** 2;
        }

        return sqrt($sum / count($data));
    }

    /**
     * @throws \League\Csv\Exception
     */
    public function handle(): void
    {
        $csv = LeagueCsv\Reader::createFromPath(base_path('data/titanic.csv'));
        $csv->setHeaderOffset(0);

        $headers = $csv->getHeader();
        $dataset = collect($csv->getRecords())->values();

        $columnTotal = count($headers);
        $rowTotal = $dataset->count();

        render(<<<HTML
            <ul class="bg-orange-300 p-1">
            <li><b class='font-bold'>Jumlah Baris : $rowTotal</b></li>
            <li><b class='font-bold'>Jumlah Kolom : $columnTotal</b></li>
            </ul>
        HTML
        );

        // transform data to model
        $dataset = $dataset->map(fn(array $data) => Titanic::fromArray($data));

        // fill empty age column with mean
        $survivedMean = $dataset->where(fn(Titanic $titanic) => $titanic->survived)->avg('age');
        $unsurvivedMean = $dataset->where(fn(Titanic $titanic) => !$titanic->survived)->avg('age');

        $dataset->whereNull('age')->each(function (Titanic $titanic) use ($survivedMean, $unsurvivedMean) {
            $titanic->age = $titanic->survived ? $survivedMean : $unsurvivedMean;
        });

        $this->processMinMax($dataset, 'age', 0, 1);
        $this->processMinMax($dataset, 'fare', 0, 1);
        $this->processZScore($dataset, 'age');
        $this->processZScore($dataset, 'fare');
        $this->processSigmoid($dataset, 'age');
        $this->processSigmoid($dataset, 'fare');

        $dataset;

        $writer = LeagueCsv\Writer::createFromPath(base_path('data/titanic_normalized.csv'), 'w+');
        /** @var Titanic $sample */
        $sample = $dataset->first();

        $headers = array_merge($headers, array_keys($sample->additionalProperty->getAttributes()));
        $writer->insertOne($headers);
        $writer->insertAll($dataset->toArray());
    }

    private function processZScore(Collection $data, string $key): void
    {
        $mean = $data->avg($key);
        $stdv = self::standardDeviation($data->pluck($key)->toArray());

        $data->each(function (Titanic $titanic) use ($key, $mean, $stdv) {
            $titanic->additionalProperty->offsetSet($key . '_z_score', ($titanic->{$key} - $mean) / $stdv);
        });
    }

    private function processMinMax(Collection $data, string $key, float $newMinValue = 0., float $newMaxValue = 1.): void
    {
        $minValue = $data->min($key);
        $maxValue = $data->max($key);

        $data->each(function (Titanic $titanic) use ($key, $minValue, $maxValue, $newMinValue, $newMaxValue) {
            $titanic->additionalProperty->offsetSet($key . '_min_max', $this->minMaxNormalization($titanic->$key, $minValue, $maxValue, $newMinValue, $newMaxValue));
        });
    }

    private function minMaxNormalization($param, mixed $minValue, mixed $maxValue, float $newMinValue, float $newMaxValue): float
    {
        return (($param - $minValue) / ($maxValue - $minValue)) * ($newMaxValue - $newMinValue) + $newMinValue;
    }

    private function processSigmoid(Collection $data, string $key): void
    {
        $mean = $data->avg($key);
        $stdv = self::standardDeviation($data->pluck($key)->toArray());

        $data->each(function (Titanic $titanic) use ($key, $mean, $stdv) {
            $data = $titanic->{$key};
            $x = ($data - $mean) / $stdv;
            $titanic->additionalProperty->offsetSet($key . '_sigmoid', (1 - exp(-$x)) / (1 + exp(-$x)));
        });
    }
}
