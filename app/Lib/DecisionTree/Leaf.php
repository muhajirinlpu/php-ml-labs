<?php

namespace App\Lib\DecisionTree;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Leaf implements Arrayable, Jsonable
{
    public array $results;

    public function __construct(public mixed $value, public Collection $dataset, protected ?Node $node = null, protected mixed $subjectKey = null)
    {
        $this->results = $this->dataset->map(fn($data) => $data[$this->subjectKey])->unique()->values()->toArray();
    }

    /**
     * @return \App\Lib\DecisionTree\Node|null
     */
    public function getNode(): ?Node
    {
        return $this->node;
    }

    /**
     * @param \App\Lib\DecisionTree\Node|null $node
     */
    public function setNode(?Node $node): void
    {
        $this->node = $node;
    }

    public function getHeaders(array $except): array
    {
        return array_keys(Arr::except($this->dataset->first(), $except));
    }

    public function toArray()
    {
        $data = [
            'value' => $this->value,
        ];

        if ($this->node) {
            $data['node'] = $this->node->toArray();
        } else {
            $data['results'] = $this->results;
        }

        return $data;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
