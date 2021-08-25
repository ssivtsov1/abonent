<?php
$name = $_GET['name'];
$str = $_GET['str'];
$num = $_GET['num'];
$name1 = mb_ucfirst($name);
$name2 = mb_strtoupper($name,"UTF-8");
$y=mb_strlen($name,"UTF-8");
//$n = strpos($str, ',');
$town='м. '.$str;
$n1 = strpos($str, 'р-н');
$district=trim(substr($str,$n+1,($n1-$n-1)));
if($y<4 || is_null($name) || empty($name)) return;

switch ($num) {

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
$f=fopen('aav.cnt','w+');
fputs($f,"host=$pgHost port=5432 dbname=$pgDBName user=local password= ");

$options = " host='$pgHost' port='5432' user='local' password='' dbname='$pgDBName' ";
$Link = pg_connect($options);
$street_w = explode($name1,' ');
// Если улица состоит из 2х слов - тогда меняем слова местами
if(count($street_w)==2) {
    $tmp=$street_w[0];
    $street_w[0] = $street_w[1];
    $street_w[1] = $tmp;
    $name2 = $street_w[0] . ' ' . $street_w[1];
}
else
    $name2 = $name1;


    $sql = 'select min(id) as id,street from spr_towns where (street like '."'%".$name1."%'".
        ' or street like ' ."'%".$name2."%')" .
        ' and length('."'".$name1."')>3".' and town like '."'".$town."%'".
        ' group by street order by street';
      $result = pg_query($Link,$sql);

     fputs($f,$sql);

$i=0;
while($row = pg_fetch_array($result)) {
        $cur[$i]['id'] = $row['id'];
        $cur[$i]['street'] = $row['street'];
        $i++;
}

header("Content-type: application/json;charset=utf-8");
//echo $sql;
echo json_encode($cur);


function mb_ucfirst($str) {
    $fc = mb_strtoupper(mb_substr($str, 0, 1));
    return $fc.mb_substr($str, 1);
}
?>


