<?php

namespace App\Http\Controllers;

use App\Mail\LoanMail;
use App\Models\LoanRequest;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'amount'        => 'required|numeric|min:1',
            'duration'      => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0',
        ]);

        $monthlyPayment = $this->loanService->calculateMonthlyPayment(
            (float) $validated['amount'],
            (int)   $validated['duration'],
            (float) $validated['interest_rate']
        );

        $amortizationSchedule = $this->loanService->generateAmortizationSchedule(
            (float) $validated['amount'],
            (int)   $validated['duration'],
            (float) $validated['interest_rate'],
            $monthlyPayment
        );

        return view('simulate', [
            'loan'                 => (object) $validated,
            'monthlyPayment'       => $monthlyPayment,
            'amortizationSchedule' => $amortizationSchedule,
        ]);
    }

    public function sendMail(Request $request)
    {
        $validatedData = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string',
            'address' => 'required|string',
            'amount'  => 'required|numeric|min:1',
            'darly'   => 'required|numeric|min:1',
            'objet'   => 'required|string',
            'subject' => 'required|string',
            'npi'     => 'nullable|string',
            'status'  => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                Storage::makeDirectory('public/uploads', 0775, true);
                $files[] = $file->store('public/uploads');
            }
        }

        $loanRequest = LoanRequest::create(array_merge($validatedData, ['files' => $files]));

        Mail::to('contact@credixa.eu')->send(new LoanMail($loanRequest));

        return back()->with('success', 'Your loan request has been sent successfully.');
    }
}
