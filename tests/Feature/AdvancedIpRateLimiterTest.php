<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdvancedIpRateLimiterTest extends TestCase
{
    use RefreshDatabase;

    public function test_ip_rate_limiting_blocks_after_20_requests()
    {
        $ip = '127.0.0.1';
        $limiterKey = 'ip_req_limit_' . $ip;
        $banKey = 'ip_ban_' . $ip;

        RateLimiter::clear($limiterKey);
        Cache::forget($banKey);

        for ($i = 0; $i < 20; $i++) {
            $response = $this->get('/');
            $response->assertStatus(200);
        }

        $response = $this->get('/');
        $response->assertStatus(403);

        $this->assertTrue(Cache::has($banKey));
    }
}
