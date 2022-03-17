<?php

it('decision tree test', function () {
    $this->artisan('demo:decision-tree', [
        'dataset' => 'data/signal.csv',
    ]);
});
