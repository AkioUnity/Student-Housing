<?php
namespace Simcify\Controllers;

use Simcify\Auth as Authenticate;
use Simcify\Database;

class Auth{

    /**
     * Get Auth view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $enabledguest = $guest = $signingLink = false;
        if (isset($_COOKIE['guest'])) {
            $guest = true;
            $guestData = unserialize($_COOKIE['guest']);
            $signingLink = url("Guest@open").$guestData[0]."?signingKey=".$guestData[1];
            $enabledguest = true;
            setcookie("guest", null, time()-30000, '/');
        }
        if (!isset($_GET['secure'])) {
            redirect(url("Auth@get")."?secure=true");
        }
        return view('login', compact("guest","signingLink"));
    }

    /**
     * Sign In a user
     * 
     * @return Json
     */
    public function signin() {
        $signIn = Authenticate::login(
		    input('email'), 
		    input('password'), 
		    array(
		        "rememberme" => true,
		        "redirect" => url(""),
		        "status" => "Active"
		    )
		);

        //Sync the MySQL and PHP datetimes
        Database::table('users')->synchronizeTimezone();

        header('Content-type: application/json');
		exit(json_encode($signIn));
    }

    /**
     * Forgot password - send reset password email
     * 
     * @return Json
     */
    public function forgot() {
        $forgot = Authenticate::forgot(
		    input('email'), 
		    env('APP_URL')."/reset/[token]"
		);
        header('Content-type: application/json');
		exit(json_encode($forgot));
    }

    /**
     * Get reset password view
     * 
     * @return \Pecee\Http\Response
     */
    public function getreset($token) {
        return view('reset', array("token" => $token));
    }

    /**
     * Reset password
     * 
     * @return Json
     */
    public function reset() {
        $reset = Authenticate::reset(
		    input('token'), 
		    input('password')
		);

        header('Content-type: application/json');
		exit(json_encode($reset));
    }

    /**
     * Create an account
     * 
     * @return Json
     */
    public function signup() {

    	if (!empty(input('business'))) {
    		$companyData = array(
    				"name" => input('company'),
                    "email" => input('email'),
                    "reminders" => "Off"
    			);
	        $insert = Database::table("companies")->insert($companyData);
	        $companyId = Database::table("companies")->insertId();
	        $role = "admin";
    	}else{
    		$companyId = 0;
    		$role = "user";
    	}


        $signup = Authenticate::signup(
		    array(
		        "fname" => input('fname'),
		        "lname" => input('lname'),
		        "email" => input('email'),
		        "role" => $role,
		        "company" => $companyId,
		        "password" => Authenticate::password(input('password'))
		    ), 
		    array(
		        "authenticate" => true,
		        "redirect" => url(""),
		        "uniqueEmail" => input('email')
		    )
		);

        header('Content-type: application/json');
		exit(json_encode($signup));
    }

    /**
     * Sign Out a logged in user
     *
     */
    public function signout() {
        Authenticate::deauthenticate();
        redirect(url("Auth@get"));

    }

}
