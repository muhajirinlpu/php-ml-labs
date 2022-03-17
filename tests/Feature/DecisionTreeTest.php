<?php

it('decision tree test signal', function () {
    $this->artisan('demo:decision-tree', [
        'dataset' => 'data/signal.csv',
    ]);
});

it('decision tree test heart_decease', function () {
    $this->artisan('demo:decision-tree', [
        'dataset' => 'data/heart_decease.csv',
    ]);
});
