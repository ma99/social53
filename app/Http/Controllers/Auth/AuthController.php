<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\SocialUserService;

use Auth;
use Redirect;
use Socialite;

use App\User;
use App\SocialUser;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Oauth Service Provider (i.e, GitHub) authentication page.
    */
	public function redirectToProvider($provider) {
	
			return Socialite::driver($provider)->redirect();
	}
	
	/**
     * Obtain the user information from Oauth Service Provider (i.e, GitHub) .
     */
	public function handleProviderCallback(SocialUserService $socialUserService, $provider) { 
    
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return Redirect::to('auth/'.$provider);
        }
        //dd($user);
        /**
         * Return user if exists; create and return if doesn't
        */
        $socialUser = $socialUserService->findOrCreateUser($user, $provider);

        Auth::login($socialUser, true);

        return Redirect::to('home');
    }

   
}
