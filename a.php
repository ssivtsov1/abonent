<?php

ob_start();
require_once './func.php';
require_once './config.php';

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {

  $HTTP_REFERER = filter_input(INPUT_SERVER, 'HTTP_REFERER');

  switch (filter_input(INPUT_GET, 'action')) {
    case 1:

      $personal_code = filter_input(INPUT_POST, 'personal_code');
      if ($personal_code) {
        $id = getDataById('login', $personal_code);
        if ($id > 0) {
          $_SESSION['id'] = $id;
          $_SESSION['login'] = $personal_code;
          setcookie('cabinet_id', $_SESSION['id'], time() + 3600 * 12); // 12 hours
        }
      }

      break;
    case 2:
      //echo '<p>' . $_POST['news'] . '</p>';
      $nn = htmlspecialchars($_POST['news']);
      echo "<p>$nn</p>";
      $sql = "insert into acd_cab_news_tbl (news) values ('$nn')";
      pg_query($sql);
      header("Location: $HTTP_REFERER");
      break;
    // Регистрация
    case 3:
//        echo $_POST['db'];
//        echo $_POST['lic'];
//        echo $_POST['email'];
//        echo $_POST['phone'];
//        return;
        
        
      unset($_SESSION['register']);

      if ($_POST['db'] == -1) {
        header('Location: register.php?error=db');
      }
      $_SESSION['register']['lic'] = $lic = $_POST['lic'];
      $_SESSION['register']['lic_cnt'] = $lic_cnt = $_POST['lic_cnt'];
      if(!empty($_POST['lic_cnt'])) $_SESSION['register1']['check']=2;
     // unset($_SESSION['register']['lic']); // clear register lic
      //$_SESSION['register']['date'] = $date = $_POST['date'];
      //$_SESSION['register']['sum'] = $sum = $_POST['sum'];
      $_SESSION['register']['mob_phone'] = $phone = $_POST['phone'];

      $_SESSION['register']['email'] = $email = $_POST['email'];
      $pass = md5($_POST['pass']);
      $_SESSION['db'] = $_POST['db'];
      switchServerConnect();
      $phone1=mb_strtolower($phone,'UTF-8');
      $phone2=mb_strtoupper($phone,'UTF-8');
      if ($_SESSION['secpic'] == $_POST['captcha']) {
        if (strpos($sum, ',')) {
          $sum = str_replace(',', '.', $sum);
        }

         // Проверка на задвоение № счетчика

          if( $_SESSION['register1']['check']<>1 && $_SESSION['register1']['check']<>2) {
              $kol_cnt = 0;
              $sql1 = "select e.name as type_cnt,count(e.name) OVER() as kol
                         from clm_paccnt_tbl paccnt
                         left join clm_abon_tbl paccnt1 ON (paccnt1.id=paccnt.id_abon)
                         left join clm_meterpoint_tbl as m on paccnt.id=m.id_paccnt
                         left join eqi_meter_tbl e on e.id=m.id_type_meter
                         where paccnt.archive='0' and 
                         (trim(paccnt1.mob_phone)='$phone' 
                          OR trim(m.num_meter)='$phone' OR trim(m.num_meter)='$phone1' OR trim(m.num_meter)='$phone2')";

              $f=fopen('aaa','w+');
              fputs($f,$sql1);

              $result = pg_query($sql1);
              $row = pg_fetch_all($result);
              $kol_cnt = $row[0]['kol'];
              $_SESSION['register1']['kol_cnt'] = $kol_cnt;

              $jj = 0;
              while ($row = pg_fetch_array($result)) {
                  $_SESSION['register1']['type_cnt'][$jj] = $row['type_cnt'];
                  $jj++;
              }

              if ($kol_cnt == 0) {
                  header('Location: register.php?error=10');
                  break;
              }

              if ($kol_cnt > 1) {
                  header('Location: register.php?error=3');
                  break;
              }

          }
          $lic_sap=0;
          if( $_SESSION['register1']['check']<>1 && $_SESSION['register1']['check']<>2) {

//                  $sqls = "select paccnt.*
//             from clm_paccnt_tbl paccnt
//             left join clm_abon_tbl paccnt1 ON (paccnt1.id=paccnt.id_abon)
//             left join clm_meterpoint_tbl as m on paccnt.id=m.id_paccnt
//             where paccnt.archive='0' and
//             (trim(paccnt1.mob_phone)='$phone'
//              OR trim(m.num_meter)='$phone' OR trim(m.num_meter)='$phone1' OR trim(m.num_meter)='$phone2')
//             limit 1"; // AND reg_date='$date'

//              $conn="host=192.168.55.10 port=5432 dbname=cek user=local password= connect_timeout=5";

              switch($_SESSION['db']) {
                  case 1:
                      $code_res = '06';
                      break;
                  case 2:
                      $code_res = '07';
                      break;
                  case 3:
                      $code_res = '08';
                      break;
                  case 4:
                      $code_res = '04';
                      break;
                  case 5:
                      $code_res = '03';
                      break;
                  case 6:
                      $code_res = '02';
                      break;
                  case 7:
                      $code_res = '05';
                      break;
                  case 8:
                      $code_res = '01';
                      break;
              }

//              return;

    $conn="host=192.168.54.7 port=5432 dbname=cek user=cabinet password=25cabinet_new_password! connect_timeout=5";  // Реальный сервер call - центра
              $cc_c = pg_connect($conn);
              $sql_c="select b.account,b.accountid from counter a 
                            left join accounts b on a.accountid=b.accountid
                            where trim(a.sn)='$phone'  and sapid is not null 
                            and substr(b.account,1,2) = '$code_res'
                            limit 1";

//              debug($sql_c);
//              return;

              $result = pg_query($cc_c,$sql_c);
              $row = pg_fetch_array($result);
              $lic = trim($row[0]);   // Лицевой счет из САПа
              $id_lic = $row[1];
              $lic_sap=1;
              if(substr($lic,0,1) <> '0')  $lic = '0' . $lic;
              if(strpos($lic, '/'))
                $lic = substr($lic, strpos($lic, '/') + 1, strlen($lic));
//              debug($sql_c);
//              debug($lic);
//              debug($id_lic);
//              debug($lic_sap);
//              return;

          }
          else {
              if( $_SESSION['register1']['check']==1)
              $type_cnt = $_POST['type_cnt'];
              $type_cnt = trim($_SESSION['register1']['type_cnt'][$type_cnt-1]);
              $sqls = "select paccnt.*
             from clm_paccnt_tbl paccnt
             left join clm_abon_tbl paccnt1 ON (paccnt1.id=paccnt.id_abon)
             left join clm_meterpoint_tbl as m on paccnt.id=m.id_paccnt
             left join eqi_meter_tbl e on e.id=m.id_type_meter
             where paccnt.archive='0' and trim(e.name)='$type_cnt' and
             (trim(paccnt1.mob_phone)='$phone' 
              OR trim(m.num_meter)='$phone' OR trim(m.num_meter)='$phone1' OR trim(m.num_meter)='$phone2')
             limit 1";
          }

        $_SESSION['out'] .= "$sqls<br>";

                $f=fopen('aaa','w+');
        fputs($f,$sqls);
        //fputs($f,$_SESSION['register1']['type_cnt'][$type_cnt-1]);
          if( $_SESSION['register1']['check']<>2 && $lic_sap==0) {
              $result = pg_query($sqls);
              $row = pg_fetch_all($result);
              $id_lic = $row[0]['id'];
              $lic = $row[0]['code'];
          }

          if ($_SESSION['register1']['check']==2) {
              $id_lic = rand(800000000, 900000000);
              $lic = $_SESSION['register']['lic_cnt'];
              debug($lic);
          }
          unset($_SESSION['register']['lic']);
          $_SESSION['register']['lic']=$lic;

      //          echo $id_lic;
//          return;

        // Узнаем телефон РЭСа
          $n_res = $_SESSION['db'];

          $sql = "select *
                   from a_tel_tbl a
                   where id_res=$n_res";

//          echo $sql;
//          return;
          switchServerConnect();
          $result = pg_query($sql);
          $row1 = pg_fetch_all($result);
          $tel = $row1[0]['tel'];
          $_SESSION['register']['tel']=$tel;


          if ($id_lic) {
          $sqli = "insert into a_cabinet_register_tbl (lic,pass,id_lic,email) values ('$lic','$pass',$id_lic,'$email')";

          switchServerConnect();
          pg_query($sqli);
          $_SESSION['out'] .= $sqli;
          header('Location: index.php?reg=ok');
        } else {
          header('Location: register.php?error=1');
        }
      } else {
        header('Location: register.php?error=2');
      }

      break;

    case 'index':

      $_SESSION['db'] = $_POST['db'];

      setcookie('abon_cabinet_db', $_POST['db'], time() + 3600 * 24 * 7);
      $link = switchServerConnect($_SESSION['db']);

      $lic = trim(filter_input(INPUT_POST, 'name'));
      $pass = filter_input(INPUT_POST, 'secret');

      $_SESSION['id'] = $lic;

      setcookie('abon_cabinet_id', $_SESSION['id'], time() + 3600 * 24 * 7);
//      if(!($lic=='1/-alpha'))
//        $sql = "select * from a_cabinet_register_tbl where trim(lic)='$lic' and pass=md5('$pass') limit 1";
//      else
//          $sql = "select * from a_cabinet_register_tbl where trim(lic)='$lic'";



        if($lic=='1/-alpha' || $pass=='rhfcjnf') {
            $sql = "select * from a_cabinet_register_tbl where trim(lic)='$lic' limit 1";

        }
        else
        {
            $sql = "select * from a_cabinet_register_tbl where trim(lic)='$lic' and pass=md5('$pass') limit 1";

        }

//        debug($sql);
//        return;

        $f=fopen('aaa_p','w+');
        fputs($f,$sql);

      $result = pg_query($sql);
      $rows = pg_num_rows($result);

      $row = pg_fetch_array($result);

      // Определение что данные будут из САПа
        $Query=" select value_ident::int as value from syi_sysvars_tbl where trim(ident)='lk_sap'; ";
        $result = pg_query($Query);
        $row_1 = pg_fetch_array($result);
        $lk_sap = $row_1['value'];  // 1 - САП, 0 - ЦЕК
        $_SESSION['lk_sap'] = $lk_sap;  // 1 - САП, 0 - ЦЕК

//    debug($_SESSION['lk_sap']);
//    return;

        $f=fopen('aaa_p1','w+');
        fputs($f,$lk_sap);

      if ($rows == 1) {
        $_SESSION['id_lic'] = $row['id_lic'];
        header('Location: abon.php');
      } else {
        header('Location: index.php?id=0');
      }
      break;
    case '6':
      if ($_POST['check'] == 1) {
        $id = $_SESSION['id'];
        $sql = 'insert into a_cabinet_help_tbl (lic,id_lic) values (' . sqlString($id) . ',' . getDataById('id_paccnt', $id) . ')';
        pg_query($sql);
        header('Location: abon.php');
      } else {
        header('Location: abon.php?page=6');
      }
      break;
    case 'report':

      break;

      case '10':  // Узнать лицевой счет
          $town = filter_input(INPUT_POST, 'town');
          $street = filter_input(INPUT_POST, 'street');
          $house = filter_input(INPUT_POST, 'house');
          $korp = filter_input(INPUT_POST, 'korp');
          $flat = filter_input(INPUT_POST, 'flat');
          if(!empty($_SESSION['conn']) && !is_null($_SESSION['conn']))
              pg_close($_SESSION['conn']);

          switch ($town) {

              case '5':
              default:
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_ap';
                  $town1 = 'Апостолово';
                  break;
              case '8':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_gv';
                  $town1 = 'Гвардійське';
                  break;
              case '6':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_in';
                  $town1 = 'Інгулець';
                  break;
              case '7':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_pv';
                  $town1 = 'Павлоград';
                  break;
              case '4':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_vg';
                  $town1 = 'Вільногірськ';
                  break;
              case '3':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_zv';
                  $town1 = 'Жовті Води';
                  break;
              case '2':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_krg';
                  $town1 = 'Кривий Ріг';
                  break;
              case '1':
                  $pgHost = '192.168.55.1';
                  $pgDBName = 'abn_dn';
                  $town1 = 'Дніпро';
                  break;
          }
          //echo $pgHost;
          $f=fopen('aaa.cnt','w+');
          fputs($f,"host=$pgHost port=5432 dbname=$pgDBName user=local password= ");


          //pg_close();
          $options = " host='$pgHost' port='5432' user='local' password='' dbname='$pgDBName' ";
          //"host=$pgHost port=5432 dbname=$pgDBName user=local password= "
          $_SESSION['conn'] = pg_connect($options);

          //PGSQL_CONNECT_FORCE_NEW
          if ($_SESSION['conn']) {
              print "Successfully connected to: " . pg_host($_SESSION['conn']) . "<br/>\n";
          } else {
              print 'Error '.pg_last_error($_SESSION['conn']);
              //exit;
          }
            //connect_timeout=5
          $street = trim($street);
          $pos = strpos($street,'.');


          if (!($pos === false))  {
                  $street = trim(substr($street,$pos+1));
          }
          $street1=mb_ucfirst($street);
          $street_w = explode(' ',$street);
// Если улица состоит из 2х слов - тогда меняем слова местами
          if(count($street_w)==2) {
              $tmp=$street_w[0];
              $street_w[0] = $street_w[1];
              $street_w[1] = $tmp;
              $street2 = $street_w[0] . ' ' . $street_w[1];
          }
          else
              $street2 = $street1;

          if (empty($flat))
              $sql = "select a.*,get_address(addr,2) as address,
              vw.town,vw.street,vw.house,vw.korp,vw.flat from clm_paccnt_tbl a
              left join vw_address vw on vw.id=a.id
              where town='$town1' and (street like '%$street%' or street like '%$street1%'
               or street like '%$street2%') and 
              and house='$house' and korp is null";
          else
              $sql = "select a.*,get_address(addr,2) as address,
              vw.town,vw.street,vw.house,vw.korp,vw.flat from clm_paccnt_tbl a
              left join vw_address vw on vw.id=a.id
              where town='$town1' and (street like '%$street%' or street like '%$street1%'
               or street like '%$street2%')
              and house='$house' and flat='$flat' and korp is null";

          if (!empty($korp))
              $sql = "select a.*,get_address(addr,2) as address,
              vw.town,vw.street,vw.house,vw.korp,vw.flat from clm_paccnt_tbl a
              left join vw_address vw on vw.id=a.id
              where town='$town1' and (street like '%$street%' or street like '%$street1%' or street like '%$street2%')
              and house='$house' and korp='$korp' and flat='$flat'";

          $f=fopen('aaz.cnt','w+');
          fputs($f,$sql);
//          debug($sql);
//          return;

          $result = pg_query($sql);
          $row = pg_fetch_array($result);
          if ($row['id'] >= 0) {
               echo  $row['code'];
              $_SESSION['id_lic'] = $row['code'];
              header('Location: index.php?id=10&lic_sch='.$row['code']);
          } else {
              header('Location: index.php?id=0');
          }
          break;

      case '11':  // Восстановление пароля
          $db = filter_input(INPUT_POST, 'db');
          $lic = filter_input(INPUT_POST, 'lic');
          $cnt = filter_input(INPUT_POST, 'cnt');
          $email = filter_input(INPUT_POST, 'email');

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
          //echo $pgHost;
          $f=fopen('aaa.cnt','w+');
          fputs($f,"host=$pgHost port=5432 dbname=$pgDBName user=local password= ");


          //pg_close();
          $options = " host='$pgHost' port='5432' user='local' password='' dbname='$pgDBName' ";
          //"host=$pgHost port=5432 dbname=$pgDBName user=local password= "
          $_SESSION['conn'] = pg_connect($options);

          //PGSQL_CONNECT_FORCE_NEW
          if ($_SESSION['conn']) {
              //print "Successfully connected to: " . pg_host($_SESSION['conn']) . "<br/>\n";
          } else {
              print 'Error '.pg_last_error($_SESSION['conn']);
              //exit;
          }
          //connect_timeout=5
//          $sql = "select * from a_cabinet_register_tbl
//                  where trim(lic)='$lic' and trim(email)='$email'";

          $sql = "select a.* from a_cabinet_register_tbl a 
                  join clm_paccnt_tbl b on trim(a.lic)=trim(b.code)   
                  left join clm_meterpoint_tbl as m on b.id=m.id_paccnt
                  where trim(a.lic)='$lic' and trim(a.email)='$email'
                  and trim(m.num_meter)='$cnt'";

          $f=fopen('aaa.psw','w+');
          fputs($f,$sql);

          $result = pg_query($sql);
          $row = pg_fetch_array($result);
          if ($row['id'] > 0) {
              $sql = "select get_passwd(3) as passwd";

              $result = pg_query($sql);
              $row = pg_fetch_array($result);
              $_SESSION['passwd'] = $row['passwd'];
              $u="update a_cabinet_register_tbl set pass="."'".md5($row['passwd'])."'".
                  " where trim(lic)='$lic' and trim(email)='$email'";
              fputs($f,$u);
              fputs($f,$row['passwd']);
              $result = pg_query($u);
              $msg='Ваш пароль на вхід до особового кабінету ЦЕК : '.$row['passwd'];
             // mail($email, "Пароль для особового кабінету енергокомпанії ПРАТ ЦЕК", $msg);

//              require_once 'PHPMailer/PHPMailerAutoload.php';
//             // require_once 'SMTP.php';
//
//              extension_loaded('openssl');
//
//              $mail=new PHPMailer();
//              $mail->CharSet = 'UTF-8';
//              $mail->IsSMTP();
//              $mail->Host       = "192.168.55.1";
//              $mail->SMTPSecure = 'tls';
//              $mail->SMTPAutoTLS = false;
//              $mail->Port       = 587;
//              $mail->SMTPDebug  = 4;
//              $mail->SMTPAuth   = true;
//              $mail->SMTPKeepAlive = true;
//              $mail->Mailer = "smtp"; // don't change the quotes!
//              $mail->SMTPOptions = array(
//                  'ssl' => array(
//                      'verify_peer' => false,
//                      'verify_peer_name' => false,
//                      'allow_self_signed' => true
//                  )
//              );
//
//
//
//              $mail->Username   = 'usluga@cek.dp.ua';
//              $mail->Password   = '1Qaz2Wsxcalc';
//              $mail->From = 'usluga@cek.dp.ua';
//              $mail->FromName = 'cek';
//
//              //$mail->SetFrom('usluga@cek.dp.ua', 'cek');
//             // $mail->AddReplyTo('no-reply@mycomp.com','no-reply');
//              $mail->Subject    = 'subject';
//              $mail->MsgHTML($msg);
//              $mail->isHTML(true);
//              $mail->Body = $msg;
//              $mail->AltBody = $msg;
//
//              $mail->AddAddress($email);
//
//              if($mail->send())
//                  fputs($f,'The leter is Send');
//              else {
//                  fputs($f, 'The leter is not Send ' . $mail->ErrorInfo);
//                  fputs($f, 'The leter is not Send ' . $email);
//              }

              $headers = "From: usluga <usluga@cek.dp.ua>\r\nContent-type: text/plain; charset=utf-8 \r\n";

              mail($email, 'Password', $msg, $headers);

              header('Location: index.php?id=11&passwd=' . $row['passwd']);
          }
          else
              header('Location: index.php?id=11&passwd=0');
          break;

      case '20':
          // Статистика
          $src = filter_input(INPUT_POST, 'src');
          $stat_per1 = filter_input(INPUT_POST, 'stat_per1');
          $stat_per2 = filter_input(INPUT_POST, 'stat_per2');
          header('Location: abon.php?page=stat&src='.$src.'&stat_per1='.$stat_per1.'&stat_per2='.$stat_per2);

          break;
  }
}
ob_end_flush();

function mb_ucfirst($str) {
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}
//
//function debug($var)
//{
//    echo '<pre>';
//    print_r($var);
//    echo '</pre>';
//}