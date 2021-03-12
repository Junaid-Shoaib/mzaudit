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

    public function pd()
    {
        $a = "hello world";
        $pdf = App::make('dompdf.wrapper');
//        $pdf->loadHTML('<h1>Test</h1>');
        $pdf->loadView('pdd',compact('a'));
        return $pdf->stream('v.pdf');
    }

    public function ex()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $c = 'a2';
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->setCellValue($c, 'Hello Universe !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
    }

    public function doc()
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText(
            '"Learn from yesterday, live for today, hope for tomorrow. '
                . 'The important thing is not to stop questioning." '
                . '(Albert Einstein)',
            array('name' => 'Calibre', 'size' => 20)
        );
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('hello World.docx');
    }
}
