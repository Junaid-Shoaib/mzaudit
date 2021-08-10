<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\Models\BankBranch;
use App\Models\BankConfirmation;
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

        $balances = $query->paginate(10)
            ->through(
                function ($dash) {
                    return [
                        'id' => $dash->id,
                        'name' => $dash->name,
                    ];
                }
            );

        // dd($balances);
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
