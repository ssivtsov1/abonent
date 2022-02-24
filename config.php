<?php
// Подключение к базе данных выбранного РЭСа
if (!$_SESSION) {
  session_start();
}

define('OperatorSecret', 'OperatorSecret');
header("Cache-Control: no-store, no-cache, must-revalidate");
date_default_timezone_set('Europe/Kiev');


if (!$_SESSION['id'] && filter_input(INPUT_COOKIE, 'abon_cabinet_id')) {
  $_SESSION['id'] = filter_input(INPUT_COOKIE, 'abon_cabinet_id');
}


if (!$_SESSION['db'] && filter_input(INPUT_COOKIE, 'abon_cabinet_db')) {
  $_SESSION['db'] = filter_input(INPUT_COOKIE, 'abon_cabinet_db');
}

if(empty($_SESSION['db']) || is_null($_SESSION['db']))
    $_SESSION['db'] = 8;

//$_SESSION['db'] = 8;

function switchServerConnect() {
  //echo $_SESSION['db'];

    if(!empty($_SESSION['conn']) && !is_null($_SESSION['conn']))
        pg_close($_SESSION['conn']);

  switch ($_SESSION['db']) {

    case 1:
    //default:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_ap';
      break;
    case 2:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_gv';
      break;
    case 3:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_in';
      break;
    case 4:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_pv';
      break;
    case 5:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_vg';
      break;
    case 6:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_zv';
      break;
    case 7:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_krg';
      break;
    case 8:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_dn';
      break;
    default:
      $pgHost = '192.168.55.1';
      $pgDBName = 'abn_dn';
      break;
  }
  $_SESSION['serverString'] = "$pgDBName@$pgHost";
  $_SESSION['conn'] = pg_connect("host=$pgHost port=5432 dbname=$pgDBName user=local password= connect_timeout=5");
    if ($_SESSION['conn']) {
        //print "Successfully connected to: " . pg_host($_SESSION['conn']) . "<br/>\n";
    } else {
       // print 'Error '.pg_last_error($_SESSION['conn']);
        //exit;
    }
  return $_SESSION['conn'];
}

$link = switchServerConnect();
//echo $link;

if (!$link && basename(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME')) != 'index.php') {
 // echo '<div class="bg-danger text-center">Неможливо підключитися до бази даних.<br>' . pg_last_error() . '</div>';
}

switch ($_SESSION['id']) {
  case '1/-alpha':    // Ivanov

    $_SESSION['admin'] = TRUE;
    break;
  default:
    unset($_SESSION['admin']);
    break;
}
