<?php

$poller = new CCUFFS\Text\PollFromText();

test('empty string', function() use ($poller) {
    $poll = $poller->parse('');
    $this->assertEquals([], $poll);
});

test('string with spaces and breaks only', function() use ($poller) {
    $str = '  

              

      

    ';
    $poll = $poller->parse($str);
    $this->assertEquals([], $poll);
});