<?php

use CCUFFS\Text\PollFromText;

$poller = new PollFromText();

$config = [
    'attr_validation' => PollFromText::ATTR_AS_TEXT,
];

test('attribute in select option (star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr} * Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr}*Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, star)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   attr_true     }*Green
        {   attr_false    }   *    Blue
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => 'attr_true'
                ],
                [
                    'text' => 'Blue',
                    'marker' => '*',                    
                    'data' => 'attr_false'
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (start)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr} * Green is { my } favorite
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'marker' => '*',
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});


test('throw exception on wrong attribute format (star)', function() use ($poller, $config) {
    expect(
        $poll = $poller->parse('
            Choose favorite color
            {attr = "value" attr2 = "value"} * Green
        ', $config)
    )->toBeArray();
});


test('attribute in select option (dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr} - Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',                    
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr}-Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',                    
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   attr_true     }-Green
        {   attr_false    }   -    Blue
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '-',
                    'data' => 'attr_true'
                ],
                [
                    'text' => 'Blue',
                    'marker' => '-',                    
                    'data' => 'attr_false'
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (dash)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr} - Green is { my } favorite
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green is { my } favorite',
                    'marker' => '-',
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});


test('not throw exception on wrong attribute format (dash)', function() use ($poller, $config) {
    expect(
        $poll = $poller->parse('
            Choose favorite color
            {attr = "value" attr2 = "value"} - Green
        ', $config)
    )->toBeArray();
});


test('attribute in select option (parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr} a) Green
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
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (no spaces, parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr}a)Green
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
                    'data' => 'attr'
                ]
            ]
        ],
    ], $poll);
});

test('attribute in select option (crazy format, parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {   attr_true     }a)Green
        {   attr_false    }   b)    Blue
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
                    'data' => 'attr_true'
                ],
                'b' => [
                    'text' => 'Blue',
                    'marker' => 'b',
                    'separator' => ')',
                    'data' => 'attr_false'
                ]
            ]
        ],
    ], $poll);
});

test('option with attribute char (parentheses)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {attr_string field_20} a) Green is { my } favorite
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
                    'data' => 'attr_string field_20'
                ]
            ]
        ],
    ], $poll);
});


test('not throw exception on wrong attribute format (parentheses)', function() use ($poller, $config) {
    expect(
        $poll = $poller->parse('
            Choose favorite color
            {attr = "value" attr2 = "value"} a) Green
        ', $config)
    )->toBeArray();
});


test('attribute in select option (star, quotes)', function() use ($poller, $config) {
    $poll = $poller->parse('
        Choose favorite color
        {"attr"} * Green
    ', $config);

    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => [
                [
                    'text' => 'Green',
                    'marker' => '*',
                    'data' => '"attr"'
                ]
            ]
        ],
    ], $poll);
});