<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankAccount;
use App\Models\Bank;
use App\Models\BankBranch;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('Accounts/Index', [
            'data' => BankAccount::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Accounts/Create',[
 //           'filters' => Request::all('bank'),
            'branches' => BankBranch::all()
 //               ->filter(Request::only('bank'))
                ->map(function ($branch){
                    return [
                        'id' => $branch->id,
                        'address' => $branch->address,
                        'bank_id' => $branch->bank_id,
                        'name' => $branch->bank->name,
                    ];
                }),
            'banks' =>  Bank::all('id','name'),
        ]);
    }

    public function store(Req $request)
    {
/*
        $accounts=collect((Request::all()));
        foreach($accounts as $account){
            BankAccount::create([
                'name' => $account['name'],
                'type' => $account['type'],
                'currency' => $account['currency'],
                'branch_id' => $account['branch_id'],
                'company_id' => $account['company_id'],
            ]);
        }
*/

        Request::validate([
            '*.name' => ['required'],
            '*.type' => ['required'],
        ]);

        $accounts = $request->all();
        foreach($accounts as $account){
            BankAccount::create([
                'name' => $account['name'],
                'type' => $account['type'],
                'currency' => $account['currency'],
                'branch_id' => $account['branch_id'],
                'company_id' => $account['company_id'],
            ]);
        }
/*
        dd($accounts);
        BankAccount::create([
            'name' => Request::input('name'),
            'type' => Request::input('type'),
            'currency' => Request::input('currency'),
            'branch_id' => Request::input('branch_id'),
            'company_id' => Request::input('company_id'),
        ]);

*/
        return Redirect::route('accounts')->with('success', 'Bank Account created.');
    }

    public function show($id)
    {
        //
    }

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
            'branches' => BankBranch::all('id','address'),
        ]);
    }

    public function update(Req $request, BankAccount $account)
    {
        Request::validate([
            'name' => ['required'],
            'type' => ['required'],
        ]);

        $account->name = Request::input('name');
        $account->type = Request::input('type');
        $account->currency = Request::input('currency');
        $account->branch_id = Request::input('branch_id');
        $account->company_id = Request::input('company_id');
        $account->save();

        return Redirect::route('accounts')->with('success', 'Bank Account updated.');
    }

    public function destroy(BankAccount $account)
    {
        $account->delete();
        return Redirect::back()->with('success', 'Bank Account deleted.');
    }
}
