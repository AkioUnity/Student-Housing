<?php
namespace Simcify\Controllers;

use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Database;

class Company{

    /**
     * Get company view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "superadmin") {
            return view('errors/404');   
        }
    	$companies = Database::table("companies")->where("id",">", 1)->get();
    	$companiesData = array();
        $backgroundColors = array("bg-danger","bg-success","bg-warning","bg-purple");
    	foreach ($companies as $company) {
    		$companiesData[] = array(
    											"company" => $company,
    											"owner" => Database::table("users")->where("company",$company->id)->where("role" ,"admin")->first(),
                                                "team" => Database::table("users")->where("company" , $company->id)->count("id", "team")[0]->team,
                                                "files" => Database::table("files")->where("company" , $company->id)->count("id", "files")[0]->files,
                                                "disk" => Database::table("files")->where("company" , $company->id)->sum("size", "size")[0]->size,
    											"color" => $backgroundColors[array_rand($backgroundColors)]
    										);
    	}
    	$data = array(
    			"user" => Auth::user(),
    			"companies" => $companiesData
    		);
    	if ($data["user"]->role != "superadmin") {
    		return view('errors/404');
    	}
        return view('companies', $data);
    }

    /**
     * Create business user account
     * 
     * @return Json
     */
    public function create() {
        header('Content-type: application/json');
        $password = rand(111111, 999999);
        if (!empty(input('avatar'))) {
            $upload = File::upload(
                input('avatar'), 
                "avatar",
                array(
                    "source" => "base64",
                    "extension" => "png"
                )
            );
            $avatar = $upload['info']['name'];
        }else{
            $avatar = '';
        }

   		$companyData = array(
                "name" => escape(input('company')),
                "phone" => escape(input('phone')),
                "email" => escape(input('email')),
                "reminders" => "Off"
    		);
	    $insert = Database::table("companies")->insert($companyData);
	    $companyId = Database::table("companies")->insertId();
	    $role = "admin";
        
        $signup = Auth::signup(
            array(
                "fname" => escape(input('fname')),
                "lname" => escape(input('lname')),
                "phone" => escape(input('phone')),
                "email" => escape(input('email')),
                "address" => escape(input('address')),
                "avatar" => $avatar,
                "role" => "admin",
                "company" => $companyId,
                "password" => Auth::password($password)
            ), 
            array(
                "uniqueEmail" => input('email')
            )
        );
        if ($signup["status"] == "success") {
            Mail::send(
                input('email'),
                "Welcome to ".env("APP_NAME")."!",
                array(
                    "title" => "Welcome to ".env("APP_NAME")."!",
                    "subtitle" => "A new account has been created for you at ".env("APP_NAME").".",
                    "buttonText" => "Login Now",
                    "buttonLink" => env("APP_URL"),
                    "message" => "These are your login Credentials:<br><br><strong>Email:</strong>".input('email')."<br><strong>Password:</strong>".$password."<br><br>Thank you<br>".env("APP_NAME")." Team."
                ),
                "withbutton"
            );
            exit(json_encode(responder("success", "Account Created", "Account successfully created","reload()")));
        }else{
            if (!empty(($avatar))) {
                File::delete($avatar, "avatar");
            }
            exit(json_encode(responder("error", "Oops!", $signup["message"])));
        }
    }
    
    /**
     * Delete company account
     * 
     * @return Json
     */
    public function delete() {
        $account = Database::table("companies")->where("id", input("companyid"))->first();
        Database::table("companies")->where("id", input("companyid"))->delete();
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Company Deleted!", "Company successfully deleted.","reload()")));
    }

    /**
     * Company update view
     * 
     * @return Json
     */
    public function updateview() {
        $data = array(
                "company" => Database::table("companies")->where("id", input("companyid"))->first()
            );
        return view('extras/updatecompany', $data);
    }

    /**
     * Update company account
     * 
     * @return Json
     */
    public function update() {
        $account = Database::table("companies")->where("id", input("companyid"))->first();
        foreach (input()->post as $field) {
            if ($field->index == "csrf-token" || $field->index == "companyid") {
                continue;
            }
            Database::table("companies")->where("id" , input("companyid"))->update(array($field->index => escape($field->value)));
        }
        header('Content-type: application/json');
        exit(json_encode(responder("success", "Alright", "Company successfully updated","reload()")));
    }

}
