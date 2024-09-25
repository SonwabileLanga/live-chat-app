<?php

/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 5.1.3                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2024 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

if (!file_exists('../include/db.php')) die('[install.php] include/db.php not exist');
require_once '../include/db.php';

/* NO CHANGES FROM HERE */
if (!file_exists('../class/class.jaklic.php')) die('It looks like the boat has been reported as missing.');

// Get the ls DB class
require_once '../class/class.db.php';

// Change for 3.0.3
use JAKWEB\JAKsql;

// Absolute Path
define('DIR_APPLICATION', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');
define('DIR_jakweb', str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/');

// Errors is array
$errors = array();
// Show form
$show_form = true;
// Check if db exists
$check_db = true;
// Fresh installation
$fresh_install = false;
// check if db content exists
$check_db_content = false;
// DB Error Message
$db_error_msg = "";

// MySQL/i connection
if (JAKDB_USER && JAKDB_PASS) {

  $dsn = JAKDB_DBTYPE.':dbname='.JAKDB_NAME.';host='.JAKDB_HOST;

  try {
    $dbh = new PDO($dsn, JAKDB_USER, JAKDB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      $check_db = false;
      $db_error_msg = $e->getMessage();
  }

  try {
    $dbh->query("SELECT title FROM ".JAKDB_PREFIX."departments WHERE id = 1 LIMIT 1");
  } catch (Exception $e) {
    // We got an exception == table not found
    $fresh_install = true;
  }

  if ($check_db) {

    // Database connection
    $jakdb = new JAKsql([
      // required
      'database_type' => JAKDB_DBTYPE,
      'database_name' => JAKDB_NAME,
      'server' => JAKDB_HOST,
      'username' => JAKDB_USER,
      'password' => JAKDB_PASS,
      'charset' => 'utf8',
      'port' => JAKDB_PORT,
      'prefix' => JAKDB_PREFIX,
   
      // [optional] driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
      'option' => [PDO::ATTR_CASE => PDO::CASE_NATURAL]
      ]);

    if ($fresh_install) {

      // Nothing to do, please proceed

    } else {

      $result = $jakdb->has("departments", "title", ["id" => 1]);
      
      if ($result) {
          $check_db_content = true;
          $db_error_msg = "Already installed, please delete the install directory.";
      }

    }

  }
  
}

// Test for the config.php File
if (@file_exists('../config.php')) {
  
  $data_file = '<strong style="color:green">config.php available</strong>';
} else {
  
  $data_file = '<strong style="color:red">config.php not available!</strong>';
}

// Connect to the database

$result_mysqli = '';
$result_mysqlv = '';

if ($check_db && $jakdb && JAKDB_USER && JAKDB_PASS) {

  if (function_exists('mysqli_connect')) {
    $result_mysqli = '<strong style="color:green">MySQLi extension available, perfect!</strong>';
  } else {
    $result_mysqli = '<strong style="color:green">No support for MySQLi, please make sure you have MySQLi support.</strong>';
  }
  
  $mysqlv = $jakdb->info();
  
  if (version_compare($mysqlv["version"], '5.6') < 0) {
    $result_mysqlv = '<strong style="color:red">You need a higher version of MySQL (min. MySQL 5.6)! Any MariaDB is fine!</strong>';
  } else {
    $result_mysqlv = '<strong style="color:green">MySQL Version: '.$mysqlv["version"].'</strong>';
  }
 
    $conn_data = '<strong style="color:green">Database connection available</strong>';
} else {
 
  $conn_data = '<strong style="color:red">Could not connect to the database!</strong>';
}

// Database exist

if ($check_db && $jakdb) {
 
    $data_exist = '<strong style="color:green">Database "'.JAKDB_NAME.'" available ('.JAKDB_NAME.')</strong>';
} else {
 
  $data_exist = '<strong style="color:red">Could not find the database "'.JAKDB_NAME.'"!</strong>';

}

// Test the minimum PHP version
$php_version = PHP_VERSION;
$php_big = '';
if (version_compare($php_version, '8') < 0) {
  $result_php = '<strong style="color:red">You need a higher version of PHP (min. PHP 8)!</strong>';
} else {
  
  if (version_compare($php_version, '8.3.1') > 0) $php_big = '<br><strong style="color:red">The software has not been tested on your php version yet.</strong>';

  // We also give feedback on whether we're running in safe mode
  $result_safe = '<strong style="color:green">PHP Version: '.$php_version.'</strong>';
  if (@ini_get('safe_mode') || strtolower(@ini_get('safe_mode')) == 'on') {
    $result_safe .= ', <strong style="color:red">Safe Mode activated</strong>.';
  } else {
    $result_safe .= '<strong style="color:green">, Safe Mode deactivated.</strong>';
  }
  
  $result_safe .= $php_big;
}

$dircc = DIR_jakweb."/".JAK_CACHE_DIRECTORY;
$writecc = false;
// Now really check
      if (file_exists($dircc) && is_dir($dircc))
      {
        if (@is_writable($dircc))
        {
          $writecc = true;
        }
        $existscc = true;
      }

      @$passedcc['files'] = ($existscc && $passedcc['files']) ? true : false;

      @$existscc = ($existscc) ? '<strong style="color:green">Found folder ('.JAK_CACHE_DIRECTORY.')</strong>' : '<strong style="color:red">Folder not found! ('.JAK_CACHE_DIRECTORY.'), </strong>';
      @$writecc = ($writecc) ? '<strong style="color:green">permission set</strong> ('.JAK_CACHE_DIRECTORY.'), ' : (($existscc) ? '<strong style="color:red">permission not set (check guide)!</strong> ('.JAK_CACHE_DIRECTORY.'), ' : ''); 

// Check if the files directory is writeable      
$dirc = DIR_jakweb."/".JAK_FILES_DIRECTORY;
$writec = false;
// Now really check
      if (file_exists($dirc) && is_dir($dirc))
      {
        if (@is_writable($dirc))
        {
          $writec = true;
        }
        $existsc = true;
      }

      @$passedc['files'] = ($existsc && $passedc['files']) ? true : false;

      @$existsc = ($existsc) ? '<strong style="color:green">Found folder</strong> ('.JAK_FILES_DIRECTORY.')' : '<strong style="color:red">Folder not found!</strong> ('.JAK_FILES_DIRECTORY.')';
      @$writec = ($writec) ? '<strong style="color:green">permission set</strong> ('.JAK_FILES_DIRECTORY.')' : (($existsc) ? '<strong style="color:red">permission not set!</strong> ('.JAK_FILES_DIRECTORY.')' : '');
      
// GD Graphics Support

if (!extension_loaded("gd")) {
  $gd_data = '<strong style="color:orange">GD-Libary not available</strong>';
} else {
  $gd_data = '<strong style="color:green">GD-Libary available</strong>';
}

// Zlip for auto updater
if (!extension_loaded('curl')) {
  $curl_data = '<strong style="color:orange">cURL is not available, some features like SMS and Push notifications do not work.</strong>';
} else {
  $curl_data = '<strong style="color:green">cURL is available, IP/GEO, Push Notifications and SMS should now work!</strong>';
}

// Zlip for auto updater
if (!extension_loaded('zlib') && !ini_get('allow_url_fopen')) {
  $zip_data = '<strong style="color:orange">Zlib-Library not available and/or allow_url_fopen is disabled. Auto Updater will not work.</strong>';
} else {
  $zip_data = '<strong style="color:green">Zlib-Library available and allow_url_fopen is enabled. Sweet, update to future versions possible with a click. Enjoy the integrated Auto Updater.</strong>';
}
function generateRandomCode() {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	$code = '';

	for ($i = 0; $i < 36; $i++) {
		if (in_array($i, [8, 13, 18, 23])) {
			$code .= '-';
		} else {
			$code .= $characters[rand(0, strlen($characters) - 1)];
		}
	}

	return $code;
}

$randomCode = generateRandomCode();
?>
<!DOCTYPE html>
<html lang="en">

<head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LiveChat 3 - Installation Wizard</title>

        <!-- CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../css/fontawesome.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style.css">

        <!-- Favicon and touch icons -->
        <link rel="shortcut icon" href="../img/ico/favicon.png">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../img/ico/144.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../img/ico/114.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../img/ico/72.png">
        <link rel="apple-touch-icon-precomposed" href="../img/ico/57.png">

    </head>
<body>

        <!-- Top content -->
        <div class="top-content">
            <div class="container">
                
                <div class="row justify-content-center">
                    <div class="col-sm-8 text">
                        <h1>LiveChat 3 <strong>Installation</strong> Wizard</h1>
                        <div class="description">
                            <p>
                                This will guide you through the installation for LiveChat 3. Make sure to follow the steps carefully.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-sm-10 form-box">
                      <div class="f1">

                        <h3>Ready for the future?</h3>
                        <p>Install the most advanced Live Chat solution on the market.</p>
                        <div class="f1-steps">
                          <div class="f1-progress">
                              <div class="f1-progress-line" data-now-value="15" data-number-of-steps="4" style="width: 15%;"></div>
                          </div>
                                <div class="f1-step active">
                                    <div class="f1-step-icon"><i class="fa fa-info"></i></div>
                                    <p>info</p>
                                </div>
                          <div class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-check-circle"></i></div>
                            <p>check</p>
                          </div>
                          <div class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-database"></i></div>
                            <p>database</p>
                          </div>
                            <div class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-user"></i></div>
                            <p>account</p>
                          </div>
                        </div>

                        <fieldset>
                          <h4>Ready to rock the boat?</h4>
                          <p>Hang on a minute captain, have you read the manual located in the download package?</p>
                          <p class="text-center"><img src="assets/img/installation_guide.png" alt="installation guide, may the force be with you" class="img-fluid img-thumbnail"></p>
                          <p>If not we recommend to read it carefully it provides a lot of good inside.</p>
                          <h5>Manual? Reading? Kidding me!</h5>
                          <p>Ok, we have made a video for you so you don't get lost...</p>
                          <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/OLgzAUqIrGo?rel=0&amp;showinfo=0" allowfullscreen></iframe>
                          </div>
                          <hr>
                          <p>Btw we also have a great <a href="https://jakweb.ch/faq">FAQ</a> database which will help you with the most common questions.</p>
                          <p>There is a also a search function available in our FAQ which will help you find the answer for your questions quicker.</p>
                          <p class="text-center"><img src="assets/img/faq_search.png" alt="use the force of the search" class="img-fluid img-thumbnail"></p>
                          <hr>
                          <p>We have added a complete manual for the operator panel, chat widget and even a start guide how to create your own chat widget template.</p>
                          <p class="text-center"><img src="assets/img/manual.png" alt="read the manual" class="img-fluid img-thumbnail"></p>
                          <hr>
                          <p>In case you need support, please <a href="https://jakweb.ch/profile">register</a> on our site with your purchase code and head to the support area.</p>
                          <p class="text-center"><img src="assets/img/register_support.png" alt="obi van kenobi will help you" class="img-fluid img-thumbnail"></p>
                          <p>Now all set? Let's start the engine and check if your server is ready.</p>
                          <div class="f1-buttons">
                            <button type="button" class="btn btn-next">Check the Server</button>
                          </div>
                        </fieldset>
                        
                        <fieldset>
                          <h4>Check your Engine</h4>
                          <table class="table table-striped">
                            <tr>
                              <td><strong>What we check</strong></td>
                              <td><strong>Result</strong></td>
                            </tr>
                            <tr>
                              <td>config.php:</td>
                              <td><?php echo $data_file;?></td>
                            </tr>
                            <tr>
                              <td>Database connection</td>
                              <td><?php echo $conn_data;?></td>
                            </tr>
                            <tr>
                              <td>DB Version</td>
                              <td><?php echo $result_mysqlv;?> / <?php echo $result_mysqli;?></td>
                            </tr>
                            <tr>
                              <td>Database</td>
                              <td><?php echo $data_exist?></td>
                            </tr>
                            <tr>
                              <td>PHP Version and Safe Mode:</td>
                              <td><?php echo @$result_php?> <?php echo $result_safe;?></td>
                            </tr>
                            <tr>
                              <td valign="top">Folders:</td>
                              <td><?php echo $writecc.$writec;?></td>
                            </tr>
                            <tr>
                              <td>GD Library Support:</td>
                              <td><?php echo $gd_data;?></td>
                            </tr>
                            <tr>
                              <td>cURL Support:</td>
                              <td><?php echo $curl_data;?></td>
                            </tr>
                            <tr>
                              <td>Zlib Library and allow_url_fopen Support (optional):</td>
                              <td><?php echo $zip_data;?></td>
                            </tr>
                            <tr>
                              <td><strong>Important</strong></td>
                              <td>We cannot check every bit on your server, therefore we cannot guarantee if your server meets the minimum requirements with this basic check.</td>
                            </tr>
                          </table>
                          <div class="f1-buttons">
                            <?php if ($check_db) { ?>
                            <button type="button" class="btn btn-previous">Previous</button>
                            <button type="button" class="btn btn-next">Install the Database</button>
                            <?php } else { ?>
                            <button type="button" class="btn btn-previous" onclick=location=URL>Engine failure, please fix.</button>
                            <?php } ?>
                          </div>
                        </fieldset>

                            <fieldset>
                                <h4>Install Database</h4>
                                <div id="database_installing">
                                  <p class="text-center"><i class="fa fa-database fa-spin fa-5x"></i></p>
                                </div>
                                <div id="database_success" style="display: none">
                                  <div class="alert alert-success">
                                    Batteries fully charged, database installed. Please get on board.
                                  </div>
                                </div>
                                <div id="database_already" style="display: none">
                                  <div class="alert alert-info">
                                    Batteries already full, database has been installed previously. Please get on board.
                                  </div>
                                </div>
                                <div id="database_failure" style="display: none">
                                  <div class="alert alert-danger">
                                    Uh oh, there was a spark in the engine room. Database failure, please try again.
                                  </div>
                                </div>
                                <div class="f1-buttons">
                                    <button type="button" class="btn btn-previous">Previous</button>
                                    <button type="button" class="btn btn-next">Get on Board</button>
                                </div>
                            </fieldset>

                            <fieldset>
                                <div id="form-success" style="display: none">
                                  <div class="alert alert-success">
                                    <p><h4>Welcome on board of LiveChat 3</h4>LC3 has been installed succesfully, please delete the <strong>install</strong> directory and login into your <a href="../<?php echo JAK_OPERATOR_LOC;?>/">operator</a> panel. Enjoy!!!</p>
                                    <p>In case you have any questions or problems, please check our <a href="https://jakweb.ch/faq">FAQ</a> or <a href="https://jakweb.ch/profile">create a support ticket</a> on our website.<br>Your JAKWEB - Team.</p>
                                  </div>
                                </div>
                                <div id="form-elements">
                                  <h4>Create your Account</h4>
                                  <div id="form-error" style="display: none">
                                  <div class="alert alert-danger">
                                    Oh dear, there is some fire on board. Some fields are not correct, please fix it.<br>
                                    <span id="error_msg"></span>
                                  </div>
                                </div>
                                <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" class="form-onboard">
                                  <div class="form-group">
                                      <label for="f1-onumber">Order Number / Purchase Code</label>
                                      <input type="text" name="f1-onumber" value="<?= $randomCode ?>" placeholder="Order Number / Purchase Code" class="f1-onumber form-control" id="f1-onumber" autocomplete="off">
                                  </div>
                                  <div class="form-group">
                                      <label for="f1-envname">Envato Username</label>
                                      <input type="text" name="f1-envname" value="raz0r" placeholder="Envato Usernname..." class="f1-envname form-control" id="f1-envname" autocomplete="off">
                                  </div>
                                  <div class="form-group">
                                      <label for="f1-name">Name</label>
                                      <input type="text" name="f1-name" placeholder="Name..." class="f1-name form-control" id="f1-name">
                                  </div>
                                  <div class="form-group">
                                      <label for="f1-username">Username</label>
                                      <input type="text" name="f1-username" placeholder="Username..." class="f1-username form-control" id="f1-username">
                                  </div>
                                  <div class="form-group">
                                      <label for="f1-email">Email</label>
                                      <input type="text" name="f1-email" placeholder="Email..." class="f1-email form-control" id="f1-email">
                                  </div>
                                  <?php if ($jakhs['hostactive']) { ?>
                                  <div class="form-group">
                                      <label for="f1-password">Hashed Password</label>
                                      <input type="password" name="f1-password" placeholder="Password..." class="f1-password form-control" id="f1-password">
                                  </div>
                                  <div class="form-group">
                                      <label for="f1-timestamp">Live Chat access till (Unix Timestamp) </label>
                                      <input type="password" name="f1-timestamp" placeholder="Unix Timestamp" class="f1-timestamp form-control" id="f1-timestamp">
                                  </div>
                                  <?php } else { ?>
                                  <div class="form-group">
                                      <label for="f1-password">Password</label>
                                      <input type="password" name="f1-password" placeholder="Password..." class="f1-password form-control" id="f1-password">
                                  </div>
                                  <?php } ?>
                                </div>
                                <div class="f1-buttons">
                                    <button type="button" class="btn btn-previous">Previous</button>
                                    <button type="submit" class="btn btn-submit" id="onBoard"><i class="fa fa-paper-plane"></i> Submit</button>
                                </div>
                                </form>
                            </fieldset>
                      
                      </div>
                    </div>
                </div>

                <footer>
  <p>Copyright 2024 by <a href="https://jakweb.ch">Live Chat 3 - JAKWEB</a></p>
</footer>
                    
            </div>
        </div>

        <!-- Javascript -->
        <script src="../js/jquery.js"></script>
        <script src="../js/functions.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/scripts.js"></script>

    </body>

</html>