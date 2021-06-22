<?php

$poller = new CCUFFS\Text\PollFromText();

test('attribute in select option (star)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} * Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, star)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}*Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, star)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }*Green
        {   "attr"  :    false     }   *    Blue
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ],
                [
                    'text' => 'Blue',
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (start)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} * Green is { my } favorite
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (star)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} * Green
    ');
})->throws(UnexpectedValueException::class);


test('attribute in select option (dash)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} - Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, dash)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}-Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, dash)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }-Green
        {   "attr"  :    false     }   -    Blue
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ],
                [
                    'text' => 'Blue',
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (dash)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} - Green is { my } favorite
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (dash)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} - Green
    ');
})->throws(UnexpectedValueException::class);


test('attribute in select option (parentheses)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} a) Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, parentheses)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}a)Green
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, parentheses)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }a)Green
        {   "attr"  :    false     }   b)    Blue
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'data' => ['attr' => true]
                ],
                'b' => [
                    'text' => 'Blue',
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (parentheses)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":"string", "field": 20} a) Green is { my } favorite
    ');

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green is { my } favorite',
                    'data' => ['attr' => 'string', 'field' => 20]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (parentheses)', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} a) Green
    ');
})->throws(UnexpectedValueException::class);
