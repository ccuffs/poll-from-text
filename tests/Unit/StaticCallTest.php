<?php

use CCUFFS\Text\PollFromText;

test('one simple question', function() {
    $poll = PollFromText::make('Favorite color?');
    $this->assertEquals([[
        'text' => 'Favorite color?',
        'type' => 'input'
    ]], $poll);
});