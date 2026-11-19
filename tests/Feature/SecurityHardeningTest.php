<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function test_responses_contain_standard_security_headers(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_vercel_serverless_configuration(): void
    {
        $vercelJson = json_decode(file_get_contents(base_path('vercel.json')), true);

        $this->assertEquals('database', $vercelJson['env']['CACHE_STORE']);
        $this->assertEquals('database', $vercelJson['env']['SESSION_DRIVER']);
        $this->assertEquals('/tmp/storage/framework/views', $vercelJson['env']['VIEW_COMPILED_PATH']);
    }

    public function test_login_rate_limiting(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->post(route('login'), [
                'email' => 'rate.limit@school.edu',
                'password' => 'WrongPassword',
            ]);
        }

        // 21st attempt hits throttle:20,1
        $response = $this->post(route('login'), [
            'email' => 'rate.limit@school.edu',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(429);
    }
}
