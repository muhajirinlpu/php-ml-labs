<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Fluent;

class Titanic implements Arrayable
{
    public Fluent $additionalProperty;

    public function __construct(
        public readonly string $passengerId,
        public readonly bool   $survived,
        public readonly string $pClass,
        public readonly string $name,
        public readonly string $gender,
        public ?int            $age,
        public readonly string $sibSp,
        public readonly string $parch,
        public readonly string $ticket,
        public readonly ?float $fare,
        public readonly string $cabin,
        public readonly string $embarked,
    )
    {
        $this->additionalProperty = new Fluent();
    }

    public static function fromArray(array $data): Titanic
    {
        $age = ('' !== $data['Age']) ? (int)$data['Age'] : null;

        return new Titanic(
            passengerId: $data['PassengerId'],
            survived: (bool)$data['Survived'],
            pClass: $data['Pclass'],
            name: $data['Name'],
            gender: $data['Sex'],
            age: $age,
            sibSp: $data['SibSp'],
            parch: $data['Parch'],
            ticket: $data['Ticket'],
            fare: (float)$data['Fare'],
            cabin: $data['Cabin'],
            embarked: $data['Embarked'],
        );
    }


    public function toArray(): array
    {
        return array_merge([
            'passengerId' => $this->passengerId,
            'survived' => $this->survived ? 'Yes' : 'No',
            'pClass' => $this->pClass,
            'name' => $this->name,
            'gender' => $this->gender,
            'age' => $this->age,
            'sibSp' => $this->sibSp,
            'parch' => $this->parch,
            'ticket' => $this->ticket,
            'fare' => $this->fare,
            'cabin' => $this->cabin,
            'embarked' => $this->embarked,
        ], $this->additionalProperty->getAttributes());
    }
}
