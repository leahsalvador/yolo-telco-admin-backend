<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
    <!-- This is a wide open CSP declaration. To lock this down for production, see below. -->
    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' gap:; style-src 'self' 'unsafe-inline'; media-src *" />

     <!-- Bootstrap Core CSS -->
     <link href="/telco/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

     <!-- MetisMenu CSS -->
     <link href="/telco/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
 
     <!-- DataTables CSS -->
     <link href="/telco/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
 
     <!-- DataTables Responsive CSS -->
     <link href="/telco/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">
 
     <!-- Custom CSS -->
     <link href="/telco/dist/css/sb-admin-2.css" rel="stylesheet">
 
     <!-- Custom Fonts -->
     <link href="/telco/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- <link rel="stylesheet" type="text/css" href="css/index.css" /> -->

    <title>Teleco</title>
    <style>
        .logout{
            text-align: right;
        }
    </style>

</head>


<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
           
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li class="logout"><a href="/login/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                        <input type="text" id="nameSearch" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                            <button class="btn btn-default" id="search" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                        </div>
                        <!-- /input-group -->
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-dashboard fa-fw"></i> Users</a>
                    </li>
                   
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>



    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Users</h1>
            </div>
            <!-- /.col-lg-12 -->
            
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_user">Add User</button>
           </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-users">
                            <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Address</th>
            <th>phone</th>
            <th>E-mail</th>
            <th>Date</th>
          
      </tr>
  </thead>
  <tbody>
  </tbody>
  
                        </table>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->


    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->


        
        
<div class="modal fade sign_up_modal" id="add_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style=" background-color: #0981b5;border-radius: 6px 6px 0 0;color:#fff;">
          <button type="button" data-dismiss="modal" class="close" data-number="2" aria-label="Close">
            <span aria-hidden="true" style="color:#fff;">&times;</span>
          </button>
          <h4 class="modal-title" id="exampleModalLabel">Add User</h4>
        </div>
        <div class="modal-body">
          <form method="post" id="add_form">
                        <div class="form-group">
                          <label for="name" class="col-form-label">First Name:</label>
                          <input type="text" class="form-control" id="first_name" name="first_name">
                        </div>

                        <div class="form-group">
                          <label for="name" class="col-form-label">Last Name:</label>
                          <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>

                        <div class="form-group">
                          <label for="name" class="col-form-label">Address:</label>
                          <input type="text" class="form-control" id="address" name="address">
                        </div>

                        <div class="form-group">
                          <label for="email" class="col-form-label">Phone:</label>
                          <input type="number" class="form-control" name="phone" id="phone">
                        </div>

                        <div class="form-group">
                          <label for="email" class="col-form-label">Email:</label>
                          <input type="email" class="form-control" name="email" id="email"  type="email" autofocus>
                        </div>

                        <div class="form-group">
                          <label for="password" class="col-form-label">Password:</label>
                          <input type="password" class="form-control" id="password" name="password">
                        </div>

                        <div class="form-group">
                            <input type="hidden" class="form-control" id="date" name="date" value="">
                        </div>
                       
                        <input type="hidden"  name="user_group" id="user_group" class="form-control" value="1">
                        

                        
                    </div>
                    <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal" name="close" id="close">Close</button>
                          <input type="button" id="register_user" class="btn btn-primary" name="register_user" value="Submit">
                    </div>

      </div>
    </div>
  </div> 


<!-- jQuery -->
<script src="/telco/vendor/jquery/jquery.min.js"></script>
<script src="/telco/vendor/sweetalert/sweetalert2.all.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="/telco/vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="/telco/vendor/metisMenu/metisMenu.min.js"></script>

<!-- DataTables JavaScript -->
<script src="/telco/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="/telco/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="/telco/vendor/datatables-responsive/dataTables.responsive.js"></script>

<!-- Custom Theme JavaScript -->
<script src="/telco/dist/js/sb-admin-2.js"></script>


    <!-- <script type="text/javascript" src="cordova.js"></script>
    <script type="text/javascript" src="js/index.js"></script> -->
    <script>

         $(document).ready(function(){

            $('#dataTables-users').DataTable({
                 "ajax" : "/telco/authentication/json_users",
                 "columns" : [
                     { data : "user_id"},
                     { data : "first_name"},
                     { data : "last_name"},
                     { data : "address"},
                     { data : "phone"},
                     { data : "email"},
                     { data : "date_created"},
                    
                 ],responsive:true, "order": [[ 0, "desc" ]]
            });

            $('#register_user').click(function(){

                var first_name = $('#first_name').val();
                var last_name = $('#last_name').val();
                var address = $('#address').val();
                var phone = $('#phone').val();
                var email = $('#email').val();
                var password = $('#password').val();
                var date = new Date();

   
                $.ajax({
                        url:"/telco/authentication/register",
                        method:'POST',
                        data:{
                                first_name:first_name,
                                last_name:last_name,
                                address:address,
                                phone:phone,
                                email:email,
                                password:password,
                                date:date
                            },success:function(data)
                            {
                                swal(
                                    'User Updated!',
                                    'User Successfully Updated!',
                                    'success'
                                ).then(function(){
                                        $('#editUserModal').modal('hide');
                                        location.reload();
                                }); 
                            }      
                           
                });

            });//$('#register) closing ------------------


         });// document ready closing
    </script>

</body>

</html>