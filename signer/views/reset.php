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

    <div class="login-card">
        <img src="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}" class="img-responsive">
        <div class="reset-password">
            <h5 class="mb-30">Enter your new password.</h5>

            <form class="text-left simcy-form" action="<?=url("Auth@reset");?>" method="POST" data-parsley-validate="" loader="true">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>New Password</label>
                            <input type="Password" class="form-control" name="password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Password is too short!" id="new-password" placeholder="New Password">
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="token" value="{{ $token }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Confirm Password</label>
                            <input type="Password" class="form-control" data-parsley-required="true" data-parsley-equalto="#new-password" data-parsley-error-message="Passwords don't Match!" placeholder="Confirm Password">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="pull-left m-t-5"><a href="<?=url("Auth@get");?>">Sign In?</a></p>
                            <button class="btn btn-primary pull-right" type="submit" name="reset">Reset password</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="copyright">
            <p class="text-center"><?=date("Y")?> &copy; <?=env("APP_NAME")?> | All Rights Reserved.</p>
        </div>
    </div>

    <!-- scripts -->
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
</body>

</html>
