<?php

namespace App\Http\Controllers;

use App\Models\AdviserConfirmation;
use App\Models\Advisor;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Year;
use App\Models\Company;
use App\Models\AdviserAccount;
use App\Models\BankConfirmation;
use Inertia\Inertia;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;


class AdviserConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        // dd($active_co);
        $coch_hold = Company::where('id', $active_co->value)->first();
        // dd($coch_hold);

        //Condition Create Button
       $branches = AdviserAccount::all()
        ->filter(
            function ($branch) {
                // dd($branch);
                // foreach ($branch as $account) {
                    if ($branch->company_id == session('company_id')) {
                        // dd($branch);
                        if ($branch->advisorConfirmations()
                            ->where('year_id', session('year_id'))->first('confirm_create')
                        ) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                }
        )->first();

        return Inertia::render('Advisor_Confirmations/Index', [
            'create' => $branches,
            'balances' => AdviserConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->paginate(10)->withQueryString()
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
                        'branch' => $confirmation->advisorAccount->advisor->name . " - " . $confirmation->advisorAccount->advisor->type,
                        //  . " - " . $confirmation->bankBranch->address,
                        'path' => $confirmation->path  ? $confirmation->path : null,
                        'company' => $confirmation->company->name,
                        'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
                    ]
                ),

                'role' => auth()->user()->role == 0 ? true :  false ,
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $branches = AdviserAccount::all()
        ->filter(
            function ($branch) {
                // dd($branch);
                // foreach ($branch as $account) {
                    if ($branch->company_id == session('company_id')) {
                        // dd($branch);
                        if ($branch->advisorConfirmations()
                            ->where('year_id', session('year_id'))->first('confirm_create')
                        ) {
                            return false;
                        } else {
                            return true;
                        }
                    }

            }
        )->map(function ($branch) {
            $confirm_create = Carbon::now();
// dd($branch->id);
            AdviserConfirmation::create([
                'confirm_create' => $confirm_create->format('Y-m-d'),
                'company_id' => session('company_id'),
                'year_id' => session('year_id'),
                'advisor_id' => $branch->id,
            ]);
        });

    return back()->withInput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdviserConfirmation  $adviserConfirmation
     * @return \Illuminate\Http\Response
     */
    public function show(AdviserConfirmation $adviserConfirmation)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdviserConfirmation  $adviserConfirmation
     * @return \Illuminate\Http\Response
     */
    public function edit(AdviserConfirmation $adviserConfirmation)
    {

        //
        $data = AdviserConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
        ->get()
        ->map(function ($confirmation) {
            return [
                'id' => $confirmation->id,
                'name' => $confirmation->advisorAccount->advisor->name . "-" . $confirmation->advisorAccount->advisor->type,
                'confirm_create' => $confirmation->confirm_create,
                'sent' => $confirmation->sent,
                'reminder' => $confirmation->reminder,
                'received' => $confirmation->received,

            ];
        });
// dd($data);
        return Inertia::render('Advisor_Confirmations/Edit', [
            'data' => AdviserConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
                ->get()
                ->map(function ($confirmation) {
                    return [
                        'id' => $confirmation->id,
                        'name' => $confirmation->advisorAccount->advisor->name . "-" . $confirmation->advisorAccount->advisor->type,
                        'confirm_create' => $confirmation->confirm_create,
                        'sent' => $confirmation->sent,
                        'reminder' => $confirmation->reminder,
                        'received' => $confirmation->received,

                    ];
                }),

            'branches' => AdviserAccount::all()
                ->filter(function ($branch) {
                    // foreach ($branch->bankAccounts as $account) {
                        if ($branch->company_id == session('company_id'))
                            return true;
                    // }
                })

                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->advisor->name . "-" . $branch->advisor->type,
                        // 'name' => $branch->bank->name . " - " . $branch->address,
                    ];
                }),
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdviserConfirmation  $adviserConfirmation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdviserConfirmation $adviserConfirmation)
    {
        $request->validate([
            'balances.*.confirm_create' => ['required'],
        ]);
        foreach ($request->balances as $balance) {
            $bal = AdviserConfirmation::find($balance['id']);
            $bal->update([
                'sent' => $balance['sent'],
                'confirm_create' => $balance['confirm_create'],
                'reminder' =>  $balance['reminder'],
                'received' => $balance['received'],


            ]);
        }
        return Redirect::route('advisor_confirmations')->with('success', 'Advisor Confirmation updated.');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdviserConfirmation  $adviserConfirmation
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdviserConfirmation $adviserConfirmation)
    {
        //
        $adviserConfirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }


    public function advisorupload(Req $request,$id){
       $validated = $request->validate([
        'file' => 'required|mimes:pdf'
        ]);

            $confirm = AdviserConfirmation::find($id);
            if($confirm){
            $fileName = "A".$request->id.'.'.$validated['file']->getClientOriginalExtension();
            $path  =  Request::file('file')->storeAs(session('company_id') . '/' . session('year_id') , $fileName, 'public');
            $validated['file']->move(storage_path('app/public/' . session('company_id') . '/' . session('year_id') . '/'), $fileName);
            $confirm->path = $path;
            $confirm->received = Carbon::now();
                $confirm->save();
                return back()->with('success', 'File Uploaded');
            }else{
                return back()->with('success', 'Only Pdf File Upload');
            }
    }


    public function  advisorconfirmUpload($id)
    {
        $confirm = \App\Models\AdviserConfirmation::find($id);
             if ($confirm) {
                //  dd($confirm->path);
                     return response()->download(storage_path('app/public/' .$confirm->path));
            // Storage::disk('public')->exists($confirm->path);
        } else {
            return Redirect::route('advisor_confirmations')->with('error', 'File Not Found.');
        }

    }




    public function advisor_word()

    {
        $phpWord = new PhpWord();
        $i = 0;
        $phpWord->addParagraphStyle('p1Style', array('align' => 'both', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addParagraphStyle('p2Style', array('align' => 'both'));
        $phpWord->addParagraphStyle('p3Style', array('align' => 'right', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addFontStyle('f1Style', array('name' => 'Calibri', 'size' => 10));
        $phpWord->addFontStyle('f2Style', array('name' => 'Calibri', 'bold' => true, 'size' => 12));
        $company = \App\Models\Company::where('id', session('company_id'))->first();

        if ($company->advisorAccounts()->first()) {
            $branch = $company->advisorAccounts()->get();

            // foreach ($branch as $b) {
            //     $branches[$i] = $b->bankBranch;
            //     $i++;
            // }


            $branches = null;
            // dd($branch);
            foreach ($branch as $b) {
                // dd($b);
                $check = true;
                if ($branches) {
                    // dd($branches);
                    foreach ($branches as $bran) {
                        if ($bran->id == $b->advisor->id) {
                            $check = false;
                            break;
                        }
                    }
                }
                if ($check) {
                    $branches[$i] = $b->advisor;
                    $i++;
                }
            }
        } else {
            return Redirect::route('advisor_accounts.create')->with('success', 'Create Advisor Account First');
        }

        $period = Year::where('id', session('year_id'))->first();
        $begin = new Carbon($period->begin);
        $end = new Carbon($period->end);
        $year = $end->year;
        $str = "first Monday of July {$year}";
        $date = new Carbon($str);
        $name = str_replace(["(", ")"], "", $company->name);
        $words = preg_split("/[\s,_-]+/", $name);
        $acronym = "";
        $i = 0;
        $count = 1;

        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        // dd($branches);
        foreach ($branches  as $branch) {
            // dd($branch);
            $section = $phpWord->addSection();
            $textrun = $section->addTextRun();
            $section->addTextBreak(2);
            $acronyms = str_replace(["&"], "&amp;", $acronym);
            $ref = "MZ-BCONF/" . $acronyms . "/" . $year . "/" . $count++;
            $section->addText($ref, 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(1);
            $section->addText($date->format('F j, Y'), 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(0);


            // $section->addText('The Manager,', 'f1Style', 'p1Style');
            $advisorname = str_replace(["&"], "&amp;", $branch->name);
            $section->addText($advisorname . ",", 'f1Style', 'p1Style');
            $adviosraddress = str_replace(["&"], "&amp;", $branch->address);
            $adviosrtype = str_replace(["&"], "&amp;", $branch->type);
            $branch = explode("\n", $adviosraddress);


            foreach ($branch as $branchadd) {
                $section->addText($branchadd . "", 'f1Style', 'p1Style');
                $branchadd++;
            }

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addText('Dear Sir,', 'f1Style', 'p2Style');

            $textrun = $section->addTextRun();
            $textrun = $section->addTextRun('p2Style');
            // $textrun->addText('Subject: ', 'f1Style');
            // $textrun->addText('Bank Report for Audit Purpose of ', 'f2Style');
            $companyname = str_replace(["&"], "&amp;", $company->name);
            $textrun->addText($companyname, 'f2Style');

            $textrun = $section->addTextRun();
            $textrun = $section->addTextRun('p2Style');
            $textrun->addText('REQUEST FOR INFORMATION FOR AUDIT PURPOSES FOR THE YEAR ENDED ', 'f2Style');
            $textrun->addText($end->format('F j, Y'), 'f2Style');

            if($adviosrtype == 'legal')
            {
                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);
                    $section->addTextBreak(0);
                    // dd($section);
                    $textrun = $section->addTextRun('p2Style');
                    $textrun->addText(
                        "We will shortly be required to express our opinion as to the fairness with which the
                        financial statements present the financial position of the company as ",
                        'f1Style'
                    );
                    $textrun->addText($end->format('F j, Y'), 'f2Style');
                    $textrun->addText(
                    " and the results of its operations from " , 'f1Style'
                    );
                    $textrun->addText($begin->format('F j, Y'), 'f2Style');
                    $textrun->addText(
                        " to ",
                        'f1Style',
                    );
                    $textrun->addText($end->format('F j, Y'), 'f2Style');
                    $textrun->addText(
                        " In this connection,  we  shall  be  grateful  if  you  would  please  furnish  to  us  directly,  the information requested below involving matters as to which you have been engaged and to which you have devoted substantive attention on behalf of the Company in the form of legal consultation or representation. Please provide the information requested below, taking into consideration matters that existed at ",
                        'f1Style',
                    );
                    $textrun->addText($end->format('F j, Y'), 'f2Style');
                    $textrun->addText(
                        " and for the period from that date to the effective date of your response if it is other than date of reply. ",
                        'f1Style',
                    );

                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Pending or Threatened Litigation.",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "1. The nature of the litigation.",
                        'f1Style',
                        'p2Style'
                    );
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "2. The progress of the case to date.",
                        'f1Style',
                        'p2Style'
                    );
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "3. How management is responding or intends to respond the litigation; for example to",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    contest the case vigorously or to seek out of court settlement, and.",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "4. Evaluation of the likelihood of an unfavorable outcome and an estimate, if one can be",
                        'f1Style',
                        'p2Style'
                    );


                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    made, of the amount or the range of potential loss.",
                        'f1Style',
                        'p2Style'
                    );
                    

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "5. Please also indicate, if there are any assessments by the assessing authority in progress for.",
                        'f1Style',
                        'p2Style'
                    );
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    a) the years involved;",
                        'f1Style',
                        'p2Style'
                    );
                 

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    b) any issue raised by the income tax department.",
                        'f1Style',
                        'p2Style'
                    );
              

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    c) the amounts paid to the taxation authorities on account or of any tax losses which will be  available to offset future taxliabialities; and",
                        'f1Style',
                        'p2Style'
                    );
                

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    d) en estimate of any additional taxes, over those already paid on account, which may be payable.",
                        'f1Style',
                        'p2Style'
                    );
                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);


                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Also, please identify any pending or threatened litigation with respect to which you have not yet devoted substantive attention.",
                        'f1Style',
                        'p2Style'
                    );
            }
            if($adviosrtype == 'tax')
            {
                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);
                    $section->addTextBreak(0);
                    // dd($section);
                    $textrun = $section->addTextRun('p2Style');
                    $textrun->addText(
                        "In connection with the audit of the financial statement of the ",
                        'f1Style'
                    );
                    $textrun->addText($companyname, 'f2Style');
                    $textrun->addText(
                        " for the year ending ",
                        'f1Style'
                    );
                    $textrun->addText($end->format('F j, Y'), 'f2Style');
                    $textrun->addText(
                    " we shall be grateful if you would please provide us directly the following information: " , 'f1Style'
                    );

                    $textrun = $section->addTextRun();
                    $section->addTextBreak(0);

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "1. Detailed position of the company’s open ended tax years / assessments, if any;",
                        'f1Style',
                        'p2Style'
                    );
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "2. Differences, if any, between income and tax returned and  that assessed for",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "     each open ended tax year, along with the particulars thereof;",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "3. Year wise position of tax refundable / payable;",
                        'f1Style',
                        'p2Style'
                    );


                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "4. Status of appeals and the amounts in dispute, and the likely outcome; and",
                        'f1Style',
                        'p2Style'
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "5. Any other matters that may have an effect on the aforementioned financial",
                        'f1Style',
                        'p2Style'
                    );


                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "    statements of the company.",
                        'f1Style',
                        'p2Style'
                    );
            }


         $textrun = $section->addTextRun();
            $section->addTextBreak(1);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Yours faithfully,",
                'f1Style',
                'p2Style'
            );

            //  $section->addText(
            //    "AUTHORIZED SIGNATORY",
            //     'f2Style',
            //     'p3Style'
            // );

            // $section->addText(
            //     "(Client's Signature)",
            //     'f2Style',
            //     'p3Style'
            // );

            $textrun = $section->addTextRun();
            $section->addTextBreak(1);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "_____________________                                                     ______________________________",
                'f2Style',
                'p2Style'
            );
            $textrun = $section->addTextRun();
            $textrun->addText(
                "Chartered Accountants                                                           CLIENT'S AUTHORIZED SIGNATORY",
                'f2Style',
                'p2Style'
            );

        }
        // dd( $company->id . '/' . $period->id);
        $writer = new Word2007($phpWord);
        $writer->save(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Advisors Letters.docx'));

        //Template FIle Generated.
        $end = $period->end ? new Carbon($period->end) : null;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
        $templateProcessor->setValue('client', $company->name);
        $templateProcessor->setValue('end', $end->format("F j Y"));
        $templateProcessor->saveAs(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Remaining_pages.docx'));
        return response()->download(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Advisors Letters.docx'));
    }


    public function advisorspdf(Request $request)
    {
        $company = adviserConfirmation::where('company_id', session('company_id'))->first();
        if ($company) {

            $year = Year::where('company_id', session('company_id'))
            ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $names = str_replace(["&"], "&amp;", $year->company->name);
            $endDate = $end->format("F j Y");
            //   $a = "hello world";

            $confirmation = AdviserConfirmation::where('company_id', session('company_id'))->get()
            ->map(function ($confirm){
                return[
                    // dd($confirm->advisorAccount),
                    'id' => $confirm->id,
                    'type' => ucwords($confirm->advisorAccount->advisor->type),
                    'branch' => $confirm->advisorAccount->advisor->name . " - " . $confirm->advisorAccount->advisor->address,
                ];
            });
            // dd($confirmation);


        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('advisorspdf', compact( 'names', 'endDate' ,'confirmation' ));
        return $pdf->stream($names ." ".'Advisors.pdf');



        }else{
            return Redirect::route('advisor_accounts.create')->with('success', 'Create Advisor Account first.');

        }


    }

}
