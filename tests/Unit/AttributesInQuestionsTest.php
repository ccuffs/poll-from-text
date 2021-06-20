<?php

$poller = new CCUFFS\Text\PollFromText();

test('recognize attribute simple question', function() use ($poller) {
    $poll = $poller->parse('
        {attr} Choose favorite color
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
        ],
    ], $poll);
});

test('recognize attribute select question', function() use ($poller) {
    $poll = $poller->parse('
        {attr} Choose favorite color
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

test('complext attribute list', function() use ($poller) {
    $poll = $poller->parse('
        {attr:value attr:value} Choose favorite color
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
        ],
    ], $poll);
});

test('question with attribute char', function() use ($poller) {
    $poll = $poller->parse('
        {attr:value attr:value} Choose { favorite } color
    ');

    $this->assertEquals([
        [
            'text' => 'Choose { favorite } color',
            'type' => 'input',
        ],
    ], $poll);
});