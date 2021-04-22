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

        // $Allbranches = BankBranch::all()
        //     ->filter(function ($branch) {
        //         foreach ($branch->bankAccounts as $account) {
        //             if ($account->company_id == session('company_id'))
        //                 return true;
        //         }
        //     })
        //     ->map(function ($branch) {
        //         return [
        //             'id' => $branch->id,
        //             'name' => $branch->bank->name . " - " . $branch->address,
        //         ];
        //     });

        // $i = 0;
        // foreach ($Allbranches as $branch) {

        //     if ($branch) {

        //         $branches[$i] = $branch;
        //         $i++;
        //     }
        // };

        //CHECKING CODE
        $branches = BankBranch::all()
            // 'branches' => $branches,
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
        };
        // dd($branche);
        // 'year' => Year::where('id', session('year_id'))->first();
        // // dd($year),
        // dd($branches);
        // ]);
        // CHECKING END

        return Inertia::render('Confirmations/Create', [
            // 'branches' => BankBranch::all()
            'branches' => $branche,
            //     ->filter(function ($branch) {
            //         foreach ($branch->bankAccounts as $account) {
            //             if ($account->company_id == session('company_id'))
            //                 return true;
            //         }
            //     })
            //     ->map(function ($branch) {
            //         return [
            //             'id' => $branch->id,
            //             'name' => $branch->bank->name . " - " . $branch->address,
            //         ];
            //     }),
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }

    public function store(Req $request)
    {
        //        dd($request->all());
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

    // $sent = new Carbon($request->sent); 
    public function edit(BankConfirmation $confirmation)
    {
        return Inertia::render('Confirmations/Edit', [
            'confirmation' => [
                'id' => $confirmation->id,
                'sent' => $confirmation->sent,
                'reminder' => $confirmation->reminder,
                'confirm_create' => $confirmation->confirm_create,
                'received' => $confirmation->received,
                'company_id' => $confirmation->company_id,
                'branch_id' => $confirmation->branch_id,
                'year_id' => $confirmation->year_id,
            ],
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

    public function update(Req $request, BankConfirmation $confirmation)
    {
        // dd($confirmation);
        Request::validate([
            'sent' => ['required'],
            'reminder' => ['required'],
            'confirm_create' => ['required'],
            'received' => ['required'],
            //            'company_id' => ['required'],
            //            'year_id' => ['required'],
        ]);
        $reminder = new Carbon($request->reminder);
        $sent = new Carbon($request->sent);
        $confirm_create = new Carbon($request->confirm_create);
        $received = new Carbon($request->received);



        $confirmation->sent = $sent->format('Y-m-d');
        $confirmation->reminder =  $reminder->format('Y-m-d');
        $confirmation->confirm_create = $confirm_create->format('Y-m-d');
        $confirmation->received = $received->format('Y-m-d');
        //      $confirmation->company_id = Request::input('company_id');
        $confirmation->branch_id = Request::input('branch_id');
        //        $confirmation->year_id = Request::input('year_id');
        $confirmation->save();

        return Redirect::route('confirmations')->with('success', 'Bank Confirmation updated.');
    }

    public function destroy(BankConfirmation $confirmation)
    {
        $confirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }
}
