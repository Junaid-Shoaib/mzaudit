<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Year;
use App\Models\Company;
use App\Models\BankConfirmation;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;

class BankConfirmationController extends Controller
{
//Index
    public function index()
    {
        //Company Change
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();

        //Condition Create Button
        $branches = BankBranch::all()
            ->filter(
                function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id')) {
                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('confirm_create')
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            )->first();


        return Inertia::render('Confirmations/Index', [
            'create' => $branches,
            'balances' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->paginate(10)->withQueryString()
                ->through(
                    fn ($confirmation) =>
                    [
                        $sent = $confirmation->sent ? new Carbon($confirmation->sent) : null,
                        $reminder = $confirmation->reminder ? new Carbon($confirmation->reminder) : null,
                        $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
                        $received = $confirmation->received ? new Carbon($confirmation->received) : null,
                        'id' => $confirmation->id,
                        'sent' => $sent ? $sent->format("M d Y") : null,
                        'reminder' => $reminder ? $reminder->format("M d Y") : null,
                        'confirm_create' => $confirm_create ?  $confirm_create->format("M d Y") : null,
                        'received' => $received ? $received->format("M d Y") : null,
                        'path' => $confirmation->path  ? $confirmation->path : null,
                        'branch' => $confirmation->bankBranch->bank->name . " - " . $confirmation->bankBranch->address,
                        'company' => $confirmation->company->name,
                        'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
                    ]
                ),


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
                    $begin = new Carbon($year->begin);
                    return [
                        'id' => $year->id,
                        'begin' => $begin->format("F j Y"),
                        'end' => $end->format("F j Y"),
                    ];
                }),
        ]);
    }
//Create
    public  function create()
    {

        $branches = BankBranch::all()
            ->filter(
                function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id')) {
                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('confirm_create')
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            )

            ->map(function ($branch) {
                $confirm_create = Carbon::now();

                BankConfirmation::create([
                    'confirm_create' => $confirm_create->format('Y-m-d'),
                    'company_id' => session('company_id'),
                    'year_id' => session('year_id'),
                    'branch_id' => $branch->id,
                ]);
            });

        return back()->withInput();
        // }
    }
//Show
    public function show($id)
    {
        //
    }
//Edit
    public function edit()
    {
        // $data = BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
        //         ->get()
        //         ->map(function ($confirmation) {
        //             return [
        //                 'id' => $confirmation->id,
        //                 'name' => $confirmation->bankBranch->bank->name . "-" . $confirmation->bankBranch->address,
        //                 'confirm_create' => $confirmation->confirm_create,
        //                 'sent' => $confirmation->sent,
        //                 'reminder' => $confirmation->reminder,
        //                 'received' => $confirmation->received,
        //                 'path' =>  $confirmation->path,

        //             ];
        //         });
        //         dd($data);
        return Inertia::render('Confirmations/Edit', [
            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
                ->get()
                ->map(function ($confirmation) {
                    return [
                        'id' => $confirmation->id,
                        'name' => $confirmation->bankBranch->bank->name . "-" . $confirmation->bankBranch->address,
                        'confirm_create' => $confirmation->confirm_create,
                        'sent' => $confirmation->sent,
                        'reminder' => $confirmation->reminder,
                        'received' => $confirmation->received,
                        'path' =>  $confirmation->path,
                    ];
                }),
                // return response()->download(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
            'branches' => BankBranch::all()
                ->filter(function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id'))
                            return true;
                    }
                })

                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->bank->name . " - " . $branch->address,
                    ];
                }),
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }
//Update


    public function updated(Req $request){
        // dd($request->all());
       $validated = $request->validate([
        'file' => 'required|mimes:pdf'
        ]);

            $confirm = BankConfirmation::find($request->id);
            if($confirm){
                $fileName = "B".$request->id.'.'.$validated['file']->getClientOriginalExtension();
                $path = storage_path('app/public/' . session('company_id') . '/' . session('year_id') . '/'.$fileName);
                $confirm->path = $path;
                $validated['file']->move(storage_path('app/public/' . session('company_id') . '/' . session('year_id') . '/'), $fileName);
                // dd('success');
                $confirm->save();
                return back()->with('success', 'File Uploaded');
            }else{
                // dd('1-0');
                return back()->with('success', 'Only Pdf File Upload');
            }
    }


    public function update(Req $request, BankConfirmation $balance)
    {
        // dd($request->all());
        Request::validate([
            'balances.*.confirm_create' => ['required'],
        ]);
        foreach ($request->balances as $balance) {
            $bal = BankConfirmation::find($balance['id']);
            $bal->update([
                'sent' => $balance['sent'],
                'confirm_create' => $balance['confirm_create'],
                'reminder' =>  $balance['reminder'],
                'received' => $balance['received'],
                'path' => $balance['path'],


            ]);
        }
        return Redirect::route('confirmations')->with('success', 'Bank Confirmation updated.');
    }

//Delete
    public function destroy(BankConfirmation $confirmation)
    {
        $confirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }

    //Template Sheet

    public function  bankconfirmUpload($id)
    {
        $confirm = \App\Models\BankConfirmation::find($id);
             if ($confirm) {
                     return response()->download($confirm->path);
            // Storage::disk('public')->exists($confirm->path);
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Balance first.');
        }

    }
    public function bankConfig()
    {
        //template
        $account = \App\Models\BankAccount::where('company_id', session('company_id'))->first();
        if ($account) {
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
            $names = str_replace(["&"], "&amp;", $year->company->name);
            $templateProcessor->setValue('client', $names);
            $templateProcessor->setValue('end', $end->format("F j Y"));
            $templateProcessor->saveAs(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
            return response()->download(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Balance first.');
        }
    }

//Branches Pdf
    public function branchespdf(Request $request)
    {
        $company = BankConfirmation::where('company_id', session('company_id'))->first();
        if ($company) {

            $year = Year::where('company_id', session('company_id'))
            ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $names = str_replace(["&"], "&amp;", $year->company->name);
            $endDate = $end->format("F j Y");
            //   $a = "hello world";

            $confirmation = BankConfirmation::where('company_id', session('company_id'))->get()
            ->map(function ($confirm){
                return[
                    'id' => $confirm->id,
                    'branch' => $confirm->bankBranch->bank->name . " - " . $confirm->bankBranch->address,
                ];
            });
            // dd($confirmation);


        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('branchespdf', compact( 'names', 'endDate' ,'confirmation' ));
        return $pdf->stream($names ." ".'branches.pdf');



        }else{
            return Redirect::route('accounts.create')->with('success', 'Create Account first.');

        }


    }
}
