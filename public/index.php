<?php

require 'header.php';
include 'config.php';

$fullname = $email = $gatewayname = $clientip = $gatewayaddress = $hid = $gatewaymac = $clientif = $redir = $client_zone = "";

$key = "128bcddbf4df3e16147dbb31b3b1b16472a3d2608f10b5407c8cdc352433761f";
$cipher = "AES-256-CBC";
$iv = $_GET['iv'];
$string = $_GET['fas'];

$ndsparamlist = explode(" ", "clientip clientmac client_type gatewayname gatewayurl version hid gatewayaddress gatewaymac authdir originurl clientif admin_email location");

$decrypted = openssl_decrypt(base64_decode($string), $cipher, $key, 0, $iv);
$dec_r = explode(", ", $decrypted);

foreach ($ndsparamlist as $ndsparm) {
  foreach ($dec_r as $dec) {
    @list($name, $value) = explode("=", $dec);
    if ($name == $ndsparm) {
      $$name = $value;
      break;
    }
  }
}

if (isset($gatewayurl)) {
  $gatewayurl = rawurldecode($gatewayurl);
}

$me = $_SERVER['SCRIPT_NAME'];
$host = $_SERVER['HTTP_HOST'];
$fas = $GLOBALS["fas"];
$iv = $GLOBALS["iv"];
$clientip = $GLOBALS["clientip"];
$gatewayname = $GLOBALS["gatewayname"];
$gatewayaddress = $GLOBALS["gatewayaddress"];
$gatewaymac = $GLOBALS["gatewaymac"];
$key = $GLOBALS["key"];
$hid = $GLOBALS["hid"];
$clientif = $GLOBALS["clientif"];
$originurl = $GLOBALS["originurl"];

$_SESSION['authaction'] = "http://$gatewayaddress/opennds_auth/";
$_SESSION['tok'] = hash('sha256', $hid . $key);
$_SESSION['mac'] = $GLOBALS["clientmac"];
$_SESSION['redir'] = $GLOBALS["redir"];
$table_name = $_SERVER['TABLE_DATA'];
$_SESSION["user_type"] = "new";

# Checking DB to see if user exists or not.

mysqli_report(MYSQLI_REPORT_OFF);
$result = mysqli_query($con, "SELECT * FROM `$table_name` WHERE mac='$_SESSION[mac]'");

if ($result->num_rows >= 1) {
  $row = mysqli_fetch_array($result);

  mysqli_close($con);

  $_SESSION["user_type"] = "repeat";
  header("Location: welcome.php");
} else {
  mysqli_close($con);
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>
      <?php echo htmlspecialchars($business_name); ?> WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="assets/styles/bulma.min.css"/>
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.css"/>
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-16x16.png" sizes="16x16"/>
    <link rel="stylesheet" href="assets/styles/style.css"/>
</head>

<body>
<div class="page">

    <div class="head">
        <br>
        <figure id="logo">
            <img src="assets/images/logo.png">
        </figure>
    </div>

    <div class="main">
        <section class="section">
            <div class="container">
                <div id="contact_form" class="content is-size-5 has-text-centered has-text-weight-bold">Enter your details
                </div>
                <form method="post" action="connect.php">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="fname" placeholder="First Name" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="form_font" name="lname" placeholder="Last Name" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="email" id="form_font" name="email" placeholder="Email" required>
                            <span class="icon is-small is-left">
                                <i class="fas fa-envelope"></i>
                            </span>
                        </div>
                    </div>
                    <br>
                    <div class="columns is-centered is-mobile">
                        <div class="control">
                            <label class="checkbox">
                                <input type="checkbox" required>
                                I agree to the <a href="policy.php">Terms of Use</a>
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="buttons is-centered">
                        <button class="button is-link">Connect</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
</body>
</html>