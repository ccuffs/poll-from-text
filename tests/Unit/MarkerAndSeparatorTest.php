<?php

$poller = new CCUFFS\Text\PollFromText();

test('dash marker', function() use ($poller) {
    $poll = $poller->parse('
        Choose best food
        - Pasta
        - Stake
    ');
    $this->assertEquals([
        [
            'text' => 'Choose best food',
            'type' => 'select',
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Stake', 'marker' => '-'],
            ]
        ]
    ], $poll);
});

test('start marker', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        * Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                ['text' => 'Green', 'marker' => '*'],
            ]
        ],
    ], $poll);
});

test('dash marker in multiple options', function() use ($poller) {
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '-'],
                ['text' => 'Tofu', 'marker' => '-'],
            ]
        ],
    ], $poll);
});

test('star marker in multiple options', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        * Pasta
        * Steak
        * Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => [
                ['text' => 'Pasta', 'marker' => '*'],
                ['text' => 'Steak', 'marker' => '*'],
                ['text' => 'Tofu', 'marker' => '*'],
            ]
        ],
    ], $poll);
});

test('parentheses separator in multiple options and their values', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        a) Pasta
        b) Steak
        c) Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => [
                'a' => ['text' => 'Pasta', 'marker' => 'a', 'separator' => ')'],
                'b' => ['text' => 'Steak', 'marker' => 'b', 'separator' => ')'],
                'c' => ['text' => 'Tofu', 'marker' => 'c', 'separator' => ')'],
            ]
        ],
    ], $poll);
});

test('dash marker in multiple options with spaces', function() use ($poller) {
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '-'],
                ['text' => 'Tofu', 'marker' => '-'],
            ]
        ],
    ], $poll);
});

test('different markers in multiple options mixed spaces', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        -   Pasta
        *  Steak
        a) Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '*'],
                'a' => ['text' => 'Tofu', 'marker' => 'a', 'separator' => ')'],
            ]
        ],
    ], $poll);
});
