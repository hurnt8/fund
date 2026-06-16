<?php

namespace App\Services;

class LoanService
{
    public function calculateMonthlyPayment(float $amount, int $duration, float $interestRate): float
    {
        if ($interestRate == 0) {
            return round($amount / $duration, 2);
        }

        $monthlyRate = $interestRate / 100 / 12;
        $payment = ($amount * $monthlyRate * pow(1 + $monthlyRate, $duration))
            / (pow(1 + $monthlyRate, $duration) - 1);

        return round($payment, 2);
    }

    public function generateAmortizationSchedule(float $amount, int $duration, float $interestRate, float $monthlyPayment): array
    {
        $monthlyRate = $interestRate / 100 / 12;
        $balance     = $amount;
        $schedule    = [];

        for ($i = 1; $i <= $duration; $i++) {
            $interest  = round($balance * $monthlyRate, 2);
            $principal = round($monthlyPayment - $interest, 2);
            $balance   = round($balance - $principal, 2);

            $schedule[] = [
                'month'     => $i,
                'payment'   => $monthlyPayment,
                'principal' => $principal,
                'interest'  => $interest,
                'balance'   => $balance,
            ];
        }

        return $schedule;
    }
}
