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

class BankConfirmationController extends Controller
{


    public function index()
    {
        return Inertia::render('Confirmations/Index', [
            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()
                ->map(function ($confirmation) {
                    return [


                        'id' => $confirmation->id,
                        'sent' => $confirmation->sent,
                        'reminder' => $confirmation->reminder,
                        'confirm_create' => $confirmation->confirm_create,
                        'received' => $confirmation->received,
                        'branch' => $confirmation->bankBranch->bank->name . " - " . $confirmation->bankBranch->address,
                        'company' => $confirmation->company->name,
                        'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
                    ];
                }),
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




    public function create()
    {

        $branches = BankBranch::all()
            ->filter(
                function ($branch) {
                    // dd($branch);
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id')) {

                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('sent')
                            ) {
                                return false;
                            } else {

                                return true;
                            }
                            // return true;
                        }
                    }
                }
            )

            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->bank->name . " - " . $branch->address,
                    // 
                    // $branch->bankAccounts->first()->name . " - " .
                ];
            });


        $i = 0;
        foreach ($branches as $branch) {
            if ($branch) {

                $branche[$i] = $branch;
                $i++;
            }
            // dd($branche);    
        };




        return Inertia::render('Confirmations/Create', [
            // 'branches' => BankBranch::all()
            'branches' => $branche,
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }



    public function store(Req $request)
    {
        // dd($request->all());
        Request::validate([
            'sent' => ['required'],

            // 'received' => ['required']
        ]);

        $sent = new Carbon($request->sent);
        BankConfirmation::create([
            'sent' => $sent->format('Y-m-d'),
            'company_id' => session('company_id'),
            'year_id' => session('year_id'),
            'branch_id' => Request::input('branch_id'),
        ]);

        return Redirect::route('confirmations')->with('success', 'Bank Confirmation created.');
    }

    public function show($id)
    {
        //
    }


    public function edit()
    {
        return Inertia::render('Confirmations/Edit', [
            // return Inertia::render('Balances/Edit', [
            //         'accounts' => BankAccount::all()
            //             ->map(function ($account) {
            //                 return [
            // 'confirmation' => $data,
            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get(),

            // return[
            //     'id' => $confirmation->id,
            //     'sent' => $confirmation->sent,
            //     'reminder' => $confirmation->reminder,
            //     'confirm_create' => $confirmation->confirm_create,
            //     'received' => $confirmation->received,
            //     'company_id' => $confirmation->company_id,
            //     'branch_id' => $confirmation->branch_id,
            //     'year_id' => $confirmation->year_id,
            // ],
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
            // 'sent' => ['required'],
            // 'reminder' => ['required'],
            // 'confirm_create' => ['required'],
            // 'received' => ['required'],
            //            'company_id' => ['required'],
            //            'year_id' => ['required'],
        ]);

        // $sent = new Carbon($balance['sent']),
        // $reminder = new Carbon($balance['sent']),
        // $confirm_create = new Carbon($balance->confirm_create),
        // $received = new Carbon($balance->received),

        foreach ($request->balances as $balance) {
            $bal = BankConfirmation::find($balance['id']);

            $sent = new Carbon($balance['sent']);
            $confirm_create = new Carbon($balance['confirm_create']);
            $reminder = new Carbon($balance['reminder']);
            $received = new Carbon($balance['received']);


            $bal->update([

                // dd($balance),



                'sent' => $sent->format('Y-m-d'),
                'confirm_create' => $confirm_create->format('Y-m-d'),
                'reminder' => $ad = $reminder->format('Y-m-d'),
                dd($ad),
                'received' => $as = $received->format('Y-m-d'),
                dd($as),

            ]);
        }


        // $confirmation->sent = $sent->format('Y-m-d');
        // $confirmation->reminder = $reminder->format('Y-m-d');

        // $confirmation->confirm_create = $confirm_create->format('Y-m-d');
        // $confirmation->received = $received->format('Y-m-d');
        // //      $confirmation->company_id = Request::input('company_id');
        // $confirmation->branch_id = Request::input('branch_id');
        // //        $confirmation->year_id = Request::input('year_id');
        // $confirmation->save();

        return Redirect::route('confirmations')->with('success', 'Bank Confirmation updated.');
    }

    public function destroy(BankConfirmation $confirmation)
    {
        $confirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }
}
