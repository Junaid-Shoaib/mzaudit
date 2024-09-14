<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Company;
use App\Models\Year;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{

    public function create()
    {
        
        if(auth()->user()->role != 0){
            abort(403, "You haven't permission for access this Page.");
        }
        return Inertia::render('User/Create');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', new Password, 'confirmed'],
            'role' => ['required'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => (int)$input['role'],
        ]);

           $company = Company::first();
           if($company){
               $year = Year::where('company_id', $company->id)->first();
               $settings = [ 
                    [
                        'key' => 'active_company',
                        'value' => $company->id,
                        'user_id' => $user->id,
                    ],
                
                    [
                        'key' => 'active_year',
                        'value' => $year->id,
                        'user_id' => $user->id,
                    ],
                ];

                foreach($settings as $key => $setting){
                    Setting::create($setting);    
                }
           }
            
           


        return Redirect::route('users.create')->with('success', 'User Created Successfully');
    }



    //
}
