<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoanTest extends TestCase
{
    public function test_simulation_returns_view_with_results(): void
    {
        $response = $this->post('/loan/simulate', [
            'amount'        => 10000,
            'duration'      => 12,
            'interest_rate' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('simulate');
        $response->assertViewHas('monthlyPayment');
        $response->assertViewHas('amortizationSchedule');
    }

    public function test_simulation_fails_without_required_fields(): void
    {
        $response = $this->post('/loan/simulate', []);

        $response->assertSessionHasErrors(['amount', 'duration', 'interest_rate']);
    }

    public function test_simulation_fails_with_negative_amount(): void
    {
        $response = $this->post('/loan/simulate', [
            'amount'        => -500,
            'duration'      => 12,
            'interest_rate' => 5,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_locale_routes_return_200(): void
    {
        foreach (['en', 'fr', 'de', 'es', 'it'] as $locale) {
            $this->get("/{$locale}")->assertStatus(200);
        }
    }

    public function test_root_redirects_to_detected_locale(): void
    {
        $response = $this->get('/', ['Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8']);
        $response->assertRedirect('/fr');
    }

    public function test_root_redirects_to_english_for_unsupported_locale(): void
    {
        $response = $this->get('/', ['Accept-Language' => 'zh-CN,zh;q=0.9']);
        $response->assertRedirect('/en');
    }

    public function test_root_redirects_to_english_without_accept_language(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/en');
    }
}
