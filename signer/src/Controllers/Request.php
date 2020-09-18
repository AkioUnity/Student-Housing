<?php
namespace Simcify\Controllers;

use Simcify\Str;
use Simcify\File;
use Simcify\Mail;
use Simcify\Auth;
use Simcify\Signer;
use Simcify\Database;

class Request{

    /**
     * Get requests view
     *
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if (!isset($_GET['view'])) {
            if ($user->role == "user") {
                $requestsData = Database::table("requests")->where("sender", $user->id)->orderBy("id", false)->get();
            }else{
                $requestsData = Database::table("requests")->where("company", $user->company)->orderBy("id", false)->get();
            }
        } else {
            $requestsData = Database::table("requests")->where("receiver", $user->id)->orderBy("id", false)->get();
        }
        $requests = array();
        foreach ($requestsData as $request) {
        	if (!empty($request->receiver)) {
        		$receiver = Database::table("users")->where("id" , $request->receiver)->first();
        		if (!empty($receiver)) {
        			$receiverInfo = $receiver;
        		}
        	}else{
    			$receiver = Database::table("users")->where("email" , $request->email)->first();
        		if (!empty($receiver)) {
        			$receiverInfo = $receiver;
        		}else{
        			$receiverInfo = $request->email;
        		}
    		}
        	$requests[] = array(
                                                "data" => $request,
                                                "file" => Database::table("files")->where("document_key" , $request->document)->first(),
                                                "sender" => Database::table("users")->where("id" , $request->sender)->first(),
                                                "receiver" => $receiverInfo
                                            );
        }
        
        return view('requests', compact("user", "requests"));
    }

    /**
     * Send signing request
     * 
     * @return Json
     */
    public function send() {
    	header('Content-type: application/json');
    	$user = Auth::user();
    	$document = Database::table("files")->where("document_key", input("document_key"))->first();
        $emailjson = str_replace('&#34;', '"', $_POST['emails']);
    	$emails = json_decode($emailjson, true);
    	$message = $_POST['message'];
    	$documentKey = $document->document_key;
    	$duplicate = $_POST['duplicate'];
    	$chain = $_POST['chain'];
    	$chainEmails = $chainPositions = '';
        $actionTakenBy = escape($user->fname.' '.$user->lname);
        /*
         * Check, whether IP address register is allowed in .env
         * If yes, then capture the user's IP address
         */
        if (env('REGISTER_IP_ADDRESS_IN_HISTORY') == 'Enabled') {
            $actionTakenBy .= ' ['.getUserIpAddr().']';
        }
    	$activity = 'Signing request sent to <span class="text-primary">'.implode(", ", $emails).'</span> by <span class="text-primary">'.$actionTakenBy.'</span>.';
    	if (!empty($_POST['positions']) && $document->is_template == "No") {
            $posjson = str_replace('&#34;', '"', $_POST['positions']);
	    	$positionsOriginal = json_decode($posjson, true);
	    	if(count($emails) > 1){
	    	    foreach($positionsOriginal as $pIndex => $positionsSingle){
	    	        $positionsSingle = json_decode($positionsSingle, true);
	    	        if (!empty($positionsSingle)) {
            	    	if (input("docWidth") != "set") { array_unshift($positionsSingle, input("docWidth")); }
                        $positions[] = json_encode($positionsSingle, JSON_UNESCAPED_UNICODE);
                        $chain_positions[] = $positionsSingle;
	    	        }else{
                		$positions[] = '';
                	}
	    	    }
	    	}else{
    	    	$positionsSingle = json_decode($positionsOriginal[0], true);
    	    	if (input("docWidth") != "set") { array_unshift($positionsSingle, input("docWidth")); }
    	    	$positions[0] = json_encode($positionsSingle);
	    	}
    	}elseif(!empty($_POST['positions']) && $document->is_template == "Yes"){
            $posjson1 = str_replace('&#34;', '"', $_POST['positions']);
            $positionsOriginal1 = json_decode($posjson1, true);
            if(count($emails) > 1){
                foreach($positionsOriginal1 as $pIndex => $positionsOriginal){
                    $positionsOriginal = json_decode($positionsOriginal, true);
                    if (!empty($positionsOriginal)) {
                        if (input("docWidth") != "set") { array_unshift($positionsOriginal, input("docWidth")); }
                        $positions[] = json_encode($positionsOriginal, JSON_UNESCAPED_UNICODE);
                        $chain_positions[] = $positionsOriginal;
                    }else{
                        $positions[] = '';
                    }
                }
            }else{
                $positionsOriginal = json_decode($positionsOriginal1[0], true);
                if (input("docWidth") != "set") { array_unshift($positionsOriginal, input("docWidth")); }
                $positions[0] = json_encode($positionsOriginal, JSON_UNESCAPED_UNICODE);
            }
        }else{
            foreach($emails as $index => $email){
                $positions[] = '';
                $chain_positions[] = '';
    	    }
    	}
        if($chain == "Yes"){
            $chainEmails = $emails;
            unset($chainEmails[0]);
            $chainEmails = array_values ( $chainEmails );
            $chainEmails = json_encode($chainEmails);
            $chainPositions = $chain_positions;
            unset($chainPositions[0]);
            $chainPositions = array_values ( $chainPositions );
            $chainPositions = json_encode($chainPositions);
        }
    	foreach($emails as $index => $email){
    		$signingKey = Str::random(32);
    		if ($duplicate == "Yes" || $document->is_template == "Yes") {
    			$duplicateDocId = Signer::duplicate($document->id, $document->name." (".$email.")");
    			$duplicateDoc = Database::table("files")->where("id", $duplicateDocId)->first();
    			$documentKey = $duplicateDoc->document_key;
                Database::table("files")->where("id", $duplicateDoc->id)->update(array("is_template" => "No"));
                $actionTakenBy = escape($user->fname.' '.$user->lname);
                /*
                 * Check, whether IP address register is allowed in .env
                 * If yes, then capture the user's IP address
                 */
                if (env('REGISTER_IP_ADDRESS_IN_HISTORY') == 'Enabled') {
                    $actionTakenBy .= ' ['.getUserIpAddr().']';
                }
    			$duplicateActivity = 'Signing request sent to <span class="text-primary">'.escape($email).'</span> by <span class="text-primary">'.$actionTakenBy.'</span>.';
    			Signer::keephistory($documentKey, $duplicateActivity, "default");
    		}
    		if( env('GUEST_SIGNING') == "Enabled" AND env('FORCE_GUEST_SIGNING') == "Enabled"){
    		    $signingLink = env("APP_URL")."/view/".$documentKey."?signingKey=".$signingKey;
    		}else{
    		    $signingLink = env("APP_URL")."/document/".$documentKey."?signingKey=".$signingKey;
    		}
			$trackerLink = env("APP_URL")."/mailopen?signingKey=".$signingKey;
			$receiverData = Database::table("users")->where("email", $email)->first();
			if (!empty($receiverData)) { $receiver = $receiverData->id; }else{ $receiver = 0; }
			$request = array( "sender_note" => escape(input("message")),  "chain_emails" => $chainEmails, "chain_positions" => $chainPositions, "company" => $user->company, "document" => $documentKey, "signing_key" => $signingKey, "positions" => $positions[$index], "email" => $email, "sender" => $user->id, "receiver" => $receiver );
			Database::table("requests")->insert($request);
    		$send = Mail::send(
                $email, $user->fname." ".$user->lname." has invited you to sign a document",
                array(
                    "title" => "Sign Lease Agreement",
                    "subtitle" => "Click the link below to respond to the invite.",
                    "buttonText" => "Sign Now",
                    "buttonLink" => $signingLink,
                    "message" => "You have been invited to sign a document by ".$user->fname." ".$user->lname.". Click the link above to respond to the invite.<br><strong>Message:</strong> ".input("message")."<br><br>Thank you<br>".env("APP_NAME")." Team
                    <img src='".$trackerLink."' width='0' height='0'>"
                ),
                "withbutton"
            );
            if (!$send) { exit(json_encode(responder("error", "Email Sender Error!", $send->ErrorInfo))); }
            if($chain == "Yes"){ break; }
    	}
    	Signer::keephistory($document->document_key, $activity, "default");
    	exit(json_encode(responder("success", "Sent!", "Request successfully sent.","reload()")));
    }

    public function sendAgreement() {
        header('Content-type: application/json');
        $document = Database::table("files")->where("document_key", input("document_key"))->first();
//        exit("OK");
        $emailjson = str_replace('&#34;', '"', $_POST['emails']);
//        exit($_POST['emails']);
        $emails = json_decode($emailjson, true);

        $documentKey = $document->document_key;
        $duplicate = $_POST['duplicate'];
        $chain = $_POST['chain'];
        $chainEmails = $chainPositions = '';
        $actionTakenBy = escape('Holtz Builders');

        $activity = 'Signing request sent to <span class="text-primary">'.implode(", ", $emails).'</span> by <span class="text-primary">'.$actionTakenBy.'</span>.';
        if(!empty($_POST['positions']) && $document->is_template == "Yes"){
            $posjson1 = str_replace('&#34;', '"', $_POST['positions']);
            $positionsOriginal1 = json_decode($posjson1, true);
            if(count($emails) > 1){
                foreach($positionsOriginal1 as $pIndex => $positionsOriginal){
                    $positionsOriginal = json_decode($positionsOriginal, true);
                    if (!empty($positionsOriginal)) {
                        if (input("docWidth") != "set") { array_unshift($positionsOriginal, input("docWidth")); }
                        $positions[] = json_encode($positionsOriginal, JSON_UNESCAPED_UNICODE);
                        $chain_positions[] = $positionsOriginal;
                    }else{
                        $positions[] = '';
                    }
                }
            }else{
                $positionsOriginal = json_decode($positionsOriginal1[0], true);
                if (input("docWidth") != "set") { array_unshift($positionsOriginal, input("docWidth")); }
                $positions[0] = json_encode($positionsOriginal, JSON_UNESCAPED_UNICODE);
            }
        }else{
            foreach($emails as $index => $email){
                $positions[] = '';
                $chain_positions[] = '';
            }
        }

        foreach($emails as $index => $email){
            $signingKey = Str::random(32);
            if ($duplicate == "Yes" || $document->is_template == "Yes") {
                $duplicateDocId = Signer::duplicate($document->id, $document->name." (".$email.")");
                $duplicateDoc = Database::table("files")->where("id", $duplicateDocId)->first();
                $documentKey = $duplicateDoc->document_key;
                Database::table("files")->where("id", $duplicateDoc->id)->update(array("is_template" => "No"));

                $duplicateActivity = 'Signing request sent to <span class="text-primary">'.escape($email).'</span> by <span class="text-primary">'.$actionTakenBy.'</span>.';
                Signer::keephistory($documentKey, $duplicateActivity, "default");
            }
            $signingLink = env("APP_URL")."/view/".$documentKey."?signingKey=".$signingKey;
            $trackerLink = env("APP_URL")."/mailopen?signingKey=".$signingKey;
            $receiverData = Database::table("users")->where("email", $email)->first();
            if (!empty($receiverData)) { $receiver = $receiverData->id; }else{ $receiver = 0; }
            $request = array( "sender_note" => escape(input("message")),  "chain_emails" => $chainEmails, "chain_positions" => $chainPositions, "company" => 1, "document" => $documentKey, "signing_key" => $signingKey, "positions" => $positions[$index], "email" => $email, "sender" => 1, "receiver" => $receiver );
            Database::table("requests")->insert($request);
            $send = Mail::send(
                $email, "Hiawatha Student Housing Lease Agreement Sign",
                array(
                    "title" => "Sign Lease Agreement",
                    "subtitle" => "Click the link below to respond to the invite.",
                    "buttonText" => "Sign Now",
                    "buttonLink" => $signingLink,
                    "message" => "You have been invited to sign a lease agreement by Holtz Builders inc.<br> Click the link above to respond to the invite.<br><br>Thank you<br>".env("APP_NAME")." Team
                    <img src='".$trackerLink."' width='0' height='0'>"
                ),
                "withbutton"
            );
            if (!$send) { exit(json_encode(responder("error", "Email Sender Error!", $send->ErrorInfo))); }
            if($chain == "Yes"){ break; }
        }
        Signer::keephistory($document->document_key, $activity, "default");
        exit(json_encode(responder("success", "Sent!", "Request successfully sent.","reload()")));
    }
    /**
     * Delete signing request
     * 
     * @return Json
     */
    public function delete() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	Database::table("requests")->where("id", $requestId)->delete();
    	exit(json_encode(responder("success", "Deleted!", "Request successfully deleted.","reload()")));
    }

    /**
     * Cancel signing request
     * 
     * @return Json
     */
    public function cancel() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	Database::table("requests")->where("id", $requestId)->update(array("status" => "Cancelled", "update_time" => date("Y-m-d H-i-s")));
    	exit(json_encode(responder("success", "Cancelled!", "Request successfully cancelled.","reload()")));
    }

    /**
     * Send a signing request reminder
     * 
     * @return Json
     */
    public function remind() {
        header('Content-type: application/json');
        $requestId = input("requestid");
        $user = Auth::user();
        $request = Database::table("requests")->where("id", $requestId)->first();
		if( env('GUEST_SIGNING') == "Enabled" AND env('FORCE_GUEST_SIGNING') == "Enabled"){
		    $signingLink = env("APP_URL")."/view/".$request->document."?signingKey=".$request->signing_key;
		}else{
		    $signingLink = env("APP_URL")."/document/".$request->document."?signingKey=".$request->signing_key;
		}
        $send = Mail::send(
            $request->email, "Signing invitation reminder from ".$user->fname." ".$user->lname,
            array(
                "title" => "Signing invitation reminder.",
                "subtitle" => "Click the link below to respond to the invite.",
                "buttonText" => "Sign Now",
                "buttonLink" => $signingLink,
                "message" => "You have been invited to sign a document by ".$user->fname." ".$user->lname.". Click the link above to respond to the invite.<br><strong>Message:</strong> ".input("message")."<br><br>Thank you<br>".env("APP_NAME")." Team"
            ),
            "withbutton"
        );
        $actionTakenBy = escape($user->fname.' '.$user->lname);
        /*
         * Check, whether IP address register is allowed in .env
         * If yes, then capture the user's IP address
         */
        if (env('REGISTER_IP_ADDRESS_IN_HISTORY') == 'Enabled') {
            $actionTakenBy .= ' ['.getUserIpAddr().']';
        }
        $activity = '<span class="text-primary">'.$actionTakenBy.'</span> sent a signing reminder to <span class="text-primary">'.escape($request->email).'</span>.';
        Signer::keephistory($request->document, $activity, "default");
        if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
        exit(json_encode(responder("success", "Sent!", "Reminder successfully send.","reload()")));
    }

    /**
     * Decline a signing request 
     *
     * @return Json
     */
    public function decline() {
    	header('Content-type: application/json');
    	$requestId = input("requestid");
    	$user = Auth::user();
        Database::table("requests")->where("id", $requestId)->update(array("status" => "Declined", "update_time" => date("Y-m-d H-i-s")));
        $request = Database::table("requests")->where("id", $requestId)->first();
        $sender = Database::table("users")->where("id", $request->sender)->first();
        $documentLink = env("APP_URL")."/document/".$request->document;
        $send = Mail::send(
            $sender->email, "Signing invitation declined by ".$user->fname." ".$user->lname,
            array(
                "title" => "Signing invitation declined.",
                "subtitle" => "Click the link below to view document.",
                "buttonText" => "View Document",
                "buttonLink" => $documentLink,
                "message" => $user->fname." ".$user->lname." has declined the signing invitation you had sent. Click the link above to view the document.<br><br>Thank you<br>".env("APP_NAME")." Team"
            ),
            "withbutton"
        );
        $actionTakenBy = escape($user->fname.' '.$user->lname);
        /*
         * Check, whether IP address register is allowed in .env
         * If yes, then capture the user's IP address
         */
        if (env('REGISTER_IP_ADDRESS_IN_HISTORY') == 'Enabled') {
            $actionTakenBy .= ' ['.getUserIpAddr().']';
        }
        $activity = '<span class="text-primary">'.$actionTakenBy.'</span> declined a signing invitation of this document.';
        Signer::keephistory($request->document, $activity, "default");
        $notification = '<span class="text-primary">'.escape($user->fname.' '.$user->lname).'</span> declined a signing invitation of this <a href="'.url("Document@open").$request->document.'">document</a>.';
        Signer::notification($sender->id, $notification, "decline");
        if (!$send) { exit(json_encode(responder("error", "Oops!", $send->ErrorInfo))); }
    	exit(json_encode(responder("success", "Declined!", "Request declined and sender notified.","reload()")));
    }

}
