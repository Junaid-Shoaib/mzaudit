<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $banks = [
            [
                'name' => 'Al Baraka Bank (Pakistan) Limited',
            ],
            [
                'name' => 'Allied Bank Limited',
            ],
            [
                'name' => 'Askari Bank Limited',
            ],
            [
                'name' => 'Bank Alfalah Limited',
            ],
            [
                'name' => 'Bank Al-Habib Limited',
            ],
            [
                'name' => 'Bank of China',
            ],
            [
                'name' => 'Bank Of Khyber',
            ],

            [
                'name' => 'Bank of Punjab',
            ],
            [
                'name' => 'BankIslami Pakistan Limited',
            ],
            [
                'name' => 'Citibank ',
            ],
            [
                'name' => 'Deutsche Bank AG',
            ],
            [
                'name' => 'Dubai Islamic Bank Pakistan Limited',
            ],
            [
                'name' => 'Faysal Bank Limited',
            ],
            [
                'name' => 'First Women Bank Limited',
            ],
            [
                'name' => 'Habib Metropolitan Bank Limited',
            ],
            [
                'name' => 'House Building Finance Corporation',
            ],
            [
                'name' => 'Industrial Development Bank of Pakistan',
            ],
            [
                'name' => 'Jahangir Siddiqui Investment Bank Limited',
            ],
            [
                'name' => 'JS Bank Limited',
            ],
            [
                'name' => 'MCB Islamic Bank Limited',
            ],
            [
                'name' => 'Meezan Bank Limited',
            ],
            [
                'name' => 'Muslim Commercial Bank',
            ],
            [
                'name' => 'National Bank of Pakistan',
            ],
            [
                'name' => 'PAIR Investment Compan',
            ],
            [
                'name' => 'Pak Brunei Investment Company Limited',
            ],
            [
                'name' => 'Pak China Investment Company Limited',
            ],
            [
                'name' => 'Pak Kuwait Investment Company',
            ],
            [
                'name' => 'Pak Libya Holding Company Limited',
            ],
            [
                'name' => 'Pak Oman Investment Company',
            ],
            [
                'name' => 'Pakistan Mortgage Refinance Company',
            ],
            [
                'name' => 'Samba Bank Limited',
            ],
            [
                'name' => 'Saudi Pak Industrial & Agricultural Investment Company Limited',
            ],
            [
                'name' => 'Silkbank Limited',
            ],
            [
                'name' => 'Sindh Bank Limited',
            ],
            [
                'name' => 'SME Bank Limited',
            ],
            [
                'name' => 'Soneri Bank Limited',
            ],
            [
                'name' => 'Standard Chartered Bank (Pakistan) Limited',
            ],
            [
                'name' => 'Summit Bank Limited',
            ],
            [
                'name' => 'United Bank Limited ',
            ],
            [
                'name' => 'Zarai Taraqiati Bank Limited',
            ],
            [
                'name' => 'FINCA Microfinance Bank Limited',
            ],
            [
                'name' => 'U Microfinance Bank Limited',
            ],
            [
                'name' => 'Pak Oman Microfinance Bank Limited',
            ],
            [
                'name' => 'NRSP Microfinance Bank Limited',
            ],
            [
                'name' => 'Telenor Microfinance Bank Limited',
            ],
            [
                'name' => 'Apna Microfinance Bank Limited',
            ],
            [
                'name' => 'Advans Microfinance Bank Limited',
            ],
            [
                'name' => 'Mobilink Microfinance Bank Limited',
            ],
            [
                'name' => 'Sindh Microfinance Bank Limited',
            ],
            [
                'name' => 'Khushhali Bank Limited',
            ],
            [
                'name' => 'The First Micro Finance Bank Limited.',
            ],
            [
                'name' => 'HBL Microfinance Bank Limited',
            ],
            
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
