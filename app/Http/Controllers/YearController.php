<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use App\Models\Year;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
                        'delete' => $year->id == Year::where('company_id', session('company_id'))->first()->id  ? false : true,
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
        $year = Year::where('company_id', session('company_id'))->latest()->first();
        $begin = explode('-', $year->begin);
        $begin[0]++;
        $end = explode('-', $year->end);
        $end[0]++;
        $newBegin = implode('-', $begin);
        $newEnd = implode('-', $end);

        //Meri Changes
        //yahan tak
        Year::create([
            'begin' => $newBegin,
            'end' => $newEnd,
            'company_id' => session('company_id'),
        ]);

        $year = Year::where('company_id', session('company_id'))->latest()->first();
        Storage::makedirectory('/public/' . $year->company->name . '/' . $newEnd);


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
