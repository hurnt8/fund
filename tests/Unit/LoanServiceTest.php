<?php

namespace Tests\Unit;

use App\Services\LoanService;
use PHPUnit\Framework\TestCase;

class LoanServiceTest extends TestCase
{
    private LoanService $service;

    protected function setUp(): void
    {
        $this->service = new LoanService();
    }

    public function test_monthly_payment_with_interest(): void
    {
        // 10 000 €, 12 mois, 5 % annuel → ~856.07 €
        $payment = $this->service->calculateMonthlyPayment(10000, 12, 5);
        $this->assertEquals(856.07, $payment);
    }

    public function test_monthly_payment_zero_interest(): void
    {
        // Sans intérêts : paiement = montant / durée
        $payment = $this->service->calculateMonthlyPayment(1200, 12, 0);
        $this->assertEquals(100.00, $payment);
    }

    public function test_amortization_schedule_has_correct_length(): void
    {
        $payment  = $this->service->calculateMonthlyPayment(10000, 12, 5);
        $schedule = $this->service->generateAmortizationSchedule(10000, 12, 5, $payment);
        $this->assertCount(12, $schedule);
    }

    public function test_amortization_schedule_last_balance_near_zero(): void
    {
        $payment      = $this->service->calculateMonthlyPayment(10000, 24, 6);
        $schedule     = $this->service->generateAmortizationSchedule(10000, 24, 6, $payment);
        $lastBalance  = end($schedule)['balance'];
        // Tolérance d'1 € pour les arrondis successifs
        $this->assertLessThanOrEqual(1.0, abs($lastBalance));
    }

    public function test_each_schedule_entry_has_required_keys(): void
    {
        $payment  = $this->service->calculateMonthlyPayment(5000, 6, 3);
        $schedule = $this->service->generateAmortizationSchedule(5000, 6, 3, $payment);

        foreach ($schedule as $entry) {
            $this->assertArrayHasKey('month', $entry);
            $this->assertArrayHasKey('payment', $entry);
            $this->assertArrayHasKey('principal', $entry);
            $this->assertArrayHasKey('interest', $entry);
            $this->assertArrayHasKey('balance', $entry);
        }
    }
}
