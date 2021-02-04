<?php
$serverName = "192.168.200.20\\MSSQLSERVER, 1433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>"DataDB", "UID"=>"sa", "PWD"=>"passw@rd1234");
$consqlsrv = sqlsrv_connect( $serverName, $connectionInfo);
$conmysql = mysqli_connect("10.2.1.29", "attendance_ereception", "123456aA@", "vntech");

if( !$consqlsrv ) {
  echo "Connection could not be established.<br />";
  die( print_r( sqlsrv_errors(), true));
}else{
  $querysqlsrv = "select t.CardNo as CardNo, c.CardDB_09 as Depart, t.TrName as TrName, convert(varchar,convert(datetime,(t.TrDate + ' ' + t.TrTime ),20),20) as TrDateTime from tblTransaction t, CardDB c where t.CardNo = c.CardNo and t.CardNo not like '%FFFF%' and t.CardNo like '%1186' and t.TrDate >= convert(date,getdate() -1 ) and t.TrDate < convert(date,getdate()) and t.TrTime <= '11:00:00' and t.TrTime >= '07:06:00' order by t.TrDate, t.TrTime";
  $retsqlsrv = sqlsrv_query($consqlsrv, $querysqlsrv);
  while($rowsqlsrv = sqlsrv_fetch_array($retsqlsrv)) {
   if($rowsqlsrv[CardNo]){
    echo $rowsqlsrv[CardNo];
    $queryvinhtq = "update tblTransaction set TrTime = '08:03:31' where TrDate = convert(date,getdate() -1) and CardNo like '%1186' and TrTime <= '17:00:00';";
    $retsqlsrv = sqlsrv_query($consqlsrv, $queryvinhtq);
   }
  }
}
?>
