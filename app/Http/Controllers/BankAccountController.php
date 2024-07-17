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
use App\Models\BankConfirmation;
use App\Models\Company;
use App\Models\Setting;
use App\Rules\UniqueBranchForCompany;
use Illuminate\Support\Facades\Auth;
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


        $active_co = Setting::where('user_id', Auth::user()->id)
        ->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();

        $bal = $query
        ->where('company_id', session('company_id'))
        ->paginate(15)
        ->through(
            function ($branch) {
                $BB = BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))->where('account_id', $branch->id)->first();
                $AC = BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->where('branch_id', $branch->branch_id)->first();

                

                // dd(BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))->where('account_id', $branch->id)->first() == null ? false : true);
                return
                    [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'type' => $branch->type,
                        'currency' => $branch->currency,
                        'branches' => $branch->bankBranch->bank->name . " - " . $branch->bankBranch->address,
                        'delete' => $BB || $AC == true ? false : true,
                    ];
            }
        );

        


        

        
        if (BankBranch::all()->first()) {
            return Inertia::render(
                'Accounts/Index',
                [
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'balances' => $bal,
                    
                    'dataEdit' => BankAccount::where('company_id', session('company_id'))->first(),

                    'companies' => Company::all()
                    ->map(function ($company) {
                        return [
                            'id' => $company->id,
                            'name' => $company->name,
                        ];
                    }),

                    
                    'cochange' => $coch_hold,
                    'role' => auth()->user()->role != 2 ? true : false,
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

            $bal = BankAccount::all()
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
                            'delete' => BankBalance::where('account_id', $branch->id)->first() ? true : false,
                        ];
                }
            );



            $branch_use =  BankAccount::where('company_id', session('company_id'))
            ->get()->pluck('branch_id')->toArray();

            $branches = BankBranch::whereNotIn('id' , $branch_use)->get()->map(function ($branch) {
                    return
                        [
                            'id' => $branch->id,    
                            'address' => $branch->bank->name . " - " . $branch->address, 
                        ];
                    });



            return Inertia::render('Accounts/Create', [

                //just fetch crete
                'balances' =>$bal,


                    
                // $branches = BankBranch::all()

                'branches' => $branches

                // dd($branches),
            ]);
        } else {
            return Redirect::route('branches.create', 'accounts')->with('success', 'Create Branch First');
        }
    }

    public function store(Req $request)
    {
        // dd($request->accounts);
       
        Request::validate([

            // 'accounts.*.name' => 'required|unique:App\Models\BankAccount,name',
            'accounts.*.type' => ['required'],
            'accounts.*.currency' => ['required'],
            // 'accounts.*.branch_id' => ['required']
            'accounts.*.branch_id' => ['required', new UniqueBranchForCompany()],

        ]);

        $accounts = $request->accounts;
        // dd($accounts);
        foreach ($accounts as $acc) {
            // dd($acc);
            BankAccount::create([
                'name' => $acc['name'] ? $acc['name'] : 0,
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
            // 'balances.*.name' => 'required',
            'accounts.*.type' => ['required'],
            'accounts.*.currency' => ['required'],
        ]);


        foreach ($request->balances as $account) {

            $acc = BankAccount::find($account['id']);
            $acc->update([

                'name' => $account['name'] ? $account['name'] : 0 ,
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
