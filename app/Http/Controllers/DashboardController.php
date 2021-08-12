<?php

namespace App\Http\Controllers;

use App\Models\BankConfirmation;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
// use App\Models\BankConfirmation;
use Inertia\Inertia;

class DashboardController extends Controller
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

        $query = Company::query();

        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } else {
            $query->orderBy(('name'), ('asc'));
        }
        // $dataa = \App\Models\BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->first()->confirm_create;
        // dd($dataa);

        $balances = $query->paginate(10)
            ->through(
                function ($dash) {
                    if ($dash->bankconfirmations()->get()->first()->confirm_create) {
                        $confirm_create = $dash->bankconfirmations()->get()->first()->confirm_create;
                    }



                    return [
                        'id' => $dash->id,
                        'name' => $dash->name,
                        // 'confirm_create' => $dash->bankconfirmations->first()->confirm_create,
                        'confirm' => $confirm_create,
                        // $confirm =
                        // $dash->bankconfirmations()
                        // ->select('confirm_create')
                        // ->get()->first()->confirm_create,
                        // 'confirm_create' => $confirm[0]['confirm_create'],
                        // ->first()->confirm_create,
                        // $confirm_create = BankConfirmation::all()->where('company_id', $dash->id)->map->only('confirm_create'),
                        // 'confirm_create' => BankConfirmation::all()->where('company_id', $dash->id)->map->only('confirm_create')->first(),

                        // ->where('company_id', session('company_id'))
                        // ->where('year_id', session('year_id'))

                        // ->first()->confirm_create,
                        // ()->select('confirm_create'),
                        // 'confirm_create' => $confirm->select(['confirm_create']),


                        // dd($confirm->confirm_create),
                        // $confirm_create = BankConfirmation::find($dash)->where('company_id', $dash->id),
                        // ->confirm_create,
                        // dd($confirm),
                        // ->confirm_create,

                        // dd($confirm_create[2]->confirm_create[0]),
                        // $confirm_create = $dash->bankconfirmations->first()
                        // ->where('company_id', session('company_id'))
                        // ->where('year_id', session('year_id'))->first(),

                        // ->first()->confirm_create,
                    ];
                }
            );
        // //
        //         dd($balances);

        return Inertia::render('Dashboard/Index', [

            'filters' => request()->all(['search', 'field', 'direction']),
            'balances' => $balances,

        ]);
    }

    // 'balances' => Bank::paginate(6)

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
}
