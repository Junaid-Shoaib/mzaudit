<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\BankAccount;
use Inertia\Inertia;

class BankBranchController extends Controller
{
    public function index()
    {
        return Inertia::render('Branches/Index', [
            'data' => BankBranch::all()
                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'address' => $branch->address,
                        'bank_id' => $branch->bank_id,
                        'name' => $branch->bank->name,
                        'delete' => BankAccount::where('branch_id', $branch->id)->first() ? false : true,

                    ];
                }),
        ]);
    }

    //Create Branches
    public function create()
    {
        return Inertia::render('Branches/Create', [
            'banks' => \App\Models\Bank::all()->map->only('id', 'name')
        ]);
    }

    public function store(Req $request)
    {
        Request::validate([
            'bank_id' => ['required'],
            'address' => ['required'],
        ]);

        BankBranch::create([
            'bank_id' => Request::input('bank_id'),
            'address' => Request::input('address'),
        ]);

        return Redirect::route('branches')->with('success', 'Bank Branch created.');
    }

    //Branches show
    public function show($id)
    {
        //
    }

    //Branches Edit
    public function edit(BankBranch $branch)
    {
        return Inertia::render('Branches/Edit', [
            'branch' => [
                'id' => $branch->id,
                'bank_id' => $branch->bank_id,
                'address' => $branch->address,
            ],
            'banks' => \App\Models\Bank::all()->map->only('id', 'name'),
        ]);
    }

    //Branches Update
    public function update(Req $request, BankBranch $branch)
    {
        Request::validate([
            'bank_id' => ['required'],
            'address' => ['required'],
        ]);

        $branch->bank_id = Request::input('bank_id');
        $branch->address = Request::input('address');
        $branch->save();

        return Redirect::route('branches')->with('success', 'Bank Branch updated.');
    }
    //Branches Delete
    public function destroy(BankBranch $branch)
    {
        $branch->delete();
        return Redirect::back()->with('success', 'Bank Branch deleted.');
    }
}
