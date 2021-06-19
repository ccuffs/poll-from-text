<?php

$poller = new CCUFFS\Text\PollFromText();

test('one simple question', function() use ($poller) {
    $poll = $poller->parse('Favorite color?');
    $this->assertEquals([[
        'text' => 'Favorite color?',
        'type' => 'input'
    ]], $poll);
});

test('two simple questions (easy)', function() use ($poller) {
    $poll = $poller->parse('
        Favorite color?
        Best food?
    ');
    $this->assertEquals([
        ['text' => 'Favorite color?', 'type' => 'input'],
        ['text' => 'Best food?', 'type' => 'input']
    ], $poll);
});
