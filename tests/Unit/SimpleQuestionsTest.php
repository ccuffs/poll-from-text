<?php

$poller = new CCUFFS\Text\PollFromText();

test('one simple question', function() use ($poller) {
    $poll = $poller->parse('Favorite color?');
    $this->assertEquals([[
        'text' => 'Favorite color?',
        'type' => 'input'
    ]], $poll);
});

test('two simple questions', function() use ($poller) {
    $poll = $poller->parse('
        Favorite color?
        Best food?
    ');
    $this->assertEquals([
        ['text' => 'Favorite color?', 'type' => 'input'],
        ['text' => 'Best food?', 'type' => 'input']
    ], $poll);
});

test('very short simple question', function() use ($poller) {
    $poll = $poller->parse('a');
    $this->assertEquals([[
        'text' => 'a',
        'type' => 'input'
    ]], $poll);
});

test('non-alpha chars simple question', function() use ($poller) {
    $question = 'sh - s- ej % @112338{}[]`"\k/_';
    
    $poll = $poller->parse($question);
    $this->assertEquals([[
        'text' => $question,
        'type' => 'input'
    ]], $poll);
});

test('multiline simple question', function() use ($poller) {
    $question = '
     Is this
        a 
             multiline question?
    ';
    $poll = $poller->parse($question, [
        'mutliline_question' => true
    ]);
    $this->assertEquals([[
        'text' => 'Is this a multiline question?',
        'type' => 'input'
    ]], $poll);
});

test('multiline parsed as one simple question', function() use ($poller) {
    $question = '
      This is one question
        This is another
    ';
    $poll = $poller->parse($question, [
        'mutliline_question' => false
    ]);
    $this->assertEquals([
        [
            'text' => 'This is one question',
            'type' => 'input'
        ],
        [
            'text' => 'This is another',
            'type' => 'input'
        ]
    ], $poll);
});