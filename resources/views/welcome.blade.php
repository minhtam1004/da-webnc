<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<head>
    <title>Internet Banking</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <hr>
    <div class="container bootstrap snippet">
        <div class="row">
            <div class="col-sm-10">
                <div class="text-center">
                    <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
                    <!-- <input type="file" class="text-center center-block file-upload"> -->
                </div>
                </hr><br>
                <div style="text-align: center">
                    <h1>Account name</h1>
                    <h3>Excess</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home">Profile</a></li>
                    <li><a data-toggle="tab" href="#messages">Transfers</a></li>
                    <li><a data-toggle="tab" href="#messages">Transaction History</a></li>
                    <li><a data-toggle="tab" href="#messages">Debt List</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="home">
                        <hr>
                        <form class="form" action="##" method="post" id="registrationForm">
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="first_name">
                                        <h4>Account number</h4>
                                    </label>
                                    <input type="text" class="form-control" name="first_name" id="first_name">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="last_name">
                                        <h4>Full name</h4>
                                    </label>
                                    <input type="text" class="form-control" name="last_name" id="last_name">
                                </div>
                            </div>

                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="phone">
                                        <h4>Excess</h4>
                                    </label>
                                    <input type="text" class="form-control" name="phone" id="phone">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="mobile">
                                        <h4>Date of birth</h4>
                                    </label>
                                    <input type="text" class="form-control" name="mobile" id="mobile">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="email">
                                        <h4>Mobile</h4>
                                    </label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>
                            </div>
                        </form>
                        <hr>
                    </div>
                    <div class="tab-pane" id="messages">

                        <h2></h2>

                        <hr>
                        <form class="form" action="##" method="post" id="registrationForm">
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="first_name">
                                        <h4>Account number</h4>
                                    </label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Account number of the payee">
                                </div>
                            </div>
                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="last_name">
                                        <h4>Amount of money</h4>
                                    </label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Amount of money">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="last_name">
                                        <h4>Password</h4>
                                    </label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter your password">
                                </div>
                            </div>

                            <div class="form-group">

                                <div class="col-xs-6">
                                    <label for="phone">
                                        <h4>Reason</h4>
                                    </label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Reason">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-xs-12">
                                    <br>
                                    <button type="button" type="submit" class="btn btn-success">Confirm</button>
                                    <button type="button" type="reset" class="btn btn-danger">Reset</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>