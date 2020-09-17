<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Simcify\Database;
use Simcify\Auth;
use Simcify\Signer;

class Authenticate implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {

        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            // Set the locale to the user's preference
            //date_default_timezone_set($request->user->timezone);
            config('app.locale.default', $request->user->{config('auth.locale')});
        } else {
            if (isset($_GET['signingKey'])) { 
                $guest = serialize(array(self::getDocumentKey(), $_GET['signingKey']));
                setcookie("guest", $guest, time()+3600000*24*30, '/');
                $signingKey = $_GET['signingKey'];
                $signRequest = Database::table("requests")->where("signing_key", $signingKey)->first();
                $actionTakenBy = escape($signRequest->email);
                /*
                 * Check, whether IP address register is allowed in .env
                 * If yes, then capture the user's IP address
                 */
                if (env('REGISTER_IP_ADDRESS_IN_HISTORY') == 'Enabled') {
                    $actionTakenBy .= ' ['.getUserIpAddr().']';
                }
                $activity = '<span class="text-primary">'.$actionTakenBy.'</span> opened this document.';
                Signer::keephistory($request->document, $activity, "default");
            }
            $request->setRewriteUrl(url('Auth@get'));
        }
        return $request;

    }
    
    /**
     * Get document key from signing url
     * 
     * @return  $document_key
     */
    public static function getDocumentKey() {
        $fullUrl = $_SERVER['REQUEST_URI'];
        $signingKey = $_GET['signingKey'];
        $basename = basename($fullUrl);
        $unwantedPart = "?signingKey=".$signingKey;
        $document_key = str_replace($unwantedPart, "", $basename);
        return $document_key;
    }
}
