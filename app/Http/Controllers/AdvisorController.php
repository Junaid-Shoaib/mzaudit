<?php

namespace App\Http\Controllers;

use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Http\Request as Req;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class AdvisorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,address'],
        ]);
        $query = Advisor::query();
        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }
        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } else {
            $query->orderBy(('name'), ('asc'));
        }


        return Inertia::render('Advisors/Index', [
            'filters' => request()->all(['search', 'field', 'direction']),
            'balances' => $query->paginate(10)
                ->through(
                    fn ($advisor) =>
                    [
                        'id' => $advisor->id,
                        'name' => $advisor->name,
                        'address' => $advisor->address,
                        'type' => $advisor->type,
                        // 'delete' => BankBranch::where('bank_id', $bank->id)->first() ? false : true,
                        'delete' => true,

                    ]
                ),

        ]);
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Advisors/Create');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
            'name' => 'required|unique:App\Models\Advisor,name',
            'address' => 'required|unique:App\Models\Advisor,address',
            'type' => 'required',
        ]);

        // dd($validated);
        Advisor::create([
            'name' => strtoupper($validated['name']),
            'address' => ucwords($validated['address']),
            'type' => ucwords($validated['type']),
        ]);

        return Redirect::route('advisors')->with('success', 'Advisor created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function show(Advisor $advisor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function edit(Advisor $advisor)
    {
        // dd($advisor);
        return Inertia::render('Advisors/Edit', [
            'advisor' => [
                'id' => $advisor->id,
                'name' =>$advisor->name,
                'address' => $advisor->address,
                'type' => $advisor->type,
            ],
        ]);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advisor $advisor)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'type' => 'required',
        ]);


        $advisor->name = strtoupper($validated['name']);
        $advisor->address = strtoupper($validated['address']);
        $advisor->type = strtoupper($validated['type']);
        $advisor->save();

        return Redirect::route('advisors')->with('success', 'Advisor Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Advisor $advisor)
    {
        //
        $advisor->delete();
        return Redirect::back()->with('success', 'Advisor deleted.');
    }
}
