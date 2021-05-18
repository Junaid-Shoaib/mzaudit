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
use App;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use Carbon\Carbon;
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
                'data' => Company::all()
                    ->map(function ($company) {
                        return [
                            'id' => $company->id,
                            'name' => $company->name,
                            'address' => $company->address,
                            'email' => $company->email,
                            'web' => $company->web,
                            'phone' => $company->phone,
                            'fiscal' => $company->fiscal,
                            'incorp' => $company->incorp,
                            'delete' => Year::where('company_id', $company->id)->first() ? false : true,
                        ];
                    })
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
                'name' => Request::input('name'),
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


            $startDate = $startYear . '-' . $startMonth . '-' . $startMonthDays;
            $endDate = $endYear . '-' . $endMonth . '-' . $endMonthDays;


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
        $company->address = Request::input('address');
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

        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        $active_co->value = $id;
        $active_co->save();
        session(['company_id' => $id]);

        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
        $active_yr->value = Year::where('company_id', $id)->latest()->first()->id;
        $active_yr->save();
        session(['year_id' => $active_yr->value]);
        return redirect()->back();
    }

    // year Change Function
    public function yrch($id)
    {
        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
        $active_yr->value = $id;
        $active_yr->save();
        session(['year_id' => $active_yr->value]);
        return redirect()->back();
    }

    public function pd()
    {
        $a = "hello world";
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdd', compact('a'));
        return $pdf->stream('v.pdf');
    }

    // excel file Generator
    public function ex()
    {
        $spreadsheet = new Spreadsheet();

        $colArray = ['H:H', 'I:I', 'J:J'];
        foreach ($colArray as $key => $col) {
            $spreadsheet->getActiveSheet()->getStyle($col)->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        // $spreadsheet->getActiveSheet()->getStyle('H:H')->getNumberFormat()
        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        // $spreadsheet->getActiveSheet()->getStyle('I:I')->getNumberFormat()
        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        // $spreadsheet->getActiveSheet()->getStyle('J:J')->getNumberFormat()
        // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $spreadsheet->getActiveSheet()->getStyle('B3:N3')->applyFromArray(
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

        //        $c = 'a2';
        //       $sheet->setCellValue($c, 'Universes!');

        $rowArray = ['SR#', 'BANK', 'ACCOUNT#', 'ACCOUNT TYPE', 'CURRENCY', 'ADDRESS', 'AS PER LEDGER', 'AS PER BANK STATEMENT', 'AS PER CONFIRMATION', 'PREPARED', 'DISPATCHED', 'REMINDER', 'RECEIVED'];
        //        $columnArray = array_chunk($rowArray, 1);
        //        $spreadsheet->getActiveSheet()->fromArray($columnArray, NULL, 'C10');
        $spreadsheet->getActiveSheet()->fromArray($rowArray, NULL, 'B3');

        $widthArray = ['10', '5', '20', '20', '20', '15', '25', '17', '17', '17', '20', '20', '20', '20'];
        foreach (range('A', 'N') as $key => $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($widthArray[$key]);
        }
        // $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
        // $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        // $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        // $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        // $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);

        // $spreadsheet->getActiveSheet()->getStyle('I3')
        // ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // $spreadsheet->getActiveSheet()->getStyle('I3')
        // ->getAlignment()->setWrapText(true);

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
                        'sent' => $bal->bankAccount->bankBranch->bankConfirmations
                            // dd($bal),
                            ->filter(function ($confirmation) {
                                // dd($confirmation);
                                return ($confirmation->company_id == session('company_id') && $confirmation->year_id == session('year_id'));
                            })->first()->sent,
                        'remind_first' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->reminder,
                        'remind_second' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->confirm_create,
                        'received' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->received,
                    ];
                }
            )
            ->toArray();
        // dd($data);
        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            $data[$i]['sent'] = $data[$i]['sent'] ? new Carbon($data[$i]['sent']) : null;
            $data[$i]['sent'] = $data[$i]['sent'] ? $data[$i]['sent']->format('F j, Y') : null;
            $data[$i]['remind_first'] = $data[$i]['remind_first'] ? new Carbon($data[$i]['remind_first']) : null;
            $data[$i]['remind_first'] = $data[$i]['remind_first'] ? $data[$i]['remind_first']->format('F j, Y') : null;
            $data[$i]['remind_second'] = $data[$i]['remind_second'] ? new Carbon($data[$i]['remind_second']) : null;
            $data[$i]['remind_second'] = $data[$i]['remind_second'] ? $data[$i]['remind_second']->format('F j, Y') : null;
            $data[$i]['received'] = $data[$i]['received'] ? new Carbon($data[$i]['received']) : null;
            $data[$i]['received'] = $data[$i]['received'] ? $data[$i]['received']->format('F j, Y') : null;
        }
        // dd($data);
        //        $abc= \App\Models\BankBranch::with(['bankAccounts.bankBalances','bankConfirmations','bank'])->get()->toArray();
        //        dd($abc);
        //        $data2 = [];
        //        foreach($data as $key=>$value){
        //            $data2[$key] = array_values($value);
        //        }
        //        dd($data2);
        $spreadsheet->getActiveSheet()->fromArray($data, NULL, 'B5');

        $total = 0;
        for ($i = 0; $i < $cnt; $i++) {
            $total = $total + $data[$i]['ledger'];
        }

        $tstr = $cnt + 5;
        $tcell = "H" . strval($tstr);
        $spreadsheet->getActiveSheet()->setCellValue($tcell, $total);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['rgb' => '484848'],
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle($tcell)->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
        return response()->download(public_path('hello World.xlsx'));
    }

    //word file Generator
    public function doc()
    {

        $phpWord = new PhpWord();

        $phpWord->addParagraphStyle('p1Style', array('align' => 'both', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addParagraphStyle('p2Style', array('align' => 'both'));
        $phpWord->addParagraphStyle('p3Style', array('align' => 'right', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addFontStyle('f1Style', array('name' => 'Calibri', 'size' => 12));
        $phpWord->addFontStyle('f2Style', array('name' => 'Calibri', 'bold' => true, 'size' => 12));
        $company = \App\Models\Company::where('id', session('company_id'))->first();
        // dd($company);
        $branch = $company->bankAccounts()->get()->bankBranch;
        // dd($branch);
        $period = \App\Models\Year::where('company_id', session('company_id'))->first();
        $begin = new Carbon($period->begin);
        $end = new Carbon($period->end);
        $year = $end->year;
        $str = "first Monday of July {$year}";
        $date = new Carbon($str);

        $name = str_replace(["(", ")"], "", $company->name);
        $words = preg_split("/[\s,_-]+/", $name);
        $acronym = "";
        $count = 1;

        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        for ($i = 0; $i < $branch->count(); $i++) {
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
            $section->addText($branch->address . ".", 'f1Style', 'p1Style');



            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addText('Dear Sir,', 'f1Style', 'p2Style');

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText('Subject: ', 'f1Style');
            $textrun->addText('Bank Report for Audit Purpose of ', 'f2Style');
            $textrun->addText($company->name, 'f2Style');

            $textrun = $section->addTextRun();
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
        $writer->save('hello World.docx');
        return response()->download(public_path('hello World.docx'));
    }
}
