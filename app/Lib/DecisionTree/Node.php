<?php

namespace App\Lib\DecisionTree;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

class Node implements Arrayable, Jsonable
{
    public function __construct(
        public mixed $key,
        public Collection $leafs,
        protected ?array $paramsEntropyScores = null,
    ) {}

    public function test(array $data): mixed
    {
        $predict = static function (Leaf $leaf) use ($data, &$predict) {
            if ($node = $leaf->getNode()) {
                foreach ($node->leafs as $leaf) {
                    if ($leaf->value === $data[$node->key]) {
                        return $predict($leaf);
                    }
                }
            }

            return $leaf->results;
        };

        foreach ($this->leafs as $leaf) {
            if ($leaf->value === $data[$this->key]) {
                return $predict($leaf);
            }
        }

        return null;
    }

    public function toArray()
    {
        return [
            'key' => $this->key,
            'leafs' => $this->leafs,
            'paramsEntropyScores' => $this->paramsEntropyScores,
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
