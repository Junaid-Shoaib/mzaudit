<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankBalance;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use App\Models\Year;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class YearController extends Controller
{
    public function index()
    {
        
            $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
            $coch_hold = Company::where('id', $active_co->value)->first();

        return Inertia::render('Years/Index', [


            'balances' => Year::where('company_id', session('company_id'))->paginate(6)->withQueryString()
                ->through(
                    fn ($year) =>
                    [
                        $end = new Carbon($year->end),
                        $begin = new Carbon($year->begin),
                        'id' => $year->id,
                        'begin' => $begin->format("F j Y"),
                        'end'  => $end->format("F j Y"),
                        'delete' => $year->id == Year::where('company_id', session('company_id'))->first()->id  ? false : true,
                    ]

                    // 'delete' => BankBalance::where('year_id', $branch->id)->first() ? false : true,
                ),
            
            'cochange' => $coch_hold,

            'companies' => Company::all()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                    ];
                }),
        ]);
    }

    public function create()
    {
        $year = Year::where('company_id', session('company_id'))->latest()->first();
        $begin = explode('-', $year->begin);
        $begin[0]++;
        $end = explode('-', $year->end);
        $end[0]++;
        $newBegin = implode('-', $begin);
        $newEnd = implode('-', $end);


        // dd($newBegin);
        Year::create([
            'begin' => $newBegin,
            'end' => $newEnd,
            'company_id' => session('company_id'),

        ]);

        $year = Year::where('company_id', session('company_id'))->latest()->first();
        Storage::makedirectory('/public/' . $year->company->id . '/' . $year->id);


        return Redirect::back()->with('success', 'Year created.');
    }


    public function show($id)
    {
        //
    }

    public function edit(Year $year)
    {
        return Inertia::render('Years/Edit', [
            'year' => [
                'id' => $year->id,
                'begin' => $year->begin,
                'end' => $year->end,
                'company_id' => $year->company_id,
            ],
        ]);
    }

    public function update(Req $request, Year $year)
    {
        Request::validate([
            'begin' => ['required'],
            'end' => ['required'],
        ]);

        $year->begin = Request::input('begin');
        $year->end = Request::input('end');
        $year->save();

        return Redirect::route('years')->with('success', 'Year updated.');
    }

    public function destroy(Year $year)
    {
        $year->delete();
        return Redirect::back()->with('success', 'Year deleted.');
    }
}
