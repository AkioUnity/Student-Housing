<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Signer | Email Sign Agreement</title>
    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/libs/select2/css/select2.min.css" rel="stylesheet">
    <link href="<?=url("");?>assets/css/simcify.min.css" rel="stylesheet">
    <!-- Signer CSS -->
    <link href="<?=url("");?>assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- header start -->
    {{ view("includes/header", $data); }}

    <!-- sidebar -->
    {{ view("includes/sidebar", $data); }}

<!--    <div class="content">-->
<!--        <div class="page-title">-->
<!--            <div class="pull-right page-actions">-->
<!--                <a href="--><?//=url("Settings@get");?><!--" class="btn btn-primary-ish hidden-xs"><i class="ion-edit"></i> Settings</a>-->
<!--                <a href="--><?//=url("Document@get");?><!--" class="btn btn-primary"><i class="ion-document-text"></i> Documents</a>-->
<!--            </div>-->
<!--            <h3 class="m-t-5">Dashboard</h3>-->
<!--        </div>-->
<!---->
<!--        <!---->
<!--        Display the error message when logged in user as superadmin-->
<!--        mysql datetime and PHP datetime does not match-->
<!--        -->-->
<!--        @if ( ($user->role == "superadmin") && ( date('Y-m-d H:i') != date('Y-m-d H:i', strtotime($mysqlCurrentDateTime))) )-->
<!--            <div class="alert alert-danger" role="alert">-->
<!--                <b>ERROR: Your MySQL and PHP are configured with different timezone settings.</b><br/>-->
<!--                Your current MySQL date/time is <b>{{ date('F j, Y. H:i', strtotime($mysqlCurrentDateTime)) }}</b> ({{ $mysqlCurrentTimezone }}).<br/>-->
<!--                Your current PHP date/time is <b>{{ date('F j, Y. H:i') }}</b>.</br>-->
<!--                For time conversions to function correctly your MySQL and PHP times must match. For more information please view our <a href="https://simcycreative.com/signer-timezone-configuration/" class="alert-link" target="_blank">support article</a>.-->
<!--            </div>-->
<!--            <br/>-->
<!--        @endif-->
<!---->
<!--        <div class="row">-->
<!--            <!-- Widget knob -->-->
<!--            <div class="col-md-6">-->
<!--                <div class="light-card widget">-->
<!--                    <div class="meter-widget">-->
<!--                        <div id="meter" style="height: 430px"></div>-->
<!--                        <p class="text-center text-muted">File Statistics • Pending Requests<span class="text-success">{{ $pendingRequests }}</span></p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- End widget knob -->-->
<!--            <!-- disk usage -->-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card knob-widget widget">-->
<!--                    <h5>Disk Usage</h5>-->
<!--                    <div class="text-center">-->
<!--                        <div class="knob-holder">-->
<!--                            <input type="text" value="{{ round((($diskUsage / 1000) / $diskLimit) * 100) }}" class="dial" data-thickness=".1" data-width="150" data-linecap="round" data-fgColor="#3DA4FF" readonly>-->
<!--                        </div>-->
<!--                        <div class="knob-widget-info">-->
<!--                            <p class="pull-left text-xs">-->
<!--                                <span class="text-primary"><i class="ion-ios-circle-filled"></i></span>-->
<!--                                <span class="count"> {{ round($diskUsage / 1000) }}MBs </span>-->
<!--                                <span class="text-xs">Used</span>-->
<!--                            </p>-->
<!--                            <p class="pull-right text-xs">-->
<!--                                <span class="text-danger"><i class="ion-ios-circle-filled"></i></span>-->
<!--                                <span class="count"> {{ $diskLimit - round($diskUsage / 1000) }}MBs </span>-->
<!--                                <span class="">Remaining</span>-->
<!--                            </p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- file usage -->-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card knob-widget widget bg-success">-->
<!--                    <h5 class="text-white">File Usage</h5>-->
<!--                    <div class="text-center">-->
<!--                        <div class="knob-holder">-->
<!--                            <input type="text" value="{{ round(($fileUsage / $fileLimit) * 100) }}" class="dial" data-thickness=".1" data-width="150" data-linecap="round" data-fgColor="#008000" readonly>-->
<!--                        </div>-->
<!--                        <div class="knob-widget-info ">-->
<!--                            <p class="pull-left text-xs text-white">-->
<!--                                <span class="text-white"><i class="ion-ios-circle-filled"></i></span>-->
<!--                                <span class="count"> {{ $fileUsage }} </span>-->
<!--                                <span class="text-xs">Used</span>-->
<!--                            </p>-->
<!--                            <p class="pull-right text-xs text-white">-->
<!--                                <span class="text-warning"><i class="ion-ios-circle-filled"></i></span>-->
<!--                                <span class="count"> {{ $fileLimit - $fileUsage }} </span>-->
<!--                                <span class="">Remaining</span>-->
<!--                            </p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- awaiting signing -->-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card folder-counter">-->
<!--                    <div class="widget-icon widget-success">-->
<!--                        <i class="ion-folder"></i>-->
<!--                    </div>-->
<!--                    <h4>{{ $folders }}</h4>-->
<!--                    <p>Folders</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- awaiting signing -->-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card widget-signature">-->
<!--                    @if ( empty($user->signature) )-->
<!--                    <img src="--><?//=url("");?><!--uploads/signatures/demo.png" class="img-responsive">-->
<!--                    @else-->
<!--                    <img src="--><?//=url("");?><!--uploads/signatures/{{ $user->signature }}" class="img-responsive">-->
<!--                    @endif-->
<!--                    <p>Your Signature</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- awaiting signing -->-->
<!--        </div>-->
<!--        @if ( $user->role == "superadmin" )-->
<!--        <!-- admin -->-->
<!--        <div class="row">-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card widget">-->
<!--                    <h5>Account Types</h5>-->
<!--                    <div class="account-types">-->
<!--                        <div class="account-type-single">-->
<!--                            <strong class="pull-right">{{ $businessAccounts }}</strong>-->
<!--                            <p class="text-muted">Business Accounts</p>-->
<!--                            <div class="progress progress-bar-success-alt">-->
<!--                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ round(($businessAccounts / ($personalAccounts + $businessAccounts)) * 100) }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ round(($businessAccounts / ($personalAccounts + $businessAccounts)) * 100) }}%">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="account-type-single">-->
<!--                            <strong class="pull-right">{{ $personalAccounts }}</strong>-->
<!--                            <p class="text-muted">Personal Accounts</p>-->
<!--                            <div class="progress progress-bar-primary-alt">-->
<!--                                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="{{ round(($personalAccounts / ($personalAccounts + $businessAccounts)) * 100) }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ round(($personalAccounts / ($personalAccounts + $businessAccounts)) * 100) }}%">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-3">-->
<!--                <div class="light-card widget">-->
<!--                    <h5>Disk Usage</h5>-->
<!--                    <div class="disk-usage">-->
<!--                        <ol>-->
<!--                            <li><strong>PDF Files </strong><span class="pull-right text-muted">{{ $totalPdf }}</span></li>-->
<!--                            <li><strong>MS Word </strong> <span class="pull-right text-muted">{{ $totalWord }}</span></li>-->
<!--                            <li><strong>MS Excel </strong> <span class="pull-right text-muted">{{ $totalExcel }}</span></li>-->
<!--                            <li><strong>Power Point</strong> <span class="pull-right text-muted">{{ $totalPpt }}</span></li>-->
<!--                        </ol>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-6">-->
<!--                <div class="light-card">-->
<!--                    <div class="row">-->
<!--                        <div class="col-md-4">-->
<!--                            <div class="system-counter">-->
<!--                                <div class="widget-icon widget-success"> <i class="ion-document-text"></i> </div>-->
<!--                                <h4>{{ $systemFiles }}</h4>-->
<!--                                <p>Total Files</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-md-4">-->
<!--                            <div class="system-counter">-->
<!--                                <div class="widget-icon widget-info"> <i class="ion-android-upload"></i> </div>-->
<!--                                <h4>{{ round($systemDisk / 1000) }}</h4>-->
<!--                                <p>Total MBs</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-md-4">-->
<!--                            <div class="system-counter">-->
<!--                                <div class="widget-icon widget-danger"> <i class="ion-ios-person"></i> </div>-->
<!--                                <h4>{{ $systemUsers }}</h4>-->
<!--                                <p>Total Users</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <!-- End team member -->-->
<!--        </div>-->
<!--        @endif-->
<!---->
<!--        <!-- documents -->-->
<!---->
<!--        <div class="page-title documents-page" style="overflow:visible;">-->
<!--            <div class="row">-->
<!--                <div class="col-md-6 col-xs-6">-->
<!--                    <h3>Documents</h3>-->
<!--                    <p class="breadcrumbs text-muted"><span class="home-folder">Home Folder</span></p>-->
<!--                </div>-->
<!--                <div class="col-md-6 col-xs-6 text-right page-actions">-->
<!--                    <a href="--><?//=url("Document@get");?><!--" class="btn btn-primary"><i class="ion-document-text"></i> Documents</a>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="row">-->
<!--            <div class="col-md-12 documents-group-holder">-->
<!--                <div class="documents-filter light-card hidden-xs">-->
<!--                    <div class="light-card-title">-->
<!--                        <h4>Filter</h4>-->
<!--                    </div>-->
<!--                    <div class="documents-filter-form">-->
<!--                        <form>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="status" value="" checked><span class="outer"><span class="inner"></span></span>All</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="status" value="Signed"><span class="outer"><span class="inner"></span></span>Signed</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="status" value="Unsigned"><span class="outer"><span class="inner"></span></span>Un-Signed</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="divider"></div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="type" value="" checked><span class="outer"><span class="inner"></span></span>All</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="type" value="files"><span class="outer"><span class="inner"></span></span>Files</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="type" value="folders"><span class="outer"><span class="inner"></span></span>Folders</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="divider"></div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="extension" value="" checked><span class="outer"><span class="inner"></span></span>All</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="extension" value="pdf"><span class="outer"><span class="inner"></span></span>PDF</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="extension" value="doc"><span class="outer"><span class="inner"></span></span>Word</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="extension" value="xls"><span class="outer"><span class="inner"></span></span>Excel</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="row">-->
<!--                                    <div class="col-md-12">-->
<!--                                        <label class="radio"><input type="radio" name="extension" value="ppt"><span class="outer"><span class="inner"></span></span>Power Point</label>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </form>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="row documents-grid">-->
<!--                    <div class="col-md-12 content-list"><div class="loader-box"><div class="circle-loader"></div></div></div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <!-- footer -->
<!--    {{ view("includes/footer"); }}-->

    <div class="select-option">
        <div class="btn-group btn-group-justified">
            <a href="" action="open" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Open"><i class="ion-ios-eye"></i></a>
            @if ( in_array("delete",json_decode($user->permissions)) )
            <a href="" action="delete" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Delete"><i class="ion-ios-trash"></i></a>
            @endif
            <a href="" action="rename" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Rename"><i class="ion-edit"></i></a>
            @if ( env('DISABLE_SHARE') != "Enabled" )
            <a href="" action="share" class="btn btn-primary" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="Share"><i class="ion-share"></i></a>
            @endif
        </div>
    </div>


    <!-- Rename folder Modal -->
    <div class="modal fade" id="renamefolder" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rename Folder</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@updatefolder");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Change the name of your folder.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>Folder name</label>
                                    <input type="text" class="form-control" name="foldername" placeholder="Folder name" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="folderid">
                                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Rename Folder</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <!-- Rename file Modal -->
    <div class="modal fade" id="renamefile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rename File</h4>
                </div>
                <form class="simcy-form"action="<?=url("Document@updatefile");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Change the name of your file.</p>
                        <div class="form-group">
                             <div class="row">
                                <div class="col-md-12">
                                    <label>File name</label>
                                    <input type="text" class="form-control" name="filename" placeholder="File name" data-parsley-required="true">
                                    <input type="hidden" name="folder" value="1">
                                    <input type="hidden" name="fileid">
                                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Rename File</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <!-- Shared Modal -->
    <div class="modal fade" id="shared" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Information </h4>
                </div>
                <form class="shared-holder simcy-form"action="" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>



    <!-- Share Modal -->
    <div class="modal fade" id="sharefile" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Document Sharing</h4>
                </div>
                <div class="modal-body">
                    <p>Anyone with the link below can view and edit this document.</p>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Sharing link</label>
                                <input type="text" id="foo" class="form-control sharing-link" placeholder="Sharing link" readonly="readonly">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary copy-link" data-clipboard-action="copy" data-clipboard-target="#foo">Copy Link</button>
                </div>
            </div>

        </div>
    </div>


    <!-- folder right click -->
    <div id="folder-menu" class="dropdown clearfix folder-actions">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li><a tabindex="-1" action="open" href="">Open</a>
            </li>
            <li><a tabindex="-1" action="rename" href="">Rename</a>
            </li>
            <li><a tabindex="-1" action="protect" href="">Protect</a>
            </li>
            @if ( $user->role != "user" )
            <li><a tabindex="-1" action="access" href="">Accessibility</a>
            </li>
            @endif
            @if ( in_array("delete",json_decode($user->permissions)) )
            <li class="divider"></li>
            <li><a tabindex="-1" action="delete" href="">Delete</a>
            </li>
            @endif
        </ul>
    </div>
    
    <!--  file right click -->
    <div id="file-menu" class="dropdown clearfix file-actions">
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
            <li><a action="open" href="">Open</a>
            </li>
            <li><a action="rename" href="">Rename</a>
            </li>
            <li><a action="duplicate" href="">Duplicate</a>
            </li>
            @if ( env('DISABLE_SHARE') != "Enabled" )
            <li><a action="share" href="">Share</a>
            </li>
            @endif
            <li><a action="download" href="">Download</a>
            </li>
            @if ( $user->role != "user" )
            <li><a tabindex="-1" action="access" href="">Accessibility</a>
            </li>
            @endif
            @if ( in_array("delete",json_decode($user->permissions)) )
            <li class="divider"></li>
            <li><a action="delete" href="">Delete</a>
            </li>
            @endif
        </ul>
    </div>



    <!-- scripts -->
    <script type="text/javascript">
        var dropboxExtesions = ['.pdf'<?php if(env("ALLOW_NON_PDF") == "Enabled"){ ?>, '.doc', '.docx', '.ppt', '.pptx', '.xls', '.xlsx'<?php } ?>],
              docType = "documents",
              appUrl = "<?=env("APP_URL");?>",
              openFileUrl = "<?=url("Document@open");?>",
              documentsUrl = "<?=url("Document@fetch");?>",
              templatesUrl = "<?=url("Template@fetch");?>",
              relocateDocumentsUrl = "<?=url("Document@relocate");?>",
              duplicateFileUrl = "<?=url("Document@duplicate");?>",
              deleteUrl = "<?=url("Document@delete");?>",
              deleteFileUrl = "<?=url("Document@deletefile");?>",
              deleteFolderUrl = "<?=url("Document@deletefolder");?>",
              folderProtectViewUrl = "<?=url("Document@updatefolderprotectview");?>",
              folderProtectUrl = "<?=url("Document@updatefolderprotect");?>",
              folderAccessViewUrl = "<?=url("Document@updatefolderaccessview");?>",
              folderAccessUrl = "<?=url("Document@updatefolderaccess");?>",
              fileAccessViewUrl = "<?=url("Document@updatefileaccessview");?>",
              fileAccessUrl = "<?=url("Document@updatefileaccess");?>",
              dropboxUrl = "<?=url("Document@dropboximport");?>",
              googledriveimportUrl = "<?=url("Document@googledriveimport");?>",
              allowNonPDF = '<?=env("ALLOW_NON_PDF")?>';
    </script>
    <script src="<?=url("");?>assets/js/jquery-3.2.1.min.js"></script>
    <script src="<?=url("");?>assets/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/libs/clipboard/clipboard.min.js"></script>
    <script src="<?=url("");?>assets/libs/knob/jquery.knob.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/echarts.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-validate.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-additional-methods.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <!--<script src="<?/*=url("");*/?>assets/js/auth.js"></script>-->
    <script src="<?=url("");?>assets/js/files.js"></script>
    <script src="<?=url("");?>assets/js/custom.js"></script>
    <script>
        $(function() {
            $(".dial").knob();
        });
        $('.meter-widget').width($('.col-md-6').width());

        var dom = document.getElementById("meter");
        var myChart = echarts.init(dom);
        var app = {};
        option = null;

        option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            color: ["#f62d51", "#009efb", "#55ce63", "#ffbc34", "#2f3d4a"],
            series: [{
                    name: 'Signed percentage.',
                    type: 'pie',
                    selectedMode: 'single',
                    radius: [0, '30%'],

                    label: {
                        normal: {
                            position: 'inner'
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false,
                            color: "#f62d51",
                            type: "dashed"
                        }
                    },
                    data: [{
                            value: {{ $signed }},
                            name: 'Signed',
                            selected: true
                        },
                        {
                            value: {{ $unsigned }},
                            name: 'Un-Signed'
                        }
                    ]
                },
                {
                    name: 'File Types',
                    type: 'pie',
                    radius: ['40%', '55%'],
                    data: [
                        {
                            value: {{ $myWord }},
                            name: 'Microsoft Word'
                        },
                        {
                            value: {{ $myPpt }},
                            name: 'Power Point'
                        },
                        {
                            value: {{ $myExcel }},
                            name: 'Excel'
                        },
                        {
                            value: {{ $myPdf }},
                            name: 'PDF',
                            selected: true
                        }
                    ]
                }
            ]
        };;
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }

    </script>
</body>

</html>
