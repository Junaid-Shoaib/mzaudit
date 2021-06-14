<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Bank;
use App\Models\BankAccount;
use Inertia\Inertia;
use PhpParser\Node\Expr\Cast\Bool_;

use function PHPUnit\Framework\isTrue;

class BankBranchController extends Controller
{



    public function index()
    {
        return Inertia::render('Branches/Index', [
            'balances' => BankBranch::paginate(6)
                ->through(
                    fn ($branch) =>
                    [
                        'id' => $branch->id,
                        'address' => $branch->address,
                        'bank_id' => $branch->bank_id,
                        'name' => $branch->bank->name,

                        'delete' => BankAccount::where('branch_id', $branch->id)->first() ? false : true,
                    ]
                ),
        ]);
    }

    //Create Branches
    public function create($accounts)
    {

        $data  = Bank::all()->map->only('id')->first();
        if ($data) {
            return Inertia::render('Branches/Create', [
                "accounts" => $accounts,
                'banks' => \App\Models\Bank::all()->map->only('id', 'name')
            ]);
        } else {
            return Redirect::route('banks.create', 'accounts')->with('success', 'Create Bank first.');
        }
    }

    public function store(Req $request)
    {
        Request::validate([
            'bank_id' => ['required'],
            'address' => ['required'],
        ]);

        BankBranch::create([
            'bank_id' => Request::input('bank_id'),
            'address' => ucwords(Request::input('address')),
        ]);
        if ($request->accounts == 'accounts') {
            return Redirect::route('accounts.create')->with('success', 'Bank Branch created.');
        } else {
            return Redirect::route('branches')->with('success', 'Bank Branch created.');
        }
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
        $branch->address = ucwords(Request::input('address'));
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
