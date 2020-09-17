<?php
namespace Simcify\Controllers;

use PDO;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Update {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "superadmin") {
            return view('errors/404');   
        }
        $versions = self::versions(); 
        $currentVersion = env("APP_VERSION");
        $latest = $versions[$currentVersion];
        return view('update', compact("user","latest"));
    }

    /**
     * Get settings view
     * 
     * @return Json
     */
    public function scan() {
        header('Content-type: application/json');
        $currentVersion = env("APP_VERSION");
        $versions = self::versions(); 
        $updateTo = $versions[$currentVersion];
        if (is_null($updateTo)) {
            exit(json_encode(responder("warning", "Hmm", "You are running on the latest Version ".$updateTo)));
        }
        self::update($updateTo);
        exit(json_encode(responder("success", "Complete!", "Updated successfully completed","reload()")));
    }

    /**
     * App versions
     * 
     * @return Array
     */
    public function versions() {
        return array(
                    "3.0" => "3.1",
                    "3.1" => "3.2",
                    "3.2" => NULL
                    );
    }

    /**
     * Update Signer
     * 
     * @return Json
     */
    public function update($version) {
        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env = new DotEnvWriter($envPath);
        $env->castBooleans();
        $versionUpdates = file_get_contents(config("app.storage")."updates/".$version.".json");
        $updates = explode("@signer", $versionUpdates);

        foreach ($updates as $update) {
            Database::table("users")->command($update);
        }
        
        if($version == "3.2"){
            $env->set("GUEST_SIGNING", "Enabled");
            $env->set("FORCE_GUEST_SIGNING", "Enabled");
        }
        
        $env->set("APP_VERSION", $version);
        $env->save();
    }


}
