<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class BankBranchController extends Controller
{



    public function index()
    {

        request()->validate([
            'direction' => ['in:asc,desc'],
        'field' => ['in:bank_id,address'],
        ]);

        $query = BankBranch::query();

        if (request('search')) {
            $search = request('search');
            $query->where('address', 'LIKE', '%' . $search . '%')
            ->orWhereHas('bank', function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } 
        else {
            $query->orderBy(('bank_id'), ('asc'));
        }


        return Inertia::render(
            'Branches/Index',
            [
                'filters' => request()->all(['search', 'field', 'direction']),
                'balances' => $query->paginate(10)
                    ->through(
                        function ($branch) {

                            return [
                                'id' => $branch->id,
                                'address' => $branch->address,
                                'bank_id' => $branch->bank_id,
                                'name' => $branch->bank->name,
                                'delete' => BankAccount::where('branch_id', $branch->id)->first() ? false : true,
                                
                            ];
                        }
                    ),

                    'role' => auth()->user()->role != 2 ? true : false,
             

                ]

        );
    }

    //Create Branches
    public function create($accounts)
    {
        $branches = BankBranch::all()
            ->map(
                function ($branch) {
                    return [
                        'add' => $branch->address,
                        'bank_id' => $branch->bank_id,
                    ];
                }
            );
        $data  = Bank::all()->map->only('id')->first();

        if ($data) {
            return Inertia::render('Branches/Create', [
                "accounts" => $accounts,
                "branches" => $branches,
                'banks' => \App\Models\Bank::all()->map->only('id', 'name'),
                // $banks = \App\Models\Bank::all()->map->only('id', 'name'),
                // dd($banks),
            ]);
        } else {
            return Redirect::route('banks.create', 'accounts')->with('success', 'Create Bank first.');
        }
    }

    public function store(Req $request)
    {
        // dd($request);
        Request::validate([
            'bank_id' => ['required'],
            'address' => ['required'],
        ]);

        $branches = BankBranch::all()->where("bank_id", $request->bank_id);
        $replace = str_replace([" "], "\n", $request->address);
        $explode = explode("\n", $replace);
           $address = "";
        foreach ($explode as $ex) {
            if ($ex != "") {
                $address = $address .  $ex . "\n";
            }
        }

        
        $branchi = true;
        foreach ($branches as $branch) {
            $replace = str_replace([" "], "\n", $branch->address);
            $explode = explode("\n", $replace);
            $add = "";
            foreach ($explode as $ex) {
                if ($ex != "") {
                    $add = $add .  $ex . "\n";
                }
            }
            if (strtolower($add) == strtolower($address)) {

                $branchi = false;
            }
        }
    
        if ($branchi == true) {
            
            BankBranch::create([                
                'bank_id' => Request::input('bank_id')['id'],
                'address' => ucwords($address),
            ]);

            if ($request->accounts == 'accounts') {
                return Redirect::route('accounts.create')->with('success', 'Bank Branch created.');
            } else {
                return Redirect::route('branches')->with('success', 'Bank Branch created.');
            }
        } else {
            return Redirect::route('branches.create', 'create')->with('success', 'The Name has Already been taken.');
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


        $branches = BankBranch::all()->where("bank_id", $request->bank_id);
        $replace = str_replace([" "], "\n", $request->address);
      
        $explode = explode("\n", $replace);
        $address = "";
        foreach ($explode as $ex) {
            if ($ex != "") {
                $address = $address .  $ex . "\n";
            }
        }

        $branchi = true;
        foreach ($branches as $branch) {
            $replace1 = str_replace([" "], "\n", $branch->address);
            $explode = explode("\n", $replace1);
            $add = "";
            foreach ($explode as $ex) {
                if ($ex != "") {
                    $add = $add .  $ex . "\n";
                }
            }
           
            if (strtolower($add) == strtolower($address)) {
                $branchi = false;
            }
            
        }
        if ($branchi == true) {
            $branch->bank_id = Request::input('bank_id');
            $branch->address = ucwords($address);
            $branch->save();
            return Redirect::route('branches')->with('success', 'Bank Branch updated.');

        } 
        else 
        {
            return Redirect::route('branches.edit', $branch->id)->with('success', 'The Name has Already been taken.');
        }

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
