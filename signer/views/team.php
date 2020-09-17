<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Team | Email Sign Agreement</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}
    
    <div class="content">
        <div class="page-title">
            <div class="pull-right page-actions lower">
                @if (count($team) <  env('TEAM_LIMIT') )
                <button class="btn btn-primary" data-toggle="modal" data-target="#create" data-backdrop="static" data-keyboard="false"><i class="ion-plus-round"></i> Add Team</button>
                @else
                <p>Maximum of {{  env('TEAM_LIMIT') }} team/staff created </p>
                @endif
            </div>
            <div>
                <h3>Team</h3><p>You have created {{count($team)}} of your {{ env('TEAM_LIMIT')}} team/staff limit.<p>
            </div>
        </div>
        <div class="row">
            @if ( count($team) > 0 )
            @foreach ( $team as $team )
            <!-- Team member -->
            <div class="col-md-4">
                <div class="light-card team-card-info text-center">
                    @if ( !empty($team->avatar) )
                    <img src="<?=url("")?>uploads/avatar/{{ $team->avatar }}" class="img-circle">
                    @else
                    <img src="<?=url("")?>assets/images/avatar.png" class="">
                    @endif
                    <h4>{{ $team->fname }} {{ $team->lname }}</h4>
                    <p>{{ $team->email }}</p>
                    <div class="team-card-extra">
                        <p class="pull-left">
                            @if ( in_array("delete",json_decode($team->permissions)) )
                            <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Can Delete"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                            @if ( in_array("upload",json_decode($team->permissions)) )
                            <span class="text-success" data-toggle="tooltip" data-placement="top" title="Can Upload"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                            @if ( in_array("edit",json_decode($team->permissions)) )
                            <span class="text-primary" data-toggle="tooltip" data-placement="top" title="Can Sign"><i class="ion-ios-circle-filled"></i></span>
                            @endif
                        </p>
                        <div class="dropup">
                            <span class="team-action dropdown-toggle" data-toggle="dropdown"><i class="ion-ios-more-outline"></i></span>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                <li role="presentation"><a class="fetch-display-click" data="memberid:{{ $team->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Team@updateview");?>" holder=".update-holder" modal="#update" href="">Edit</a></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a class="send-to-server-click"  data="memberid:{{ $team->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Team@delete");?>" warning-title="Are you sure?" warning-message="This member's profile and data will be deleted." warning-button="Continue" loader="true" href="">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End team member -->
            @endforeach
            @else
                <div class="center-notify">
                    <i class="ion-ios-information-outline"></i>
                    <h3>No teams added yet!</h3>
                </div>
            @endif
        </div>
    </div>


    <!--Add Team Member-->
    <div class="modal fade" id="create" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Team Account</h4>
                </div>
                <form class="simcy-form" id="create-team-form" action="<?=url("Team@create");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p>Fill in team member's details, an email with login details will be sent to member.</p>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <label>First name</label>
                                    <input type="text" class="form-control" name="fname" placeholder="First name" data-parsley-required="true">
                                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                </div>
                                <div class="col-md-6">
                                    <label>Last name</label>
                                    <input type="text" class="form-control" name="lname" placeholder="Last name" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Email address</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email address" data-parsley-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label>Phone number</label>
                                    <input type="text" class="form-control" name="phone" placeholder="Phone number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Permissions</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="checkbox" class="switch" name="permissions[]" value="upload" checked readonly/>  Upload Files
                                        </div>
                                        <div class="col-md-3">
                                            <input type="checkbox" class="switch" name="permissions[]" value="edit" checked  /> Edit & Sign
                                        </div>
                                        <div class="col-md-5">
                                            <input type="checkbox" class="switch" name="permissions[]" value="delete" checked  /> Delete Files & folders
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Profile picture</label>
                                    <input type="file" name="avatar" class="croppie" default="<?=url("")?>assets/images/avatar.png" crop-width="200" crop-height="199"  accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Update Team Member Modal -->
    <div class="modal fade" id="update" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Team Account </h4>
                </div>
                <form class="update-holder simcy-form" id="update-team-form" action="<?=url("Team@update");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}


    <!-- Modals -->

    <!-- add team modal -->
    <!-- Modal -->
    <div class="modal fade" id="addTeam" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Team Member</h4>
                </div>
                <form class="add-team-form" action="files/ajaxProcesses.php" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    <div class="modal-body">
                        <div class="alert alert-info alert-dismissable text-center saving" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="ion-loading-c"></i> Saving...
                        </div>
                        <p>Fill in team member's details.</p>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>First name</label>
                                <input type="text" class="form-control" name="fname" placeholder="First name" data-parsley-required="true">
                                <input type="hidden" name="action" value="addTeam">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Last name</label>
                                <input type="text" class="form-control" name="lname" placeholder="Last name" data-parsley-required="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>Email address</label>
                                <input type="email" class="form-control" name="email" placeholder="Email address" data-parsley-required="true">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Phone number</label>
                                <input type="text" class="form-control" name="phone" placeholder="Phone number">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 p-l-o">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" data-parsley-required="true" data-parsley-minlength="6" data-parsley-error-message="Password is too short!">
                            </div>
                            <div class="col-md-6 p-r-o">
                                <label>Confirm password</label>
                                <input type="password" class="form-control" placeholder="Confirm password" data-parsley-required="true" data-parsley-equalto="#password" data-parsley-error-message="Passwords don't Match!">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 p-lr-o">
                                <label>Profile picture <span class="text-muted text-xs">Atleast 200x200</span></label>
                                <input type="file" name="avatar" class="dropify" data-default-file="uploads/avatar/avatar.png" data-min-width="199" data-min-height="199" data-allowed-file-extensions="png jpg">
                            </div>
                        </div>
                        <div class="form-group permissions">
                            <div class="col-md-12 p-l-o">
                                <label>Permissions</label>
                            </div>
                            <div class="col-md-4 p-l-o">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="upload" checked readonly /> Can upload
                            </div>
                            <div class="col-md-4">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="sign" checked /> Can sign
                            </div>
                            <div class="col-md-4 p-r-o">
                                <input type="checkbox" class="js-switch" name="permissions[]" value="delete" checked /> Can delete
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Team Member</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- add team modal -->
    <!-- Modal -->
    <div class="modal fade" id="editTeam" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit account information </h4>
                </div>
                <form class="edit-team-form" action="files/ajaxProcesses.php" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    <div class="modal-body">

                        <p>Update in team member's details.</p>

                        <div class="alert alert-info alert-dismissable text-center saving" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="ion-loading-c"></i> Saving...
                        </div>
                        <div class="center-notify">
                            <i class="ion-loading-c"></i>
                        </div>
                        <div class="edit-fields"></div>

                    </div>
                    <div class="modal-footer" style="display: none;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>

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
