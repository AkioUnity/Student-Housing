<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create Digital signatures and Sign PDF documents online.">
    <meta name="author" content="Simcy Creative">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=url("");?>uploads/app/{{ env('APP_ICON'); }}">
    <title>Signing requests | Email Sign Agreement</title>

    <!--datatables-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" />

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
            <h3>Signing requests</h3>
            <p>All pending, complete and declined signing requests</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li><a href="<?=url("");?>requests/" @if (!isset($_GET['view']))  class="active" @endif>Sent</a></li>
                    <li><a href="<?=url("");?>requests/?view=recd"@if (isset($_GET['view']) && $_GET['view'] == "recd")  class="active" @endif>Received</a></li>
                </ul>
                <div class="light-card table-responsive p-b-3em">
                    <table id="signer-datatable" class="table display companies-list">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center w-70">Image</th>
                                <th>Name</th>
                                <th>File name</th>
                                <th>Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center w-70">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ( count($requests) > 0 )
                            @foreach ( $requests as $index => $request )
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if ( is_object($request['receiver']) && !empty($request['receiver']->avatar) )
                                    <img src="<?=url("")?>uploads/avatar/{{ $request['receiver']->avatar }}" class="img-responsive img-circle table-avatar">
                                    @else
                                    <img src="<?=url("")?>assets/images/avatar.png" class="img-responsive table-avatar">
                                    @endif
                                </td>
                                <td>
                                    @if ( is_object($request['receiver']) )
                                    <strong>{{ $request['receiver']->fname }} {{ $request['receiver']->lname }}</strong>
                                    @else
                                    <strong>{{ $request['data']->email }}</strong>
                                    @endif
                                    <br>Sent by: {{ $request['sender']->fname }} {{ $request['sender']->lname }}</td>
                                <td>
                                @if (!isset($_GET['view']))
                                <a href="{{ url('Document@open').$request['file']->document_key }}">{{ $request['file']->name }}</a>
                                @else
                                <a href="{{ url('Document@open').$request['file']->document_key."?signingKey=".$request['data']->signing_key }}">{{ $request['file']->name }}</a>
                                @endif
                                </td>
                                <td><strong>Requested: </strong>{{ setToUserTimezone($request['data']->send_time,$user->timezone) }}                                   @if ( $request['data']->status != "Pending" )
                                    <br><strong>Updated: </strong>{{ setToUserTimezone($request['data']->update_time,$user->timezone) }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ( $request['data']->status == "Signed" )
                                    <span class="label label-success">Signed</span>
                                    @elseif($request['data']->status == "Declined")
                                    <span class="label label-danger">Declined</span>
                                    @elseif($request['data']->status == "Cancelled")
                                    <span class="label label-warning">Cancelled</span>
                                    @else
                                    <span class="label label-info">Pending</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="dropdown">
                                        <span class="company-action dropdown-toggle" data-toggle="dropdown"><i class="ion-ios-more"></i></span>
                                        <ul class="dropdown-menu" role="menu">
                                            <li role="presentation">
                                                @if (!isset($_GET['view']))
                                                    @if ( $request['data']->status == "Pending" )
                                                    <a class="request-remind" data-id="{{ $request['data']->id }}" href="">Remind</a>
                                                    <a class="send-to-server-click"  data="requestid:{{ $request['data']->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Request@cancel");?>" warning-title="Are you sure?" warning-message="This request will be canceled." warning-button="Cancel Now" loader="true" href="">Cancel</a>
                                                    @endif
                                                    <a class="send-to-server-click"  data="requestid:{{ $request['data']->id }}|csrf-token:{{ csrf_token() }}" url="<?=url("Request@delete");?>" warning-title="Are you sure?" warning-message="This request will be deleted parmanently." warning-button="Delete Now" loader="true" href="">Delete</a>
                                                @else
                                                    @if ( $request['data']->status == "Pending" )
                                                    <a href="{{ url('Document@open').$request['file']->document_key."?signingKey=".$request['data']->signing_key }}">Sign Now</a>
                                                    @else
                                                    <a href="{{ url('Document@open').$request['file']->document_key."?signingKey=".$request['data']->signing_key }}">View</a>
                                                    @endif 
                                                @endif

                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center">It's empty here</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- remind modal -->
    <div class="modal fade" id="remindRequest" role="dialog">
        <div class="close-modal" data-dismiss="modal">&times;</div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send reminder</h4>
                </div>
                <form class="simcy-form"action="<?=url("Request@remind");?>" data-parsley-validate="" loader="true" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Message to receiver</label>
                                    <input type="hidden" name="requestid">
                                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                                    <textarea class="form-control" name="message" rows="8" required>Hello there,

I hope you are doing well.
I am writing to remind you about the signing request I had sent earlier.

Thank you
{{ env("APP_NAME"); }} Team.
</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Reminder</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- footer -->
    {{ view("includes/footer"); }}

    <!-- scripts -->
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
    <!--datatables-->
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js"></script>
    <!--libraries-->
    <script src="<?=url("");?>assets/libs/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=url("");?>assets/js//jquery.slimscroll.min.js"></script>
    <script src="<?=url("");?>assets/js/simcify.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-validate.min.js"></script>
    <script src="<?=url("");?>assets/js/jquery-additional-methods.min.js"></script>

    <!-- custom scripts -->
    <script src="<?=url("");?>assets/js/app.js"></script>
    @if ( count($requests) > 0 )
    <script>
        $(document).ready(function() {
            $('#signer-datatable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5'
                ]
            });
        });
    </script>
    @endif
    <script src="<?=url("");?>assets/js/custom.js"></script>
</body>

</html>
