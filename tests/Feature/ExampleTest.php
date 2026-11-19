<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Guest visiting root should be redirected directly to Browse Directory.
     */
    public function test_guest_is_redirected_to_browse_items(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/items');
    }
}
