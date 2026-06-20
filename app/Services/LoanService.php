<?php

namespace App\Services;

class LoanService
{
    public function calculateMonthlyPayment(float $amount, int $duration, float $interestRate): float
    {
        if ($interestRate == 0) {
            return round($amount / $duration, 2);
        }
        $r = $interestRate / 100 / 12;
        return round(($amount * $r * pow(1 + $r, $duration)) / (pow(1 + $r, $duration) - 1), 2);
    }

    public function calculateAll(float $amount, float $interestRate, int $duration): array
    {
        $monthly            = $this->calculateMonthlyPayment($amount, $duration, $interestRate);
        $totalWithInterest  = round($monthly * $duration, 2);
        $totalCost          = round($totalWithInterest - $amount, 2);
        $schedule           = $this->generateAmortizationSchedule($amount, $duration, $interestRate, $monthly);

        return [
            'monthly_payment'      => $monthly,
            'total_with_interest'  => $totalWithInterest,
            'total_cost'           => $totalCost,
            'amortization_schedule'=> $schedule,
        ];
    }

    public function generateAmortizationSchedule(float $amount, int $duration, float $interestRate, float $monthlyPayment): array
    {
        $r        = $interestRate / 100 / 12;
        $balance  = $amount;
        $schedule = [];

        for ($i = 1; $i <= $duration; $i++) {
            $interest  = round($balance * $r, 2);
            $principal = round($monthlyPayment - $interest, 2);
            $balance   = max(0, round($balance - $principal, 2));

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
