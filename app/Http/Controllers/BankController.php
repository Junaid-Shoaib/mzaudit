<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Bank;
use App\Models\BankBranch;
use Inertia\Inertia;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Banks/Index', [
            'balances' => Bank::paginate(6)
                ->through(
                    fn ($bank) =>
                    [
                        'id' => $bank->id,
                        'name' => $bank->name,
                        'delete' => BankBranch::where('bank_id', $bank->id)->first() ? false : true,

                    ]
                ),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    // Bank Create
    public function create($accounts)
    {
        // dd($bankcreate);
        return Inertia::render('Banks/Create', ["accounts" => $accounts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Req $request)
    {
        // dd($request->bankcreate);
        Request::validate([
            'name' => ['required'],
        ]);

        Bank::create([
            'name' => strtoupper(Request::input('name')),
        ]);

        if ($request->accounts == 'accounts') {
            return Redirect::route('branches.create', ['accounts' => $request->accounts])->with('success', 'Bank created.');
        } else {
            return Redirect::route('banks')->with('success', 'Bank created.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    //Bank Show
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Bank Edit
    public function edit(Bank $bank)
    {
        return Inertia::render('Banks/Edit', [
            'bank' => [
                'id' => $bank->id,
                'name' => $bank->name,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Bank Update
    public function update(Req $request, Bank $bank)
    {
        Request::validate([
            'name' => ['required'],
        ]);

        $bank->name = strtoupper(Request::input('name'));
        $bank->save();

        return Redirect::route('banks')->with('success', 'Bank updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //Bank Delete
    public function destroy(Bank $bank)
    {
        $bank->delete();
        return Redirect::back()->with('success', 'Bank deleted.');
    }
}
