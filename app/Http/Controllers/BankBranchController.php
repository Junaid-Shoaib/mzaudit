<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use Inertia\Inertia;

class BankBranchController extends Controller
{
    public function index()
    {
        return Inertia::render('Branches/Index', [
            'data' => BankBranch::all()
                ->map(function ($branch){
                    return [
                        'id' => $branch->id,
                        'address' => $branch->address,
                        'bank_id' => $branch->bank_id,
                        'name' => $branch->bank->name,
                    ];
                }), 
        ]);
    }

    public function create()
    {
        return Inertia::render('Branches/Create',[
            'banks' => \App\Models\Bank::all()->map->only('id','name')
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
            'banks' => \App\Models\Bank::all()->map->only('id','name'),
        ]);
    }

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

    public function destroy(BankBranch $branch)
    {
        $branch->delete();
        return Redirect::back()->with('success', 'Bank Branch deleted.');
    }
}
