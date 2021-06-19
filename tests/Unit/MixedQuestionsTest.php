<?php

$poller = new CCUFFS\Text\PollFromText();

test('one simple, one select', function() use ($poller) {
    $poll = $poller->parse('
        Favorite color?
        Choose best food
        - Pasta
        - Stake
    ');
    $this->assertEquals([
        [
            'text' => 'Favorite color?',
            'type' => 'input'
        ],
        [
            'text' => 'Choose best food',
            'type' => 'select',
            'options' => ['Pasta', 'Stake']
        ]
    ], $poll);
});