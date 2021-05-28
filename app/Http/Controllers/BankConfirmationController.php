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

        return Inertia::render('Confirmations/Index', [
            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()
                ->map(function ($confirmation) {

                    // if ($confirmation->reminder) {
                    //     $reminder = new Carbon($confirmation->reminder);
                    //     $reminder = $reminder->format("M d Y");
                    // } else {
                    //     $reminder = null;
                    // }
                    return [


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

                    // if ($branch->bankAccounts()->first()) {
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
                $sent = Carbon::now();
                // dd($sent);
                BankConfirmation::create([
                    // dd($branch),
                    'sent' => $sent->format('Y-m-d'),
                    'company_id' => session('company_id'),
                    'year_id' => session('year_id'),
                    'branch_id' => $branch->id,

                ]);
            });



        return Redirect()->back();
        // }
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

        foreach ($request->balances as $balance) {
            $bal = BankConfirmation::find($balance['id']);
            // dd($balance);

            // $sent = new Carbon($balance['sent']);
            // $confirm_create = new Carbon($balance['confirm_create']);
            // $reminder = new Carbon($balance['reminder']);
            // $received = new Carbon($balance['received']);
            $bal->update([

                'sent' => $balance['sent'],
                'confirm_create' => $balance['confirm_create'],
                'reminder' =>  $balance['reminder'],
                'received' => $balance['received'],


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
