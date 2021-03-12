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
        $pdf->loadView('pdd',compact('a'));
        return $pdf->stream('v.pdf');
    }

    public function ex()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $c = 'a2';
        $sheet->setCellValue($c, 'Hello Universe !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
    }

    public function doc()
    {
        $phpWord = new PhpWord();

        $section1 = $phpWord->addSection();
        $phpWord->addParagraphStyle('p2Style', array('align'=>'both', 'spaceAfter'=>0, 'spaceBefore'=>0));
        $section1->addText(
            '"Learn from yesterday,                                         live for today, hope for tomorrow. '
                . 'The important thing is not to stop questioning." '
                . '(Albert Einstein)',
            array('name' => 'Calibri', 'size' => 12),
            'p2Style'
        );

        $text ="Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. Some more text to test justify.";
        $phpWord->addFontStyle('r2Style', array('bold'=>false, 'italic'=>true, 'size'=>12));
        $section1->addText($text, 'r2Style', 'p2Style');

        $section2 = $phpWord->addSection();
        $section2->addText(
            "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
            ,
            array('name' => 'Book Antiqua', 'size' => 12)
        );


        $textrun = $section2->addTextRun();
        $textrun->addText('blah-blah', 'p_bold');
        $section2->addTextBreak(4);
        $textrun = $section2->addTextRun();
        $textrun->addText('blah-blah-blah in new line ', 'p');

        $writer = new Word2007($phpWord);
        $writer->save('hello World.docx');
    }
}
