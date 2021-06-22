<?php

$poller = new CCUFFS\Text\PollFromText();

test('recognize attribute simple question', function() use ($poller) {
    $poll = $poller->parse('
        {"attr":true} Choose favorite color
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'input',
            'data' => ['attr' => true]            
        ],
    ], $poll);
});

test('recognize attribute select question', function() use ($poller) {
    $poll = $poller->parse('
        {"attr":true} Choose favorite color
        * Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => ['Green'],
            'data' => ['attr' => true]
        ],
    ], $poll);
});

test('complext attribute list', function() use ($poller) {
    $poll = $poller->parse('
        {"attr":"value", "attr2":"value"} Type favorite color
    ');

    $this->assertEquals([
        [
            'text' => 'Type favorite color',
            'type' => 'input',
            'data' => ['attr' => 'value', 'attr2' => 'value']
        ],
    ], $poll);
});

test('question with attribute char', function() use ($poller) {
    $poll = $poller->parse('
        {"attr":"value"} Choose { favorite } color
    ');

    $this->assertEquals([
        [
            'text' => 'Choose { favorite } color',
            'type' => 'input',
            'data' => ['attr' => 'value']
        ],
    ], $poll);
});

test('throw exception on wrong attribute format', function() use ($poller) {
    $poll = $poller->parse('
        {attr = "value" attr2 = "value"} Choose favorite color
    ');
})->throws(UnexpectedValueException::class);