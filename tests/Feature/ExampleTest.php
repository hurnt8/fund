<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // GET / redirige vers la locale détectée (302), puis la page charge en 200
        $response = $this->get('/');

        $response->assertRedirect();
    }
}
