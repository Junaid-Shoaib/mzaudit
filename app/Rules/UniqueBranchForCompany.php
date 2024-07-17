<?php

namespace App\Rules;

use App\Models\BankAccount;
use Illuminate\Contracts\Validation\Rule;

class UniqueBranchForCompany implements Rule
{
    
    protected $i;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    
        $this->i = -1;
         
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        

        $res = !BankAccount::where('company_id', session('company_id'))
        ->where('branch_id', $value)
        ->exists();

        $this->i++;
        return $res;
        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Branch '.  $this->i .' already exists for the given Company.';
    }
}
