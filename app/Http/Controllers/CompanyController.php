<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Company;
use App\Models\Year;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Bank;
use App\Models\BankBranch;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CompanyController extends Controller
{
    // Company Index
    public function index()
    {

        return Inertia::render(
            'Companies/Index',
            [
                'data' => Company::all(),

                'balances' => Company::paginate(6)->withQueryString()
                    ->through(
                        fn ($company) =>
                        [
                            'id' => $company->id,
                            'name' => $company->name,
                            'address' => $company->address,
                            'email' => $company->email,
                            'web' => $company->web,
                            'phone' => $company->phone,
                            'fiscal' => $company->fiscal,
                            'incorp' => $company->incorp,
                            'delete' => Year::where('company_id', $company->id)->first() ? false : true,
                        ]
                    ),
            ],
        );
    }

    // Company Create
    public function create()
    {
        return Inertia::render('Companies/Create');
    }

    public function store()
    {
        Request::validate([
            'name' => ['required'],
            'fiscal' => ['required'],
        ]);
        DB::transaction(function () {
            $company = Company::create([
                'name' => strtoupper(Request::input('name')),
                'address' => Request::input('address'),
                'email' => Request::input('email'),
                'web' => Request::input('web'),
                'phone' => Request::input('phone'),
                'fiscal' => Request::input('fiscal'),
                'incorp' => Request::input('incorp'),
            ]);


            //Start Month & End Month
            $startMonth = Carbon::parse($company->fiscal)->month + 1;
            $endMonth = Carbon::parse($company->fiscal)->month;
            if ($startMonth == 13) {
                $startMonth = 1;
            }

            //Start Month Day & End Month Day
            $startMonthDays = 1;
            $endMonthDays = Carbon::create()->month($endMonth)->daysInMonth;

            // Year Get 
            $today = Carbon::today();
            $startYear = 0;
            $endYear = 0;
            if ($startMonth == 1) {
                $startYear = $today->year;
                $endYear = $today->year;
            } else {
                $endYear = ($today->month >= $startMonth) ? $today->year + 1 : $today->year;
                $startYear = $endYear - 1;
            }


            $startDate = $startYear . '-' . '0' . $startMonth . '-' . $startMonthDays;
            $endDate = $endYear . '-' . '0' . $endMonth . '-' . $endMonthDays;


            $year = Year::create([
                'begin' => $startDate,
                'end' => $endDate,
                'company_id' => $company->id,
            ]);
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => Auth::user()->id,
            ]);

            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => Auth::user()->id,
            ]);

            session(['company_id' => $company->id]);
            session(['year_id' => $year->id]);


            Storage::makeDirectory('/public/' . $company->id);
            Storage::makeDirectory('/public/' . $company->id . '/' . $year->id);
        });
        return Redirect::route('companies')->with('success', 'Company created');
    }

    // Company Show
    public function show(Company $company)
    {
    }

    // Company Edit
    public function edit(Company $company)
    {
        return Inertia::render('Companies/Edit', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'address' => $company->address,
                'email' => $company->email,
                'web' => $company->web,
                'phone' => $company->phone,
                'fiscal' => $company->fiscal,
                'incorp' => $company->incorp,
            ],
        ]);
    }
    // Company Update
    public function update(Company $company)
    {
        Request::validate([
            'name' => ['required'],
            'fiscal' => ['required'],
        ]);

        $company->name = Request::input('name');
        $company->address = strtoupper(Request::input('address'));
        $company->email = Request::input('email');
        $company->web = Request::input('web');
        $company->phone = Request::input('phone');
        $company->fiscal = Request::input('fiscal');
        $company->incorp = Request::input('incorp');
        $company->save();

        return Redirect::route('companies')->with('success', 'Company updated.');
    }

    // Company Delete
    public function destroy(Company $company)
    {
        $company->delete();
        return Redirect::back()->with('success', 'Company deleted.');
    }

    // Extra Function bind to getBank
    public function getBanks()
    {
        $data = Bank::all();
        $sbank = 0;
        return Inertia::render('Companies/Indexx', ['data' => $data, 'sbank' => $sbank]);
    }

    // Extra Function bind to getbranch
    public function getBranches(Bank $bank)
    {

        $data = Bank::all();
        $data2 = BankBranch::where('bank_id', $bank->id)->get();
        return Inertia::render('Companies/Indexx', ['data' => $data, 'data2' => $data2, 'sbank' => $bank->id]);
    }

    // Extra Function bind to indexy(debit & credit)
    public function indexy()
    {
        return Inertia::render('Companies/Indexy');
    }

    //Company Change function
    public function coch($id)
    {
        // dd($id);
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        if ($active_co) {
            $active_co->value = $id;
            $active_co->save();
        } else {
            $active_co = $id;
        }

        session(['company_id' => $id]);

        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
        // dd($active_yr);
        if ($active_yr) {
            $active_yr->value = Year::where('company_id', $id)->latest()->first()->id;
            $active_yr->save();
            session(['year_id' => $active_yr->value]);
        } else {
            $active_yr = Year::where('company_id', $id)->latest()->first()->id;
            session(['year_id' => $active_yr]);
        }

        // return redirect()->back();
        return back()->withInput();
    }

    // year Change Function
    public function yrch($id)
    {
        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
        $active_yr->value = $id;
        $active_yr->save();
        session(['year_id' => $active_yr->value]);

        return back()->withInput();
    }

    // public function pd()
    // {
    //     $a = "hello world";
    //     $pdf = App::make('dompdf.wrapper');
    //     $pdf->loadView('pdd', compact('a'));
    //     return $pdf->stream('v.pdf');
    // }

    // excel file Generator
    public function ex()
    {
        $s = '';
        $spreadsheet = new Spreadsheet();

        $colArray = ['H:H', 'I:I', 'J:J', 'K:K'];
        foreach ($colArray as $key => $col) {

            $FORMAT_ACCOUNTING = '_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"??_);_(@_)';
            $spreadsheet->getActiveSheet()->getStyle($col)->getNumberFormat()->setFormatCode($FORMAT_ACCOUNTING);
        }


        $spreadsheet->getActiveSheet()->getstyle('D3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('D4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('D5:E5')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getstyle('L3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('L4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getstyle('O3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('O4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->setMergeCells(['D5:E5']);
        $spreadsheet->getActiveSheet()->getStyle('C3:C5')->applyFromArray(
            array(
                'font'  => array(
                    'bold'  =>  true,
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );

        $spreadsheet->getActiveSheet()->getStyle('D3:D5')->applyFromArray(
            array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );
        $company = \App\Models\BankBalance::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))->first();
        if ($company) {
            $end = $company->year->end ? new Carbon($company->year->end) : null;
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Account first.');
        }
        $spreadsheet->getActiveSheet()->fromArray(['CLIENT:'], NULL, 'C3');
        $spreadsheet->getActiveSheet()->fromArray(['PERIOD:'], NULL, 'C4');
        $spreadsheet->getActiveSheet()->fromArray(['SUBJECT:'], NULL, 'C5');
        $spreadsheet->getActiveSheet()->fromArray([$company->company->name], NULL, 'D3');
        $spreadsheet->getActiveSheet()->fromArray([$end ? $end->format("M d Y") : null], NULL, 'D4');
        $spreadsheet->getActiveSheet()->fromArray(['Bank Confirmation Control Sheet'], NULL, 'D5');

        $spreadsheet->getActiveSheet()->fromArray(['Prepared By:'], NULL, 'K3');
        $spreadsheet->getActiveSheet()->fromArray(['Reviewed By:'], NULL, 'K4');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'N3');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'N4');



        $spreadsheet->getActiveSheet()->getStyle('B7:O7')->applyFromArray(
            array(
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('rgb' => '484848')
                ),
                'font'  => array(
                    'bold'  =>  true,
                    'color' => array('rgb' => 'FFFFFF')
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );

        $rowArray = ['SR#', 'BANK', 'ACCOUNT#', 'ACCOUNT TYPE', 'CURRENCY', 'ADDRESS', 'AS PER LEDGER', 'AS PER BANK STATEMENT', 'AS PER CONFIRMATION', 'DIFFERENCE', 'PREPARED', 'DISPATCHED', 'REMINDER', 'RECEIVED'];
        $spreadsheet->getActiveSheet()->fromArray($rowArray, NULL, 'B7');
        $widthArray = ['10', '5', '20', '20', '20', '15', '25', '17', '17', '17', '20', '20', '20', '20', '20'];
        foreach (range('A', 'O') as $key => $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($widthArray[$key]);
        }

        $dataa = \App\Models\BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->first();
        if ($dataa) {
            $data = \App\Models\BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()
                ->map(
                    function ($bal) {
                        return [
                            'id' => $bal->id,
                            'bank' => $bal->bankAccount->bankBranch->bank->name,
                            'number' => $bal->bankAccount->name,
                            'type' => $bal->bankAccount->type,
                            'currency' => $bal->bankAccount->currency,
                            'branch' => $bal->bankAccount->bankBranch->address,
                            'ledger' => $bal->ledger,
                            'statement' => $bal->statement,
                            'confirmation' => $bal->confirmation,
                            'difference' => $bal->statement - $bal->confirmation ? $bal->statement - $bal->confirmation : '0',
                            'sent' => $bal->bankAccount->bankBranch->bankConfirmations
                                ->filter(function ($confirmation) {
                                    return ($confirmation->company_id == session('company_id') && $confirmation->year_id == session('year_id'));
                                })->first()->sent,
                            'remind_first' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->reminder,
                            'remind_second' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->confirm_create,
                            'received' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->received,
                        ];
                    }
                )
                ->toArray();
        } else {
            return Redirect::route('confirmations')->with('success', 'Please Create Confirmation.');
        }

        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            // dd($data[$i]);
            $data[$i]['sent'] = $data[$i]['sent'] ? new Carbon($data[$i]['sent']) : null;
            $data[$i]['sent'] = $data[$i]['sent'] ? $data[$i]['sent']->format('F j, Y') : null;
            $data[$i]['remind_first'] = $data[$i]['remind_first'] ? new Carbon($data[$i]['remind_first']) : null;
            $data[$i]['remind_first'] = $data[$i]['remind_first'] ? $data[$i]['remind_first']->format('F j, Y') : null;
            $data[$i]['remind_second'] = $data[$i]['remind_second'] ? new Carbon($data[$i]['remind_second']) : null;
            $data[$i]['remind_second'] = $data[$i]['remind_second'] ? $data[$i]['remind_second']->format('F j, Y') : null;
            $data[$i]['received'] = $data[$i]['received'] ? new Carbon($data[$i]['received']) : null;
            $data[$i]['received'] = $data[$i]['received'] ? $data[$i]['received']->format('F j, Y') : null;
        }

        // dd($cnt);
        $spreadsheet->getActiveSheet()->fromArray($data, NULL, 'B9');




        $total = 0;
        for ($i = 0; $i < $cnt; $i++) {
            $total = $total + $data[$i]['ledger'];
        }

        $tstr = $cnt + 9;
        $tcell = "H" . strval($tstr);
        $spreadsheet->getActiveSheet()->setCellValue($tcell, $total);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => [
                        'rgb' => '484848',
                    ],


                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle($tcell)->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $company->company->id  . '/' . $company->year->id . '/' .  'Control Sheet.xlsx'));
        return response()->download(storage_path('app/public/' . $company->company->id . '/' . $company->year->id . '/' .  'Control Sheet.xlsx'));
    }

    //word file Generator
    public function word()

    {
        // dd('Junaid');
        $phpWord = new PhpWord();
        $i = 0;
        $phpWord->addParagraphStyle('p1Style', array('align' => 'both', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addParagraphStyle('p2Style', array('align' => 'both'));
        $phpWord->addParagraphStyle('p3Style', array('align' => 'right', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addFontStyle('f1Style', array('name' => 'Calibri', 'size' => 12));
        $phpWord->addFontStyle('f2Style', array('name' => 'Calibri', 'bold' => true, 'size' => 12));
        $company = \App\Models\Company::where('id', session('company_id'))->first();
        // dd($company);

        if ($company->bankAccounts()->first()) {
            $branch = $company->bankAccounts()->get();
            foreach ($branch as $b) {
                $branches[$i] = $b->bankBranch;
                $i++;
            }
        } else {
            return Redirect::route('accounts.create')->with('success', 'Create Account First');
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



        foreach ($branches  as $branch) {
            // dd($branch);
            $section = $phpWord->addSection();
            $textrun = $section->addTextRun();
            $section->addTextBreak(2);
            $ref = "MZ-BCONF/" . $acronym . "/" . $year . "/" . $count++;
            $section->addText($ref, 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(1);
            $section->addText($date->format('F j, Y'), 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(0);


            $section->addText('The Manager,', 'f1Style', 'p1Style');
            $section->addText($branch->bank->name . ",", 'f1Style', 'p1Style');
            $branch = explode("\n", $branch->address);

            foreach ($branch as $branchadd) {
                $section->addText($branchadd . ",", 'f1Style', 'p1Style');
                $branchadd++;
            }

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addText('Dear Sir,', 'f1Style', 'p2Style');

            $textrun = $section->addTextRun();

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText('Subject: ', 'f1Style');
            $textrun->addText('Bank Report for Audit Purpose of ', 'f2Style');
            $textrun->addText($company->name, 'f2Style');

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addTextBreak(0);

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText(
                "In accordance with your above named customer’s instructions given hereon, please send DIRECT to us at the below address, as auditors of your customer, the following information relating to their affairs at your branch as at the close of business on ",
                'f1Style',
            );
            $textrun->addText($end->format('F j, Y'), 'f2Style');
            $textrun->addText(
                " and, in the case of items 2, 4 and 9, during the period since ",
                'f1Style',
            );
            $textrun->addText($begin->format('F j, Y'), 'f2Style');

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Please state against each item any factors which may limit the completeness of your reply; if there is nothing to report, state ‘NONE’.",
                'f1Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "It is understood that any replies given are in strict confidence, for the purposes of audit.",
                'f1Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Yours truly,",
                'f1Style',
                'p2Style'
            );

            $section->addText(
                "Disclosure  Authorized",
                'f2Style',
                'p3Style'
            );

            $section->addText(
                "For  and  on  behalf  of",
                'f2Style',
                'p3Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(1);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Chartered Accountants                                                                                  ___________________",
                'f2Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Enclosures:",
                'f1Style',
                'p2Style'
            );
        }
        $writer = new Word2007($phpWord);
        $writer->save(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Bank Letters.docx'));

        //Template FIle Generated.
        $end = $period->end ? new Carbon($period->end) : null;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
        $templateProcessor->setValue('client', $company->name);
        $templateProcessor->setValue('end', $end->format("F j Y"));
        $templateProcessor->saveAs(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Remaining_pages.docx'));
        return response()->download(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Bank Letters.docx'));
    }
}
