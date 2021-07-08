<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankAccount;
use App\Models\BankBalance;
use App\Models\Year;
use App\Models\BankBranch;
use App\Models\Company;
use Inertia\Inertia;
use Carbon\Carbon;

class BankAccountController extends Controller
{
    public function index()
    {

        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name'],
        ]);

        $query = BankAccount::query();

        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        }

        return Inertia::render(
            'Accounts/Index',
            [
                'filters' => request()->all(['search', 'field', 'direction']),
                'balances' =>  $query->paginate(6),
                // 'balances' => BankAccount::where('company_id', session('company_id'))->paginate(6)
                //     ->through(
                //         fn ($branch) =>
                //         [
                //             'id' => $branch->id,
                //             'name' => $branch->name,
                //             'type' => $branch->type,
                //             'currency' => $branch->currency,
                //             'branches' => $branch->bankBranch->address,
                //             'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                //         ]
                //         // }
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
    }

    //BankAccount Create
    public function create()
    {
        $data  = BankBranch::all()->map->only('bank_id')->first();
        if ($data) {
            return Inertia::render('Accounts/Create', [
                'branches' => BankBranch::all()
                    ->map(function ($branch) {
                        return [
                            'id' => $branch->id,
                            'address' => $branch->address,
                            'bank_id' => $branch->bank_id,
                            'name' => $branch->bank->name,
                        ];
                    }),
            ]);
        } else {
            return Redirect::route('branches.create', 'accounts')->with('success', 'Create Branch First');
        }
    }

    public function store(Req $request)
    {
        // dd($request);
        Request::validate([
            'accounts.*.name' => 'required|unique:App\Models\BankAccount,name',
            'accounts.*.type' => ['required'],
            'accounts.*.currency' => ['required'],

        ]);

        $accounts = $request->accounts;
        foreach ($accounts as $acc) {
            BankAccount::create([
                'name' => $acc['name'],
                'type' => $acc['type'],
                'currency' => $acc['currency'],
                'branch_id' => $acc['branch_id'],
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
    public function edit(BankAccount $account)
    {
        return Inertia::render('Accounts/Edit', [
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
                'currency' => $account->currency,
                'company_id' => $account->company_id,
                'branch_id' => $account->branch_id,
            ],
            'branches' => BankBranch::all('id', 'address'),
        ]);
    }

    //BankAccount Update
    public function update(Req $request, BankAccount $account)
    {
        Request::validate([
            'name' => 'required|unique:App\Models\BankAccount,name',
            'type' => ['required'],
            'currency' => ['required'],
        ]);

        $account->name = Request::input('name');
        $account->type = Request::input('type');
        $account->currency = Request::input('currency');
        $account->branch_id = Request::input('branch_id');
        $account->save();
        return Redirect::route('accounts')->with('success', 'Bank Account updated.');
    }

    //BanKAccount Delete
    public function destroy(BankAccount $account)
    {
        $account->delete();
        return Redirect::back()->with('success', 'Bank Account deleted.');
    }
}
