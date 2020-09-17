<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Settings | Email Sign Agreement</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Berkshire+Swash|Cookie|Courgette|Dr+Sugiyama|Grand+Hotel|Great+Vibes|League+Script|Meie+Script|Miss+Fajardose|Niconne|Pacifico|Petit+Formal+Script|Rochester|Sacramento|Tangerine" crossorigin="anonymous" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signature pad css -->
    <link rel="stylesheet" href="<?=url("");?>assets/css/signature-pad.css">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
    <script src="<?=url("");?>assets/js/jscolor.js"></script>
</head>

<body class="settings_template">

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}

    <div class="content">
        <div class="page-title">
            <h3>Settings</h3>
        </div>
        <div class="light-card settings-card">
            <div class="settings-menu">
                <ul>
                    <li class="active"><a data-toggle="tab" class="tab" href="#profile">Profile</a></li>
                    <li><a data-toggle="tab" class="tab" href="#signature">Signature</a></li>
                    @if ( $user->role == "superadmin" || $user->role == "admin" )
                    <li><a data-toggle="tab" class="tab" href="#company">Company</a></li>
                    <li><a data-toggle="tab" class="tab" href="#reminders">Reminders</a></li>
                    @endif
                    @if ( $user->role == "superadmin" )
                    <li><a data-toggle="tab" class="tab" href="#system">System</a></li>
                    @endif
                    <li><a data-toggle="tab" class="tab" href="#password">Password</a></li>
                </ul>
            </div>
            <div class="settings-forms">
                <div class="col-md-5 tab-content">
                    <!-- Profile start -->
                    <div id="profile" class="tab-pane fade in active">
                        <h4>Profile</h4>
                        <form class="simcy-form" id="setting-profile-form" action="<?=url("Settings@updateprofile");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Profile picture</label>
                                        @if( !empty($user->avatar) )
                                        <input type="file" name="avatar" class="croppie" default="<?=url("");?>uploads/avatar/{{ $user->avatar }}" crop-width="200" crop-height="200" accept="image/*">
                                        @else
                                        <input type="file" name="avatar" class="croppie" crop-width="200" crop-height="200" accept="image/*">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>First name</label>
                                        <input type="text" class="form-control" name="fname" value="{{ $user->fname }}" placeholder="First name" required>
                                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Last name</label>
                                        <input type="text" class="form-control" name="lname" value="{{ $user->lname }}" placeholder="Last name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Email address</label>
                                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" placeholder="Email address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Phone number</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" placeholder="Phone number">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" value="{{ $user->address }}" placeholder="Address">
                                    </div>
                                </div>
                            </div>
                                      <div class="form-group">
                                          <div class="row">
                                              <div class="col-md-12">
                                                  <label>Time Zone</label>
                                                  <select class="form-control select2" name="timezone" required="">
                                                    @foreach ( $timezones as $timezone )
                                                    <option value="{{ $timezone->zone }}" @if( $timezone->zone == $user->timezone ) selected @endif>{{ $timezone->name }}</option>
                                                    @endforeach
                                                  </select>
                                              </div>
                                          </div>
                                      </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit" >Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- profile end -->
                    @if ( $user->role == "superadmin" || $user->role == "admin" )
                    <!-- Company start -->
                    <div id="reminders" class="tab-pane fade">
                        <h4>Reminders</h4>
                        <p>Reminders are emails sent to someone when no action has been taken by them after a signing request had been sent.
                            The emails will be sent after the sent number of days</p>
                        <form class="simcy-form" id="setting-reminder-form" action="<?=url("Settings@updatereminders");?>" data-parsley-validate="" loader="true" method="POST">
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if( $company->reminders == "On" )
                                        <input type="checkbox" id="enable-reminders" class="switch" name="reminders" value="On" checked />
                                        @else
                                        <input type="checkbox" id="enable-reminders" class="switch" name="reminders" value="Off" />
                                        @endif
                                        <label for="enable-reminders">Enable reminders</label>
                                    </div>
                                </div>
                            </div>
                            @if( $company->reminders == "On" )
                            <div class="panel-group reminders-holder" id="accordion">
                            @else
                            <div class="panel-group reminders-holder" id="accordion" style="display: none;">
                            @endif
                                @if( count($reminders) > 0 )
                                @foreach ($reminders as $index => $reminder)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        @if( $index >  0 )
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        @endif
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse{{ $index + 1 }}">Reminder #<span class="count">{{ $index + 1 }}</span></a></h4>
                                    </div>
                                    @if( $index ==  0 )
                                    <div class="panel-collapse collapse in" id="collapse{{ $index + 1 }}">
                                    @else
                                    <div class="panel-collapse collapse" id="collapse{{ $index + 1 }}">
                                    @endif
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Email subject</label> <input class="form-control" name="subject[]" placeholder="Email subject" required type="text" value="{{ $reminder->subject }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Days after request is sent</label> <input class="form-control" name="days[]" min="1" placeholder="Days after request is sent" required type="number" value="{{ $reminder->days }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Message</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">{{ $reminder->message }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse1">Reminder #<span class="count">1</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse in" id="collapse1">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Email subject</label> <input class="form-control" name="subject[]" placeholder="Email subject" required type="text" value="Signing invitation reminder ">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Days after request is sent</label> <input class="form-control" name="days[]" min="1" placeholder="Days after request is sent" required type="number" value="3">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Message</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">Hello there,

I hope you are doing well.
I am writing to remind you about the signing request I had sent earlier.

Thank you
{{ $user->fname }} {{ $user->lname }}
</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse2">Reminder #<span class="count">2</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse " id="collapse2">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Email subject</label> <input class="form-control" name="subject[]" placeholder="Email subject" required type="text" value="Signing invitation reminder ">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Days after request is sent</label> <input class="form-control" name="days[]" min="1" placeholder="Days after request is sent" required type="number" value="7">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Message</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">Hello there,

I hope you are doing well.
I am writing to remind you about the signing request I had sent earlier.

Thank you
{{ $user->fname }} {{ $user->lname }}
</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <span class="delete-reminder" data-toggle="tooltip" title="Remove reminder"><i class="ion-ios-trash"></i></span>
                                        <h4 class="panel-title"><a data-parent="#accordion" data-toggle="collapse" href="#collapse3">Reminder #<span class="count">3</span></a></h4>
                                    </div>
                                    <div class="panel-collapse collapse " id="collapse3">
                                        <div class="panel-body">
                                            <div class="remider-item">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <input type="hidden" name="count[]" value="1">
                                                            <label>Email subject</label> <input class="form-control" name="subject[]" placeholder="Email subject" required type="text" value="Signing invitation reminder">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Days after request is sent</label> <input class="form-control" name="days[]" min="1" placeholder="Days after request is sent" required type="number" value="14">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Message</label>
                                                            <textarea class="form-control" name="message[]" required rows="9">Hello there,

I hope you are doing well.
I am writing to remind you about the signing request I had sent earlier.

Thank you
{{ $user->fname }} {{ $user->lname }}
</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-default add-reminder" type="button">Add reminder</button>
                                        <button class="btn btn-primary" type="submit">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- reminder end -->
                    <!-- Company start -->
                    <div id="company" class="tab-pane fade">
                        <h4>Company</h4>
                        <form class="simcy-form" id="setting-company-form" action="<?=url("Settings@updatecompany");?>" data-parsley-validate="" loader="true" method="POST">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Company name</label>
                                        <input type="text" class="form-control" name="name" placeholder="Company name" value="{{ $company->name }}" required>
                                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Email address</label>
                                        <input type="email" class="form-control" name="email" placeholder="Email address" value="{{ $company->email }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Phone number</label>
                                        <input type="text" class="form-control" name="phone" placeholder="Phone number" value="{{ $company->phone }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- Company end -->
                    @endif
                    @if ( $user->role == "superadmin" )
                    <!-- System start -->
                    <div id="system" class="tab-pane fade">
                        <h4>System</h4>
                        <form class="simcy-form"action="<?=url("Settings@updatesystem");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>System name</label>
                                        <input type="text" class="form-control system-name" placeholder="System name" name="APP_NAME" value="{{ env('APP_NAME'); }}" required>
                                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>System Logo </label>
                                        <input type="file" name="APP_LOGO" class="croppie" default="<?=url("");?>uploads/app/{{ env('APP_LOGO'); }}" crop-width="541" crop-height="152" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>System favicon/icon </label>
                                        <input type="file" name="APP_ICON" class="croppie" default="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}" crop-width="152" crop-height="152" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Login Name</label>
                                        <input type="text" class="form-control" name="MAIL_USERNAME" placeholder="SMTP Username" value="{{ env('MAIL_USERNAME'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Sender/From Address</label>
                                        <input type="email" class="form-control" name="MAIL_FROM" placeholder="SMTP Sender" value="{{ (env('MAIL_FROM') != '' ? env('MAIL_FROM') : env('MAIL_USERNAME')); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Host</label>
                                        <input type="text" class="form-control" placeholder="SMTP Host" name="SMTP_HOST" value="{{ env('SMTP_HOST'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Port</label>
                                        <input type="text" class="form-control" placeholder="SMTP Port" name="SMTP_PORT" value="{{ env('SMTP_PORT'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Password</label>
                                        <input type="password" class="form-control" placeholder="SMTP Password" name="SMTP_PASSWORD" value="{{ env('SMTP_PASSWORD'); }}" autocomplete="false" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>SMTP Encryption</label>
                                        <input type="text" class="form-control" placeholder="SMTP Encryption" name="MAIL_ENCRYPTION" value="{{ env('MAIL_ENCRYPTION'); }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('SMTP_AUTH') == "Enabled" )
                                        <input type="checkbox" class="switch" name="SMTP_AUTH" value="true" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="SMTP_AUTH" value="true" />
                                        @endif
                                        <label>SMTP Authenticate.</label>
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Google-->
                            <!--<h5>Google Settings</h5>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Google Client ID</label>
                                        <input type="text" class="form-control" placeholder="Google Client ID" name="GOOGLE_CLIENT_ID" value="{{ env('GOOGLE_CLIENT_ID'); }}" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Google API key</label>
                                        <input type="text" class="form-control" placeholder="Google API key" name="GOOGLE_API_KEY" value="{{ env('GOOGLE_API_KEY'); }}">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Dropbox-->
                            <!--<h5>Dropbox Settings</h5>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Dropbox App key</label>
                                        <input type="text" class="form-control" placeholder="Dropbox App key" name="DROPBOX_APP_KEY" value="{{ env('DROPBOX_APP_KEY'); }}">
                                    </div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <!--Cloud Convert-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('ALLOW_NON_PDF') == "Enabled" )
                                        <input type="checkbox" class="switch" name="ALLOW_NON_PDF" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="ALLOW_NON_PDF" value="Enabled" />
                                        @endif
                                        <label>Allow users to upload Word, Excel and Power Point. </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ( env('USE_CLOUD_CONVERT') == "Enabled" )
                                        <input type="checkbox" class="switch" name="USE_CLOUD_CONVERT" value="Enabled" checked />
                                        @else
                                        <input type="checkbox" class="switch" name="USE_CLOUD_CONVERT" value="Enabled" />
                                        @endif
                                        <label>Use Cloud Convert for converting files to PDF </label><span class="text-muted text-xs"> Required for file conversion to PDF.</span>
                                    </div>
                                </div>
                            </div>
                            @if ( env('USE_CLOUD_CONVERT') == "Enabled" )
                            <div class="form-group cloud-convert-holder">
                                @else
                                <div class="form-group cloud-convert-holder" style="display: none;">
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Cloud Convert API key</label>
                                            <input type="text" class="form-control" placeholder="Cloud Convert App key" name="CLOUDCONVERT_APP_KEY" value="{{ env('CLOUDCONVERT_APP_KEY'); }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="divider"></div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ( env('PKI_STATUS') == "Enabled" )
                                            <input type="checkbox" class="switch" name="PKI_STATUS" value="Enabled" checked />
                                            @else
                                            <input type="checkbox" class="switch" name="PKI_STATUS" value="Enabled" />
                                            @endif
                                            <label>Activate PKI digital signature.</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ( env('CERTIFICATE_DOWNLOAD') == "Enabled" )
                                            <input type="checkbox" class="switch" name="CERTIFICATE_DOWNLOAD" value="Enabled" checked />
                                            @else
                                            <input type="checkbox" class="switch" name="CERTIFICATE_DOWNLOAD" value="Enabled" />
                                            @endif
                                            <label>Allow users to download p12 certificate</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ( env('NEW_ACCOUNTS') == "Enabled" )
                                            <input type="checkbox" class="switch" name="NEW_ACCOUNTS" value="Enabled" checked />
                                            @else
                                            <input type="checkbox" class="switch" name="NEW_ACCOUNTS" value="Enabled" />
                                            @endif
                                            <label>Allow new users & business to sign up</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ( env('SHOW_SAAS') == "Enabled" )
                                            <input type="checkbox" class="switch" name="SHOW_SAAS" value="Enabled" checked />
                                            @else
                                            <input type="checkbox" class="switch" name="SHOW_SAAS" value="Enabled" />
                                            @endif
                                            <label>Show Saas menu</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            @if ( env('GUEST_SIGNING') == "Enabled" )
                                            <input type="checkbox" class="switch" name="GUEST_SIGNING" value="Enabled" checked />
                                            @else
                                            <input type="checkbox" class="switch" name="GUEST_SIGNING" value="Enabled" />
                                            @endif
                                            <label>Allow guest Signing</label>
                                        </div>
                                    </div>
                                </div>
                                @if ( env('GUEST_SIGNING') == "Enabled" )
                                <div class="form-group force-guest-sign">
                                    @else
                                    <div class="form-group force-guest-sign" style="display:none;">
                                        @endif
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if ( env('FORCE_GUEST_SIGNING') == "Enabled" )
                                                <input type="checkbox" class="switch" name="FORCE_GUEST_SIGNING" value="Enabled" checked />
                                                @else
                                                <input type="checkbox" class="switch" name="FORCE_GUEST_SIGNING" value="Enabled" />
                                                @endif
                                                <label>Send requests to guest Signing</label><br><span class="text-muted text-xs"> Signing requests will be sent with guest signing link and no login required.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @if ( env('REGISTER_IP_ADDRESS_IN_HISTORY') == "Enabled" )
                                                <input type="checkbox" class="switch" name="REGISTER_IP_ADDRESS_IN_HISTORY" value="Enabled" checked />
                                                @else
                                                <input type="checkbox" class="switch" name="REGISTER_IP_ADDRESS_IN_HISTORY" value="Enabled" />
                                                @endif
                                                <label>Record IP address in history</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider"></div>
                                    <h5>Business Accounts</h5>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Disk Limit (MBs)</label>
                                                <input type="number" class="form-control" name="BUSINESS_DISK_LIMIT" placeholder="Disk Limit (MBs)" value="{{ env('BUSINESS_DISK_LIMIT'); }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>File Limit</label>
                                                <input type="number" class="form-control" name="BUSINESS_FILE_LIMIT" placeholder="File Limit" value="{{ env('BUSINESS_FILE_LIMIT'); }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Team/Staff Limit </label>
                                                <input type="number" class="form-control" name="TEAM_LIMIT" placeholder="Team/Staff Limit" value="{{ env('TEAM_LIMIT'); }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <h5>Personal Accounts</h5>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Disk Limit (MBs)</label>
                                                <input type="number" class="form-control" name="PERSONAL_DISK_LIMIT" placeholder="Disk Limit (MBs)" value="{{ env('PERSONAL_DISK_LIMIT'); }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>File Limit</label>
                                                <input type="number" class="form-control" name="PERSONAL_FILE_LIMIT" placeholder="File Limit" value="{{ env('PERSONAL_FILE_LIMIT'); }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <button class="btn btn-primary" type="submit">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>

                        </form>

                    </div>
                    <!-- system end -->
                    @endif
                    <!-- Signature start -->
                    <div id="signature" class="tab-pane fade">
                        <h4>Signature</h4>
                        <p>This is your signature, update any time.</p>
                        <div class="row">
                            <div class="col-md-12">
                             <div class="signature-holder">
                                <div class="signature-body">
                                    @if ( empty( $user->signature ) )
                                    <img src="<?=url("");?>uploads/signatures/demo.png" class="img-responsive">
                                    @else
                                    <img src="<?=url("");?>uploads/signatures/{{ $user->signature }}" class="img-responsive">
                                    @endif
                                </div>
                            </div>
                            <div class="signature-btn-holder">
                                <button class="btn btn-primary btn-block"  data-toggle="modal" data-target="#updateSignature" data-target="#createFolder" data-backdrop="static" data-keyboard="false"> Update Signature</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- password end -->
                    <!-- password start -->
                    <div id="password" class="tab-pane fade">
                        <h4>Password</h4>
                        <form class="simcy-form"action="<?=url("Settings@updatepassword");?>" data-parsley-validate="" loader="true" method="POST">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Current password</label>
                                        <input type="password" class="form-control" name="current" required placeholder="Current password">
                                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>New password</label>
                                        <input type="password" class="form-control" name="password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Password is too short!" id="newPassword" placeholder="New password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Confirm password</label>
                                        <input type="password" class="form-control" data-parsley-required="true" data-parsley-equalto="#newPassword" data-parsley-error-message="Passwords don't Match!" placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <!-- password end -->
                </div>
            </div>
        </div>
    </div>


<!-- Upload file Modal -->
<div class="modal fade updateSignature" id="updateSignature" role="dialog">
    <div class="close-modal" data-dismiss="modal">&times;</div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Signature </h4>
            </div>
            <ul class="head-links">
                <li type="capture" class="active"><a data-toggle="tab" href="#text">Text</a></li>
                <li type="upload"><a data-toggle="tab" href="#upload">Upload</a></li>
                <li type="draw"><a data-toggle="tab" href="#edit-draw">Draw</a></li>
            </ul>
            <div class="modal-body">
                <div class="tab-content">
                    <div id="text" class="tab-pane fade in active">
                        <form>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Type your signature</label>
                                        <input type="text" class="form-control signature-input" name=""
                                               placeholder="Type your signature" maxlength="18" value="Your Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Select font</label>
                                        <select class="form-control signature-font" name="">
                                            <option value="Lato">Lato</option>
                                            <option value="Miss Fajardose">Miss Fajardose</option>
                                            <option value="Meie Script">Meie Script</option>
                                            <option value="Petit Formal Script">Petit Formal Script</option>
                                            <option value="Niconne">Niconne</option>
                                            <option value="Rochester">Rochester</option>
                                            <option value="Tangerine">Tangerine</option>
                                            <option value="Great Vibes">Great Vibes</option>
                                            <option value="Berkshire Swash">Berkshire Swash</option>
                                            <option value="Sacramento">Sacramento</option>
                                            <option value="Dr Sugiyama">Dr Sugiyama</option>
                                            <option value="League Script">League Script</option>
                                            <option value="Courgette">Courgette</option>
                                            <option value="Pacifico">Pacifico</option>
                                            <option value="Cookie">Cookie</option>
                                            <option value="Grand Hotel">Grand Hotel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Weight</label>
                                        <select class="form-control signature-weight" name="">
                                            <option value="normal">Regular</option>
                                            <option value="bold">Bold</option>
                                            <option value="lighter">Lighter</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Color</label>
                                        <input class="form-control signature-color jscolor { valueElement:null,borderRadius:'1px', borderColor:'#e6eaee',value:'000000',zIndex:'99999', onFineChange:'updateSignatureColor(this)'}"
                                               readonly="">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Style</label>
                                        <select class="form-control signature-style" name="">
                                            <option value="normal">Regular</option>
                                            <option value="italic">Italic</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="divider"></div>
                        <h4 class="text-center">Preview</h4>
                        <div class="text-signature-preview">
                            <div class="text-signature" id="text-signature"
                                 style="color: #000000;text-align:center;position: relative;">Your Name
                            </div>
                        </div>

                    </div>
                    <div id="upload" class="tab-pane fade">
                        <p>Upload your signature if you already have it.</p>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Upload your signature</label>
                                    <input type="file" name="signatureupload" class="croppie" crop-width="400"
                                           crop-height="150">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="edit-draw" class="tab-pane fade text-center">
                        <h4>Signature</h4>
                        <p>This is your signature, update any time.</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="signature-holder">
                                    <div class="signature-body">
                                        @if ( empty( $user->signature ) )
                                        <img src="<?= url(""); ?>uploads/signatures/demo.png" class="img-responsive">
                                        @else
                                        <img src="<?= url(""); ?>uploads/signatures/{{ $user->signature }}"
                                             class="img-responsive">
                                        @endif
                                    </div>
                                </div>
                                <div class="signature-btn-holder">
                                    <button type="button" class="btn btn-primary edit-draw" data-toggle="modal" data-target="#editDrawModal" data-backdrop="static" data-keyboard="false">Edit Signature</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-signature">Save Signature</button>
            </div>
        </div>

    </div>
</div>


<!-- update draw modal -->
<div class="modal fade" id="editDrawModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">&times;</div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="signature-pad" class="signature-pad">
                                    <div class="signature-pad--body">
                                        <canvas id="create-signature"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <!--<button type="button" class="button clear" data-action="clear">Clear</button>
                                                <button type="button" class="button" data-action="change-color">Change color</button>
                                                <button type="button" class="button" data-action="undo">Undo</button>-->
                                        <div class="signature-pad--actions" id="controls">
                                            <!--<div class="signature-tool-item">
                                                <button class="jscolor { valueElement:null,borderRadius:'1px', borderColor:'#e6eaee',value:'000000',zIndex:'99999', onFineChange:'changeColorButton(this)'}"></button>
                                            </div>-->
                                            <div class="signature-tool-item">
                                                <div class="tool-icon tool-undo" data-action="undo"></div>
                                            </div>
                                            <div class="signature-tool-item">
                                                <div class="tool-icon tool-erase clear" data-action="clear"></div>
                                            </div>
                                            <div class="signature-tool-item">
                                                <button type="button" class="btn btn-primary save-signature edit-signature"></button>
                                            </div>
                                            <!--<div>
                                                <button type="button" class="button save" data-action="save-png">Save as PNG</button>
                                                <button type="button" class="button save" data-action="save-jpg">Save as JPG</button>
                                                <button type="button" class="button save" data-action="save-svg">Save as SVG</button>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- footer -->
    {{ view("includes/footer"); }}

    <!-- scripts -->
    <script>
        var fullName = "{{ $user->fname }} {{ $user->lname }}",
              saveSignatureUrl = "<?=url("Signature@save");?>",
              auth = true;
    </script>
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <!--<script src="<?/*=url("");*/?>assets/libs/html2canvas/html2canvas.min.js"></script>-->
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/jcanvas.min.js"></script>
    <script src="<?=url("");?>assets/js/dom-to-image.min.js"></script>
    <script src="<?=url("");?>assets/libs/jcanvas/signature.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-validate.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-additional-methods.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/custom.js"></script>
    <script src="<?=url("");?>assets/js/signature-pad/signature_pad.umd.js"></script>
    <script src="<?=url("");?>assets/js/signature-pad/app.js"></script>

</body>

</html>
