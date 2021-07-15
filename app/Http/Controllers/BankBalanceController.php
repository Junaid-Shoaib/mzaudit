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
use Carbon\Carbon;

class BankBalanceController extends Controller
{
    public function index()
    {
        return Inertia::render(
            'Balances/Index',
            [
                'balances' => BankBalance::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))->paginate(6)->withQueryString()
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

                // dd($balances['participants']->links()),
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
                    // dd($account);
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

            BankBalance::create([
                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
                'account_id' => $balance['account_id'],
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
            'accounts' => BankAccount::all()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'name' => $account->name,
                        'type' => $account->type,
                        'currency' => $account->currency,
                        'branch' => $account->bankBranch->bank->name . " - " . $account->bankBranch->address,
                        'company_id' => $account->company_id,
                    ];
                }),
            'data' => BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get(),
        ]);
    }

    public function update(Req $request, BankBalance $balance)
    {
        Request::validate([
            'balances.*.company_id' => ['required'],
            'balances.*.ledger' => ['required'],
        ]);
        foreach ($request->balances as $balance) {
            $bal = BankBalance::find($balance['id']);
            $bal->update([

                'ledger' => $balance['ledger'],
                'statement' => $balance['statement'],
                'confirmation' => $balance['confirmation'],
                'account_id' => $balance['account_id'],
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
