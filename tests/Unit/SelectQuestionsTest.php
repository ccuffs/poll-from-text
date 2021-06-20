<?php

$poller = new CCUFFS\Text\PollFromText();

test('one option with dash', function() use ($poller) {
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

test('one option with star', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        * Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => ['Green']
        ],
    ], $poll);
});

test('multiple options with dashes', function() use ($poller) {
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

test('multiple options with stars', function() use ($poller) {
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
            'options' => ['Pasta', 'Steak', 'Tofu']
        ],
    ], $poll);
});

test('multiple options and their values', function() use ($poller) {
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
            'options' => ['a' => 'Pasta', 'b' => 'Steak', 'c' => 'Tofu']
        ],
    ], $poll);
});

test('multiple options with spaces and dashes', function() use ($poller) {
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

test('multiple options mixed spaces, dashes and stars', function() use ($poller) {
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
            'options' => ['Pasta', 'Steak', 'a' => 'Tofu']
        ],
    ], $poll);
});

test('no option (forgot dash or start)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
        ],
        [
            'text' => 'Green',
            'type' => 'input',
        ],        
    ], $poll);
});