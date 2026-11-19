<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Guest visiting root should view the landing page.
     */
    public function test_guest_can_view_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Find what you’ve lost');
        $response->assertSee('Browse Directory');
    }
}
