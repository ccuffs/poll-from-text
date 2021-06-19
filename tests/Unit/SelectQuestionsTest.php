<?php

$poller = new CCUFFS\Text\PollFromText();

test('select question with one option', function() use ($poller) {
    $poll = $poller->parse('
        Choose favorite color
        - Green
    ');

    $this->assertTrue(count($poll) == 1);
    $this->assertEquals([
        [
            'text' => 'Choose favorite color',
            'type' => 'select',
            'options' => ['Green']
        ],
    ], $poll);
});
