<?php

namespace App\Http\Controllers;

use App\Models\BankConfirmation;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Year;
use Carbon\Carbon;

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
            'type' => [''],
        ]);
        // dd(request('type'));
        $query = Company::query();

        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(request('field'), request('direction'));
        } else {
            $query->orderBy(('name'), ('asc'));
        }

        // Dashboard Data
        $balances = $query->paginate(10)
            ->through(
                function ($dash) {
                    $confirmations = request('type') == 'Advisor' ? $dash->advisorConfirmations()->get() : $dash->bankconfirmations()->get();
                    $total_sent = 0;
                    $total_recieve = 0;
                    $total_confirm = 0;
                    $confirm_create = "";
                    foreach ($confirmations as $confirmation) {
                        // dd($confirmation->received);
                        if ($confirmation->sent) {
                            $total_sent++;
                        }

                        if ($total_confirm == 0) {
                            $confirm_create = $confirmation->confirm_create;
                        }

                        if ($confirmation->received) {
                            $total_recieve++;
                        }

                        $total_confirm++;
                    }




                    return [
                        'id' => $dash->id,
                        'name' => $dash->name,
                        $confirm_create = $confirm_create ? new Carbon($confirm_create) : null,
                        'create_confirm' => $confirm_create ?  $confirm_create->format("M d Y") : null,
                        'total_confirm' => $total_confirm,
                        'total_sent' => $total_sent,
                        'reamaning' => $total_confirm - $total_recieve,
                    ];
                }
            );

        //UnConfirmation Record
        $companies = Company::all();
        $name = 0;
        $confirm = 0;
        foreach ($companies as $company) {
            if ($company->name) {
                $name++;
            }
                $get = request('type') == 'Advisor' ? $company->advisorConfirmations()->get()->first() : $company->bankConfirmations()->get()->first();
            if (!$get) {
                $confirm++;
            }
        }


        $active_co = Setting::where('user_id', auth()->user()->id)->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();

        
        // dd($balances);
        return Inertia::render('Dashboard/Index', [

            'filters' => request()->all(['search', 'field', 'direction']),
            'balances' => $balances,
            'confirmation' => $confirm,
            'client' => $name,
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

          
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
}
