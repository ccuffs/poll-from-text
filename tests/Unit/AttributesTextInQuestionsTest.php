<?php

use CCUFFS\Text\PollFromText;

$poller = new PollFromText();

$config = [
    'attr_validation' => PollFromText::ATTR_AS_TEXT
];

test('recognize attribute simple question', function() use ($poller, $config) {
    $poll = $poller->parse('
        {attr} Choose favorite color
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
            'data' => 'attr'            
        ],
    ], $poll);
});

test('recognize attribute select question', function() use ($poller, $config) {
    $poll = $poller->parse('
        {attr} Choose favorite color
        * Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => ['Green'],
            'data' => 'attr'
        ],
    ], $poll);
});

test('complext attribute list', function() use ($poller, $config) {
    $poll = $poller->parse('
        {"attr":"value", "attr2":"value"} Type favorite color
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Type favorite color',
            'type' => 'input',
            'data' => '"attr":"value", "attr2":"value"'
        ],
    ], $poll);
});

test('question with attribute char', function() use ($poller, $config) {
    $poll = $poller->parse('
        {attr_value} Choose { favorite } color
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose { favorite } color',
            'type' => 'input',
            'data' => 'attr_value'
        ],
    ], $poll);
});

test('not throw exception on wrong attribute format', function() use ($poller, $config) {
    expect($poller->parse('
            {attr = "value" attr2 = "value"} Choose favorite color
        ', $config)
    )->toBeArray();
});