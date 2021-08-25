<?php

use \Ccon_soap;

if (!$_SESSION) {
  session_start();
}

//require_once './config.php';

function getDataById($param, $search = NULL, $s2 = NULL) {
  $id_client = $_SESSION['id'];
  switch ($param) {
    case 'c_clm_client_tbl':
      $sql = "SELECT COUNT(*) FROM clm_client_tbl";
      break;

    case 'saldo_date':
      $sql = "SELECT dat_sal FROM acm_saldo_tbl WHERE id_client=$search LIMIT 1";
      break;

    case 'c_acm_bill_tbl':
      $date1 = "AND reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_pref'] > 0) {
        $id_pref = 'AND id_pref = ' . $_SESSION['id_pref'];
      }
      $sql = "SELECT COUNT(*) FROM acm_bill_tbl WHERE id_client=$id_client $date1 $date2 $id_pref";
      break;

    case 'c_acm_pay_tbl':
      $date1 = "AND b.pay_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND b.pay_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_pref'] > 0) {
        $id_pref = 'AND b.id_pref = ' . $_SESSION['id_pref'];
      }
      $sql = "SELECT COUNT(*) FROM acm_pay_tbl b WHERE id_client=$id_client $date1 $date2 $id_pref";
      break;

    case 'c_acd_demandlimit_tbl':
      $date1 = "AND month_limit >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND month_limit <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_area'] > 0) {
        $id_area = 'AND d.id_area = ' . $_SESSION['id_area'];
      }
      $sql = "SELECT COUNT(*) FROM acd_demandlimit_tbl d WHERE id_client=$id_client $date1 $date2 $id_area";
      break;

    case 'c_acm_headindication_tbl':
      $date1 = "AND h.reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND h.reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['num_eqp'] > 0) {
        $num_eqp = "AND i.num_eqp = '" . $_SESSION['num_eqp'] . "'";
      }
      $sql = "SELECT COUNT(*) FROM acm_headindication_tbl h
 JOIN acd_indication_tbl i ON i.id_doc=h.id_doc
 WHERE h.id_client=$id_client $date1 $date2 $num_eqp";
      break;

    case 'c_sub_headindication':
      $date1 = "AND h.reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND h.reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['sub_num_eqp'] > 0) {
        $num_eqp = "AND i.num_eqp = '" . $_SESSION['sub_num_eqp'] . "'";
      }
      $sql = "SELECT DISTINCT COUNT(*)
 FROM clm_subabons_tbl s
 JOIN clm_client_tbl c ON c.id=s.id_client_sub
 LEFT JOIN eqm_point_tbl p ON p.code_eqp=s.id_point
 JOIN acm_headindication_tbl h ON h.id_client=s.id_client_sub
 JOIN acd_indication_tbl i ON (i.id_doc=h.id_doc AND s.id_meter_sub=i.id_meter)
 WHERE s.id_client=$id_client $date1 $date2 $num_eqp";
      break;
    case 'indication_input':
      $sql = "SELECT value FROM acd_indication_input_tbl WHERE id_meter=$search AND kind_energy=$s2";
      break;
    case 'c_tt_connect':
      $sql = "SELECT COUNT(*)
 FROM eqm_point_tbl p
 JOIN eqm_ground_tbl g ON g.code_eqp=p.id_ground
 LEFT JOIN aci_tarif_tbl t ON t.id=p.id_tarif
 WHERE p.id_client=$id_client";
      break;
    case 'login':
      $sql = "SELECT id_client FROM clm_client_cabinet_tbl WHERE login='$search'";
      break;
    case 'loginFromId':
      $sql = "SELECT login FROM clm_client_cabinet_tbl WHERE id_client=$search";
      break;
    case 'secret':
      $sql = "SELECT pass FROM clm_client_cabinet_tbl WHERE login='$search'";
      break;
    case 'cabIndClient':
      $sql = "SELECT id_client FROM acd_cabindication_tbl WHERE id_client=$id_client LIMIT 1";
      break;
    case 'download.php':
      $sql = "SELECT filename FROM acd_cab_upload_tbl WHERE id=$search";
      break;
    case 'indication_view.php':
      $sql = "SELECT COUNT(DISTINCT id_client) FROM acd_cabindication_tbl";
      break;
    case 'abon.php':
      $sql = "SELECT COUNT(DISTINCT id_paccnt)
 FROM acd_cabindication_tbl c
 JOIN clm_paccnt_tbl paccnt ON (paccnt.id=c.id_paccnt)
 WHERE mmgg=fun_mmgg() AND paccnt.code='$search'";
      break;
    case 'fun_mmgg':
      $sql = "SELECT fun_mmgg()";
      break;
    case 'id_paccnt':
      $sql = "SELECT c.id_paccnt
 FROM acm_indication_tbl c
 JOIN clm_paccnt_tbl paccnt ON (paccnt.id=c.id_paccnt)
 WHERE paccnt.code='$search'
 LIMIT 1";
      //echo $sql;
      break;
  }
  $result = pg_query($sql);
  if (!$result && $_SESSION['admin']) {
    PRE(pg_last_error());
  }
  $row = pg_fetch_array($result);
  return $row[0];
}

function PRE($message) {
  if ($message) {
    $r = "<PRE>$message</PRE>";
    echo $r;
  }
  return $r;
}

function getTable($param, $Limit = 15, $Offset = 0, $id = NULL) {
  if ($Offset < 0) {
    $Offset = 0;
  }
  $id_client = $_SESSION['id'];
  switch ($param) {

    case 'acm_saldo_tbl':
      $sql = "SELECT id, id_client, id_pref, dt_b, dt_b_tax, kt_b, kt_b_tax, summ_avans, 
            summ_avanstax, summ_bill, summ_billtax, kvt_bill, sum_pay, sum_paytax, 
            dt_e, dt_e_tax, kt_e, kt_e_tax, mmgg, dt, dat_sal
  FROM acm_cabinet_saldo_tbl
 WHERE id_client=$id_client ORDER BY id_pref";
      $result = pg_query($sql);
      if (!$result) {
//PRE($sql);
      }
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td class="text-center">' . $row['id_pref'] . '</td>
  <td class="text-right">' . nf($row['deb_b']) . '</td>
  <td class="text-right">' . nf($row['kt_b']) . '</td>
  <td class="text-right">' . nf($row['summ_avans']) . '</td>
  <td class="text-right">' . nf($row['summ_bill']) . '</td>
  <td class="text-right">' . nf($row['kvt_bill']) . '</td>
  <td class="text-right">' . nf($row['sum_pay']) . '</td>
  <td class="text-right">' . nf($row['deb_e']) . '</td>
  <td class="text-right">' . nf($row['kt_e']) . '</td>
</tr>';
      }
      break;

    case 'clm_client_tbl':
      $sql = "SELECT id,name,short_name,addr_tax FROM $param LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$Offset . '</td>
  <td>' . $row['id'] . '</td>
  <td>' . $row['name'] . '</td>
  <td>' . $row['short_name'] . '</td>
  <td>' . $row['addr_tax'] . '</td>
</tr>';
      }
      break;

    case 'acm_bill_tbl':
      $date1 = "AND b.reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND b.reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_pref'] > 0) {
        $id_pref = 'AND b.id_pref = ' . $_SESSION['id_pref'];
      }
      $sql = "SELECT reg_num,b.reg_date,value,demand_val,mmgg_bill
 ,p.name AS id_pref
 ,d.name AS idk_doc
 FROM acm_bill_tbl b
 JOIN aci_pref_tbl p ON p.id=b.id_pref
 JOIN dci_document_tbl d ON d.id=b.idk_doc
 WHERE id_client=$id_client $date1 $date2 $id_pref
 ORDER BY mmgg_bill DESC,demand_val DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      while ($row = pg_fetch_array($result)) {
        $r .= '
<tr>
  <td>' . ++$Offset . '</td>
  <td>' . $row['reg_num'] . '</td>
  <td>' . df($row['reg_date']) . '</td>
  <td class="text-right">' . nf($row['value']) . '</td>
  <td class="text-right">' . $row['demand_val'] . '</td>
  <td>' . df($row['mmgg_bill'], 2) . '</td>
  <td>' . $row['id_pref'] . '</td>
  <td>' . $row['idk_doc'] . '</td>
</tr>';
      }
      break;

    case 'acm_pay_tbl':
      $date1 = "AND b.pay_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND b.pay_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_pref'] > 0) {
        $id_pref = 'AND b.id_pref = ' . $_SESSION['id_pref'];
      }
      $sql = "SELECT reg_num,reg_date,value_pay,value_tax,pay_date,mmgg_pay
 ,p.name AS id_pref
 FROM acm_pay_tbl b
 JOIN aci_pref_tbl p ON p.id=b.id_pref
 WHERE id_client=$id_client $date1 $date2 $id_pref
 ORDER BY mmgg_pay DESC,value_pay DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      while ($row = pg_fetch_array($result)) {
        $r .= '
  <tr>
    <td>' . ++$Offset . '</td>
    <td>' . $row['reg_num'] . '</td>
    <td class="text-right">' . nf($row['value_pay']) . '</td>
    <td class="text-right">' . nf($row['value_tax']) . '</td>
    <td>' . df($row['pay_date']) . '</td>
    <td>' . df($row['mmgg_pay'], 2) . '</td>
    <td>' . $row['id_pref'] . '</td>
  </tr>';
      }
      break;

    case 'acd_demandlimit_tbl':
      $date1 = "AND d1.month_limit >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND d1.month_limit <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['id_area'] > 0) {
        $id_area = 'AND d1.id_area = ' . $_SESSION['id_area'];
      }
      /* $sql = "SELECT night_day,month_limit,value_dem,change_date
        ,e.name AS doc
        ,g.name_eqp AS id_area
        FROM acd_demandlimit_tbl d
        JOIN eqm_ground_tbl g ON g.code_eqp=d.id_area
        JOIN dci_document_tbl e ON e.id=d.idk_document
        WHERE d.id_client=$id_client $date1 $date2 $id_area
        ORDER BY month_limit DESC,value_dem DESC
        LIMIT $Limit OFFSET $Offset"; */
      $sql = "select d1.id, hl.idk_document, hl.id_client, d1.id_area, d1.night_day, d1.dt, d1.id_person, ee.name_eqp,
    d1.month_limit, d1.value_dem, d1.mmgg, d1.max_dem, hl.reg_date,hl.idk_document,dd.name AS doc
   from acd_demandlimit_tbl as d1  
   join acm_headdemandlimit_tbl as hl on (hl.id_doc = d1.id_doc)   
   join (  
   select h2.id_client,d2.month_limit, d2.id_area, max(h2.reg_date) as maxdate , max(h2.mmgg) as maxmmgg  
   from acm_headdemandlimit_tbl as h2  
   join acd_demandlimit_tbl as d2  on  (h2.id_doc = d2.id_doc)  
   left join eqm_ground_tbl as g on (g.code_eqp = d2.id_area) 
   where h2.idk_document = 600 
   and (d2.id_area is null or g.code_eqp is not null)  
   group by h2.id_client , d2.id_area, d2.month_limit order by h2.id_client  
  ) as hh on (hh.id_client = hl.id_client and hh.maxdate = hl.reg_date and hh.maxmmgg = hl.mmgg
     and hh.month_limit = d1.month_limit and hh.id_area = d1.id_area)  
     join eqm_equipment_tbl ee ON ee.id=d1.id_area
     JOIN dci_document_tbl dd ON dd.id=hl.idk_document
where hl.idk_document = 600  and d1.mmgg >='2013-01-01' and 
d1.id_client=$id_client $date1 $date2 $id_area
order by hl.id_client,id_area
LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      if (!$result) {
        echo pg_last_error();
      }
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
    <td>' . ++$Offset . '</td>
    <td>' . $row['id_area'] . '</td>
    <td class="text-right">' . nf($row['value_dem']) . '</td>
    <td>' . df($row['month_limit'], 2) . '</td>
    <td>' . df($row['reg_date']) . '</td>
    <td>' . $row['doc'] . '</td>
</tr>';
      }
      break;

    case 'clm_subabons_tbl':
      $sql = "SELECT DISTINCT s.id_point,s.id_client_sub,s.id_point_sub
 ,c.short_name AS sub_name
 ,ps.name_eqp AS sub_point
 ,p.name_eqp AS client_point
 ,m.name_eqp AS m_name_eqp
 ,i.dat_ind as ddd
 FROM clm_subabons_tbl s
 JOIN clm_client_tbl c ON c.id=s.id_client_sub
 LEFT JOIN eqm_point_tbl p ON p.code_eqp=s.id_point
 JOIN eqm_point_tbl ps ON ps.code_eqp=s.id_point_sub
 JOIN eqm_meter_tbl m ON m.id_point=ps.code_eqp
 JOIN acd_indication_tbl i ON i.id_meter=m.code_eqp
 JOIN (SELECT id_meter,MAX(dat_ind) AS max_dat_ind FROM acd_indication_tbl GROUP BY id_meter) AS iii ON (i.id_meter=iii.id_meter AND i.dat_ind=max_dat_ind)
 WHERE s.id_client=$id_client
 ORDER BY sub_name,sub_point";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$i . '</td>
  <td>' . $row['client_point'] . '</td>
  <td>' . $row['sub_name'] . '</td>
  <td>' . $row['sub_point'] . '</td>
  <td>' . $row['m_name_eqp'] . '</td>
  <td>' . df($row['ddd']) . '</td>
</tr>';
      }
      break;

    case 'acm_headindication_tbl':
      $date1 = "AND h.reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND h.reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['num_eqp'] > 0) {
        $num_eqp = "AND i.num_eqp = '" . $_SESSION['num_eqp'] . "'";
      }
      $sql = "SELECT DISTINCT h.reg_date
 ,i.num_eqp,i.dat_ind,i.value_prev,i.value,i.coef_comp,i.value_dem
 ,d.name AS idk_document
 ,e.name AS kind_energy
 ,z.name AS id_zone
 FROM acm_headindication_tbl h
 JOIN acd_indication_tbl i ON i.id_doc=h.id_doc
 JOIN dci_document_tbl d ON d.id=h.idk_document
 JOIN eqk_energy_tbl e ON e.id=i.kind_energy
 JOIN eqk_zone_tbl z ON z.id=i.id_zone
 WHERE h.id_client=$id_client $date1 $date2 $num_eqp
 ORDER BY h.reg_date DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);

//      if ($_SESSION['admin']) {
//        pre($sql);
//      }

      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$Offset . '</td>
  <td>' . df($row['reg_date']) . '</td>
  <td>' . $row['idk_document'] . '</td>
  <td>' . $row['num_eqp'] . '</td>
  <td>' . df($row['dat_ind']) . '</td>
  <td class="text-right">' . nf($row['value'] - $row['value_dem']) . '</td>
  <td class="text-right">' . nf($row['value']) . '</td>
  <td class="text-right">' . nf($row['value_dem']) . '</td>
  <td>' . nf($row['coef_comp']) . '</td>
  <td>' . $row['kind_energy'] . '</td>
  <td>' . $row['id_zone'] . '</td>
</tr>';
      }
      break;

    case 'sub_headindication':
      $date1 = "AND h.reg_date >= '" . $_SESSION['date1'] . "'";
      $date2 = "AND h.reg_date <= '" . $_SESSION['date2'] . "'";
      if ($_SESSION['sub_num_eqp'] > 0) {
        $num_eqp = "AND i.num_eqp = '" . $_SESSION['sub_num_eqp'] . "'";
      }
      $sql = "SELECT s.id_point
 ,i.num_eqp,i.dat_ind,i.value_prev,i.value,i.coef_comp,i.value_dem
 ,p.name_eqp AS client_point
 ,c.short_name AS sub_name
 ,h.reg_date
 ,e.name AS kind_energy
 ,z.name AS id_zone
 FROM clm_subabons_tbl s
 JOIN clm_client_tbl c ON c.id=s.id_client_sub
 LEFT JOIN eqm_point_tbl p ON p.code_eqp=s.id_point
 JOIN acm_headindication_tbl h ON h.id_client=s.id_client_sub
 JOIN acd_indication_tbl i ON (i.id_doc=h.id_doc AND s.id_meter_sub=i.id_meter)
 JOIN eqk_energy_tbl e ON e.id=i.kind_energy
 JOIN eqk_zone_tbl z ON z.id=i.id_zone
 WHERE s.id_client=$id_client $date1 $date2 $num_eqp
 ORDER BY h.reg_date DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$Offset . '</td>
  <td>' . $row['client_point'] . '</td>
  <td>' . $row['sub_name'] . '</td>
  <td>' . $row['num_eqp'] . '</td>
  <td>' . df($row['dat_ind']) . '</td>
  <td class="text-right">' . nf($row['value_prev']) . '</td>
  <td class="text-right">' . nf($row['value']) . '</td>
  <td class="text-right">' . nf($row['value_dem']) . '</td>
  <td>' . nf($row['coef_comp'], 0) . '</td>
  <td>' . $row['kind_energy'] . '</td>
  <td>' . $row['id_zone'] . '</td>
</tr>';
      }
      break;

    case 'tt_connect':
//      $sql = "SELECT p.name_eqp,p.addr,p.power,p.connect_power,p.code_eqp
// ,t.name AS tarif_name,p.id_tarif
// ,g.name_eqp AS id_ground
// FROM eqm_point_tbl p
// JOIN eqm_ground_tbl g ON g.code_eqp=p.id_ground
// LEFT JOIN aci_tarif_tbl t ON t.id=p.id_tarif
// WHERE p.id_client=$id_client
// ORDER BY p.id_ground,p.power DESC
// LIMIT $Limit OFFSET $Offset";
      $sql = "select eq.name_eqp,adr.adr,p.power,p.connect_power,p.code_eqp,p.id_tarif,t.name AS tarif_name
 from rep_areas_points_tbl as ap
 join eqm_equipment_tbl as eq on (eq.id= ap.id_point)
 join eqm_point_tbl as p on  (p.code_eqp= ap.id_point)
 left join adv_address_tbl as adr on (adr.id = eq.id_addres)
 LEFT JOIN aci_tarif_tbl t ON t.id=p.id_tarif
 where ap.id_client=$id_client
 ORDER BY p.power DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      if (!$result && $_SESSION['admin'] == 1) {
//        echo '<PRE>' . pg_last_error() . "<br>$sql</PRE>";
        PRE(pg_last_error() . '<br>' . $sql);
      }
      $i = $Offset;
      $page = ($Offset / $Limit) + 1;
      while ($row = pg_fetch_array($result)) {
        $bgColor = '';
        if ($id == $row['code_eqp']) {
          $bgColor = 'class="bg-success"';
        }
        $a = '<a href="?page=' . $page . '&id=' . $row['code_eqp'] . '">' . $row['name_eqp'] . '</a>';
        $r .= '<tr ' . $bgColor . '>
  <td>' . ++$i . '</td>
  
  <td>' . $a . '</td>
  <td>' . $row['adr'] . '</td>
  <td class="text-right">' . nf($row['power']) . '</td>
  
  <td>' . $row['tarif_name'] . '</td>
</tr>';
      } // <td>' . $row['id_ground'] . '</td>
      break;

    case 'connect_meter':
      $sql = "SELECT m.num_eqp
 ,mt.type AS id_type_eqp
 ,e.name AS kind_energy
 ,z.name AS zone_name
 ,mt.carry
 ,m.koef
 ,mt.term_control
 ,m.dt_control
 ,CASE WHEN m.dt_control is not null THEN m.dt_control + (text(mt.term_control)||' year')::interval ELSE NULL END as newcontrol
 FROM eqm_meter_tbl m
 JOIN eqi_meter_tbl mt ON mt.id=m.id_type_eqp
 JOIN eqd_point_energy_tbl pe ON pe.code_eqp=m.id_point
 JOIN eqk_energy_tbl e ON e.id=pe.kind_energy
 JOIN eqd_meter_zone_tbl mz ON mz.code_eqp=m.code_eqp
 JOIN eqk_zone_tbl z ON z.id=mz.zone
 WHERE id_point=$id";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$i . '</td>
  <td>' . $row['num_eqp'] . '</td>
  <td>' . $row['id_type_eqp'] . '</td>
  <td>' . $row['kind_energy'] . '</td>
  <td>' . $row['zone_name'] . '</td>
  <td>' . $row['koef'] . '</td>
  <td>' . $row['carry'] . '</td>
  <td>' . $row['term_control'] . '</td>
  <td>' . df($row['dt_control']) . '</td>
  <td>' . df($row['newcontrol']) . '</td>
</tr>';
      }
      break;

    case 'indication_ins':
      $date1 = $_SESSION['date1'];
      $date2 = date('d.m.Y');

      $sql = "SELECT DISTINCT i.id_meter
,i.id AS id_previndic
,i.id_zone
,mtp.id AS id_meter_type
,i.kind_energy
,(SELECT fun_mmgg()) AS mmgg
,i.num_eqp
,coalesce(koef_i,1)*coalesce(koef_u,1) as koef
,e.name AS kind_energy_name
,z.name AS zone_name
,mtp.type AS meter_type_name
,mtp.carry
,i.dat_ind
,i.value AS value_prev

 FROM acm_headindication_tbl h
 JOIN acd_indication_tbl i ON i.id_doc=h.id_doc
 JOIN dci_document_tbl d ON d.id=h.idk_document
 JOIN eqk_energy_tbl e ON e.id=i.kind_energy
 JOIN eqk_zone_tbl z ON z.id=i.id_zone
 join eqm_equipment_tbl as eq on (eq.id= i.id_meter)
 join eqm_meter_tbl as m on  (m.code_eqp= i.id_meter)
 join eqi_meter_tbl as mtp on (m.id_type_eqp=mtp.id)
    left join 
    ( select  CASE WHEN eq2.type_eqp = 1 THEN eq2.id WHEN eq3.type_eqp = 1 THEN eq3.id END as id_meter, 
     CASE WHEN coalesce(ic.amperage2_nom,0)=0 THEN 0 ELSE ic.amperage_nom/ic.amperage2_nom END::int as koef_i
    from 
     eqm_compensator_i_tbl as c 
     join eqm_equipment_tbl as eq on (eq.id =c.code_eqp ) 
     join eqi_compensator_i_tbl as ic on (ic.id = c.id_type_eqp) 
     left join eqm_eqp_tree_tbl as tt on (tt.code_eqp_e=c.code_eqp )   
     left join eqm_eqp_tree_tbl as tt2 on (tt2.code_eqp_e=tt.code_eqp ) 
     left join eqm_equipment_tbl as eq2 on (eq2.id =tt.code_eqp ) 
     left join eqm_equipment_tbl as eq3 on (eq3.id =tt2.code_eqp ) 
     where ic.conversion = 1 order by id_meter 
   ) as sti on (sti.id_meter = eq.id) 
    left join 
    ( select  CASE WHEN eq2.type_eqp = 1 THEN eq2.id WHEN eq3.type_eqp = 1 THEN eq3.id END as id_meter, 
    CASE WHEN coalesce(ic.voltage2_nom,0)=0 THEN 0 ELSE ic.voltage_nom/ic.voltage2_nom END::int as koef_u
    from 
     eqm_compensator_i_tbl as c 
     join eqm_equipment_tbl as eq on (eq.id =c.code_eqp ) 
     join eqi_compensator_i_tbl as ic on (ic.id = c.id_type_eqp) 
     left join eqm_eqp_tree_tbl as tt on (tt.code_eqp_e=c.code_eqp )   
     left join eqm_eqp_tree_tbl as tt2 on (tt2.code_eqp_e=tt.code_eqp ) 
     left join eqm_equipment_tbl as eq2 on (eq2.id =tt.code_eqp ) 
     left join eqm_equipment_tbl as eq3 on (eq3.id =tt2.code_eqp ) 
     where ic.conversion = 2 order by id_meter 
   ) as stu on (stu.id_meter = eq.id)

 WHERE h.id_client=$id_client AND h.reg_date >= '29.02.2016' AND h.reg_date <= '$date2'
 ORDER BY i.num_eqp ASC, i.value DESC";
      $result = pg_query($sql);

      if ($_SESSION['admin'] && $_GET['t']) {
        PRE($sql);
      }
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $_SESSION['ind'][$i] = array(
            'id_meter' => $row['id_meter'],
            'id_previndic' => $row['id_previndic'],
            'id_zone' => $row['id_zone'],
            'id_meter_type' => $row['id_meter_type'],
            'kind_energy' => $row['kind_energy'],
            'mmgg' => $row['mmgg'],
            'num_eqp' => $row['num_eqp'],
            'koef' => $row['koef'],
            'kind_energy_name' => $row['kind_energy_name'],
            'zone_name' => $row['zone_name'],
            'meter_type_name' => $row['meter_type_name'],
            'carry' => $row['carry'],
            'dat_ind' => $row['dat_ind'],
            'value_prev' => $row['value_prev']);

        $r .= '<tr>
  <td>' . ++$i . '</td>
  <td>' . $row['num_eqp'] . '</td>
  <td>' . $row['koef'] . '</td>
  <td>' . $row['kind_energy_name'] . '</td>
  <td>' . $row['zone_name'] . '</td>
  <td>' . $row['meter_type_name'] . '</td>
  <td>' . $row['carry'] . '</td>
  <td>' . df($row['dat_ind']) . '</td>
  <td class="text-right">' . nf($row['value_prev']) . '</td>';
        $r .= '<td><input type="text" name="v[]" class="form-control input-sm" value="" autocomplete="off" required></td>';
      }
      $r .= '<tr><td colspan="9"></td>
<td class="text-left"><button type="submit" class="btn btn-default btn-sm">' . translate('calc') . '</button></td>
</tr>';
      break;

    case 'indication_select':
      $sql = "SELECT * FROM acd_cabindication_tbl WHERE id_client=$id_client";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$i . '</td>
  <td>' . $row['num_eqp'] . '</td>
  <td>' . $row['koef'] . '</td>
  <td>' . $row['kind_energy_name'] . '</td>
  <td>' . $row['zone_name'] . '</td>
  <td>' . $row['meter_type_name'] . '</td>
  <td>' . $row['carry'] . '</td>
  <td>' . df($row['dat_ind']) . '</td>
  <td class="text-right">' . nf($row['value_prev']) . '</td>
  <td class="text-right">' . nf($row['value_ind']) . '</td>
  <td class="text-right">' . nf($row['calc_ind_pr']) . '</td>
</tr>';
      }
      break;

    case 'statistic_tbl':
      $sql = "SELECT * FROM statistic_tbl
 ORDER BY now DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>
  <td>' . ++$i . '</td>
  <td>' . $row['login'] . '</td>';

        if (substr($row['ip_addr'], 0, 5) != '10.71') {
          $r .= '<td><b>' . $row['ip_addr'] . '</b></td>';
        } else {
          $r .= '<td>' . $row['ip_addr'] . '</td>';
        }
        if (date('Y-m-d') == substr($row['now'], 0, 10)) {
          $r .= "<td><b>" . df($row['now'], 'time') . "</b></td>";
        } else {
          $r .= "<td>" . df($row['now'], 'all') . "</td>";
        }
        $r .= '</tr>';
      }
      break;



    case 'indication_view.php':
      $sql = "SELECT c.code,c.short_name,i.now
 FROM acd_cabindication_tbl i
 JOIN clm_client_tbl c ON (i.id_client=c.id)
 GROUP BY c.code,c.short_name,i.now
 ORDER BY now DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>';
        $r .= '<td>' . ++$i . '</td>';
        $r .= '<td>' . $row['short_name'] . '</td>';
        $r .= '<td>' . $row['code'] . '</td>';
        $r .= '<td>' . df($row['now'], 'all') . '</td>';
        $r .= '</tr>';
      }
      break;

    case 'feedback_view.php':
      $sql = "SELECT c.code,c.short_name,f.now,f.email,f.feedback
 FROM acd_cabfeedback_tbl f
 JOIN clm_client_tbl c ON (f.id_client=c.id)
 ORDER BY now DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      $i = 0;
      while ($row = pg_fetch_array($result)) {
        $r .= '<tr>';
        $r .= '<td>' . ++$i . '</td>';
        $r .= '<td>' . $row['short_name'] . '</td>';
        $r .= '<td>' . $row['code'] . '</td>';
        $r .= '<td>' . $row['email'] . '</td>';
        $r .= '<td>' . $row['feedback'] . '</td>';
        $r .= '<td>' . df($row['now'], 'all') . '</td>';
        $r .= '</tr>';
      }
      break;

    case 'download.php':
      $where = '';
      if ($_SESSION['admin'] != 1) {
        $where = "where id_client=" . $_SESSION['login'] . ' AND act=1';
      }

      $sql = "SELECT * FROM acd_cab_upload_tbl $where
 ORDER BY id DESC
 LIMIT $Limit OFFSET $Offset";
      $result = pg_query($sql);
      if (!$result) {
        echo PRE(pg_last_error());
      }
      $i = 0;
      while ($row = pg_fetch_array($result)) {

        if ($_SESSION['admin'] == 1) {
          $act = '<td>' .
                  '<
                  span class="label label-danger curp" onclick="deleteThis(\'' . $row['id'] . '\')">' . translate('delete') . '</span> ' .
                  '<span class="label label-default curp" onclick="confirmThis(\'' . $row['id'] . '\')">' . translate('confirm') . '</span>' .
                  '</td>';
        }

        if ($_SESSION['admin'] == 1 && $row['act'] == 1) {
          $act = '<td class=""><span class="label label-success">' . translate('confirmed') . '</span></td>';
        }

        $fn = './upload/' . $row['filename'];
        $ahref = '<a href="' . $fn . '" target="_blank">' . $row['filename'] . '</a>';

        $r .= '<tr>';
        $r .= '<td>' . ++$i . '</td>';
        $r .= '<td>' . $ahref . '</td>';
        $r .= '<td>' . $row['note'] . '</td>';
        $r .= $act;
        $r .= '<td>' . df($row['now'], 'all') . '</td>';
        $r .= '</tr>';
      }
      break;
  }

  return $r;
}

function getDescription($param, $id) {
  switch ($param) {
    case 'clm_client_tbl' :
      $sql = "SELECT c.id, name, short_name, code, id_state, addr_tax, addr_main, addr_local, 
       licens_num, okpo_num, tax_num, e_mail, phone, flag_taxpay, doc_num, 
       doc_dat, doc_ground, period_indicat, dt_indicat, month_indicat, 
       dt_start, day_pay_bill, tr_doc_num, tr_doc_date, tr_year_price, 
       tr_doc_type,tr_doc_period, id_section, c.id_department, mmgg_b, c.dt
from 
clm_client_tbl as c 
join clm_statecl_tbl as s on (c.id = s.id_client)
where c.idk_work not in (0,99) and coalesce(c.id_state,0) not in (50,99) and c.id=$id";
      $result = pg_query($sql);
      $row = pg_fetch_array($result);
//      $r .= ' <dt>Краткое наименование</dt><dd>' . $row['short_name'] . '</dd>';
//      $r .= ' <dt>Телефон офиса</dt><dd>' . $row['phone'] . '</dd>';
//      $r .= ' <dt>Номер свидетельства</dt><dd>' . $row['licens_num'] . '</dd>';
//      $r .= ' <dt>Налоговый номер</dt><dd>' . $row['tax_num'] . '</dd>';
      $r = '<dl class = "dl-horizontal">';
      $r .= ' <dt>' . translate('full_name') . '</dt><dd>' . $row['name'] . '</dd>';
      $r .= ' <dt>' . translate('addr_tax') . '</dt><dd> ' . $row['addr_tax'] . '<br>' . $row['addr_main'] . '</dd>';
      $r .= ' <dt>' . translate('doc_num_date') . '</dt><dd>' . $row['doc_num'] . ' (' . df($row['doc_dat']) . ')</dd>';
      $r .= ' <dt title = "' . translate('okpo_num_title') . '"><u>' . translate('okpo_num') . '</u></dt><dd>' . $row['okpo_num'] . '</dd>';
      $r .= ' <dt>' . translate('personal_code') . '</dt><dd>' . $row ['code'] . '</dd>';
      $r .= ' <dt>' . translate('period_indicat') . '</dt><dd>' . getStringFromSomething($row['period_indicat']) . '</dd>';
      $r .= ' <dt>' . translate('dt_indicat') . '</dt><dd>' . $row['dt_indicat'] . '</dd>';
      $r .= '</dl>';
      break;
  }
  return $r;
}

function getSelectOption($param, $selected) {
  $id_client = $_SESSION['id'];
  switch ($param) {
    case 'aci_pref_tbl':
      $sql = "SELECT id,name FROM aci_pref_tbl ORDER BY id";
      $r .= "<option value='-1'>Всі</option>";
      break;
    case 'eqm_ground_tbl':
      $sql = "SELECT code_eqp,name_eqp FROM eqm_ground_tbl WHERE id_client=$id_client ORDER BY power";
      $r .= "<option value='-1'>Всі</option>";
      break;
    case 'acd_indication_tbl':
      $sql = "SELECT DISTINCT num_eqp,num_eqp FROM acd_indication_tbl WHERE id_client=$id_client ORDER BY num_eqp";
      $r .= "<option value='-1'>Всі</option>";
      break;
    case 'sub_indication':
      $sql = "SELECT DISTINCT i.num_eqp,i.num_eqp
 FROM clm_subabons_tbl s
 JOIN clm_client_tbl c ON c.id=s.id_client_sub
 LEFT JOIN eqm_point_tbl p ON p.code_eqp=s.id_point
 JOIN acm_headindication_tbl h ON h.id_client=s.id_client_sub
 JOIN acd_indication_tbl i ON (i.id_doc=h.id_doc AND s.id_meter_sub=i.id_meter)
 WHERE s.id_client=$id_client
 ORDER BY i.num_eqp";
      $r .= "<option value='-1'>Всі</option>";
      break;
  }
  $result = pg_query($sql);
  while ($row = pg_fetch_array($result)) {
    if ($row[0] == $selected) {
      $r .= "<option value='$row[0]' selected>$row[1]</option>";
    } else {
      $r .= "<option value='$row[0]'>$row[1]</option>";
    }
  }

  return $r;
}

function insertStatistic($action = NULL) {
  $login = sqlString($_SESSION['login']);
  if ($_SESSION['admin'] == 1) {
    $login = sqlString('operator');
  }
  $ip = sqlString(filter_input(INPUT_SERVER, 'REMOTE_ADDR'));
  if ($action == 'index') {
    $sql = "INSERT INTO statistic_tbl (login,ip_addr) VALUES ($login,$ip)";
    pg_query($sql);
    $result = pg_query("SELECT currval('statistic_tbl_id_seq')");
    if ($result) {
      pg_fetch_row($result);
      $_SESSION['lastActionId'] = 12;
//      echo '<PRE>lastActionId: ' . $_SESSION['lastActionId'] . '</PRE>';
    }
//    else {
//      die(pg_last_error() . ' <br> ' . pg_errormessage());
//    }
  } else {
    $_SESSION['lastActionId'] = 12;
    $_SESSION['history'] = $_SESSION['history'] . "[$action] ";
    $sql = "UPDATE statistic_tbl SET action='" . $_SESSION['history'] . "' WHERE id=" . $_SESSION['lastActionId'];
    pg_query($sql);
//    echo '<PRE>lastActionId: ' . $_SESSION['lastActionId'] . '</PRE>';
  }
}

function sqlString($string, $type = NULL) {
  $r = 'NULL';
  if ($string || $string == '0') {
    switch (
    $type) {
      case 'num':
      case 'int':
      case 1:
        $r = "$string";
        break;
      default:
        $r = "'" . str_replace("'", "''", trim($string)) . "'";
        break;
    }
  }
  return $r;
}

function nf($number, $decimals = 0, $dec_point = ',', $thousands_sep = '') {
  if (strpos($number, '.') > -1) {
    $N = floatval($number);
    $decimals = 0;
    if ($N * 10 % 10 != 0) {
      $decimals = 1;
    }
    if ($N * 100 % 10 != 0) {
      $decimals = 2;
    }
    if ($N * 1000 % 10 != 0) {
      $decimals = 3;
    }
  } else {
    $N = intval($number);
  }
  return number_format($N, $decimals, $dec_point, $thousands_sep);
}

function df($date, $type = 1) {
  switch ($type) {
    case 2:
      $f = 'm.Y';
      break;
    case 'all':
      $f = 'd.m.Y - H:i';
      break;
    case 'time':
      $f = 'H:i';
      break;
    default:
      $f = 'd.m.Y';
      break;
  }
  return date($f, strtotime($date));
}

function getStringFromSomething($param) {
  switch ($param) {
    default:
    case 1:
      $r = 'кожен місяць';
      break;
    case 2:
      $r = 'кожен другий місяць';
      break;
    case 3:
      $r = 'кожен третій місяць';
      break;
  }
  return $r;
}

function numericDecimals($string) {
  $decimals = 0;
  $pos = strpos($string, '.');
  if ($pos > -1) {
    if (substr($string, $pos + 4, 1) > 4) {
      $decimals = 3;
    } elseif (substr($string, $pos + 3, 1) != 0) {
      $decimals = 3;
    } elseif (substr($string, $pos + 2, 1) != 0) {
      $decimals = 2;
    } elseif (substr($string, $pos + 1, 1) != 0) {
      $decimals = 1;
    }
  }
  return $decimals;
}

function stringFormat($string, $type, $value = NULL, $column = '') {

  switch ($type) {
    case 'text':
    default:
      if($column=='home_phone' || $column=='mob_phone' || $column=='work_phone'){
          $R = tel_normal($string);
         }
      else
          $R = $string;
      break;
    case 'input':
      $R = '<input class="form-control input input-sm" name="' . $value . '[]" value="" autocomplete="off" required>';
      break;
      case 'input_date':
          $R1 = date('Y-m-d');
          //class="dtpicker" id ="fdt_e"
          $R = '<input type="date" class="form-control" name="' . $value . '[]" value="'.$R1.'" autocomplete="off" required>';
          break;
    case 'href':
      $R = '<a href="bill_print.php?id=' . $string . '" target="_blank" class="label label-primary">Переглянути</a>';
      break;
    case 'date':
      $R = date('d.m.Y', strtotime($string));
      if($R=='01.01.1970') $R='';
      break;
    case 'date3':
      if($column=='mmgg_bill|date3')
      {
          $month=array(
              '1'=>"Січень",'2'=>"Лютий",'3'=>"Березень",
              '4'=>"Квітень",'5'=>"Травень",'6'=>"Червень",
              '7'=>"Липень",'8'=>"Серпень",'9'=>"Вересень",
              '10'=>"Жовтень",'11'=>"Листопад",'12'=>"Грудень");
              $R = date('m.Y', strtotime($string));
              $mon = (int) substr($R,0,2);
              $R = mb_strtolower($month[$mon],'UTF-8').' '.substr($R,3).' р.';


      }
      else
      $R = date('m.Y', strtotime($string));

      break;
    case 'date2':
      $R = date('d.m.Y - H:i', strtotime($string));

      break;
    case 'int':
    case 'num':
      $R = number_format($string, numericDecimals($string), ',', '');
      break;
  }
  return $R;
}

function getArrayFromBase($page, $where = NULL)
{
    $limit = '20';
    if (!$where) {
        $where = '1=1';
    }
    if ($_SESSION['lk_sap'] == 1) {
        // Если данные тянутся из САП
        require_once './ccon_soap.php';
//        $hSoap = 'http://erpqs1.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/scs/sap/zint_ws_source_mr_interact?sap-client=100'; //Quality
        $hSoap = 'http://erppr3.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A101AD1/bndg_url/sap/bc/srt/scs/sap/zint_ws_source_mr_interact?sap-client=100'; // Prod
        $lSoap = 'WEBFRGATE_CK'; /*логін*/
        $pSoap = 'sjgi5n27'; /*пароль*/
        $op_post = $_SESSION['id'];

        switch ($_SESSION['db']) {
            case 1:
                $res = 'CK0106';
                break;
            case 2:
                $res = 'CK0107';
                break;
            case 3:
                $res = 'CK0108';
                break;
            case 4:
                $res = 'CK0104';
                break;
            case 5:
                $res = 'CK0103';
                break;
            case 6:
                $res = 'CK0102';
                break;
            case 7:
                $res = 'CK0105';
                break;
            case 8:
                $res = 'CK0101';
                break;
            default:
                $res = 'CK0101';
                break;
        }

        $adapter = new ccon_soap($hSoap, $lSoap, $pSoap);
        // Получаем общую информацию
        $proc = "ZintWsMrFindAccounts";
//        $proc="ZintUplMrdataInd";
        $arr = array(
            $proc => array(
                'IvArea' => $res,//якшо пошук по ОР то тут дільн нада
                'IvCheckPeriod' => '',
                'IvCompany' => 'CK',
                'IvEic' => '', //eic
                'IvMrData' => '',
                'IvPhone' => '',  //tel
                'IvSrccode' => '05', //джерело
                'IvVkona' => $op_post,//OP
            ),
        );

        $result = objectToArray($adapter->soap_blina($arr[$proc], $proc));
//        debug($result);
//        return 'select 1';

        if (isset($result['EtAccounts']['item'])) {
            $a_account = $result['EtAccounts']['item']['Vkona'];
            $address = $result['EtAccounts']['item']['Address'];
            $eic = $result['EtAccounts']['item']['Eic'];
            $anlg = $result['EtAccounts']['item']['Anlage'];
            $fio = $result['EtAccounts']['item']['Fio'];
            if (isset($result['EtAccounts']['item']['Telephones']['item']))
                $tel = $result['EtAccounts']['item']['Telephones']['item']['TEL_NUMBER'];
            else
                $tel = '';
        }
        // Получаем информацию по счетчику и показаниям счетчика
        $proc2 = "ZintWsMrGetDeviceByanlage";
        $arr2 = array(
            $proc2 => array(
                'IvAnlage' => $anlg,
                'IvMrDate' => Date('Y-m-d'),
            ),
        );
        $result2 = objectToArray($adapter->soap_blina($arr2[$proc2], $proc2));
//        debug($result2);
//        return 'select 1';

        if (isset($result2['EvZones'])) {
            $typ_li4 = $result2['EvBauform'];
            $counterSN = $result2['EvSernr'];
            $zonna = $result2['EvZones'];
            $EvEqunr = $result2['EvEqunr'];
            $EvFactor = $result2['EvFactor'];
            $EvMaxmr = $result2['EvMaxmr'];
            $single_zone=1;  // Признак однозонного счетчика
            if(isset($result2['EtScales']['item'][0]['MrvalPrev'])) {
                $single_zone=0;  // Не однозонный счетчик
                $MrvalPrev = 0;
                $MrdatPrev = '';
                $Zwart='';
            }
            $MrvalPrev = $result2['EtScales']['item']['MrvalPrev'];
            $MrdatPrev = $result2['EtScales']['item']['MrdatPrev'];
            $Zwart = $result2['EtScales']['item']['Zwart'];
        }
    }

//        debug($single_zone);
//        return 'select 1';


        switch ($page) {
            // Определяем как давно вводились последние показания (сколько прошло дней)
            case 'how_long':
//          $R="select max(a.now::date) as pred,now()::date as today,(now()::date-max(a.now::date)) as days
//          from acd_cabindication_tbl a
//          inner join clm_paccnt_tbl paccnt on
//          a.id_paccnt=paccnt.id
//          where $where limit 1";
                if ($_SESSION['lk_sap'] == 0) {
                    $R = "select * from
                      (select *,max(pred) over() as last from
                      (select max(a.dat_ind::date) as pred,now()::date as today,(now()::date-max(a.dat_ind::date)) as days,value_ind
                      from acd_cabindication_tbl a
                      inner join clm_paccnt_tbl paccnt on
                      a.id_paccnt=paccnt.id
                      where $where
                      group by value_ind) q ) qq
                      where pred=last limit 1";
                }
                if ($_SESSION['lk_sap'] == 1) {
                    $where = str_replace('paccnt', 'a', $where);
                    $R = "
                       select * from
                      (select *,max(pred) over() as last from
                      (select max(a.dat_ind::date) as pred,now()::date as today,(now()::date-max(a.dat_ind::date)) as days,value_ind
                      from acd_cabindication_tbl a
                      where $where
                      group by value_ind) q ) qq
                      where pred=last limit 1";
                }

                $f = fopen('aaa_a', 'w+');
                fputs($f, $R);
//                debug($R);
                break;

            case 'user1':
//      Выборка из таблицы абонентов (персон. данные)
                if ($_SESSION['lk_sap'] == 0) {
                    $R = "select a.last_name||' '||a.name||' '||a.patron_name AS fio
             ,paccnt.code AS lic
             ,address_print_full(a.addr_reg,3) AS addr_reg1
             ,address_print_full(a.addr_live,3) AS addr_live
             ,a.s_doc||' '||a.n_doc AS pasport
             ,a.dt_doc ,a.who_doc ,a.tax_number
             ,a.home_phone ,a.work_phone ,a.mob_phone,get_address(paccnt.addr,2) as addr_reg
             from clm_abon_tbl a
             join clm_paccnt_tbl paccnt ON paccnt.id_abon=a.id
             where $where and paccnt.archive='0'
             limit $limit";
                } else {
                    // Если данные тянутся из САП
                    $fio = str_replace("'","`",$fio);
                    $address = str_replace("'","`",$address);
                    $R = "select '$fio' AS fio
             ,'$a_account' AS lic
             ,'$address' AS addr_reg1
             ,'$address' AS addr_live
             ,'' AS pasport
             ,'' as dt_doc ,'' as who_doc ,'' as tax_number
             ,'' as home_phone ,'' as work_phone ,'$tel' as mob_phone,'$address' as addr_reg";
                }
//                 echo $R;
//                          return;

                break;
            case 'user2':
//      Выборка из таблицы лиц. счетов, сальдо (персон. данные)
                if ($_SESSION['lk_sap'] == 0) {
                    $R = "select distinct paccnt.code AS lic,paccnt.eic
             ,address_print_full(paccnt.addr,3) AS addr
             ,b.nm as id_gtar
             ,paccnt.idk_house
             ,paccnt.heat_area
             ,c.e_val as saldo
             ,e.date_agreem,substr(name,strpos(p.name, ' ')) as tp
             from clm_paccnt_tbl paccnt
             join aqi_grptar_tbl b ON (b.id=paccnt.id_gtar)
             join acm_saldo_tbl c ON (c.id_paccnt=paccnt.id and c.mmgg=fun_mmgg())
             left join lgm_abon_tbl d ON (d.id_paccnt=paccnt.id)
             left join clm_agreem_tbl e ON (e.id_paccnt=paccnt.id)
             left join prs_runner_paccnt t on paccnt.id=t.id_paccnt
             left join prs_runner_sectors p on t.id_sector=p.id
             where $where and paccnt.archive='0'
             limit $limit";
                } else {
                    // Если данные тянутся из САП
                    $R = "select  '$a_account' AS lic,'$eic' as eic
             ,'$address' as addr
             ,'''' as id_gtar
             ,'' as idk_house
             ,'' as heat_area
             ,'' as saldo
             ,'' as date_agreem,'' as tp";
                }
//                 echo "<PRE>$R</PRE>";

//          $f=fopen('aaa_tp','w+');
//          fputs($f,$R);

                break;

            case 'other_lic':
//      Определение дополнительных лицевых счетов по абоненту
                $where1 = str_replace('and mmgg', '--', $where);
                $where2 = substr($where1, -11);


                $R = "select z.code,address_print_full(z1.addr,3) AS addr from  
      (select distinct last_name,name,patron_name,code from
      (select a.last_name,a.name,a.patron_name,b.code,count(*) over(partition by a.last_name,a.name,a.patron_name) as kol from clm_abon_tbl a
    left join clm_paccnt_tbl b on a.id=b.id_abon where b.code is not null and length(b.code)=9) q
    where kol>1 and code in(select y.code from acd_cabindication_tbl x join clm_paccnt_tbl y on x.id_paccnt=y.id)
    and last_name||' '||name||' '||patron_name in(select a.last_name||' '||a.name||' '||a.patron_name 
     from clm_abon_tbl a
     join clm_paccnt_tbl paccnt ON paccnt.id_abon=a.id
     where paccnt.archive='0' and $where1) and code<>$where2) z 
      left join clm_paccnt_tbl z1 on z.code=z1.code
      order by 1";

                // echo "<PRE>$R</PRE>";

                $f = fopen('aaa_lc', 'w+');
                fputs($f, $R);

                break;

            case 'user3':
                $R = "select d.fio_lgt as lgt ,d.family_cnt ,d.ident_cod_l ,d.dt_start ,d.dt_end ,f.name
 from clm_paccnt_tbl paccnt
 join lgm_abon_tbl d ON (d.id_paccnt=paccnt.id)
 join lgi_group_tbl f ON (f.id=d.id_grp_lgt)
 where $where and paccnt.archive='0'
 limit $limit";
                break;
            case 'meter1':
//    Выборка информации по счетчику
                if ($_SESSION['lk_sap'] == 0) {
                    $R = "select num_meter,em.name AS type_name,z.note AS zone_name,m.carry,m.dt_b
                                 from clm_meterpoint_tbl m
                                 join clm_meter_zone_tbl mz ON (mz.id_meter=m.id)
                                 join clm_paccnt_tbl paccnt ON (paccnt.id=m.id_paccnt)
                                 join eqk_zone_tbl z ON (z.id=mz.id_zone)
                                 join eqi_meter_tbl em ON (em.id=m.id_type_meter)
                                 where $where
                                 limit $limit";
                }
                    else
                    {
                        $rozr_cnt = strlen($EvMaxmr);
                        $R = "select '$counterSN' as num_meter,'$typ_li4' AS type_name,'$zonna' AS zone_name,
                            $rozr_cnt as carry,'' as dt_b
                                ";
                    }


//      echo $R;
                break;
// Запрос для отображения таблицы: Ваші оплати в п. меню Рахунки/Оплати
            case 'pay1':
                $R = "select d.name AS doc_name,reg_date as pay_reg_date,value as pay_sum
 from acm_pay_tbl p
 join clm_paccnt_tbl paccnt ON (paccnt.id=p.id_paccnt)
 join dci_doc_tbl d ON (d.id=p.idk_doc)
 where $where AND p.id_headpay IS NOT NULL AND id_pref=10 AND (idk_doc=100 OR idk_doc=110)
 order by pay_date desc
 limit 10";
//      echo "<PRE>$R</PRE>";

                break;
            case 'bill1':
//      Запрос для отображения таблицы: Виставлені рахунки в п. меню Рахунки/Оплати
//      $R = "select d.name AS doc_name,mmgg_bill,value as sum_add,b.id_doc,b.demand
// from acm_bill_tbl as b
// join clm_paccnt_tbl paccnt ON (paccnt.id=b.id_paccnt)
// join dci_doc_tbl d ON (d.id=b.idk_doc)
// where $where AND b.id_pref = 10
// and ( b.idk_doc = 200 or (b.idk_doc = 220 and b.value_calc >=0)) and mmgg_bill>='2019-01-01'
// and not exists (select c.id_doc from acm_bill_tbl as c where c.id_paccnt = b.id_paccnt and c.id_corr_doc = b.id_doc)
// order by reg_date desc
// limit 6";

                $R = "select mmgg_bill,sum(demand0) as demand0,sum(demand10) as demand10,sum(demand9) as demand9,
 sum(demand8) as demand8,sum(demand7) as demand7,sum(demand6) as demand6, 
 sum(coalesce(demand0,0))+sum(coalesce(demand10,0))+sum(coalesce(demand9,0))+
 sum(coalesce(demand8,0))+sum(coalesce(demand7,0))+sum(coalesce(demand6,0)) as demand_all from
 (select i.mmgg as mmgg_bill,case when i.id_zone=0 then sum(value_diff)::int end as demand0,
 case when i.id_zone=10 then sum(value_diff)::int end as demand10,
 case when i.id_zone=9 then sum(value_diff)::int  end as demand9,
 case when i.id_zone=8 then sum(value_diff)::int end as demand8 ,
 case when i.id_zone=7 then sum(value_diff)::int end as demand7,
 case when i.id_zone=6 then sum(value_diff)::int end as demand6
 from acm_indication_tbl i
 join clm_paccnt_tbl paccnt ON (paccnt.id=i.id_paccnt)
 where $where and mmgg>'2018-12-31'
 group by i.mmgg,i.id_zone) x
 group by mmgg_bill
 order by 1 desc";

            $R = "select mmgg_bill,sum(demand0) as demand0,sum(demand10) as demand10,sum(demand9) as demand9,
 sum(demand8) as demand8,sum(demand7) as demand7,sum(demand6) as demand6, 
 sum(coalesce(demand0,0))+sum(coalesce(demand10,0))+sum(coalesce(demand9,0))+
 sum(coalesce(demand8,0))+sum(coalesce(demand7,0))+sum(coalesce(demand6,0)) as demand_all from
        (select '01.'||case when i.month<10 then '0' end || i.month ||'.'||i.year as mmgg_bill,case when i.id_zone=0 then sum(value_diff)::int end as demand0,
 case when i.id_zone=10 then sum(value_diff)::int end as demand10,
 case when i.id_zone=9 then sum(value_diff)::int  end as demand9,
 case when i.id_zone=8 then sum(value_diff)::int end as demand8 ,
 case when i.id_zone=7 then sum(value_diff)::int end as demand7,
 case when i.id_zone=6 then sum(value_diff)::int end as demand6
 from
 (select a.*,(a.value_ind-b.value_ind) as value_diff from
     (select Extract(year from dat_ind) as year,Extract(MONTH from dat_ind) as month,code,id_zone,max(dat_ind) as dat_ind,max(value_ind) as value_ind
from acd_cabindication_tbl paccnt
where $where
group by 1,2,code,id_zone
order by 1 desc,2 desc) a
left join
     (select Extract(year from dat_ind) as year,Extract(MONTH from dat_ind) as month,code,id_zone,max(dat_ind) as dat_ind,max(value_ind) as value_ind
from acd_cabindication_tbl paccnt
where $where
group by 1,2,code,id_zone
order by 1 desc,2 desc) b 
on a.code=b.code and case when b.month=12 then b.month=1 and b.year=a.year-1 else b.month=a.month-1 and b.year=a.year end and a.id_zone=b.id_zone
where (a.value_ind-b.value_ind)>0) i
 group by i.month,i.year,i.id_zone) x
 group by mmgg_bill
 order by 1 desc";

//      echo "<PRE>$R</PRE>";
                $f = fopen('aaa', 'w+');
                fputs($f, $R);

                break;
            case 'indication':
                if ($_SESSION['lk_sap'] == 0) {
                    $R = "select distinct i.id_paccnt,i.id_meter,i.id_prev,i.id_zone,i.id_typemet,i.id_energy,i.mmgg,z.note AS zone_name,
 m.num_meter,1 AS koef,i.carry,i.dat_ind as dat_prev_ind,vp.value AS value_prev,i.value AS value_ind,i.value AS calc_ind_pr,
 fun_mmgg() AS current_mmgg,max(q.value_new) as val_new,q.dat_ind as dat_new
 from acm_indication_tbl i
 join clm_paccnt_tbl paccnt ON (paccnt.id=i.id_paccnt)
 join clm_meterpoint_tbl m ON (m.id=i.id_meter)
 join clm_meter_zone_tbl mz ON (mz.id_meter=m.id)
 join eqk_zone_tbl z ON (z.id=mz.id_zone and i.id_zone=z.id)
 left join acm_indication_tbl vp ON (vp.id=i.id_prev)
 join (select id_paccnt,max(dat_ind) as dat_dd,id_zone from acm_indication_tbl 
 --where id_operation not in(23,14,25,26) 
 group by id_paccnt,id_zone) acm ON (i.id_paccnt=acm.id_paccnt and i.id_zone=acm.id_zone)
 left join
 (select aa.id_paccnt,aa.dat_ind,aa.id_zone,aa.num_eqp,ab.value_ind as value_new from
(select id_paccnt,trim(num_eqp) as num_eqp,carry,max(dat_ind) as dat_ind,id_zone,max(mmgg) as mmgg
 from acd_cabindication_tbl c
 join clm_paccnt_tbl paccnt ON (paccnt.id=c.id_paccnt)
 where $where and 
 dat_ind in(select max(dat_ind) from acd_cabindication_tbl,clm_paccnt_tbl paccnt
 where paccnt.id=acd_cabindication_tbl.id_paccnt and $where)
 group by id_paccnt,trim(num_eqp),carry,id_zone) aa
 join acd_cabindication_tbl ab on 
 aa.id_paccnt=ab.id_paccnt and aa.id_zone=ab.id_zone and aa.dat_ind=ab.dat_ind and aa.mmgg=ab.mmgg
 ) q on i.id_paccnt=q.id_paccnt and q.id_zone=i.id_zone 
 where $where and i.dat_ind=dat_dd 
 group by 
 i.id_paccnt,i.id_meter,i.id_prev,i.id_zone,i.id_typemet,i.id_energy,i.mmgg,z.note,
 m.num_meter,i.carry,i.dat_ind ,vp.value,i.value,q.dat_ind 
 order by i.mmgg desc,i.value desc
 limit $limit";

                }
                else
                {
                    // Если тянем данные из САП
                   // Получаем данные по потреблению для извлечения предыдущих показаний
                    $conn="host=192.168.54.7 port=5432 dbname=cek user=cabinet password=25cabinet_new_password! connect_timeout=5";  // Реальный сервер call - центра
                    $cc_c = pg_connect($conn);
                    $lic = $_SESSION['id'];
                    $sql_c="select a.sapid,b.zonity from accounts a
                    left join counter b on a.accountid=b.accountid
                    where a.account='$lic' and a.fu=1";

                    $result = pg_query($cc_c,$sql_c);
                    $row = pg_fetch_array($result);
                    $cnt_sch = $row[0];   // Контокоррентный счет из САПа
                    $q_zones =  $row[1];  // кол-во зон
                    switchServerConnect();
                    $hSoap='http://erppr3.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A1011D1/bndg_url/sap/bc/srt/scs/sap/zint_ws_cconline_exp?sap-client=100';
//                    $hSoap='http://erpqs1.esf.ext:8000/sap/bc/srt/wsdl/flv_10002A1011D1/bndg_url/sap/bc/srt/scs/sap/zint_ws_cconline_exp?sap-client=100';
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
//            debug($result);
//            debug($result['EtMeterData']['item']);
                    if(!isset($result['EtMeterData']['item'][0]))
                        $result['EtMeterData']['item'][0]=$result['EtMeterData']['item'];

//            debug($result['EtMeterData']['item']);
//                    $q_zones = $result['EtDeviceInfo']['item']['Zones'] ;  // кол-во зон
                    // Сборка данных
                    $i=0;
                    $j=0;
                    $t='';
                    foreach ($result['EtMeterData']['item'] as $v){
                        if ($q_zones>1)
                            if(trim($v['Zwart'])=='ЗГ') continue;

                        if(format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']) <> $t) $j++;
//                debug($v);
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
//                        $i=$i+10;
                                break;
                            default:
                                $mas[$i]['zone'] = $v['Zwart'];
                        }
                        $mas[$i]['period'] =  format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']);
                        $mas[$i]['cons'] = $v['Mrcon'];
                        $mas[$i]['order'] = $j;
                        $mas[$i]['val'] = $v['Mrval'];
                        $mas[$i]['val_prev'] = $v['MrvalPrev'];
                        $mas[$i]['dat_prev'] =format_date($v['Adat']);
                        $i++;
                        $t= format_date($v['MrdatPrev'])  . ' - ' . format_date($v['Adat']);
                    }

                    // Правильная сортировка в случае замены счетчика
                    $prev_begin='';
                    $p_end = '';
                    $index = -1;
                    foreach ($mas as $k=>$v) {
                        $period = $v['period'];
                        $p_begin = substr($period,0,10);
                        $p_end = trim(substr($period,13));
                        if($k==0) {
                            $a_begin = $p_end;
                        }
                        if($p_begin=='00.00.0000') $p_begin = '01.03.2021';
                        $p1=date("d.m.Y", strtotime($p_begin));
                        $p2=date("d.m.Y", strtotime($p_end));

                        if(strlen($prev_begin)>0){
                            if(strtotime($p1)>strtotime($prev_begin)){
                                $index = $k;
//                                break;
                            }
                        }

                        $prev_begin = $p1;
                        $prev_end = $p2;
                    }
                    $y = count($mas);
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
                    // Формируем новый порядок в случае замены счетчика
                    if($index<>-1) {
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
                    $mas = sort_nested_arrays($mas1,['order'=>'asc','zone1'=>'asc']);

//                    debug($mas);
//                    return;
                    // Узнаем последние предыдущие показания
//                debug($mas);
                $val_prev = $mas[0]['val'];
                if(is_null($val_prev)) $val_prev = 0;
                $dat_prev = $mas[0]['dat_prev'];
                $val_prev1 = 0;
                $val_prev2 = 0;
                $val_prev3 = 0;
                $m=0;
                for($i=0;$i<count($mas);$i++) {
                    if($mas[$i]['val']!=$mas[$i]['val_prev']) {
                        if($mas[$i]['zone1']==4 && $zonna==1) {
                            $val_prev = $mas[$i]['val'];  // Общая зона (1-зонные счетчики)
                            break;
                        }
                        if($mas[$i]['zone1']==1) {
                            $val_prev1 = $mas[$i]['val'];  // День
                            $m++;
                        }
                        if($mas[$i]['zone1']==3) {
                            $val_prev2 = $mas[$i]['val'];  // Ночь
                            $m++;
                        }
                        if($mas[$i]['zone1']==2) {
                            $val_prev3 = $mas[$i]['val'];  // Полупик
                            $m++;
                        }
                        $dat_prev = $mas[$i]['dat_prev'];
                        if($m==$zonna)
                             break;
                    }
                }

//                debug($val_prev1);
//                debug($val_prev2);
//                debug($val_prev3);

//                    debug('single_zone '.$single_zone);
                    $rozr_cnt = strlen($EvMaxmr);
                    if($single_zone==1) {
                        // Если однозонный счетчик
                        $R = "select  -12345678 as id_paccnt,-23456789 as id_meter,0 as id_prev,
                         0 as id_zone,-1234 as id_typemet,10 as id_energy,
                         '$counterSN'::text as num_meter,'Загальна'::text as zone_name,1 AS koef,$rozr_cnt as carry,'$MrdatPrev'::date as dat_prev_ind,$MrvalPrev AS value_prev,
                         $MrvalPrev  AS value_ind1,0 AS calc_ind_pr,
                         fun_mmgg() AS current_mmgg,0 as val_new1,''::text as dat_new,
                         (select max(dat_ind) as dat_prev from acd_cabindication_tbl paccnt where $where) as dat_prev1,
                         '$dat_prev'::text as dat_prev,$val_prev as val_prev";
//                        debug($R);
                    }
                    else{
                        // Если многозонный счетчик
                        $y= count($result2['EtScales']['item']);
                        $R='';
                        for($i=0;$i<$y;$i++)
                        { $par1=$result2['EtScales']['item'][$i]['Zwart'];
                            $MrdatPrev = $result2['EtScales']['item'][$i]['MrdatPrev'];
                            $MrvalPrev = $result2['EtScales']['item'][$i]['MrvalPrev'];
                            if($i!=($y-1))
                            $R1 = "
                        select  -12345678 as id_paccnt,-23456789 as id_meter,0 as id_prev, 
                              case when '$par1'='НП' then 7
                          when '$par1'='НЧ' and $y=3 then 6 
                          when '$par1'='НЧ' and $y=2 then 9 
                          when '$par1'='ПК' then 8
                          when '$par1'='ДН' then 10 end as id_zone,
                          case when '$par1'='НП' then $val_prev3
                          when '$par1'='НЧ' and $y=3 then $val_prev2 
                          when '$par1'='НЧ' and $y=2 then $val_prev2
                          when '$par1'='ПК' then $val_prev1
                          when '$par1'='ДН' then $val_prev1 end as val_prev,
                          '$dat_prev' as dat_prev,
                          -1234 as id_typemet,10 as id_energy,
                         '$counterSN' as num_meter,
                         case when '$par1'='НП' then 'Напівпік'
                          when '$par1'='НЧ' then 'Ніч' 
                          when '$par1'='ПК' then 'Пік'
                          when '$par1'='ДН' then 'День' end as zone_name,
                          1 AS koef,$rozr_cnt as carry,'$MrdatPrev' as dat_prev_ind,$MrvalPrev AS value_prev,
                         $MrvalPrev  AS value_ind1,0 AS calc_ind_pr,
                         fun_mmgg() AS current_mmgg,0 as val_new1,'' as dat_new,
                         (select max(dat_ind) as dat_prev from acd_cabindication_tbl paccnt where $where) as dat_prev1
                         union
                         ";
                            else
//                                Для последней записи union не добавляем
                              $R1 = "select  -12345678 as id_paccnt,-23456789 as id_meter,0 as id_prev,
                               case when '$par1'='НП' then 7
                          when '$par1'='НЧ' and $y=3 then 6 
                          when '$par1'='НЧ' and $y=2 then 9 
                          when '$par1'='ПК' then 8
                          when '$par1'='ДН' then 10 end as id_zone,
                          case when '$par1'='НП' then $val_prev3
                          when '$par1'='НЧ' and $y=3 then $val_prev2 
                          when '$par1'='НЧ' and $y=2 then $val_prev2
                          when '$par1'='ПК' then $val_prev1
                          when '$par1'='ДН' then $val_prev1 end as val_prev,
                          '$dat_prev' as dat_prev,
                          -1234 as id_typemet,10 as id_energy,
                         '$counterSN' as num_meter,case when '$par1'='НП' then 'Напівпік'
                          when '$par1'='НЧ' then 'Ніч' 
                          when '$par1'='ПК' then 'Пік'
                          when '$par1'='ДН' then 'День' end as zone_name,
                          1 AS koef,$rozr_cnt as carry,'$MrdatPrev' as dat_prev_ind,$MrvalPrev AS value_prev,
                         $MrvalPrev  AS value_ind1,0 AS calc_ind_pr,
                         fun_mmgg() AS current_mmgg,0 as val_new1,'' as dat_new,
                         (select max(dat_ind) as dat_prev from acd_cabindication_tbl paccnt where $where) as dat_prev1";
                            $R.=$R1;

                        }
                    }
                }
//                  '$dat_prev' as dat_prev,$val_prev as val_prev
                $R = 'select * from ('.$R.') p order by id_zone desc';
                $R = "select distinct w.*,max(paccnt.value_ind) over() as value_ind,max(paccnt.value_ind) over() as val_new
                         from (" . $R .') w ' .
                    'left join acd_cabindication_tbl paccnt on w.dat_prev1=paccnt.dat_ind and w.id_zone=paccnt.id_zone and ' . $where .
                    ' order by id_zone desc';

//                $R = 'select * from ('.$R.') p order by id_zone desc';
//                $R = "select distinct w.*,paccnt.value_ind as value_ind,paccnt.value_ind as val_new
//                         from (" . $R .') w ' .
//                    'left join acd_cabindication_tbl paccnt on w.dat_prev1=paccnt.dat_ind and w.id_zone=paccnt.id_zone and ' . $where .
//                    ' order by id_zone desc';
//             debug($R);

                $f = fopen('aaa_', 'w+');
                fputs($f, $R);

                $result = pg_query($R);
                unset($_SESSION['abon']);
                $i = 0;
                while ($row = pg_fetch_array($result)) {
                    $_SESSION['abon'][$i]['dat_prev'] = $row['dat_prev'];
                    $_SESSION['abon'][$i]['code'] = $_SESSION['id'];
                    $_SESSION['abon'][$i]['id_paccnt'] = $row['id_paccnt'];
                    $_SESSION['abon'][$i]['id_meter'] = $row['id_meter'];
                    $_SESSION['abon'][$i]['id_previndic'] = $row['id_prev'];
                    $_SESSION['abon'][$i]['id_zone'] = $row['id_zone'];
                    $_SESSION['abon'][$i]['id_meter_type'] = $row['id_typemet'];
                    $_SESSION['abon'][$i]['kind_energy'] = $row['id_energy'];
                    $_SESSION['abon'][$i]['mmgg'] = $row['current_mmgg'];
                    $_SESSION['abon'][$i]['num_eqp'] = $row['num_meter'];
                    $_SESSION['abon'][$i]['koef'] = $row['koef'];
                    $_SESSION['abon'][$i]['carry'] = $row['carry'];
                    $_SESSION['abon'][$i]['dat_ind'] = date('Y-m-d'); //$row['dat_ind'];
//                    $_SESSION['abon'][$i]['dat_ind'] = $row['dat_prev'];
                    //$_SESSION['abon'][$i]['date_new'] = date('Y-m-d'); //$row['dat_ind'];
//                    $_SESSION['abon'][$i]['value_prev'] = $row['value_ind'];
                    $_SESSION['abon'][$i]['value_prev'] = $row['value_prev'];
                    $i++;
                }
//                debug($_SESSION['abon']);
                break;
            case 'indication2':
                $where = str_replace('and mmgg', '--', $where);
                $q1=strpos($where,'--');
                $where = substr($where,0,$q1);
                $id_pc = substr($where, 13, 9);

                if ($_SESSION['lk_sap'] == 0) {
                    $R = " select * from
                                (select *, row_number() OVER() AS rownum from
                                (select distinct c.num_eqp,c.carry,c.dat_ind::date as dat_ind,i.value as value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                c.value_ind as value_new,
                                case when (c.value_ind-i.value)<0 then 0 else c.value_ind-i.value end as value_diff,i.id_prev,i.id_paccnt,j.dat_ind as dat_prev
                             from acd_cabindication_tbl c
                             join clm_paccnt_tbl paccnt ON (paccnt.id=c.id_paccnt)
                             left join (select id_paccnt,id_zone,max(dat_ind) as dat_ind from acm_indication_tbl
                             where id_paccnt in (select id from clm_paccnt_tbl where code='$id_pc') group by id_paccnt,id_zone) j on c.id_paccnt=j.id_paccnt and c.id_zone=j.id_zone 
                             left join acm_indication_tbl i on j.id_paccnt=i.id_paccnt and j.id_zone=i.id_zone and i.dat_ind=j.dat_ind
                             where $where
                             order by 3 desc,value_new desc) w) w1
                             where rownum <= case when id_zone in(9,10) then 2
                              when id_zone in(6,7,8) then 3 
                              when id_zone in(0) then 1 end";
                }
                else
                    {
                        $where = str_replace('paccnt', 'c', $where);
                        if($single_zone==1)
//                      $R = " select * from
//                                (select *, row_number() OVER() AS rownum from
//                                (select distinct c.num_eqp,c.carry,c.dat_ind::date as dat_ind,c.value_ind as value_prev,def_zone(c.id_zone) as zone,c.id_zone,
//                                $MrvalPrev  as value_new,
//                                case when ($MrvalPrev - c.value_ind)<0 then 0 else $MrvalPrev - c.value_ind end as value_diff,'$MrdatPrev' as dat_prev
//                             from acd_cabindication_tbl c
//                             where $where
//                             order by 3 desc,c.value_ind desc) w) w1
//                             where rownum <= case when id_zone in(9,10) then 2
//                              when id_zone in(6,7,8) then 3
//                              when id_zone in(0) then 1 end";
                        $sql = "select c1.value_ind as value_prev,c1.code,c1.id_zone,c1.dat_ind  from acd_cabindication_tbl c1 join
                                                        (select max(c.dat_ind) as dat_ind,c.id_zone,c.code from acd_cabindication_tbl c
                                    where $where  and c.dat_ind<(select max(dat_ind) from acd_cabindication_tbl c
                                    where $where )
                                    group by c.id_zone,c.code) q on
                                    c1.code=q.code and c1.dat_ind=q.dat_ind and c1.id_zone=q.id_zone";

                        $result = pg_query($sql);
                        $row = pg_fetch_array($result);
                        $flag_join='join';
                        if(empty($row['value_prev']) || is_null($row['value_prev']))  $flag_join='left join';

                        $R = " select * from
                                         (select *, row_number() OVER() AS rownum from
                                          (select distinct c.num_eqp,c.carry,c.dat_ind::date as dat_prev,x.value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                    $MrvalPrev  as value_new,
                                    case when ($MrvalPrev - c.value_ind)<0 then 0 else $MrvalPrev - c.value_ind end as value_diff,'$MrdatPrev' as dat_ind
                                    from acd_cabindication_tbl c $flag_join
                                                        (select c1.value_ind as value_prev,c1.code,c1.id_zone,c1.dat_ind  from acd_cabindication_tbl c1 join
                                                        (select max(c.dat_ind) as dat_ind,c.id_zone,c.code from acd_cabindication_tbl c
                                    where $where and c.dat_ind<(select max(dat_ind) from acd_cabindication_tbl c
                                    where $where)
                                    group by c.id_zone,c.code) q on
                                    c1.code=q.code and c1.dat_ind=q.dat_ind and c1.id_zone=q.id_zone
                                    ) x on c.code=x.code and c.dat_ind=x.dat_ind and c.id_zone=x.id_zone
                                    where $where 
                                    ) w) w1";


                        if($single_zone==0) {
                            // Если многозонный счетчик
                            $y = count($result2['EtScales']['item']);
                            $R = '';
                            for ($i = 0; $i < $y; $i++) {
                                $par1 = $result2['EtScales']['item'][$i]['Zwart'];
                                $MrdatPrev = $result2['EtScales']['item'][$i]['MrdatPrev'];
                                $MrvalPrev = $result2['EtScales']['item'][$i]['MrvalPrev'];

                                if(1==1) {
                                    if ($i != ($y - 1))
                                        $R1 = " select * from (
                                 select * from
                                (select *, row_number() OVER() AS rownum from
                                (select distinct case when '$par1'='ДН' then 10
                                when '$par1'='НЧ' and $zonna=2 then 9 
                                when '$par1'='НП' then 7
                                when '$par1'='ПК' then 8
                                when '$par1'='НЧ' and $zonna=3 then 6
                                end as zone_sap, 
                                c.num_eqp,c.carry,c.dat_ind::date as dat_prev,c.value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                $MrvalPrev as value_new,
                                case when ($MrvalPrev-c.value_prev)<0 then 0 else $MrvalPrev-c.value_prev end as value_diff,'$MrdatPrev' as dat_ind
                             from acd_cabindication_tbl c
                             where $where
                             order by 4 desc,c.value_prev desc) w) w1
                             where id_zone=zone_sap 
                             limit 1 ) q 
                              union
                              ";
                                    else
//                                Для последней записи union не добавляем
                                        $R1 = " select * from (
                            select * from
                                (select *, row_number() OVER() AS rownum from
                                (select distinct case when '$par1'='ДН' then 10
                                when '$par1'='НЧ' and $zonna=2 then 9 
                                when '$par1'='НП' then 7
                                when '$par1'='ПК' then 8
                                when '$par1'='НЧ' and $zonna=3 then 6 end as zone_sap, 
                                c.num_eqp,c.carry,c.dat_ind::date as dat_prev,c.value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                $MrvalPrev as value_new,
                                case when ($MrvalPrev-c.value_prev)<0 then 0 else $MrvalPrev-c.value_prev end as value_diff,'$MrdatPrev' as dat_ind
                             from acd_cabindication_tbl c
                             where $where
                             order by 4 desc,c.value_prev desc) w) w1
                              where id_zone=zone_sap 
                              limit 1 ) q  ";
                               }
//      Экспериментальный код
                                if(1==2) {
                                    if ($i != ($y - 1))
                                        $R1 = " select * from (
                                 select * from
                                (select *, row_number() OVER() AS rownum from
                                (select distinct case when '$par1'='ДН' then 10
                                when '$par1'='НЧ' and $zonna=2 then 9 
                                when '$par1'='НП' then 7
                                when '$par1'='ПК' then 8
                                when '$par1'='НЧ' and $zonna=3 then 6
                                end as zone_sap, 
                                c.num_eqp,c.carry,c.dat_ind::date as dat_prev,x.value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                $MrvalPrev as value_new,
                                 case when ($MrvalPrev - c.value_ind)<0 then 0 else $MrvalPrev - c.value_ind end as value_diff,
                                '$MrdatPrev' as dat_ind
                             from acd_cabindication_tbl c join 
                             (select c1.value_ind as value_prev,c1.code,c1.id_zone,c1.dat_ind  from acd_cabindication_tbl c1 join
                                                        (select max(c.dat_ind) as dat_ind,c.id_zone,c.code from acd_cabindication_tbl c
                                    where $where and c.dat_ind<(select max(dat_ind) from acd_cabindication_tbl c
                                    where $where)
                                    group by c.id_zone,c.code) q on
                                    c1.code=q.code and c1.dat_ind=q.dat_ind and c1.id_zone=q.id_zone
                                    ) x on c.code=x.code and c.dat_ind=x.dat_ind and c.id_zone=x.id_zone
                             where $where
                             ) w) w1
                             where id_zone=zone_sap 
                             limit 1 ) q 
                              union
                              ";
                                    else
//                                Для последней записи union не добавляем
                                        $R1 = " select * from (
                            select * from
                                (select *, row_number() OVER() AS rownum from
                                (select distinct case when '$par1'='ДН' then 10
                                when '$par1'='НЧ' and $zonna=2 then 9 
                                when '$par1'='НП' then 7
                                when '$par1'='ПК' then 8
                                when '$par1'='НЧ' and $zonna=3 then 6 end as zone_sap, 
                                c.num_eqp,c.carry,c.dat_ind::date as dat_prev,x.value_prev,def_zone(c.id_zone) as zone,c.id_zone,
                                $MrvalPrev as value_new,
                                case when ($MrvalPrev - c.value_ind)<0 then 0 else $MrvalPrev - c.value_ind end as value_diff,
                                '$MrdatPrev' as dat_ind
                             from acd_cabindication_tbl c join
                              (select c1.value_ind as value_prev,c1.code,c1.id_zone,c1.dat_ind  from acd_cabindication_tbl c1 join
                                                        (select max(c.dat_ind) as dat_ind,c.id_zone,c.code from acd_cabindication_tbl c
                                    where $where and c.dat_ind<(select max(dat_ind) from acd_cabindication_tbl c
                                    where $where)
                                    group by c.id_zone,c.code) q on
                                    c1.code=q.code and c1.dat_ind=q.dat_ind and c1.id_zone=q.id_zone
                                    ) x on c.code=x.code and c.dat_ind=x.dat_ind and c.id_zone=x.id_zone
                             where $where
                             ) w) w1
                              where id_zone=zone_sap 
                              limit 1 ) q  ";
                                }

                                $R .= $R1;

                            }
                        }
                }
                $R = 'select * from ('.$R.') p order by id_zone desc';
//              debug($R);


//where id_paccnt in (select id from clm_paccnt_tbl where code='011001324')

//      $R = "select distinct num_eqp,carry,now,value_prev,dat_ind,
// value_ind as value_new,(value_ind-value_prev) as value_diff
// from acd_cabindication_tbl c
// join clm_paccnt_tbl paccnt ON (paccnt.id=c.id_paccnt)
// where $where
// order by value_new desc";

                $f = fopen('aa1', 'w+');
                fputs($f, $R);


                break;
            case 'indication_addon':
//      Показания счетчика
                $R = "select distinct i.id_paccnt,i.id_meter,i.id_prev,i.id_zone,i.id_typemet,i.id_energy,i.mmgg,z.note AS zone_name,
 m.num_meter,1 AS koef,i.carry,i.dat_ind as dat_prev_ind,vp.value AS value_prev,i.value AS value_ind,i.value AS calc_ind_pr,
 fun_mmgg() AS current_mmgg,t.name as indic_type
 from acm_indication_tbl i
 join clm_paccnt_tbl paccnt ON (paccnt.id=i.id_paccnt)
 join clm_meterpoint_tbl m ON (m.id=i.id_meter)
 join clm_meter_zone_tbl mz ON (mz.id_meter=m.id)
 join eqk_zone_tbl z ON (z.id=mz.id_zone and i.id_zone=z.id)
 join acm_indication_tbl vp ON (vp.id=i.id_prev)
 join cli_indic_type_tbl t ON (t.id=i.id_operation) 
 where $where 
 --and i.id_operation not in(23,14,25,26)
 and i.dat_ind>='2018-12-31'
 order by i.mmgg desc, i.dat_ind desc,value_prev desc
 limit 18";
                //echo "<PRE>$R</PRE>";

                $f = fopen('aaa', 'w+');
                fputs($f, $R);

                break;
            case 'debet_avans':
//      Исп. для отображения поточного сальдо (задолженности) п.Рахунки/Оплати
                $R = "select CASE WHEN s.e_val < 0 THEN 0 ELSE s.e_val END as debet_e, COALESCE(ba.value,0) as avans_val
   from acm_saldo_tbl as s
   join clm_paccnt_tbl paccnt ON (paccnt.id=s.id_paccnt)
   left join acm_bill_tbl as ba on (ba.id_paccnt = s.id_paccnt and ba.id_pref = 12 and s.mmgg=ba.mmgg and ba.mmgg_bill = (s.mmgg+'1 month'::interval)::date )
   where $where AND s.mmgg = fun_mmgg() and s.id_pref = 10";

                echo $R;
                break;
            case 'news':

                $R = "select * from acd_cab_news_tbl
 order by now desc
 limit $limit";
                break;
            case 'stats':
//      $R = "select 'Фізичні особи'::varchar as name,
//((select count(distinct lic) from a_cabinet_register_tbl) || ' (' || count(lic) || ')') as p_all,
//(select 'унікальних: '|| count(distinct id_paccnt) || ' (показників: ' || count(id_paccnt) || ')' from acd_cabindication_tbl) as p_indication
//from a_cabinet_register_tbl";
                //now<'2019-03-06'
                $stat_per1 = $_GET['stat_per1'];
                $stat_per2 = $_GET['stat_per2'];
                $src = $_GET['src'];
                if (is_null($stat_per1)) {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl ) || ' (' 
                || (select 'всього: ' || count(lic) from a_cabinet_register_tbl ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl ) || ' (' || 
                (select 'всього: ' || count(lic) from a_cabinet_register_tbl ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=100) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl ) || ' (' ||
                 (select 'всього: ' || count(lic) from a_cabinet_register_tbl ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=1000) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl ) || ' ('
                 || (select 'всього: ' || count(lic) from a_cabinet_register_tbl ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=300) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl ) || ' (' ||
                 (select 'всього: ' || count(lic) from a_cabinet_register_tbl ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                    }
                } else {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2' ) || 
                ' (' || (select 'всього: ' || count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2' ) || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') ||
                 ' (' || (select 'всього: ' || count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=100 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') ||
                 ' (' || (select 'всього: ' || count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=1000 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || 
                ' (' || (select 'всього: ' || count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where id_operation=300 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select 'унікальних: '|| count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || 
                ' (' || (select 'всього: ' || count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2') || ')') as p_all,
                (select 'унікальних: '|| count(distinct id_paccnt) || ' (всього показників: ' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                    }
                }

//            echo $R;
                break;
            case 'stats1':
                $stat_per1 = $_GET['stat_per1'];
                $stat_per2 = $_GET['stat_per2'];
                $src = $_GET['src'];
                if (is_null($stat_per1)) {
                    switch ($src) {
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=100) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=1000) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=300) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;

                    }
                } else {
                    switch ($src) {
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=100 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=1000 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=300 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(distinct lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;

                    }
                }

                break;
            case 'stats2':
                $stat_per1 = $_GET['stat_per1'];
                $stat_per2 = $_GET['stat_per2'];
                $src = $_GET['src'];
                if (is_null($stat_per1)) {
                    switch ($src) {
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=100) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=1000) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=300) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl )) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' from acd_cabindication_tbl ) as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                    }
                } else {
                    switch ($src) {
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=100 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=1000 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where id_operation=300 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')' 
                from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                ((select count(lic) from a_cabinet_register_tbl where now>='$stat_per1' and now<='$stat_per2')) as p_all,
                (select count(distinct id_paccnt) || ' (' || count(id_paccnt) || ')'
                 from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                from a_cabinet_register_tbl limit 1";
                            break;
                    }
                }

                break;
            case 'stats3':
                $stat_per1 = $_GET['stat_per1'];
                $stat_per2 = $_GET['stat_per2'];
                $src = $_GET['src'];
                if (is_null($stat_per1)) {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl) as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl where id_operation=100) as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl where id_operation=1000) as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl where id_operation=300) as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl) as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;

                    }
                } else {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) 
                from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) 
                 from acd_cabindication_tbl where id_operation=100 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) 
                from acd_cabindication_tbl where id_operation=1000 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) 
                 from acd_cabindication_tbl where id_operation=300 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                (select count(distinct id_paccnt) from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                 from a_cabinet_register_tbl limit 1";
                            break;

                    }
                }
                break;
            case 'stats4':
                $stat_per1 = $_GET['stat_per1'];
                $stat_per2 = $_GET['stat_per2'];
                $src = $_GET['src'];
                if (is_null($stat_per1)) {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) from acd_cabindication_tbl) as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) from acd_cabindication_tbl where id_operation=100) as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) from acd_cabindication_tbl where id_operation=1000) as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) from acd_cabindication_tbl where id_operation=300) as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) from acd_cabindication_tbl) as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                    }
                } else {
                    switch ($src) {
                        case 4:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) 
                    from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 1:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) 
                    from acd_cabindication_tbl where id_operation=100 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 2:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) 
                    from acd_cabindication_tbl where id_operation=1000 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        case 3:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) 
                    from acd_cabindication_tbl where id_operation=300 and dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                        default:
                            $R = "select 'Фізичні особи'::varchar as name,
                    (select count(id_paccnt) 
                    from acd_cabindication_tbl where dat_ind>='$stat_per1' and dat_ind<='$stat_per2') as p_indication
                    from a_cabinet_register_tbl limit 1";
                            break;
                    }
                }

                break;
            case 'ur_lic':
                $R = "select 'Юридичні особи'::varchar as name,
(select count(distinct login) from statistic_tbl) as u_all,
count(distinct id_client) as u_indication from acd_cabindication_tbl";
                break;
            case 'reportCC':
                $R = "select a.last_name||' '||a.name||' '||a.patron_name AS fio
 ,paccnt.code AS lic
 ,address_print_full(a.addr_reg,3) AS addr_reg
 ,c.value_ind as value_new,c.now
 from clm_abon_tbl a
 join clm_paccnt_tbl paccnt ON paccnt.id_abon=a.id
 join acd_cabindication_tbl c ON c.id_paccnt=paccnt.id
 where $where
 order by c.now desc
 limit $limit";
                break;
        }
//    echo $R;
             return $R;
}

// Создает шапку таблицы из строки $columns
// название берется из файла соответствия translate_ua
// Пример - $columns = 'debet_e|num' здесь - debet_e -имя колонки
// и соответствие ему слово "Борг", а num - тип данных
// этой колонки 
    function getTableHead($columns)
    {
        $a = explode(',', $columns);
        foreach ($a as $col) {
            $b = explode('|', $col);
            $value = translate($b[0]);
            $R .= "<th>$value</th>";
        }
        return $R;
    }

// Создает html текст тела таблицы с данными:
// извлекаются данные из SQL сервера и вставляются данные в таблицу
    function getTableBody($columns, $page, $where = NULL, $connect = NULL)
    {
        $sql_x = getArrayFromBase($page, $where);

            if ($connect) {
                $newLink = pg_connect($connect);
                $result = pg_query($newLink, $sql_x);
                pg_close($newLink);
            } else {
                $result = pg_query($sql_x);
            }

        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $R .= '<tr>';
                $a = explode(',', $columns);
                foreach ($a as $col) {
                    $b = explode('|', $col);
                    $value = $b[0];
                    $type = $b[1];
                    $R .= '<td>' . stringFormat($row[$value], $type, $value, $col) . '</td>';
                }
                $R .= '</tr>';
            }
        } else {
            $R = "No Result. " . pg_last_error();
        }

        return $R;
    }

    function get_pokaz($columns, $page, $where = NULL, $connect = NULL, $mode = 0)
    {
        if ($connect) {
            $newLink = pg_connect($connect);
            $result = pg_query($newLink, getArrayFromBase($page, $where));
            pg_close($newLink);
        } else {
            $result = pg_query(getArrayFromBase($page, $where));
        }

        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $a = explode(',', $columns);
                foreach ($a as $col) {
                    $b = explode('|', $col);
                    $value = $b[0];
                    $type = $b[1];
                    $s = $row[$value];
                    // $R .= '<td>' . stringFormat($row[$value], $type, $value,$col) . '</td>';
                }
                $R = $s;
            }
        } else {
            $R = "No Result. " . pg_last_error();
        }

        return $R;
    }

// Создает полный html текст таблицы: 
// при этом создается шапка таблицы (функция getTableHead) и тело таблицы
// в теле таблицы (функция getTableBody) извлекаются
// данные из SQL сервера и вставляются данные в таблицу
    function getCompleteTable($columns, $page, $where = NULL, $connect = NULL, $disp = 0)
    {
        if ($disp == 0)
            $R = "<table class='table table-bordered table-condensed table-striped'>
  <thead class='bg-success'><tr>";
        if ($disp == 1)
            $R = "<table class='table table-bordered table-condensed table-striped1'>
  <thead class='bg-success'><tr>";

        $R .= getTableHead($columns);
        $R .= '</tr></thead>
  <tbody class="small">';
        $gTB = getTableBody($columns, $page, $where, $connect);
        $R .= $gTB;
        $R .= '</tbody></table>';
        if (!$gTB) {
            $R = '';
        }
        return $R;
    }

// Генерация списка выбора типа ComboBox
    /*
     * Аргументы:
     * $name - имя списка
     * $optionList - строка с заданными через запятую пунктами списка
     * $selected - название выделенного пункта списка
     * Возвращает html код списка выбора
     * */
    function getOptions($name, $optionList, $selected = NULL)
    {
        $R = '<select class="form-control" name="' . $name . '">'; //  input-sm
        $List = explode(',', $optionList);
        foreach ($List as $option) {
            $value = explode('|', $option);
            if ($value[0] != $selected) {
                $R .= '<option value="' . $value[0] . '">' . $value[1] . '</option>';
            } else {
                $R .= '<option value="' . $value[0] . '" selected>' . $value[1] . '</option>';
            }
        }
        $R .= '</select>';
        return $R;
    }

// Нормализация № телефона
    function tel_normal($tel)
    {
        $len = strlen($tel);
        $rez = '';
        $pos = strpos($tel, '(');
        if ($pos > 0) $len = 0;
        switch ($len) {
            case 10:
                $op = substr($tel, 0, 3);
                $rez .= $op . ' ';
                $op = substr($tel, 3, 3);
                $rez .= $op . '-';
                $add = substr($tel, 6, 2);
                $rez .= $add . '-';
                $add = substr($tel, 8);
                $rez .= $add;
                return $rez;
            case 7:
                $op = substr($tel, 0, 3);
                $rez .= $op . '-';
                $add = substr($tel, 3, 2);
                $rez .= $add . '-';
                $add = substr($tel, 5);
                $rez .= $add;
                return $rez;
            case 6:
                $op = substr($tel, 0, 2);
                $rez .= $op . '-';
                $add = substr($tel, 2, 2);
                $rez .= $add . '-';
                $add = substr($tel, 4);
                $rez .= $add;
                return $rez;
            case 5:
                $op = substr($tel, 0, 1);
                $rez .= $op . '-';
                $add = substr($tel, 1, 2);
                $rez .= $add . '-';
                $add = substr($tel, 3);
                $rez .= $add;
                return $rez;
            default:
                return $tel;
        }
    }

    function objectToArray($d)
    {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
        if (is_array($d)) {
            return array_map(__FUNCTION__, $d);
        } else {
            return $d;
        }
    }

// Отображение массива в удобном для просмотра виде
    function debug($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

// Форматирование даты в удобном формате
// в виде дд.мм.гггг
function format_date($date)
{
    $dd=substr($date,8);
    $mm =substr($date,5,2);
    $yy = substr($date,0,4);
    return $dd . '.' . $mm . '.' .$yy;
}

// Форматирование даты в удобном формате (без времени)
// в виде дд.мм.гггг
function format_date2($date)
{
    $dd=substr($date,8,2);
    $mm =substr($date,5,2);
    $yy = substr($date,0,4);
    return $dd . '.' . $mm . '.' .$yy;
}

// Сортировка массива по нескольким полям
function sort_nested_arrays( $array, $args = array('votes' => 'desc') ){
usort( $array, function( $a, $b ) use ( $args ){
    $res = 0;

    $a = (object) $a;
    $b = (object) $b;

    foreach( $args as $k => $v ){
        if( $a->$k == $b->$k ) continue;

        $res = ( $a->$k < $b->$k ) ? -1 : 1;
        if( $v=='desc' ) $res= -$res;
        break;
    }

    return $res;
} );

	return $array;
}

// Определение перекрута счетчика
// Аргументы:
// $v - показатели счетчика сейчас
// $v_prev - показатели счетчика предыдущие
// $carry - разрядность счетчика
function check_rerotation($v,$v_prev,$carry)
{
    $max_val = pow(10, $carry) - 1;
    $volume = ($max_val - $v_prev) + $v;
    if($volume>3000)
        return 0;
    else
        return 1;  // Перекрут
}


/*
 function switchServerConnect1($num) {

    if(!empty($_SESSION['conn']) && !is_null($_SESSION['conn']))
        pg_close($_SESSION['conn']);

    switch ($num) {

        case 1:
            //default:
            $pgHost = '192.168.85.1';
            $pgDBName = 'abn_ap';
            break;
        case 2:
            $pgHost = '192.168.17.1';
            $pgDBName = 'abn_gv';
            break;
        case 3:
            $pgHost = '192.168.85.1';
            $pgDBName = 'abn_in';
            break;
        case 4:
            $pgHost = '192.168.21.1';
            $pgDBName = 'abn_pv';
            break;
        case 5:
            $pgHost = '192.168.20.1';
            $pgDBName = 'abn_vg';
            break;
        case 6:
            $pgHost = '192.168.26.1';
            $pgDBName = 'abn_zv';
            break;
        case 7:
            $pgHost = '192.168.75.1';
            $pgDBName = 'abn_krr';
            break;
        case 8:
            $pgHost = '192.168.15.15';
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
 */