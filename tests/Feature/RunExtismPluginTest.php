<?php

use App\Models\Plugin;

test('can execute plugin function', function () {
    $plugin = Plugin::factory()->create();
    $output = $plugin->call('count_vowels', 'Yellow, World!');
    expect($output)->toBe('{"count":3,"total":3,"vowels":"aeiouAEIOU"}');
})->skip();
