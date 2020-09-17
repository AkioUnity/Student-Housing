<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\Database;

class Field{

    /**
     * save field to database
     * 
     * @return Json
     */
    public function save() {
        header('Content-type: application/json');
        $user = Auth::user();
        if (input("type") == "stamp") {
            $value = $_POST['fieldvalue'];
        }else{
            $value = escape($_POST['fieldvalue']);
        }
        $data = array(
                        "label" => escape($_POST['fieldlabel']),
                        "value" => $value,
                        "type" => escape($_POST['type']),
                        "user" => $user->id,
                        "company" => $user->company
                    );
        Database::table("fields")->insert($data);
        $fieldId = Database::table("fields")->insertId();
        if (input("type") == "input") { 
            $callback = "inputFieldResponse('".input("fieldId")."', '".$fieldId."')"; 
        }else if(input("type") == "custom"){ 
            $callback = "fieldResponse('".input("fieldId")."', '".$fieldId."')"; 
        }else if(input("type") == "stamp"){ 
            $callback = "stampResponse('".input("fieldId")."', '".$fieldId."')"; 
        }
        exit(json_encode($response = array(
                "status" => "success",
                "callback" => $callback,
                "notify" => false,
                "callbackTime" => "instant"
            )));
    }

    /**
     * delete field
     * 
     * @return Json
     */
    public function delete() {
        header('Content-type: application/json');
    	Database::table("fields")->where("id", input("fieldId"))->delete();
        exit(json_encode(responder("success", "", "","", false)));
    }

}