<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Departments | Email Sign Agreement</title>


    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" />

    <!-- Ion icons -->
    <link href="<?=url("");?>assets/fonts/ionicons/css/ionicons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?=url("");?>assets/libs/bootstrap/css/bootstrap.css" rel="stylesheet">
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
    
    <div class="content">
        <div class="page-title">
            <div class="pull-right page-actions lower">
                <button class="btn btn-primary" data-toggle="modal" data-target="#create" data-backdrop="static" data-keyboard="false"><i class="ion-plus-round"></i> New Department</button>
            </div>
            <h3>Departments </h3>
            <p>Department helps you group your team and share files.</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="light-card table-responsive p-b-3em">
                    <table class="table display companies-list" id="data-table">
                        <thead>
                            <tr>
                                <th class=""></th>
                                <th>Department</th>
                                <th>Email</th>
                                <th class="text-center">Disk Usage</th>
                                <th class="text-center">File Usage</th>
                                <th class="text-center">Team</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( count($departments) > 0 )
                            @foreach ( $departments as $index => $department )
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $department['department']->name }}</strong></td>
                                <td><strong>{{ $department['department']->email }}</strong></td>
                                <td class="text-center">{{ round($department['disk'] / 1000) }} MBs</td>
                                <td class="text-center">{{ $department['files'] }} Files </td>
                                <td class="text-center">{{ $department['team'] }} Members </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <span class="company-action dropdown-toggle" data-toggle="dropdown"><i class="ion-ios-more"></i></span>
                                        <ul class="dropdown-menu" role="menu">
                                            <li role="presentation">
                                                <a class="fetch-display-click" data="departmentid:{{ $department['department']->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Department@updateview");?>" holder=".update-holder" modal="#update" href="">Edit</a>
                                                <a class="send-to-server-click"  data="departmentid:{{ $department['department']->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Department@delete");?>" warning-title="Are you sure?" warning-message="This department will be deleted." warning-button="Continue" loader="true" href="">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center">It's empty here</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!--Create User Account-->
    <div class="modal fade" id="create" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Department</h4>
                </div>
                <form class="simcy-form" id="create-department-form" action="<?=url("Department@create");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <p>Fill in customer's details, an email with login details will be sent to user.</p>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Department name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Department name" data-parsley-required="true">
                                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Department Email address</label>
                                    <input type="email" class="form-control" name="email" placeholder="Department Email address" data-parsley-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Select Department Members</label>
                                    <select class="form-control select2" name="members[]" multiple="">
                                        @foreach ( $team as $team )
                                        <option value="{{ $team->id }}">{{ $team->fname }} {{ $team->lname }}</option>
                                        @endif
                                    </select>
                               </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Department</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Update User Modal -->
    <div class="modal fade" id="update" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Department </h4>
                </div>
                <form class="update-holder simcy-form" id="update-department-form" action="<?=url("Department@update");?>" data-parsley-validate="" loader="true" method="POST" enctype="multipart/form-data">
                    <div class="loader-box"><div class="circle-loader"></div></div>
                </form>
            </div>

        </div>
    </div>


    <!-- footer -->
    {{ view("includes/footer"); }}

    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js"></script>
    <script src="<?=url("");?>assets/libs/select2/js/select2.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-validate.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-additional-methods.min.js"></script>
    
    <script>
        $(document).ready(function() {
            @if ( count($departments) > 0 )
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'
                ]
            });
            @endif

            //  select2
            $('.select2').select2({
                placeholder: "Select Department Members"
            });
        });
    </script>
    
    <!-- scripts -->
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    <script src="<?=url("");?>assets/js/custom.js"></script>
</body>

</html>
