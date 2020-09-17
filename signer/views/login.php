<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Login | Email Sign Agreement</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">

</head>

<body>

    <div class="login-card mb-30">
        <img src="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}" class="img-responsive">
        @if ( $guest AND env('GUEST_SIGNING') == "Enabled" )
        <a class="singing_as_guest btn btn-block btn-success m-t-50" href="{{ $signingLink }}">Sign as a Guest</a>
        @endif
        <div class="sign-in">
            <h5 class="mb-30">Sign in to your account </h5>
            <form class="text-left simcy-form" action="<?=url("Auth@signin");?>" data-parsley-validate="" loader="true" method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Email address</label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" required>
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Password</label>
                            <input type="Password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a href="" target="forgot-password">Forgot password?</a></p>
                            <button class="btn btn-primary pull-right" type="submit" name="login">Sign In</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @if ( env('NEW_ACCOUNTS') == "Enabled" ) 
        <div class="sign-up" style="display: none;">
            <h5 class="mb-30">Create a free account</h5>

            <form class="text-left simcy-form" id="register-form" action="<?=url("Auth@signup");?>" data-parsley-validate="" loader="true" method="POST">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>First name</label>
                            <input type="text" class="form-control" name="fname" placeholder="First name" required>
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                        </div>
                        <div class="col-md-6">
                            <label>Last name</label>
                            <input type="text" class="form-control" name="lname" placeholder="Last name" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label><input type="checkbox" class="switch business-account" name="business" value="1" /> This is a business account </label>
                        </div>
                    </div>
                </div>
                <div class="form-group business-name" style="display:none">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="company" placeholder="Company Name">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Email address</label>
                            <input type="email" class="form-control" name="email" placeholder="Email address" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>New Password</label>
                            <input type="Password" class="form-control" name="password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Password is too short!" id="password" placeholder="New Password">
                        </div>
                        <div class="col-md-6">
                            <label>Confirm Password</label>
                            <input type="Password" class="form-control" data-parsley-required="true" data-parsley-equalto="#password" data-parsley-error-message="Passwords don't Match!" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a href="" target="sign-in">Sign In?</a></p>
                            <button class="btn btn-primary pull-right" type="submit">Create account</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
        <div class="forgot-password" style="display: none;">
            <h5 class="mb-30">Forgot password? don't worry, we'll <br>send your a reset link.</h5>
            <form class="text-left simcy-form" action="<?=url("Auth@forgot");?>" method="POST" data-parsley-validate="" loader="true">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Email address</label>
                            <input type="text" class="form-control" name="email" placeholder="Email address" required>
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a href="" target="sign-in">Sign In?</a></p>
                            <button class="btn btn-primary pull-right" type="submit">Send Email</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @if ( env('NEW_ACCOUNTS') == "Enabled" ) 
        <div class="m-t-5">
            <a class="btn btn-block btn-primary-ish m-t-50 sign-up-btn" href="" target="sign-up">Create an account</a>
        </div>
        @endif
        <div class="copyright">
            <p class="text-center"><?=date("Y")?> &copy; <?=env("APP_NAME")?> | All Rights Reserved.</p>
        </div>
    </div>

    <!-- scripts -->
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-validate.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-additional-methods.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/custom.js"></script>
</body>

</html>
