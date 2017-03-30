<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <script src="jquery-3.2.0.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script>
            $(document).ready(function () {
                var user = null;
                var conn = null;
                $('#masuk').click(function () {
                    user = $('#username').val();
                    conn = new WebSocket('ws://192.168.1.20:8081');
                    conn.onopen = function (e) {
                        console.log("Connection established!");
                        $(".chat").append("Anda : <small>telah bergabung</small><br>");
                        var data = JSON.stringify({"user": user, "pesan": "telah bergabung"});
                        conn.send(data);
                    };
                    conn.onmessage = function (e) {
                        var msg = JSON.parse(e.data);
                        var username = msg.user;
                        if (msg.type == 'new') {
                            $("#useronline").empty();
                            for(var i =0;i<msg.useronline.length;i++){
                                var userr = msg.useronline[i];
                                if(user != userr[1]){
                                var li = "<li>"+userr[1]+"</li>";
                                $("#useronline").append(li);
                                }
                            }
                        }
                        if(msg.user != user){    
                        $(".chat").append(msg.user + " : <small>" + msg.pesan + 
                                "</small><br>").fadeIn("slow");}
                    };
                    $("#login").css('display', 'none');
                    $("#ruangchat").fadeIn("slow");
                });
                $("#kirim").click(function () {
                    conn.send($("#pesan").val());
                    $(".chat").append("Anda : <small>" + $("#pesan").val() + "</small><br>").fadeIn("slow");
                    $("#pesan").val(' ');
                });
            });

        </script>
    </head>
    <body>
        <div class="container">
            <div class="page-header">
                <h1>Chat <small>sederhana</small></h1>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <h5>User Online</h5>
                    <ul id="useronline">
                    </ul>
                </div>
                <div class="col-md-8">

                    <div id="login">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">@</span>
                                <input type="text" id='username'class="form-control" placeholder="Masukkan Nama Anda"
                                       aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" id="masuk">Masuk</button>
                        </div>
                    </div>
                    <div id='ruangchat' style="display: none">
                        <div class="page-header chat">

                        </div>
                        <div class="form-group">
                            <input type="text" id='pesan'class="form-control" 
                                   placeholder="Apa yang ingin anda katakan ?"
                                   aria-describedby="basic-addon1">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" id="kirim">Kirim</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
