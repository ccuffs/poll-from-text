<?php

test('construtor with no params', function() {
    $poller = new CCUFFS\Text\PollFromText();
    expect($poller)->toBeInstanceOf(CCUFFS\Text\PollFromText::class);
});
