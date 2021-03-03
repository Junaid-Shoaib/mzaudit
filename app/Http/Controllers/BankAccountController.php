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
            'data' => BankAccount::all()->paginate(),
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
        Request::validate([
            'name' => ['required'],
            'type' => ['required'],
        ]);

        BankAccount::create([
            'name' => Request::input('name'),
            'type' => Request::input('type'),
            'currency' => Request::input('currency'),
            'branch_id' => Request::input('branch_id'),
            'company_id' => Request::input('company_id'),
        ]);

        return Redirect::route('accounts')->with('success', 'Bank Account created.');
    }

    public function show($id)
    {
        //
    }

    public function edit(BankBranch $branch)
    {
        return Inertia::render('Branches/Edit', [
            'branch' => [
                'id' => $branch->id,
                'bank_id' => $branch->bank_id,
                'address' => $branch->address,
            ],
        ]);
    }

    public function update(Req $request, BankBranch $branch)
    {
        Request::validate([
            'name' => ['required'],
            'type' => ['required'],
        ]);

        $branch->bank_id = Request::input('bank_id');
        $branch->address = Request::input('address');
        $branch->save();

        return Redirect::route('branches')->with('success', 'Bank Branch updated.');
    }

    public function destroy(BankBranch $branch)
    {
        $branch->delete();
        return Redirect::back()->with('success', 'Bank Branch deleted.');
    }
}
