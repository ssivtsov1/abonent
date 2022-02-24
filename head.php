<?

require './translate_ua.php';

$col_model = 'col-lg-2 col-md-3 col-sm-3 col-xs-6';
$col_model_d = 'col-lg-4 col-md-6 col-sm-6 col-xs-12';
$col_model_d1 = 'col-lg-4 col-md-6 col-sm-6 col-xs-12 folic';
$col_model_m = 'col-lg-6 col-md-8 col-sm-10 col-xs-12';
$col_model_f = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
$redAst = '<span class="text-danger">*</span>';

function get_navbar($param = NULL) {
  insertStatistic($param);
  switch ($param) {
    case 'client.php':
      $a[1] = 'active';
      break;
    case 'bill.php':
      $a[2] = 'active';
      break;
    case 'pay.php':
      $a[3] = 'active';
      break;
    case 'connect.php':
      $a[4] = 'active';
      break;
    case 'limit.php':
      $a[5] = 'active';
      break;
    case 'indication.php':
      $a[6] = 'active';
      break;
    case 'sub_indication.php':
      $a[7] = 'active';
      break;
    case 'operator.php':
      $a[8] = 'active';
      break;
    case 'stat.php':
      $a[9] = 'active';
      break;
    case 'pass.php':
      $a[10] = 'active';
      break;
    case 'client_edit.php':
      $a[11] = 'active';
      break;
    case 'indication_edit.php':
      $a[12] = 'active';
      break;
    case 'indication_view.php':
      $a[13] = 'active';
      break;
    case 'feedback.php':
      $a[14] = 'active';
      break;
    case 'feedback_view.php':
      $a[15] = 'active';
      break;
    case 'upload.php':
      $a[16] = 'active';
      break;
    case 'download.php':
      $a[17] = 'active';
      break;
    case 'test.php':
      $a[21] = 'active';
      break;
  }

  $SSL = 'https';
  if (empty($_SERVER['HTTPS'])) {
    $SSL = 'http';
  }
  $serverName = $SSL . '://' . filter_input(INPUT_SERVER, 'SERVER_NAME') . ':' . filter_input(INPUT_SERVER, 'SERVER_PORT') . '/abon_cabinet';
  $s = "<nav class='navbar navbar-default'>
  <div class='container-fluid'>
    <ul class='nav navbar-nav'>";


  //$s .= "<li class='$a[7]'><a href='$serverName/sub_indication.php'><span class='glyphicon glyphicon-play'></span> " . translate('sub_indication.php') . "</a></li>";  

  $s .= "<li class='dropdown'>
        <a href='#' data-toggle='dropdown'>" . translate('menu') . " <span class='caret'></span></a>
        <ul class='dropdown-menu'>";

// FOR ADMIN
  if ($_SESSION['admin'] == 1) {
    $s .= "<li class='$a[2]'><a href='$serverName/bill.php'>" . translate('bill.php') . "</a></li>";
    $s .= "<li class='$a[3]'><a href='$serverName/pay.php'>" . translate('pay.php') . "</a></li>";
    $s .= "<li class='$a[5]'><a href='$serverName/limit.php'>" . translate('limit.php') . "</a></li>";
    $s .= "<li class='$a[6]'><a href='$serverName/indication.php'>" . translate('indication.php') . "</a></li>";
    $s .= "<li class='$a[4]'><a href='$serverName/connect.php'>" . translate('connect.php') . "</a></li>";
    $s .= "<li class='$a[12]'><a href='$serverName/indication_edit.php'>" . translate('indication_edit.php') . "</a></li>";
    $s .= "<li class='divider'>-</li>";
    $s .= "<li><a href='$serverName/abon_cabinet.php'>" . translate('abon_cabinet') . "</a></li>";
    $s .= '</ul></li>';
    $s .= "<li class='$a[1]'><a href='$serverName/client.php'>" . translate('client.php') . "</a></li>";
    $s .= "<li class='$a[8]'><a href='$serverName/operator.php'>" . translate('operator.php') . "</a></li>";
    $s .= "<li class='$a[9]'><a href='$serverName/stat.php'>" . translate('stat.php') . "</a></li>";
    $cIndView = getDataById('indication_view.php');
    if ($cIndView > 0) {
      $civStr = " <span class='badge'>$cIndView</span>";
    }
    $s .= "<li class='$a[13]'><a href='$serverName/indication_view.php'>" . translate('indication_view.php') . "$civStr</a></li>";
    // $s .= "<li class='$a[15]'><a href='$serverName/feedback_view.php'>" . translate('feedback.php') . "</a></li>";
    $s .= "<li class='$a[16]'><a href='$serverName/upload.php'>" . translate('upload.php') . "</a></li>";
    $s .= "<li class='$a[17]'><a href='$serverName/download.php'>" . translate('download.php') . "</a></li>";
  }
// FOR CLIENT
  else {
    $s .= "<li class='$a[5]'><a href='$serverName/limit.php'>" . translate('limit.php') . "</a></li>";
    $s .= "<li class='$a[2]'><a href='$serverName/bill.php'>" . translate('bill.php') . "</a></li>";
    $s .= "<li class='$a[6]'><a href='$serverName/indication.php'>" . translate('indication.php') . "</a></li>";
    $s .= "<li class='$a[4]'><a href='$serverName/connect.php'>" . translate('connect.php') . "</a></li>";
    $s .= "<li class='$a[10]'><a href='$serverName/pass.php'>" . translate('pass.php') . "</a></li>";
    $s .= '</ul></li>';
    $s .= "<li class='$a[1]'><a href='$serverName/client.php'>" . translate('client.php') . "</a></li>";
    $s .= "<li class='$a[3]'><a href='$serverName/pay.php'>" . translate('pay.php') . "</a></li>";
    $s .= "<li class='$a[12]'><a href='$serverName/indication_edit.php'>" . translate('indication_edit.php') . "</a></li>";
    $s .= "<li class='$a[17]'><a href='$serverName/download.php'>" . translate('download.php') . "</a></li>";
  }
//


  $s .= "</ul><ul class='nav navbar-nav navbar-right'>";

  if ($_SESSION['admin'] == 1) {
    $s .= '<form method="POST" action="' . $serverName . '/a.php?action=1" class="navbar-form navbar-left" style="margin-top: 5px; margin-bottom: 0px;">
        <div class="form-group">
          <input type="text" style="width: 70px;" autocomplete="off" class="form-control input-sm" name="personal_code" value="' . $_SESSION['login'] . '">
        </div>
        <button type="submit" class="btn btn-default btn-sm" title="srv: ' . $_SESSION['serverString'] . '
id: ' . $_SESSION['id'] . '">' . translate('personal_code') . '</button>
      </form>';
  }
//  else {
  // $s .= "<li class='$a[14]'><a href='$serverName/feedback.php'>" . translate('feedback.php') . "</a></li>";
//  }


  $s .= "<li><a href='$serverName/'>" . translate('exit') . "</a></li>";
  $s .= "</ul></div></nav>";
  echo $s;
}

function get_head($param = NULL) {
  $s = '
    <link href="./css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link href="./css/bootstrap-theme.css" rel="stylesheet" type="text/css"/>
    <link href="./css/personal.css" rel="stylesheet" type="text/css"/>
    <script src="./js/jquery-2.1.4.min.js" type="text/javascript"></script>
    <script src="./js/bootstrap.min.js" type="text/javascript"></script>
';
  switch ($param) {
    case 'date':
      $s .= '
    <script src="./js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="./js/jquery.ui.datepicker-ua.js" type="text/javascript"></script>
    <link href="./css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="./css/jquery-ui.structure.css" rel="stylesheet" type="text/css"/>
    <link href="./css/jquery-ui.theme.css" rel="stylesheet" type="text/css"/>
    <script>
      $(function () {
        $(".date").datepicker({
          dateFormat: "dd.mm.yy",
          changeMonth: true,
          changeYear: true,
          maxDate: 0,
          showAnim: "slideDown",
          showButtonPanel: true
        });
      });
    </script>
';
      break;
  }
  echo $s;
}
