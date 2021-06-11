<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Year;
use App\Models\Company;
use App\Models\BankConfirmation;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BankConfirmationController extends Controller
{



    public function index()
    {

        // $balances = BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()
        //     ->map(function ($confirmation) {
        //         return [
        //             $sent = $confirmation->sent ? new Carbon($confirmation->sent) : null,
        //             $reminder = $confirmation->reminder ? new Carbon($confirmation->reminder) : null,
        //             $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
        //             $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
        //             $received = $confirmation->received ? new Carbon($confirmation->received) : null,


        //             'id' => $confirmation->id,
        //             'sent' => $sent ? $sent->format("M d Y") : null,
        //             'reminder' => $reminder ? $reminder->format("M d Y") : null,
        //             'confirm_create' => $confirm_create ?  $confirm_create->format("M d Y") : null,
        //             'received' => $received ? $received->format("M d Y") : null,
        //             'branch' => $confirmation->bankBranch->bank->name . " - " . $confirmation->bankBranch->address,
        //             'company' => $confirmation->company->name,
        //             'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
        //         ];
        //     });
        // dd($balances);
        return Inertia::render('Confirmations/Index', [
            'balances' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->paginate(5)->withQueryString()
                ->through(
                    fn ($confirmation) =>
                    [
                        $sent = $confirmation->sent ? new Carbon($confirmation->sent) : null,
                        $reminder = $confirmation->reminder ? new Carbon($confirmation->reminder) : null,
                        $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
                        $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
                        $received = $confirmation->received ? new Carbon($confirmation->received) : null,


                        'id' => $confirmation->id,
                        'sent' => $sent ? $sent->format("M d Y") : null,
                        'reminder' => $reminder ? $reminder->format("M d Y") : null,
                        'confirm_create' => $confirm_create ?  $confirm_create->format("M d Y") : null,
                        'received' => $received ? $received->format("M d Y") : null,
                        'branch' => $confirmation->bankBranch->bank->name . " - " . $confirmation->bankBranch->address,
                        'company' => $confirmation->company->name,
                        'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
                    ]
                ),

            'companies' => Company::all()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                    ];
                }),

            'years' => Year::where('company_id', session('company_id'))->get()
                ->map(function ($year) {
                    $end = new Carbon($year->end);
                    $begin = new Carbon($year->begin);
                    return [
                        'id' => $year->id,
                        'begin' => $begin->format("F j Y"),
                        'end' => $end->format("F j Y"),
                    ];
                }),
        ]);
    }




    public  function create()
    {
        $branches = BankBranch::all()
            ->filter(
                function ($branch) {

                    foreach ($branch->bankAccounts as $account) {

                        if ($account->company_id == session('company_id')) {

                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('sent')
                            ) {

                                return false;
                            } else {

                                return true;
                            }
                        }
                    }
                }
            )


            ->map(function ($branch) {
                $sent = Carbon::now();

                BankConfirmation::create([
                    'sent' => $sent->format('Y-m-d'),
                    'company_id' => session('company_id'),
                    'year_id' => session('year_id'),
                    'branch_id' => $branch->id,

                ]);
            });

        return back()->withInput();


        // }
    }




    public function show($id)
    {
        //
    }


    public function edit()
    {
        return Inertia::render('Confirmations/Edit', [

            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get(),

            'branches' => BankBranch::all()
                ->filter(function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id'))
                            return true;
                    }
                })

                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->bank->name . " - " . $branch->address,
                    ];
                }),
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }

    public function update(Req $request, BankConfirmation $balance)
    {


        // dd($request->balances);

        Request::validate([
            'sent' => ['required'],
        ]);

        foreach ($request->balances as $balance) {
            $bal = BankConfirmation::find($balance['id']);

            $bal->update([

                'sent' => $balance['sent'],
                'confirm_create' => $balance['confirm_create'],
                'reminder' =>  $balance['reminder'],
                'received' => $balance['received'],


            ]);
        }


        return Redirect::route('confirmations')->with('success', 'Bank Confirmation updated.');
    }

    public function destroy(BankConfirmation $confirmation)
    {
        $confirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }

    public function bankConfig()
    {

        $account = \App\Models\BankAccount::where('company_id', session('company_id'))->first();
        if ($account) {
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;
            // dd($end);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
            $templateProcessor->setValue('client', $year->company->name);
            $templateProcessor->setValue('end', $end->format("F j Y"));
            $templateProcessor->saveAs(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
            return response()->download(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Account first.');
        }
    }
}
