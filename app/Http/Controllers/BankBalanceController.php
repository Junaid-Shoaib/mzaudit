<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankAccount;
use App\Models\BankBalance;
use App\Models\Year;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Inertia\Inertia;
use Carbon\Carbon;

class BankBalanceController extends Controller
{
    public function index()
    {

        
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();

        return Inertia::render(
            'Balances/Index',
            [
                'balances' => BankBalance::where('company_id', session('company_id'))
            
                    ->where('year_id', session('year_id'))->paginate(10)->withQueryString()
                    ->through(
                        fn ($bal) =>
                        [
                            'id' => $bal->id,
                            'number' => $bal->bankAccount->name,
                            'ledger' => $bal->ledger,
                            'statement' => $bal->statement,
                            'confirmation' => $bal->confirmation,
                        ]
                    ),
            
                    'dataEdit' => BankBalance::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))->first(),

                'cochange' => $coch_hold,
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
                        return [
                            'id' => $year->id,
                            'begin' => $year->begin,
                            'end' => $end->format('F j Y'),
                        ];
                    }),

            ],
        );
    }




    public function create()
    {
        $accounts = BankAccount::where('company_id', session('company_id'))->get()
            // dd($accounts);
            ->filter(
                function ($account) {

                    if ($account->bankBalances()
                        ->where('year_id', session('year_id'))->first('ledger')
                    ) {
                        return false;
                    } else {

                        return true;
                    }
                }

            )

            ->map(
                function ($bal) {

                    return [
                        'id' => $bal->id,
                        'name' => $bal->name,
                        'type' => $bal->type,
                        'currency' => $bal->currency,
                        'branch' => $bal->name . " - " . $bal->bankBranch->bank->name . " - " . $bal->bankBranch->address,
                    ];
                }
            );

        $account = null;

        $i = 0;
        foreach ($accounts as $acc) {
            if ($acc) {
                $account[$i] = $acc;
                $i++;
            }
        }

        // dd($accounts);
        if ($account) {

            return Inertia::render('Balances/Create', [

                'accounts' => $account,
            ]);
        } else {
            return Redirect::route('accounts.create')->with('success', 'Create Account first.');
        }
    }




    public function store(Req $request)
    {

        Request::validate([
            'balances.*.account_id' => ['required'],
            'balances.*.ledger' => ['required'],
        ]);

        foreach ($request->balances as $balance) {
            // dd($balance);
            BankBalance::create([
                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
                'account_id' => $balance['account_id']['id'],
                'company_id' => session('company_id'),
                'year_id' => session('year_id'),
            ]);
        }

        return Redirect::route('balances')->with('success', 'Bank Balance created.');
    }

    public function show($id)
    {
        //
    }

    public function edity()

    {
        return Inertia::render('Balances/Edit', [

            'data' => BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))
                ->get()
                ->map(function ($balances) {
                    return [
                        'id' => $balances->id,
                        'ledger' => $balances->ledger,
                        'statement' => $balances->statement,
                        'confirmation' => $balances->confirmation,
                        'branches' => $balances->bankAccount->bankBranch->bank->name . " - " . $balances->bankAccount->bankBranch->address,
                    ];
                }),


        ]);
    }

    public function update(Req $request, BankBalance $balance)
    {
        // dd($request);

        Request::validate([
            'balances.*.ledger' => ['required'],
        ]);
        foreach ($request->balances as $balance) {
            $bal = BankBalance::find($balance['id']);
            $bal->update([

                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
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
