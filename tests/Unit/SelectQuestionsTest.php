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
            'options' => [
                ['text' => 'Green', 'marker' => '-']
            ]
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
            'options' => [
                ['text' => 'Green', 'marker' => '*']
            ]
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '-'],
                ['text' => 'Tofu', 'marker' => '-'],
            ]
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '*'],
                ['text' => 'Steak', 'marker' => '*'],
                ['text' => 'Tofu', 'marker' => '*'],
            ]
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
            'options' => [
                'a' => ['text' => 'Pasta', 'marker' => 'a', 'separator' => ')'],
                'b' => ['text' => 'Steak', 'marker' => 'b', 'separator' => ')'],
                'c' => ['text' => 'Tofu', 'marker' => 'c', 'separator' => ')']
            ]
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '-'],
                ['text' => 'Tofu', 'marker' => '-']
            ]
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
            'options' => [
                ['text' => 'Pasta', 'marker' => '-'],
                ['text' => 'Steak', 'marker' => '*'],
                'a' => ['text' => 'Tofu', 'marker' => 'a', 'separator' => ')']
            ]
        ],
    ], $poll);
});

test('multiple options mixed parenthesis, spaces and underline', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite food
        aaaa)  Pasta
        bbb_bb) Steak
        c_c_1__c) Tofu
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite food',
            'type' => 'select',
            'options' => [
                'aaaa' => ['text' => 'Pasta', 'marker' => 'aaaa', 'separator' => ')'],
                'bbb_bb' => ['text' => 'Steak', 'marker' => 'bbb_bb', 'separator' => ')',],
                'c_c_1__c' => ['text' => 'Tofu', 'marker' => 'c_c_1__c', 'separator' => ')']
            ]
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

test('no option (parenthesis with space)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        a ) Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
        ],
        [
            'text' => 'a ) Green',
            'type' => 'input',
        ],        
    ], $poll);
});