<?php

$poller = new CCUFFS\Text\PollFromText();

test('select question with one option', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        - Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => ['Green']
        ],
    ], $poll);
});

test('select question with multiple options', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        - Pasta
        - Steak
        - Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => ['Pasta', 'Steak', 'Tofu']
        ],
    ], $poll);
});

test('select question with multiple options (with spaces)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        -   Pasta
        -  Steak
        - Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => ['Pasta', 'Steak', 'Tofu']
        ],
    ], $poll);
});
