<?php

use CCUFFS\Text\PollFromText;

$poller = new PollFromText();

$config = [
    'attr_validation' => PollFromText::ATTR_AS_STRICT_JSON
];

test('attribute in select option (star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} * Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}*Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }*Green
        {   "attr"  :    false     }   *    Blue
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => ['attr' => true]
                ],
                [
                    'text' => 'Blue',
                    'marker' => '*',
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (start)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} * Green is { my } favorite
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'marker' => '*',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} * Green
    ', $config);
})->throws(UnexpectedValueException::class);


test('attribute in select option (dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} - Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}-Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }-Green
        {   "attr"  :    false     }   -    Blue
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',
                    'data' => ['attr' => true]
                ],
                [
                    'text' => 'Blue',
                    'marker' => '-',
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} - Green is { my } favorite
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'marker' => '-',
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} - Green
    ', $config);
})->throws(UnexpectedValueException::class);


test('attribute in select option (parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true} a) Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'marker' => 'a',
                    'separator' => ')',                    
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":true}a)Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'marker' => 'a',
                    'separator' => ')',                    
                    'data' => ['attr' => true]
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   "attr"  :    true     }a)Green
        {   "attr"  :    false     }   b)    Blue
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green',
                    'marker' => 'a',
                    'separator' => ')',                    
                    'data' => ['attr' => true]
                ],
                'b' => [
                    'text' => 'Blue',
                    'marker' => 'b',
                    'separator' => ')',                    
                    'data' => ['attr' => false]
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr":"string", "field": 20} a) Green is { my } favorite
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                'a' => [
                    'text' => 'Green is { my } favorite',
                    'marker' => 'a',
                    'separator' => ')',
                    'data' => ['attr' => 'string', 'field' => 20]
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr = "value" attr2 = "value"} a) Green
    ', $config);
})->throws(UnexpectedValueException::class);
