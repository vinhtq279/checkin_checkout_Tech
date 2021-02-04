<?php
session_start();
error_reporting(0);
function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    //$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
  }

// filename for download
$filename = "dimuon" . date('Ymd', strtotime ("-1 days")) . ".csv";

header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: text/csv; charset=UTF-8");
echo "\xEF\xBB\xBF";
$out = fopen("php://output", 'w');
$flag = false;

$serverName = "172.24.104.161\\MSSQLSERVER, 1433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>"DoorDataDB", "UID"=>"sa", "PWD"=>"Nsi@ansv");
$consqlsrv = sqlsrv_connect( $serverName, $connectionInfo);
$conmysql = mysqli_connect("10.2.1.29", "attendance_ereception", "123456aA@", "vntech");

if( !$consqlsrv ) {
  echo "Connection could not be established.<br />";
  die( print_r( sqlsrv_errors(), true));

}else{
 $query="select * from dimuon";
 $ret=mysqli_query($conmysql,$query);
 $cnt=1;
 $fields = array('Số thẻ','Họ Tên','Đơn vị', 'Giờ Vào');
 fputcsv($out, $fields, ',', '"');
 while ($row=mysqli_fetch_array($ret)) {
  $line=array($row[CardNo], $row[TrName], $row[Depart], $row[TrDateTime]);
  fputcsv($out, array_values($line), ',', '"');
  $cnt =$cnt+1;
 }
fclose($out);
$querymysql = "delete from disom";
$retmysql = mysqli_query($conmysql,$querymysql);
$querymysql = "delete from dimuon";
$retmysql = mysqli_query($conmysql,$querymysql);
exit;
}
?>

