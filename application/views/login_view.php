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
           .logo {
                padding: 0;
                margin:40px auto;
                width: 250px;
                height: 60px;
                font-family: "MS Gothic";
            } 
    </style>

</head>

<body>

    <div class="app">
        <div id="deviceready" class="blink">
            <p class="event listening"></p>
            <p class="event received"></p>
        </div>
            
        <div class="logo"><h3><center><b style="color:#0981b5;">YOLO-TELCO</b></center></h3>
        <h4><center>Always Safe!</h4></center>
        </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div style="margin-top:0;" class="login-panel panel panel-default">
                    <div class="panel-heading" style="background-color:#0981b5;color:white;">
                        <h4 class="panel-title">Please Sign In</h4>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="email" id="email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" id="password" type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="button" name="login" id="login" style="background-color:#0981b5;border: #0981b5;" class="btn btn-lg btn-success btn-block">Login</button>
                                <div class="modal-footer">
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script type="text/javascript">
        // app.initialize();

      $(document).ready(function(){
        $('#login').click(function(){
          
          var email = $('#email').val();
          var password = $('#password').val();

          // alert(email);
          // alert(password);

                $.ajax({
                        url:"/telco/authentication/",
                        method:'POST',
                        data:{
                                email:email,
                                password:password
                            },success:function(data)
                            {
                              if(data == "success"){
                                window.location.href = "/telco/authentication/admin_users_view";
                              }else{

                                swal(
                                    'Error!',
                                    'E-mail or password do not match or account does not exist!',
                                    'error'
                                )
                              }

                            }      
                           
                });

        });
      });
      


    </script>

</body>

</html>
