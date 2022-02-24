<?php ob_start();

 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <?php
    #ini_set('display_errors',1);
    #ini_set('error_reporting',2047);
    require './func.php';
    require './config.php';
    require './head.php';
    require_once './ccon_soap.php';
    $BaseName = basename(__FILE__);
        
    //echo '<title>' . translate($BaseName) . '</title>';
    echo '<title>' . translate('index.php') . '</title>';
    get_head();
    ?>
    <style>
      .abon_marg {
        margin: 2px 4px;
      }
      .am1 {
        margin-top: 30px;
      }
      .fs {
          font-size: 14px;
          text-align: justify;
      }
      .w70 {
        width: 70%;
      }
      .w50 {
        width: 50%;
        margin: 30px 80px;
      }
      .w30 {
        width: 30%;
        margin: 30px 80px;
      }
    </style>
  </head>
  <body class="curd">
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      if ($_GET['d']) {
        $id = getDataById('id_paccnt', $_SESSION['id']);
        $sqld = "DELETE FROM acd_cabindication_tbl WHERE id_paccnt=$id AND mmgg='" . $_SESSION['fun_mmgg'] . "'";
        pg_query($sqld);
        header("Location: $BaseName?page=4");
      }
    }

    $out = '';
    $_SESSION['fun_mmgg'] = getDataById('fun_mmgg');
    //$Where = "paccnt.book||'/'||paccnt.code='" . $_SESSION['id'] . "'";
    $Where = "paccnt.code='" . $_SESSION['id'] . "'";

   // Соединение с САП
    $lSoap_s= 'CKSOAPMETER';
    $pSoap_s= 'aTmy9Z<faLNcJ))gTJMwYut(#eJ)NSlcY[2%Meo/';

//    $hSoap_s = 'http://erpqs1.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/scs/sap/zint_ws_upl_mrdata?sap-client=100';
    $hSoap_s = 'http://erppr3.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A1011D1/bndg_url/sap/bc/srt/scs/sap/zint_ws_upl_mrdata?sap-client=100'; // prod
    $client = new \SoapClient(
        "$hSoap_s",
        array('login' => "$lSoap_s",
            'password' => "$pSoap_s",
            'trace' => 1)
    );
    $curdate = date("Y-m-d");
    $curtime = date("Hi");
    $bukrs = 'CK01';
    $id_code=trim($_SESSION['id']);

//    if ($_SESSION['lk_sap'] == 0) {
        // Если данные тянутся из ЦЕК
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id = getDataById('id_paccnt', $_SESSION['id']);
            if ($_POST['search'] == 'true') {
                $postWhere = $_POST['where'];
                $Where = "paccnt.code='$postWhere' OR a.last_name ilike '$postWhere%'";
            } else {
                $cols = 'dat_prev,code,id_paccnt,id_meter,id_previndic,id_zone,id_meter_type,kind_energy,mmgg,num_eqp,koef,carry,dat_ind,value_prev,value_ind,calc_ind_pr';
                $insert = "INSERT INTO acd_cabindication_tbl ($cols) VALUES (";
                $i = 0;
                $k = 0;
                $f = fopen('aa2', 'w+');
//                debug($_POST['value_new']);
//                debug($_SESSION['abon']);
//                return;
//                debug($_SESSION['abon'][0]);
//                debug($_SESSION['abon'][1]);
                $sign_counter = 1;   // Признак счетчика [1 - 1зонный, 2 - 2х-зонный, 3 - 3х-зонный]
//                debug($_SESSION['abon']);
                $flag_err = 0;

                foreach ($_POST['value_new'] as $value_new) {
                    foreach ($_SESSION['abon'][$i] as $val) {
                        // Выцепляем дату и если с 1 по 5 число ставим последнее число прошлого месяца
//                        debug('val_ ' . $k . ' ' .$val);

                        if (1 == 1) {
                            if ($k == 13) {
                                $dd = 10;
                                $val1 = $_POST['date_new'][$i];
                                //$id_p = $_POST['id_paccnt'][$i];
                                $now = $_POST['now'][$i];
                                // if (empty)
                                //$val=date('Y-m-d', strtotime($val));
                                $vald = $val;
                                $val1 = date('d.m.Y', strtotime($val1));

//                                $date = new DateTime($val1);
//                                $date->modify("-3 day");
//                                $val1 = $date->format('Y-m-d');
//                                debug($val1);

                                $dd = date("j", strtotime($val1));
                                $dd = intval($dd);

                                if ($dd >= 1 && $dd <= 3) {
                                    $date = new DateTime($val1);
                                    $date->modify("-$dd day");
                                    $val1 = $date->format('Y-m-d');
                                    $curdate = $val1;
                                    //fputs($f,$val);
                                } else {
                                    $val1 = date('Y-m-d', strtotime($vald));
//                                $curdate = $val1;
                                }

                            }
                        }
                        /////////////////////////////////////////////////////////////////////////////////
                        if($k==0) {
                            $val=substr($val,6,4) . '-' . substr($val,3,2) . '-' .substr($val,0,2);
//                            debug($val);
                        }
                        $insert .= sqlString($val) . ',';

                        $k++;
                        if ($k == 12) $carry = $val;       // Разрядность счетчика
                        if ($k == 13) $date_sap = $val1; // Дата показаний для записи в САП
                        if ($k == 6) $zone_sap = $val;  // зона для записи в САП
                        if ($k == 2) $account_sap = $val; // лиц. счет для записи в САП
                        if ($k == 10) $counterSN = $val;    // № счетчика записи в САП
                        if ($k == 14) {
                            $value_prev = $val; // предыдущие показания

                        }

                    }
                    $i++;
                    $k = 0;
                    $value_new = (int)$value_new;
                    $value_sap = $value_new;
                    if ($zone_sap == 9 || $zone_sap == 10) $sign_counter = 2;  // 2х - зонный счетчик
                    if ($zone_sap == 6 || $zone_sap == 7 || $zone_sap == 8) $sign_counter = 3;  // 3х - зонный счетчик
                    if ($zone_sap == 9) $value_sap9 = $value_new;
                    if ($zone_sap == 10) $value_sap10 = $value_new;
                    if ($zone_sap == 6) $value_sap6 = $value_new;
                    if ($zone_sap == 7) $value_sap7 = $value_new;
                    if ($zone_sap == 8) $value_sap8 = $value_new;
                    $insert .= $value_new . ',' . $value_new . '),(';

//                    debug('date_sap ' .  $curdate);
//                    debug('value_sap9 '.$value_sap9);
//                    debug('value_sap10 '.$value_sap10);
//                    debug('value_prev ' . $value_prev);
//                    debug('value_new ' . $value_new);


                    if ($zone_sap == 9) {
                        if (($value_sap9 - $value_prev) < 0 && check_rerotation($value_sap9, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap9 - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }
                    if ($zone_sap == 10) {
                        if (($value_sap10 - $value_prev) < 0 && check_rerotation($value_sap10, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap10 - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }
                    if ($zone_sap == 0) {
                        if (($value_sap - $value_prev) < 0 && check_rerotation($value_sap, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }
                    if ($zone_sap == 6) {
                        if (($value_sap6 - $value_prev) < 0 && check_rerotation($value_sap6, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap6 - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }
                    if ($zone_sap == 7) {
                        if (($value_sap7 - $value_prev) < 0 && check_rerotation($value_sap7, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap7 - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }
                    if ($zone_sap == 8) {
                        if (($value_sap8 - $value_prev) < 0 && check_rerotation($value_sap8, $value_prev, $carry) == 0) {
                            echo '<script>alert("Помилка! Введені показники менше ніж введені раніше.")</script>';
                            $flag_err = 1;
                            break;
                        }
                        if (($value_sap8 - $value_prev) >10000) {
                            echo '<script>alert("Помилка! Введені показники занадто великі. Споживання більше 10000 кВт*год. ")</script>';
                            $flag_err = 1;
                            break;
                        }
                    }

//                    debug($value_sap);
//                    debug($counterSN);
//                    debug($curdate);
//                    debug($curtime);
//                    debug($account_sap);
//                    debug('--------------------');
//                    debug($sign_counter);
//                    debug($zone_sap);
//                    debug($flag_err);
                    // Запись данных в САП
                    if ($zone_sap == 0 && $flag_err == 0 && $sign_counter == 1) {
                        // Однозонный счетчик
                        $params = array(
                            'Srccode' => '01',
                            'Bukrs' => $bukrs,
                            'Consdata' => array('item' => array(
                                'Eic' => "",
                                'Account' => "$account_sap",
                                'Date' => $curdate,
                                'DateDb' => $curdate,
                                'TimeDb' => $curtime,
                                'Mrdata' => array(
                                    'item' => array(
                                        array('Id' => '121',
                                            'Zon' => '11',
                                            'Device' => $counterSN,
                                            'Data' => $value_sap,
                                            'Zwkenn' => ''
                                        )
                                    )
                                )
                            )
                            )
                        );
//                        debug($params);
                        $ablstat = 0;
                        $circle=0;
//                        while($ablstat==0) {
//                        if($ablstat == 0) {  // поставить надо в 0
                            $result = $client->__soapCall('ZintUplMrdataInd', array($params));
                            $done = $result->Retdata->item->Retcode;
                            $ablstat = $result->Retdata->item->Ablstat;
//                        }
                            $circle++;
//                            if($circle>50) break;
//                        }
//                        debug($result);
//                        if ($done=='1'){
//                            echo "Ваші показники внесено!";
//                            $cls='okok';
//                        }else {
//                            $cls = 'error';
//                            echo "Ваші показники не внесено!";
//                            debug($value_sap);
//                            debug($counterSN);
//                            debug($curtime);
//                            debug($curdate);
//                            debug($account_sap);
//                            debug($bukrs);
//                        }

                        // Логирование показаний на PostGreSQL сервер
                        $sql_log = "INSERT INTO log_cabinet (id,lic,date_t,val11,val21,val22,val31,val32,val33,status)
                                            VALUES(CAST(EXTRACT(EPOCH FROM NOW()) * 1000 AS BIGINT),'$account_sap',now(),$value_sap,
                                             0,0,0,0,0,1)";
                        pg_query($sql_log);
                    }

                }

//                debug("flag_err=".$flag_err);

                if ($sign_counter == 2 && $flag_err == 0) {
                    // 2-зонный счетчик
                    $params = array(
                        'Srccode' => '01',
                        'Bukrs' => $bukrs,
                        'Consdata' => array('item' => array(
                            'Eic' => "",
                            'Account' => "$account_sap",
                            'Date' => $curdate,
                            'DateDb' => $curdate,
                            'TimeDb' => $curtime,
                            'Mrdata' => array(
                                'item' => array(
                                    array('Id' => '121',
                                        'Zon' => '21',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap10,
                                        'Zwkenn' => ''
                                    ),
                                    array('Id' => '122',
                                        'Zon' => '22',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap9,
                                        'Zwkenn' => ''
                                    ),
                                    array('Id' => '100',
                                        'Zon' => '11',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap9+$value_sap10,
                                        'Zwkenn' => ''
                                    )
                                )
                            )
                        )
                        )
                    );

                    $result = $client->__soapCall('ZintUplMrdataInd', array($params));
//                        debug($result);


//                    $done = $result->Retdata->item->Retcode;
                    // Логирование показаний на PostGreSQL сервер
                    $sql_log = "INSERT INTO log_cabinet (id,lic,date_t,val11,val21,val22,val31,val32,val33,status)
                                            VALUES(CAST(EXTRACT(EPOCH FROM NOW()) * 1000 AS BIGINT),'$account_sap',now(),0,
                                             $value_sap9,$value_sap10,0,0,0,1)";
                    pg_query($sql_log);
                }

                if ($sign_counter == 3 && $flag_err == 0) {
                    // 3х-зонный счетчик
                    $params = array(
                        'Srccode' => '01',
                        'Bukrs' => $bukrs,
                        'Consdata' => array('item' => array(
                            'Eic' => "",
                            'Account' => "$account_sap",
                            'Date' => $curdate,
                            'DateDb' => $curdate,
                            'TimeDb' => $curtime,
                            'Mrdata' => array(
                                'item' => array(
                                    array('Id' => '300',
                                        'Zon' => '31',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap8,
                                        'Zwkenn' => ''
                                    ),
                                    array('Id' => '200',
                                        'Zon' => '33',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap6,
                                        'Zwkenn' => ''
                                    ),
                                    array('Id' => '100',
                                        'Zon' => '32',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap7,
                                        'Zwkenn' => ''
                                    ),
                                    array('Id' => '400',
                                        'Zon' => '11',
                                        'Device' => $counterSN,
                                        'Data' => $value_sap8+$value_sap6+$value_sap7,
                                        'Zwkenn' => ''
                                    )
                                )
                            )
                        )
                        )
                    );
                    $result = $client->__soapCall('ZintUplMrdataInd', array($params));
//                        debug($result);
//                    $done = $result->Retdata->item->Retcode;
                    // Логирование показаний на PostGreSQL сервер
                    $sql_log = "INSERT INTO log_cabinet (id,lic,date_t,val11,val21,val22,val31,val32,val33,status)
                                            VALUES(CAST(EXTRACT(EPOCH FROM NOW()) * 1000 AS BIGINT),'$account_sap',now(),0,
                                             0,0,$value_sap8,$value_sap7,$value_sap6,1)";
                    pg_query($sql_log);
                }
            }
            fputs($f, $insert);
//                debug($insert);
//                debug($date_sap);
//                debug($zone_sap);
//                debug($value_sap);
//                debug($account_sap);
//                debug($counterSN);
//                debug($value_prev);
//                return;
            if (1 == 1) {
                if ($flag_err == 0) {
                    $noErr = pg_query(substr($insert, 0, strlen($insert) - 2));
                    if (!$noErr) {
                        echo pg_last_error() . '<br>' . $insert;
                        echo '<script>alert("помилка!")</script>';
                    }
//                    else
//                        echo '<script>alert("Показники занесено!")</script>';

                    $update = "update acd_cabindication_tbl set dat_ind=now::date where EXTRACT(DAY FROM now)>=6 and trim(code)='$id_code'";
                    $noErr = pg_query($update);
                    if (!$noErr) {
                        echo pg_last_error() . '<br>' . $insert;
                        echo '<script>alert("помилка!")</script>';
                    }
                }

            }
        }
//    }
//    else
//    {
//        // Если данные тянутся из САП
//        // Доработка
//    }




    switch ($_GET['page']) {
     case 9:
         // Архів споживання
         $CT = getCompleteTable('period,type_meter,zone,demand', 'demand_old', $Where);
         $out .= "<div class='abon_marg w70'><label class='label label-default'>Архів споживання</label>$CT</div>";
         $CT1 = getCompleteTable('mmgg,zone,demand', 'replace_cnt_demand_old', $Where);
         if(!empty($CT1))
             $out .= "<div class='abon_marg w70'><label class='label label-default'>Архів споживання при заміні лічильника</label>$CT1</div>";

         // Удаляем временную таблицу
         $sql = "select id from clm_paccnt_tbl paccnt where $Where";
         $result = pg_query($sql);
         $row = pg_fetch_all($result);
         $id = $row[0]['id']; // id пользователя - нужен в дальнейшем
         $tname = 'tmp_demand_'.$id;  // Имя временной таблицы
        $sql = "drop table $tname";
        $result = pg_query($sql);
//         $row = pg_fetch_all($result);

         break;
      case 1:
      default:
         // echo $Where;
//      Происходит по умолчанию или при нажатии на кнопку "Особисті дані"
        //  echo $_SESSION['id'];
    if ($_SESSION['lk_sap'] == 0) {
        $CT = getCompleteTable('fio,addr_reg,home_phone,work_phone,mob_phone', 'user1', $Where);
        $out = "<div class='abon_marg am1'><label class='label label-default'>Персональні дані</label>$CT</div>";
    }
    if ($_SESSION['lk_sap'] == 1) {
            $CT = getCompleteTable('fio,addr_reg,mob_phone', 'user1', $Where);
            $out = "<div class='abon_marg am1'><label class='label label-default'>Персональні дані</label>$CT</div>";
    }
    if ($_SESSION['lk_sap'] == 0) {
        $CT2 = getCompleteTable('lic,eic,date_agreem|date|num,tp', 'user2', $Where);
        $out .= "<div class='abon_marg w70'><label class='label label-default'>Особовий рахунок</label>$CT2</div>";
    }
    if ($_SESSION['lk_sap'] == 1) {
            $CT2 = getCompleteTable('lic,eic', 'user2', $Where);
            $out .= "<div class='abon_marg w70'><label class='label label-default'>Особовий рахунок</label>$CT2</div>";

        // Получение информации об аварийных и плановых отключениях
        //        $conn="host=192.168.55.1 port=5432 dbname=cekdpua user=ssivtcov password=bpkextybt connect_timeout=5";  // сервер mysql
        $conn = new mysqli('localhost', 'Anna', "'ythubz",'cekdpua');
        $lic = $_SESSION['id'];
//        $lic = '020000824';
        $lic = substr($lic,1);
        $date_av=date('Y-m-d');
        $sql="SELECT distinct a.*,b.accountid,
                case when (a.issubmit=1 and a.factend_date is null) or (a.issubmit=0 and a.factend_date is null) then 'Активна' 
                when (a.issubmit=1 and a.factend_date is not null) then 'Закрита' 
                when (a.factend_date is not null and a.issubmit=0) then 'Скасована' end as status
                FROM cc_crash a 
                left JOIN cc_crash_account b ON 
                a.accidentid=b.accidentid
                WHERE 
                /*a.accbegin_date>='$date_av'  and */ 
                b.accountid like '%$lic%'
                order by a.accbegin_date desc";

//        a.accbegin_date>='2021-07-22' and a.planend_date<='2021-07-24'
//        and  a.planend_date>='2021-07-23'
//            debug($sql);
            $result = $conn->query($sql);

//        debug($result);

        if($result->num_rows>0) {
            $CT32 = "<table class='table table-bordered table-condensed table-striped'>
                <thead class='bg-success'>
                <tr>
                    <th>Дата відключення</th>
                    <th>Дата закінчення</th>
                    <th>Причина відключення</th>
                    <th>Статус</th>
                    
                </tr>
                </thead>
                ";
            // Отображение данных
            $integra = '';
            foreach ($result as $v) {
                $integra .= '<tr><td>' . format_date2($v['accbegin_date']) . ' ' . substr($v['accbegin_date'],11) . '</td>' .
                    '<td>' . format_date2($v['planend_date']) . ' ' . substr($v['planend_date'],11) . '</td>' .
                    '<td>' . $v['descr'] . '</td>' .
                    '<td>' . $v['status'] . '</td>';
            }
            $integra .= '</table>';
            $CT32 .= $integra;

            $out .= "<div class='abon_marg w70'><label class='label label-danger'>Увага! Аварійні або планові  відключення</label>$CT32</div>";
        }
        switchServerConnect();
    }

//        $CT2 = getCompleteTable('code,addr', 'other_lic', $Where);
//        if ($CT2)
//            $out .= "<div class='abon_marg w70'><label class='label label-default'>Інші Ваші особові рахунки</label>$CT2</div>";

        //$CT3 = getCompleteTable('lgt,family_cnt|int,dt_start|date,dt_end|date,name', 'user3', $Where);
    if ($_SESSION['lk_sap'] == 0) {
        $CT3 = getCompleteTable('lgt,dt_start|date,dt_end|date,name', 'user3', $Where);
        if ($CT3) {
            $out .= "<div class='abon_marg w70'><label class='label label-default'>Пільга</label>$CT3</div>";
        }
    }
        $li[1] = 'class="active"';
        $out .= '<a href="http://cek.dp.ua/Connect" target="_blank" class="btn btn-default" style="margin-left: 10px">Приєднання до електромереж</a>';
        $out .= '<a href="http://localhost/cekservice/web/get_message?lic=' . $_SESSION['id'] . '"' . ' target="_blank" class="btn btn-default" style="margin-left: 10px">Замовлення послуги для повідомлення по відключенням</a>';
        break;
//    }
//    else
//    {
        // Если данные тянутся из САП

//    }
      case '2':
//      при нажатии на кнопку Лічильник
    if ($_SESSION['lk_sap'] == 0) {
        $CT21 = getCompleteTable('num_meter,type_name,zone_name,carry|int,dt_b|date', 'meter1', $Where);
        $out = "<div class='abon_marg am1'>$CT21</div>";
    }
    if ($_SESSION['lk_sap'] == 1) {
                $CT21 = getCompleteTable('num_meter,type_name,zone_name,carry|int', 'meter1', $Where);
                $out = "<div class='abon_marg am1'>$CT21</div>";
    }
        $li[2] = 'class="active"';
        break;

      case '3':

//          debug($Where);


//      при нажатии на кнопку Споживання
//        $CT33 = getCompleteTable('debet_e|num', 'debet_avans', $Where); // avans_val|num,

        //$out = "<div class='abon_marg w30'><label class='label label-default'>Поточне сальдо</label>$CT33</div>";

        /*Добавляю ссылку на оплату сбоку от таблицы (Поточне сальдо)*/
        /*
        $out = "<div class='abon_marg w50'><label class='label label-default'>Поточне сальдо</label>
        <table style='width:100%'>
        <tr><td style='width:60%'>
        $CT33
        </td><td style='width:40%; vertical-align: top;'>
        <a style='margin-left: 1.5em;' href='../?q=node/758' target='_blank'><img width='85%' height='85%' src='/sites/default/files/images/payment3.jpg'></a>
        </td></tr>
        </table>
        </div>";
        */
	/* 22.08.2016 */
//            $ссHost = '192.168.54.7';
//            $ссHost = '192.168.55.10';
//            $ссDBName = 'cek';


    $lic = $_SESSION['id'];
//    $conn="host=192.168.55.10 port=5432 dbname=cek user=local password= connect_timeout=5";
    $conn="host=192.168.54.7 port=5432 dbname=cek user=cabinet password=25cabinet_new_password! connect_timeout=5";  // Реальный сервер call - центра
    $cc_c = pg_connect($conn);
    $sql_c="select a.sapid,b.zonity from accounts a
                    left join counter b on a.accountid=b.accountid
                    where a.account='$lic' and a.fu=1";
//    debug($sql_c);
    $result = pg_query($cc_c,$sql_c);
    $row = pg_fetch_array($result);
    $cnt_sch = $row[0];   // Контокоррентный счет из САПа
    $q_zones =  $row[1];  // кол-во зон
 // Получаем набор данных из САПа по потреблению
            $hSoap='http://erppr3.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A1011D1/bndg_url/sap/bc/srt/scs/sap/zint_ws_cconline_exp?sap-client=100';
//            $hSoap='http://erpqs1.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A1011D1/bndg_url/sap/bc/srt/scs/sap/zint_ws_cconline_exp?sap-client=100';
            $lSoap = 'WEBFRGATE_CK'; /*логін*/
            $pSoap = 'sjgi5n27'; /*пароль*/

            $adapter = new ccon_soap($hSoap, $lSoap, $pSoap);

            $proc = "ZintCconlineGetData";
            $arr = array(
                $proc => array(
                    'IpPhone' => '',  //tel
                    'IpVkont' => $cnt_sch,
                ),
            );
            $result = objectToArray($adapter->soap_blina($arr[$proc], $proc));
//           $q_zones = $result['EtDeviceInfo']['item']['Zones'] ;  // кол-во зон
//            debug($result['EtMeterData']['item']);
            $srctxt = '';
           if(!isset($result['EtMeterData']['item'][0])) {
               $result['EtMeterData']['item'][0] = $result['EtMeterData']['item'];
               $srctxt = $result['EtScales']['item'][0]['Srctxt'];
           }

//            debug($result['EtMeterData']['item']);
            // Сборка данных
            $i=0;
            $j=0;
            $t='';

//            debug($result['EtMeterData']['item']);

            foreach ($result['EtMeterData']['item'] as $v){
              if ($q_zones>1)
                if(trim($v['Zwart'])=='ЗГ') continue;

                if(format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']) <> $t) $j++;

                if(!isset($v['Zwart'])) continue;
                switch(trim($v['Zwart'])) {
                    case 'ПК':
                        $mas[$i]['zone'] = 'Пік';
                        $mas[$i]['zone1'] = 1;
                        break;
                    case 'НП':
                        $mas[$i]['zone'] = 'Напівпік';
                        $mas[$i]['zone1'] = 2;
                        break;
                    case 'ДН':
                          $mas[$i]['zone'] = 'День';
                         $mas[$i]['zone1'] = 1;
                          break;
                    case 'НЧ':
                         $mas[$i]['zone'] = 'Ніч';
                        $mas[$i]['zone1'] = 3;
                        break;
                    case 'ЗГ':
                        $mas[$i]['zone'] = 'Загальна';
                        $mas[$i]['zone1'] = 4;
                        break;
                    default:
                        $mas[$i]['zone'] = $v['Zwart'];
                }
                $mas[$i]['period'] =  format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']);
                $mas[$i]['cons'] = $v['Mrcon'];
                $mas[$i]['order'] = $j;
                $mas[$i]['val'] = $v['Mrval'];
                $mas[$i]['val_prev'] = $v['MrvalPrev'];
                $i++;
                $t= format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']);

            }


            // Правильная сортировка в случае замены счетчика
            $prev_begin='';
            $prev_end_1 = strtotime('01.03.2021');
            $prev_end = '01.03.2021';
            $p_end = '';
            $index = -1;
            $flag_process=0;
            $ic=0;

            foreach ($mas as $k=>$v) {
                $ic++;
                $period = $v['period'];
                $p_begin = substr($period,0,10);
                $p_end = trim(substr($period,13));
                if($k==0) {
                    $a_begin = $p_end;
                }
                if($p_begin=='00.00.0000') $p_begin = '01.03.2021';
                $p1=date("d.m.Y", strtotime($p_begin));
                $p2=date("d.m.Y", strtotime($p_end));
                $p2_1=strtotime($p_end);
                if($p2_1>$prev_end_1 &&  $flag_process==0 && $ic>1)
                    $flag_process=1;


//                debug($prev_end_1);
//                debug($prev_end);
//                debug('p2_1='.$p2_1);
//                debug('p2='.$p2);
//                debug('flag_process='.$flag_process);

                if(strlen($prev_begin)>0){
                    if(strtotime($p1)>strtotime($prev_begin)){
                        $index = $k;
//                        break;
                    }
                }

                $prev_begin = $p1;
                $prev_end = $p2;
                $prev_end_1 = $p2_1;
            }
           $y = count($mas);

//            debug($index);

            if($index<>-1) {  // в случае замены счетчика
                $j=0;
                for($i=$index;$i<$y;$i++){
                    $mas1[$i-$index] = $mas[$i];
                }
                $y = count($mas1);
                for($i=0;$i<$index;$i++){
                    $mas1[$y+$i] = $mas[$i];
                }
            }
            else {
                for ($i = 0; $i < $y; $i++) {
                    $mas1[$i] = $mas[$i];
                }
            }

//            debug($flag_process);
//            debug($mas1);

            // Формируем новый порядок в случае замены счетчика
        if($flag_process==1) {
            if ($index <> -1) {
                $old = 0;
                $j = 1;
                $y = count($mas1);
                for ($i = 0; $i < $y; $i++) {
                    $n = $mas1[$i]['order'];
                    if ($n <> $old && $old <> 0) {
                        $j++;
                    }
                    $mas1[$i]['order'] = $j;
                    $old = $n;
                }
            }
        }

    if($flag_process==0) {
        if ($index <> -1) {
            $old = 0;
            $j = 1;
            $y = count($mas1);
            for ($i = 0; $i < $y; $i++) {
                $n = $mas1[$i]['order'];
//                            if ($n <> $old && $old <> 0) {
//                                $j++;
//                            }
//                            $mas1[$i]['order'] = $j;
//                            $old = $n;
                $mas2[$n - 1] = $mas1[$i];
            }
            $mas1 = $mas2;
        }
    }

//            debug($mas1);
            $mas = sort_nested_arrays($mas1,['order'=>'asc','zone1'=>'asc']);  // Сортировка зон

//            debug($mas);

            $CT32 = "<table class='table table-bordered table-condensed table-striped'>
                <thead class='bg-success'>
                <tr>
                    <th>Період</th>
                    <th>Зона</th>
                    <th>Показники кВт/год</th>
                    <th>Попередні показники кВт/год</th>
                    <th>Споживання кВт/год</th>
                </tr>
                </thead>
                ";
             // Отображение данных
            $integra='';
            foreach ($mas as $v) {
                $v['period'] = str_replace('00.00.0000 -', "до ", $v['period']);
                $integra.='<tr><td>' . $v['period'] . '</td>' .
                    '<td>' . $v['zone'] . '</td>' .
                    '<td>' . $v['val'] . '</td>' .
                    '<td>' . $v['val_prev'] . '</td>' .
                    '<td>' . $v['cons'] . '</td></tr>' ;
            }
            $integra.='</table>';
            $CT32.=$integra;

            $out .= "<div class='abon_marg w50'><label class='label label-default'>Споживання</label>$CT32</div>";

            $out .= '<a href="/abonent/abon.php?page=9" target="_blank" class="btn btn-default" style="margin-left: 10px">Архів споживання</a><br><br>';

            switchServerConnect();

   break;

        $CT32 = getCompleteTable('mmgg_bill|date3,demand0,demand10,demand9,demand8,demand7,demand6,demand_all', 'bill1', $Where);
        $out .= "<div class='abon_marg w50'><label class='label label-default'>Споживання</label>$CT32</div>";
//        $CT31 = getCompleteTable('doc_name,pay_reg_date|date,pay_sum|num', 'pay1', $Where);
//        $out .= "<div class='abon_marg am1 w50'><label class='label label-default'>Ваші оплати</label>$CT31</div>";
        $li[3] = 'class="active"';
        //$out .= "<div><p style='margin-top: 3em;'><a href='https://www.privat24.ua/#login' target='_blank'><img style='margin-left:5em;' src='/sites/default/files/images/r_privat24.jpg'></a>
        //<a href='https://www.portmone.com.ua' target='_blank'><img style='margin-left:5em;' src='/sites/default/files/images/r_portmone.jpg'></a>
        //</p></div>";
        break;
      case '4':
//      при нажатии на кнопку "Введення показань"
    $date_input = date('Y-m-d');
    $dd_input = (int) date("j", strtotime($date_input));



    if ($_SESSION['lk_sap'] == 1) {
//          Синхронизация счетчика с САП - если счетчик менялся
        $result = pg_query(getArrayFromBase('meter1', $Where));
        $n_cnt = '';
        $row = pg_fetch_array($result);
        $cnt_zone = substr($row['zone_name'], 0, 1);  // Кол-во зон счетчика
        $n_cnt = trim($row['num_meter']);
        $carry_sap = trim($row['carry']);
        $lic = $_SESSION['id'];

        $sql = "select max(dat_ind),num_eqp  from acd_cabindication_tbl paccnt 
                         where $Where group by 2";
        $result1 = pg_query($sql);
        $row = pg_fetch_array($result1);
        $n_cnt_last = trim($row['num_eqp']);
        if ($n_cnt != $n_cnt_last) {

            $cols = 'dat_prev,code,id_paccnt,id_meter,id_previndic,id_zone,id_meter_type,kind_energy,
            mmgg,num_eqp,koef,carry,dat_ind,value_prev,value_ind,calc_ind_pr';
            $insert = "INSERT INTO acd_cabindication_tbl ($cols) VALUES (";
            $result = pg_query(getArrayFromBase('indication2', $Where . " and mmgg='" . $_SESSION['fun_mmgg'] . "'"));
            $v='';
            while ($row = pg_fetch_array($result)) {
                 $dat_ind_sap = $row['dat_ind'];
                 $val_ind_sap = $row['value_new'];
                 $zone_sap_t = $row['id_zone'];
                  $v = "null,'$lic',-12345678,-23456789,0,$zone_sap_t,0,10,null,'$n_cnt',1,$carry_sap,'$dat_ind_sap',0,$val_ind_sap,$val_ind_sap";
                $insert1 = $insert . $v . ')';
                $noErr = pg_query($insert1);
            }
        }
    }

      $out = "<div class='abon_marg am1 w70 fs'>&nbsp;&nbsp;&nbsp; Відповідно до п. 8.6.3. ККОЕЕ Зчитані та передані дані з лічильників протягом періоду, 
      що починається за два календарні дні до кінця розрахункового місяця та закінчується на третій календарний день наступного розрахункового періоду (календарного місяця), 
      вважаються даними на перше число календарного місяця. Якщо показники внесені не в зазначений вище період, то обсяги споживання електричної енергії будуть донараховані 
      з дати внесення до кінця розрахункового періоду. Наприклад якщо показники внесені 25 числа поточного місяця, то за 5 діб (з 25го по 30e) автоматично буде донараховано 
      обсяги на підставі середньодобового споживання електричної енергії.</div>";
      $result = pg_query(getArrayFromBase('how_long', $Where));
        $let_input=0;
    if ($result) {
        $row = pg_fetch_array($result);
        $hdays=$row['days'];
        $last_p=round($row['value_ind'],0);
        $hprev=date('d.m.Y', strtotime($row['pred']));
// 2 поменять на 10 потом после отладки
        if($hdays>=0 || is_null($hdays)) $let_input=1;

//        debug($hdays);
//        debug($let_input);
    }
            $let_input=1;
            if($dd_input==4 || $dd_input==5) {
                $let_input=0;
//                echo 'Увага! 4 та 5 числа кожного місяця показання в кабінет не вводяться. Проходять розрахунки.';
//                return;
            }


        //if (getDataById($BaseName, $_SESSION['id']) > 0) {
        if($let_input==0) {
          //$CT41 = getCompleteTable('num_eqp,carry|int,now|date,value_prev|num,value_new|num', 'indication2', $Where . " and mmgg='" . $_SESSION['fun_mmgg'] . "'");
          $CT41 = getCompleteTable('num_eqp,carry|int,zone,value_prev|num,dat_prev|date,value_new|num,dat_ind|date,value_diff|num', 'indication2', $Where . " and mmgg='" . $_SESSION['fun_mmgg'] . "'",null,1);
//          if($hdays==0 || is_null($hdays))
//              $out .= "<div class='abon_marg am1 w70'>$CT41".
//                  "<span class='label label-danger curp' style='margin-left: 15px'>Показники можна вводити один раз
//                    в десять днів, останній раз показники вводились $hprev. [$last_p кВт/год]</span>";
//
//          else
//          $out .= "<div class='abon_marg am1 w70'>$CT41".
//              "<span class='label label-danger curp' style='margin-left: 15px'>Показники можна вводити один раз
//                    в десять днів, останній раз показники вводились $hprev.</span></div>";

            $out .= "<div class='abon_marg am1 w70'>$CT41".
              "<span class='label label-danger curp' style='margin-left: 15px'>
                    Увага! Четвертого та п'ятого числа кожного місяця показання в кабінет не вводяться. Проходять розрахунки.
                </span></div>";

          
//<span style='margin-left: 15px'>Для введення нових показань натисніть кнопку Видалити</span></div>";

//            $CT41 = getCompleteTable('num_meter,zone_name,carry|int,dat_prev_ind|date,value_ind|num,val_new|num,value_new|input,date_new|input_date', 'indication', $Where);
//
//            $out = "<div class='abon_marg am1 w70'><form method='POST'>$CT41<button type='submit' class='btn btn-primary'>Зберегти</button></form></div>";
// <a href='?d=" . getDataById('id_paccnt', $_SESSION['id']) .
//                  "' class='btn btn-default btn-sm'>Видалити</a></div>";

        } else {
//            debug('1');

//            $CT41 = getCompleteTable('num_meter,zone_name,carry|int,dat_prev_ind|date,value_ind|num,value_new|input,date_new|input_date', 'indication', $Where);
//
//          $out = "<div class='abon_marg am1 w70'><form method='POST'>$CT41<button type='submit' class='btn btn-primary'>Зберегти</button></form></div>";
            if ($_SESSION['lk_sap'] == 0) {
                $CT41 = getCompleteTable('num_meter,zone_name,carry|int,auto_date,auto_val,dat_prev_ind|date,value_ind|num,val_new|num,value_new|input,date_new|input_date', 'indication', $Where, null, 1);
            }
            if ($_SESSION['lk_sap'] == 1) {
                $CT41 = getCompleteTable('num_meter,zone_name,carry|int,auto_date,auto_val,dat_prev_ind|date,val_prev|num,value_new|input,date_new|input_date', 'indication', $Where, null, 1);
            }

//            $out .= "<div class='abon_marg am1 w70  '><form method='POST'>$CT41<button type='submit' class='btn btn-primary'>Зберегти</button></form>
//            <span class='label label-success ' style='margin-left: 215px' >Увага, показання лічильників відобразяться в прийнятих протягом 3-х днів після передачі показань.</span></div>";

            $out .= "<div class='abon_marg am1 w70  '><form method='POST'>$CT41<button type='submit' class='btn btn-primary'>Зберегти</button></form>";


        }
//        $CT_indication_addon = getCompleteTable('num_meter,zone_name,carry|int,dat_prev_ind|date,value_ind|num,indic_type', 'indication_addon', $Where);
//        $out .= "<div class='abon_marg am1 w70'><label class='label label-default'>Попередні показники</label>$CT_indication_addon</div>";
//        $li[4] = 'class="active"';

        break;
      case '5':

        $li[5] = 'class="active"';
        $newsTable = getCompleteTable('news,now|date2', 'news');
        $out .= '<div class="col-lg-6">' . $newsTable . '</div>';
        break;
      case '6':
 //        Скарга
        $out = '
          <div class="col-lg-6">
          <form method="POST" action="a.php?action=6">
          <div class="" style="margin: 20px 60px">
          <div class="checkbox">
            <label><input class="" type="checkbox" value="1" name="check">У мене не працює прилад обліку</label>
          </div>
          <button type="submit" class="btn btn-danger btn-sm">Подати скаргу</button>
          </div>
          </form>
          </div>';
        $li[6] = 'class="list-group-item-danger"';
        break;
      case 'stat':
        $li['stat'] = 'class="active"';
//        $CTStat = getCompleteTable('name,p_all,p_indication', 'stats');
//        $out = "<div style='margin: 10px 0 0 80px'>Цей РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$CTStat</div>";
        $out = '<a href="./statist.php" class="btn btn-warning" style="margin-left: 50px">Деталізована статистика</a>';

        $CTStat = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.20.1 port=5432 dbname=abn_vg user=local password=');
        $pokaz=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.20.1 port=5432 dbname=abn_vg user=local password=');
        $pokaz1=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.20.1 port=5432 dbname=abn_vg user=local password=');
        $pokaz2=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.20.1 port=5432 dbname=abn_vg user=local password=');
        $pokaz3=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.20.1 port=5432 dbname=abn_vg user=local password=');
        $out .= "<div style='margin: 10px 0 0 80px'>Вільногірські РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$CTStat</div>";

        $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.26.1 port=5432 dbname=abn_zv user=local password=');
        $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.26.1 port=5432 dbname=abn_zv user=local password=');
        $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.26.1 port=5432 dbname=abn_zv user=local password=');
        $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.26.1 port=5432 dbname=abn_zv user=local password=');
        $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.26.1 port=5432 dbname=abn_zv user=local password=');
        $out .= "<div style='margin: 10px 0 0 80px'>Жовтоводські РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.15.15 port=5432 dbname=abn_dn user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.15.15 port=5432 dbname=abn_dn user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.15.15 port=5432 dbname=abn_dn user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.15.15 port=5432 dbname=abn_dn user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.15.15 port=5432 dbname=abn_dn user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Дніпровські РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.21.1 port=5432 dbname=abn_pv user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.21.1 port=5432 dbname=abn_pv user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.21.1 port=5432 dbname=abn_pv user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.21.1 port=5432 dbname=abn_pv user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.21.1 port=5432 dbname=abn_pv user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Павлоградські РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.75.1 port=5432 dbname=abn_krg user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.75.1 port=5432 dbname=abn_krg user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.75.1 port=5432 dbname=abn_krg user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.75.1 port=5432 dbname=abn_krg user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.75.1 port=5432 dbname=abn_krg user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Криворізькі РЕМ:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.85.1 port=5432 dbname=abn_in user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.85.1 port=5432 dbname=abn_in user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.85.1 port=5432 dbname=abn_in user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.85.1 port=5432 dbname=abn_in user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.85.1 port=5432 dbname=abn_in user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Інгулецька дільниця:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.85.1 port=5432 dbname=abn_ap user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.85.1 port=5432 dbname=abn_ap user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.85.1 port=5432 dbname=abn_ap user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.85.1 port=5432 dbname=abn_ap user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.85.1 port=5432 dbname=abn_ap user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Апостолівська дільниця:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $ctUrLic2 = getCompleteTable('name,p_all,p_indication', 'stats', NULL, 'host=192.168.17.1 port=5432 dbname=abn_gv user=local password=');
          $pokaz+=get_pokaz('p_all', 'stats1', NULL, 'host=192.168.17.1 port=5432 dbname=abn_gv user=local password=');
          $pokaz1+=get_pokaz('p_all', 'stats2', NULL, 'host=192.168.17.1 port=5432 dbname=abn_gv user=local password=');
          $pokaz2+=get_pokaz('p_indication', 'stats3', NULL, 'host=192.168.17.1 port=5432 dbname=abn_gv user=local password=');
          $pokaz3+=get_pokaz('p_indication', 'stats4', NULL, 'host=192.168.17.1 port=5432 dbname=abn_gv user=local password=');
          $out .= "<div style='margin: 10px 0 0 80px'>Гвардійська дільниця:</div><div class='abon_marg am1 w50' style='margin-top: 0'>$ctUrLic2</div>";

          $out .= "<div style='margin: 10px 0 0 80px'>Усього зареєстровано унікальних записів: $pokaz</div>";
          $out .= "<div style='margin: 10px 0 0 80px'>Усього зареєстровано записів: $pokaz1</div>";
          $out .= "<div style='margin: 10px 0 0 80px'>Усього ввело показників (кількість споживачів): $pokaz2</div>";
          $out .= "<div style='margin: 10px 0 0 80px'>Усього введено показників: $pokaz3</div>";

        break;
      case 'report':
        $li['report'] = 'class="active"';

        $out = '
  <div class="col-lg-7" style="margin: -5px 0 3px 0">
    <form method="POST" class="form-inline">
    <input name="search" type="hidden" value="true">
      <div class="form-group">
        <input class="form-control input-sm" name="where" type="text" style="width: 250px" value="' . $_POST['where'] . '" autocomplete="off">
      </div>
      <button type="submit" class="btn btn-default btn-sm">Пошук</button>
    </form>
  </div>';

        $out .= "<div class='abon_marg am1'>" .
                getCompleteTable('lic,fio,addr_reg,value_new|num,now|date2', 'reportCC', $Where)
                . "</div>";
        break;
    }

    if ($_SESSION['admin'] === TRUE) {
      $addOneString = '<li ' . $li['stat'] . '><a href="?page=stat">Stat</a></li>';
      $addOneString .= '<li ' . $li['report'] . '><a href="?page=report">Report</a></li>';
    }

    ?>

    <div style="margin: 3px 15px">
      <ul class="nav nav-pills">
        <li <?= $li[1] ?>><a href="?page=1">Особисті дані</a></li>
        <li <?= $li[2] ?>><a href="?page=2">Лічильник</a></li>

        <li <?= $li[4] ?>><a href="?page=4">Введення показань</a></li>

        <li <?= $li[5] ?>><a href="?page=3">Споживання</a></li>

        <li <?= $li[6] ?>><a href="?page=6" class="text-danger">Скарга</a></li>
          <?= $addOneString ?>
        <li><a href="index.php" title="<?php echo $_SESSION['serverString'] ?>">Вихід</a></li>
      </ul>
    </div>

    <div><?= $out ?></div>

  </body>
</html>
<? ob_end_flush()
/* <li <?= $li[3] ?><!--><a href="?page=3">Споживання</a></li>--> */
?>
