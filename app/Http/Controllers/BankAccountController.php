<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankAccount;
use App\Models\BankBalance;
use App\Models\Year;
use App\Models\BankBranch;
use App\Models\Bank;
use App\Models\Company;
use Inertia\Inertia;
use Carbon\Carbon;
use Dompdf\Renderer;

class BankAccountController extends Controller
{
    public function index()
    {
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,address'],
        ]);
        $query = BankAccount::query();

        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } else {
            $query->orderBy(('name'), ('asc'));
        }

        // dd($query->all());

        $balances = $query
            ->where('company_id', session('company_id'))
            ->paginate(15)
            ->through(
                function ($branch) {
                    return
                        [
                            'id' => $branch->id,
                            'name' => $branch->name,
                            'type' => $branch->type,
                            'currency' => $branch->currency,
                            'branches' => $branch->bankBranch->bank->name . " - " . $branch->bankBranch->address,
                            'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                        ];
                }
            );

        //        dd($balances);

        if (BankBranch::all()->first()) {
            return Inertia::render(
                'Accounts/Index',
                [
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'balances' => $balances,
                    // 'balances' => BankAccount::all()
                    //     ->where('company_id', session('company_id'))
                    //     ->map(
                    //         function ($branch) {
                    //             return
                    //                 [
                    //                     'id' => $branch->id,
                    //                     'name' => $branch->name,
                    //                     'type' => $branch->type,
                    //                     'currency' => $branch->currency,
                    //                     'branches' => $branch->bankBranch->bank->name . " - " . $branch->bankBranch->address,
                    //                     'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                    //                 ];
                    //         }
                    //     ),
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

                ],

            );
        } else {
            return Redirect::route('branches.create', 'accounts')->with('success', 'Create Branch First');
        }
    }

    //BankAccount Create
    public function create()
    {
        $data  = BankBranch::all()->map->only('bank_id')->first();
        if ($data) {

            return Inertia::render('Accounts/Create', [

                //just fetch crete
                'balances' => BankAccount::all()
                    ->where('company_id', session('company_id'))
                    ->map(
                        function ($branch) {
                            return
                                [
                                    'id' => $branch->id,
                                    'name' => $branch->name,
                                    'type' => $branch->type,
                                    'currency' => $branch->currency,
                                    'branches' => $branch->bankBranch->bank->name . " - " . $branch->bankBranch->address,
                                    'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                                ];
                        }
                    ),

                // $branches = BankBranch::all()
                'branches' => BankBranch::all()
                    ->map(function ($branch) {
                        return
                            [
                                'id' => $branch->id,
                                'address' => $branch->bank->name . " - " . $branch->address,

                            ];
                    }),

                // dd($branches),
            ]);
        } else {
            return Redirect::route('branches.create', 'accounts')->with('success', 'Create Branch First');
        }
    }

    public function store(Req $request)
    {
        // dd($request->accounts);
        // dd($request);
        Request::validate([

            'accounts.*.name' => 'required|unique:App\Models\BankAccount,name',
            'accounts.*.type' => ['required'],
            'accounts.*.currency' => ['required'],

        ]);

        $accounts = $request->accounts;
        // dd($accounts);
        foreach ($accounts as $acc) {
            // dd($acc);
            BankAccount::create([
                'name' => $acc['name'],
                'type' => $acc['type'],
                'currency' => $acc['currency'],
                'branch_id' => $acc['branch_id']['id'],
                'company_id' => session('company_id'),

            ]);
        }
        return Redirect::route('accounts')->with('success', 'Bank Account created.');
    }

    //BankAccount Show
    public function show($id)
    {
        //
    }

    //BankAccount Edit
    public function edit()
    {
        return Inertia::render(
            'Accounts/Edit',
            [
                'data' => BankAccount::where('company_id', session('company_id'))
                    ->get()
                    ->map(
                        function ($account) {
                            return
                                [
                                    'id' => $account->id,
                                    'name' => $account->name,
                                    'type' => $account->type,
                                    'currency' => $account->currency,
                                    'branches' => $account->bankBranch->bank->name . " - " . $account->bankBranch->address,

                                ];
                        }
                    )

            ]
        );
    }
    //BankAccount Update
    public function update(Req $request, BankAccount $account)
    {
        Request::validate([
            'balances.*.name' => 'required',
        ]);


        foreach ($request->balances as $account) {

            $acc = BankAccount::find($account['id']);
            $acc->update([

                'name' => $account['name'],
                'type' => $account['type'],
                'currency' => $account['currency'],
            ]);
        }
        return Redirect::route('accounts')->with('success', 'Bank Account updated.');
    }
    //BanKAccount Delete
    public function destroy(BankAccount $account)
    {
        $account->delete();
        return Redirect::back()->with('success', 'Bank Account deleted.');
    }
}
