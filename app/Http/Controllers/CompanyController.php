<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Company;
use App\Models\Bank;
use App\Models\BankBranch;
use Inertia\Inertia;
use App;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use Carbon\Carbon;

class CompanyController extends Controller
{
    public function index()
    {
        $data = Company::all();
        return Inertia::render('Companies/Index', ['data' => $data]);
    }

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

        Company::create([
            'name' => Request::input('name'),
            'address' => Request::input('address'),
            'email' => Request::input('email'),
            'web' => Request::input('web'),
            'phone' => Request::input('phone'),
            'fiscal' => Request::input('fiscal'),
            'incorp' => Request::input('incorp'),
        ]);

        return Redirect::route('companies')->with('success', 'Company created.');

    }

    public function show(Company $company)
    {
    }

    public function edit(Company $company)
    {
//        $types = \App\Models\Company::all()->map->only('id','name');
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

    public function destroy(Company $company)
    {
        $company->delete();
        return Redirect::back()->with('success', 'Company deleted.');
    }

    public function getBanks()
    {
        $data = Bank::all();
        $sbank = 0;
        return Inertia::render('Companies/Indexx', ['data' => $data, 'sbank' => $sbank]);
    }

    public function getBranches(Bank $bank)
    {
        $data = Bank::all();
        $data2 = BankBranch::where('bank_id', $bank->id)->get();
        return Inertia::render('Companies/Indexx', ['data' => $data,'data2' => $data2, 'sbank' => $bank->id]);
    }

    public function indexy()
    {
        return Inertia::render('Companies/Indexy');
    }

    public function pd()
    {
        $a = "hello world";
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdd',compact('a'));
        return $pdf->stream('v.pdf');
    }

    public function ex()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $c = 'a2';
        $sheet->setCellValue($c, 'Universes!');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
    }

    public function doc()
    {
        $phpWord = new PhpWord();

        $phpWord->addParagraphStyle('p1Style', array('align'=>'both', 'spaceAfter'=>0, 'spaceBefore'=>0));
        $phpWord->addParagraphStyle('p2Style', array('align'=>'both'));
        $phpWord->addFontStyle('f1Style', array('name' => 'Calibri', 'size'=>12));
        $phpWord->addFontStyle('f2Style', array('name' => 'Calibri','bold'=>true, 'size'=>12));
        $company = \App\Models\Company::where('id',session('company_id'))->first();
        $branch = $company->bankAccounts()->first()->bankBranch;
        $period = \App\Models\Year::where('company_id',session('company_id'))->first();
        $begin = new Carbon($period->begin);
        $end = new Carbon($period->end);
        $year = $end->year;
        $str = "first Monday of July {$year}";
        $date = new Carbon($str);

        for($i=0;$i<3;$i++) {
            $section = $phpWord->addSection();

            $section->addText($date->format('F j, Y'), 'f2Style', 'p1Style');

            $textrun = $section->addTextRun();
 //           $section->addTextBreak(1);

            $section->addText('The Manager,','f1Style','p1Style');
            $section->addText($branch->bank->name.",",'f1Style','p1Style');
            $section->addText($branch->address.".",'f1Style','p1Style');

            $textrun = $section->addTextRun();
//            $section->addTextBreak(1);
            $section->addText('Dear Sir,','f1Style','p2Style');

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText('Subject: ', 'f1Style');
            $textrun->addText('Bank Report for Audit Purpose of ', 'f2Style');
            $textrun->addText($company->name, 'f2Style');

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

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText(
                "Please state against each item any factors which may limit the completeness of your reply; if there is nothing to report, state ‘NONE’.",
                'f1Style',
            );
            
            $textrun = $section->addTextRun('p2Style');
            $textrun->addText(
                "It is understood that any replies given are in strict confidence, for the purposes of audit.",
                'f1Style',
            );
        }

        $writer = new Word2007($phpWord);
        $writer->save('hello World.docx');
    }
}
