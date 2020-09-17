<?php

ini_set('display_errors', 'Off');
error_reporting(0);

include_once 'vendor/autoload.php';

use Simcify\Application;
use Simcify\Database;
use Simcify\Auth;
use Simcify\Mail;
use Simcify\Signer;

$app = new Application();

$today = date("Y-m-d");
$companies = Database::table("companies")->where("id",">", 1)->where("reminders", "On")->get();

/**
 * Send signing reminders
 * 
 */
if ( count($companies) > 0 ) {
//	echo "Companies to process: ", count($companies), "<br>";
	foreach ($companies as $company) {
//		echo "Processing Company: ", $company->name, "<br>";
		$reminders = Database::table("reminders")->where("company", $company->id)->get();
//		echo "Reminders to process: ", count($reminders),"<br>";
		if ( count($reminders) > 0 ) {
			foreach ($reminders as $reminder) {
				$requestDate = date('Y-m-d', strtotime($today. ' - '.$reminder->days.' days'));
//				echo "Checking for pending requests dated : ", $requestDate , "<br>";
				$requests = Database::table("requests")->where("send_time",">=", $requestDate)->where("send_time","<=", $requestDate." 23:59:59.999")->where("status","Pending")->get();
//				echo "Requests to process: ", count($requests),"<br>";			
				if ( count($requests) > 0 ) {
					foreach ($requests as $request) {
						if( env('GUEST_SIGNING') == "Enabled" AND env('FORCE_GUEST_SIGNING') == "Enabled"){
							$signingLink = env("APP_URL")."/view/".$request->document."?signingKey=".$request->signing_key;
						}else{
							$signingLink = env("APP_URL")."/document/".$request->document."?signingKey=".$request->signing_key;
						}
						Mail::send(
			                $request->email, $reminder->subject,
			                array(
			                    "title" => "Document Signing reminder",
			                    "subtitle" => "Click the link below to respond to the invite.",
			                    "buttonText" => "Sign Now",
			                    "buttonLink" => $signingLink,
			                    "message" => $reminder->message
			                ),
			                "withbutton"
						);
//						echo "Sent reminder to: ", $request->email, " for document ",$request->document,"<br>";
						$activity = 'Automated signing reminder sent to <span class="text-primary">'.$request->email.'</span>.';
						Signer::keephistory($request->document, $activity, "default");
					}
				}else{
					continue;
				}
			}
		}else{
			continue;
		}
	}
}
