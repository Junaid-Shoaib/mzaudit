<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Year;
use App\Models\Company;
use App\Models\Setting;
use Carbon\Carbon;

class YearController extends Controller
{
    public function index()
    {
        return Inertia::render('Years/Index', [
            'data' => Year::where('company_id', session('company_id'))->get()
                ->map(function ($year) {
                    $begin = new Carbon($year->begin);
                    $end = new Carbon($year->end);
                    return [
                        'id' => $year->id,
                        'begin' => $begin->format("F j Y"),
                        'end'  => $end->format("F j Y"),

                    ];
                }),

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
        return Inertia::render('Years/Create');
    }

    public function store(Req $request)
    {
        //        dd($request->all());
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
            //           'company_id' => ['required'],
        ]);

        DB::transaction(function () {
            $year = Year::create([

                'begin' => Request::input('begin'),
                'end' => Request::input('end'),
                'company_id' => session('company_id'),
            ]);
            // dd($year);

            // if(!count(Auth::user()->settings()->get())){
            //                 Setting::create([
            //                         'key' => 'active_company',
            //                         'value' => session('company_id'),
            // //                        'company_id' => Request::input('company_id'),
            //                         'user_id' => Auth::user()->id,
            //                     ]);

            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                //                        'company_id' => Request::input('company_id'),
                'user_id' => Auth::user()->id,
            ]);
            // dd($year);
            // }
            //             else {
            // //                $active_co = Setting::where('user_id',Auth::user()->id)->where('company_id',Request::input('company_id'))->where('key','active_company')->first();
            // //                $active_yr = Setting::where('user_id',Auth::user()->id)->where('company_id',Request::input('company_id'))->where('key','active_year')->first();
            //                 // $active_co = Setting::where('user_id',Auth::user()->id)->where('key','active_company')->first();
            //                 $active_yr = Setting::where('user_id',Auth::user()->id)->where('key','active_year')->first();
            //                 // $active_co->value = session('company_id');
            //                 $active_yr->value = $year->id;
            //                 // $active_co->save();
            //                 $active_yr->save();
            //             }

            //            session(['company_id' => Request::input('company_id')]);
            session(['year_id' => $year->id]);
        });

        return Redirect::route('years')->with('success', 'Year created.');
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
            //            'company_id' => ['required'],
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
