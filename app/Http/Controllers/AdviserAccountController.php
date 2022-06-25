<?php

namespace App\Http\Controllers;

use App\Models\AdviserAccount;
use App\Models\AdviserConfirmation;
use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use App\Models\Year;
use App\Models\Setting;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AdviserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,address'],
        ]);
        $query = AdviserAccount::query();

        if (request('search')) {
            $query->where('company_id', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } else {
            $query->orderBy(('company_id'), ('asc'));
        }


        $active_co = Setting::where('user_id', Auth::user()->id)
        ->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();




        $balances = $query
        ->where('company_id', session('company_id'))
        ->paginate(15)
        ->through(
            function ($branch) {
                return
                    [
                        'id' => $branch->id,
                        'advisor_id' => $branch->advisor->name,
                        'company_id' => $branch->company->name,
                        // 'currency' => $branch->currency,
                        // 'branches' => $branch->Advisor->bank->name . " - " . $branch->Advisor->address,
                        // 'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                        'delete' => true,
                    ];
            }
        );
// dd($balances);

        if (Advisor::all()->first()) {
            return Inertia::render(
                'Advisor_accounts/Index',
                [
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'balances' => $query
                    ->where('company_id', session('company_id'))
                    ->paginate(15)
                    ->through(
                        function ($branch) {
                            return
                                [
                                    'id' => $branch->id,
                                    'advisor_id' => $branch->advisor->name,
                                    'company_id' => $branch->company->name,
                                    // 'currency' => $branch->currency,
                                    // 'branches' => $branch->Advisor->bank->name . " - " . $branch->Advisor->address,
                                    'delete' => AdviserConfirmation::where('advisor_id', $branch->id)->first() ? false : true,
                                    // 'delete' => true,
                                ];
                        }
                    ),

                    'dataEdit' => AdviserAccount::where('company_id', session('company_id'))->first(),

                    'companies' => Company::all()
                    ->map(function ($company) {
                        return [
                            'id' => $company->id,
                            'name' => $company->name,
                        ];
                    }),


                    'cochange' => $coch_hold,

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

                ],

            );
        } else {
            return Redirect::route('advisors.create',)->with('success', 'Create Advisor First');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ]);
        // dd($balances);
        $data  = Advisor::all()->map->only('id')->first();
        $company = Company::where('id', session('company_id'))->first();
        // dd(session('year_id'));
        // dd($company->name);
        if ($data) {

                            return Inertia::render('Advisor_accounts/Create', [

                                'company_name' =>$company ? $company->name : '',
                                //just fetch crete
                                'balances' => AdviserAccount::all()
                                ->where('company_id', session('company_id'))
                                ->map(
                                    function ($branch) {
                                        return
                                        [
                                            'id' => $branch->id,
                                            'advisors' => $branch->Advisor->name . " - " . $branch->Advisor->type,
                                            // 'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                                            // 'delete' => true,
                                        ];
                                    }
                                ),

                                // $branches = Advisor::all()
                                'advisors' => Advisor::all()
                                ->map(function ($branch) {
                                    return
                                    [
                                        'id' => $branch->id,
                                        'address' => $branch->name . " - " . $branch->type,

                                    ];
                                }),


                            ]);
                        } else {
                            return Redirect::route('advisors.create' )->with('success', 'Create Advisor First');
                        }
                    }
                    //


                    /**
                     * Store a newly created resource in storage.
                     *
                     * @param  \Illuminate\Http\Request  $request
                     * @return \Illuminate\Http\Response
                     */
    public function store(Request $request)
    {
        $request->validate([
            'accounts.*.advisor_id' => 'required|unique:App\Models\AdviserAccount,advisor_id',
        ]);
        $accounts = $request->accounts;
            foreach ($accounts as $acc) {
                // dd($acc);
            AdviserAccount::create([
                'advisor_id' => $acc['advisor_id']['id'],
                'company_id' => session('company_id'),
                'year_id' => session('year_id'),
            ]);
    }

    return Redirect::route('advisor_accounts',)->with('success', 'Advisor Account Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function show(AdviserAccount $adviserAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(AdviserAccount $adviserAccount)
    {
        //
        // $data = AdviserAccount::where('company_id', session('company_id'))
        //             ->where('year_id', session('year_id'))
        //             ->get()
        //             ->map(
        //                 function ($account) {
        //                     return
        //                         [
        //                             'id' => $account->id,
        //                             'name' => $account->company->name,
        //                             // 'type' => $account->type,
        //                             // 'currency' => $account->currency,
        //                             'advisor_id' => $account->advisor->name . " - " . $account->advisor->type,
        //                         ];
        //                 }
        //             );
        //     dd($data);

        return Inertia::render(
            'Advisor_accounts/Edit',
            [
                'data' => AdviserAccount::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->get()
                    ->map(
                        function ($account) {
                            return
                            [
                                'id' => $account->id,
                                'name' => $account->company->name,
                                // 'type' => $account->type,
                                // 'currency' => $account->currency,
                                'advisor_id' => $account->advisor->name . " - " . $account->advisor->type,
                            ];
                        }
                    )

            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdviserAccount $adviserAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdviserAccount $adviserAccount)
    {
        $adviserAccount->delete();
        return Redirect::back()->with('success', 'Advisor Account deleted.');
    }
}
