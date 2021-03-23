<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankAccount;
use App\Models\BankBalance;
use App\Models\Year;
use App\Models\Company;
use Inertia\Inertia;

class BankBalanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Balances/Index', [
            'data' => BankBalance::where('company_id',session('company_id'))->where('year_id',session('year_id'))->get(),
            'companies' => Company::all()
                ->map(function($company){
                    return [
                    'id' => $company->id,
                    'name' => $company->name,
                    ];
                }), 
            'years' => Year::where('company_id',session('company_id'))->get()
                ->map(function($year){
                    return [
                    'id' => $year->id,
                    'begin' => $year->begin,
                    'end' => $year->end,
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Balances/Create',[
            'accounts' => BankAccount::where('company_id',session('company_id'))->get()
                ->map(function ($account){
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'type' => $account->type,
                        'currency' => $account->currency,
                        'branch' => $account->bankBranch->bank->name." - ".$account->bankBranch->address,
//                        'company_id' => $account->company_id,
                    ];
                }),
        ]);
    }

    public function store(Req $request)
    {
//dd($request);
        Request::validate([
//            'balances.*.company_id' => ['required'],
            'balances.*.account_id' => ['required'],
//            'balances.*.year_id' => ['required'],
        ]);
        foreach($request->balances as $balance){
            BankBalance::create([
                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
                'company_id' => session('company_id'),
                'account_id' => $balance['account_id'],
                'year_id' => session('year_id'),
            ]);
        }

        return Redirect::route('balances')->with('success', 'Bank Balance created.');
    }

    public function show($id)
    {
        //
    }

    public function edit(BankBalance $balance)
    {
        return Inertia::render('Balances/Edit', [
            'accounts' => BankAccount::all()
                ->map(function ($account){
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'type' => $account->type,
                        'currency' => $account->currency,
                        'branch' => $account->bankBranch->bank->name." - ".$account->bankBranch->address,
                        'company_id' => $account->company_id,
                    ];
                }),
            'data' => BankBalance::where('company_id',session('company_id'))->where('year_id',session('year_id'))->get(),
        ]);
    }

    public function update(Req $request, BankBalance $balance)
    {
//    dd($request->balances);

        Request::validate([
//            'balances.*.company_id' => ['required'],
            'balances.*.account_id' => ['required'],
//            'balances.*.year_id' => ['required'],
        ]);

    foreach($request->balances as $balance){
            $bal = BankBalance::find($balance['id']);
            $bal->update([
                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
//                'company_id' => $balance['company_id'],
                'account_id' => $balance['account_id'],
//                'year_id' => $balance['year_id'],
            ]);
        }

        return Redirect::route('balances')->with('success', 'Bank Balance updated.');
    }

    public function destroy(BankBalance $balance)
    {
        $balance->delete();
        return Redirect::back()->with('success', 'Bank Balance deleted.');
    }
}
