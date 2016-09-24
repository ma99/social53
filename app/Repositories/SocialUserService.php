<?php

namespace App\Repositories;

use App\User;
use App\SocialUser;

class SocialUserService {
 
    
	public function findOrCreateUser($providerUser, $providerName) {
       
       // if oauth service provider's user exists in social_users table then return it's respective info from users table
        if ($socialUser = SocialUser::where('provider_user_id', $providerUser->id)->first()) {  
            return $socialUser->user;  
        }
        
        // check the service provider's email exist in users table or not
       // $user = User::whereEmail($providerUser->getEmail())->first();
        $user = User::where('email', $providerUser->email)->first();
        
        if (!$user) {

                $name = ($providerUser->name==null) ? $providerUser->nickname : $providerUser->name;
                $user = User::create([
                    'email' => $providerUser->email,
                    'name' => $name,
                    //'password' => bcrypt( str_random(5) ),
                    //'remember_token' => str_random(10),
                ]);
            }

       $user->social()->create([
            'provider_user_id' => $providerUser->id,
            'provider' => $providerName, //facebook/ github/ twitter ..
            'avatar' => $providerUser->avatar
        ]);
        
        return $user; //email, name, id

    }	
}
