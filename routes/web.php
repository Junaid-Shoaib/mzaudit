<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankBranchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Companies

Route::get('companies', [CompanyController::class, 'index'])
    ->name('companies')
    ->middleware('auth');

Route::get('companies/create', [CompanyController::class, 'create'])
    ->name('companies.create')
    ->middleware('auth');

Route::get('companies/{company}', [CompanyController::class, 'show'])
    ->name('companies.show')
    ->middleware('auth');

Route::post('companies', [CompanyController::class, 'store'])
    ->name('companies.store')
    ->middleware('auth');

Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])
    ->name('companies.edit')
    ->middleware('auth');

Route::put('companies/{company}', [CompanyController::class, 'update'])
    ->name('companies.update')
    ->middleware('auth');

Route::delete('companies/{company}', [CompanyController::class, 'destroy'])
    ->name('companies.destroy')
    ->middleware('auth');

Route::get('get_banks', [CompanyController::class, 'getBanks']);
Route::get('get_branches/{bank}', [CompanyController::class, 'getBranches'])
    ->name('branches')
    ->middleware('auth');

// Banks

Route::get('banks', [BankController::class, 'index'])
    ->name('banks')
    ->middleware('auth');

Route::get('banks/create', [BankController::class, 'create'])
    ->name('banks.create')
    ->middleware('auth');

Route::get('banks/{bank}', [BankController::class, 'show'])
    ->name('banks.show')
    ->middleware('auth');

Route::post('banks', [BankController::class, 'store'])
    ->name('banks.store')
    ->middleware('auth');

Route::get('banks/{bank}/edit', [BankController::class, 'edit'])
    ->name('banks.edit')
    ->middleware('auth');

Route::put('banks/{bank}', [BankController::class, 'update'])
    ->name('banks.update')
    ->middleware('auth');

Route::delete('banks/{bank}', [BankController::class, 'destroy'])
    ->name('banks.destroy')
    ->middleware('auth');

// Bank Branches

Route::get('branches', [BankBranchController::class, 'index'])
    ->name('branches')
    ->middleware('auth');

Route::get('branches/create', [BankBranchController::class, 'create'])
    ->name('branches.create')
    ->middleware('auth');

Route::get('branches/{branch}', [BankBranchController::class, 'show'])
    ->name('branches.show')
    ->middleware('auth');

Route::post('branches', [BankBranchController::class, 'store'])
    ->name('branches.store')
    ->middleware('auth');

Route::get('branches/{branch}/edit', [BankBranchController::class, 'edit'])
    ->name('branches.edit')
    ->middleware('auth');

Route::put('branches/{branch}', [BankBranchController::class, 'update'])
    ->name('branches.update')
    ->middleware('auth');

Route::delete('branches/{branch}', [BankBranchController::class, 'destroy'])
    ->name('branches.destroy')
    ->middleware('auth');