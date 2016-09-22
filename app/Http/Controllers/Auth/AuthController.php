<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Redirect;
use Socialite;

use App\User;
use App\SocialUser;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Oauth Service Provider (i.e, GitHub) authentication page.
     *
     * @return Response
     */
	public function redirectToProvider($provider) {
	
			return Socialite::driver($provider)->redirect();
	}
	
	/**
     * Obtain the user information from Oauth Service Provider (i.e, GitHub) .
     *
     * @return Response
     */
	public function handleProviderCallback($provider) { 
    
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return Redirect::to('auth/'.$provider);
        }

        $socialUser = $this->findOrCreateUser($user, $provider);
        //dd($socialUser);
        Auth::login($socialUser, true);

        return Redirect::to('home');
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $githubUser
     * @return User
     */
    private function findOrCreateUser($providerUser, $providerName) {
        
        // if oauth service provider's user exists in social_users table then return it's respective info from users table
        //if ($authUser = SocialAccount::whereProviderUserId( $providerUser->getId() )->first()) {  
        if ($socialUser = SocialUser::where('provider_user_id', $providerUser->id)->first()) {  
            return $socialUser->user;  
            //dd($socialUser->user); 
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
