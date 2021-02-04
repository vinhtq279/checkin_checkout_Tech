<!DOCTYPE html>
<html lang="en"><head></head>
<body>
<?php
function cleanData(&$str){
 if($str == 't') $str = 'TRUE';
 if($str == 'f') $str = 'FALSE';
 if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
  $str = "'$str";
 }
 if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}



$serverName = "192.168.200.20\\MSSQLSERVER, 1433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>"DataDB", "UID"=>"sa", "PWD"=>"passw@rd1234");
$consqlsrv = sqlsrv_connect( $serverName, $connectionInfo);
$conmysql = mysqli_connect("10.2.1.29", "attendance_ereception", "123456aA@", "vntech");

if( !$consqlsrv ) {
  echo "Connection could not be established.<br />";
  die( print_r( sqlsrv_errors(), true));
}else{
/******* Ve som bat duoc tu quet the ******************/
  $querysqlsrv = "select t.CardNo as CardNo, c.CardDB_09 as Depart, t.TrName as TrName, convert(varchar,convert(datetime,(t.TrDate + ' ' + t.TrTime ),20),20) as TrDateTime from tblTransaction t, CardDB c where t.CardNo = c.CardNo and t.CardNo not like '%FFFF%' and t.TrDate >= convert(date,getdate() -1 ) and t.TrDate < convert(date,getdate()) and t.TrTime < '17:15:00' and t.TrTime >= '12:00:00' order by t.TrDate, t.TrTime";
  $retsqlsrv = sqlsrv_query($consqlsrv, $querysqlsrv);
  while($rowsqlsrv = sqlsrv_fetch_array($retsqlsrv)) {
   $checkquery = "select count(*) as allcount from vesom where CardNo = $rowsqlsrv[CardNo]";
   $retmysql=mysqli_query($conmysql,$checkquery);
   $rowmysql=mysqli_fetch_array($retmysql);
   $allcount = $rowmysql['allcount'];
   if($allcount == 0){
    $querymysql = "insert into vesom (CardNo, Depart, TrName, TrDateTime) values ($rowsqlsrv[CardNo], '$rowsqlsrv[Depart]', '$rowsqlsrv[TrName]', '$rowsqlsrv[TrDateTime]')";
    $retmysql = mysqli_query($conmysql,$querymysql);
    //if($retmysql){echo "ok";}else{echo "not ok";}
   }else{
    //echo "not ok";
   }
  }

/************ Them nhung thang ve som ma ko quet the ma bat dc tu camera *************************/
  $querymysql = "select l.user_id as userID, l.updated_at as TrDateTime, e.card_num as CardNo, c.id as CameraID, c.name as CameraName, p.full_name as TrName from log_dossier l, employee e, camera c, person p  where l.user_id = e.user_id and l.camera_id = c.id and p.id = l.user_id and c.id = 1 and l.updated_at >= date_add(date_sub(curdate(), interval 1 day), interval '12:00' HOUR_MINUTE) and l.updated_at < date_add(date_sub(curdate(), interval 1 day), interval '17:15' HOUR_MINUTE) order by l.updated_at desc"; //camera cua chinh ra - camera.id = 1
  $retmysql = mysqli_query($conmysql,$querymysql);
  while ($rowmysql=mysqli_fetch_array($retmysql)) {
   $checkquery = "select count(*) as allcount from vesom where CardNo = $rowsqlsrv[CardNo]";
   $retmysql=mysqli_query($conmysql,$checkquery);
   $rowmysql=mysqli_fetch_array($retmysql);
   $allcount = $rowmysql['allcount'];
   if($allcount == 0){
    $querymysql = "insert into vesom (CardNo, Depart, TrName, TrDateTime, Camera) values ($rowsqlsrv[CardNo], '$rowsqlsrv[Depart]', '$rowsqlsrv[TrName]', '$rowsqlsrv[TrDateTime]', 'Camera')";
    $ret = mysqli_query($conmysql,$querymy);
   }
  }


/************************* Insert vemuon table from card data**********************************/


  $querysqlsrv = "select t.CardNo as CardNo, c.CardDB_09 as Depart, t.TrName as TrName, convert(varchar,convert(datetime,(t.TrDate + ' ' + t.TrTime ),20),20) as TrDateTime from tblTransaction t, CardDB c where t.CardNo = c.CardNo and t.CardNo not like '%FFFF%' and t.TrDate >= convert(date,getdate() - 1 ) and t.TrDate < convert(date,getdate()) and t.TrTime >= '17:15:00' and t.TrTime <= '23:59:00' order by t.TrDate, t.TrTime";
  $retsqlsrv = sqlsrv_query($consqlsrv, $querysqlsrv);
  while($rowsqlsrv = sqlsrv_fetch_array($retsqlsrv)) {
   $checkquery = "select count(*) as allcount from vemuon where CardNo = $rowsqlsrv[CardNo]";
   $retmysql=mysqli_query($conmysql,$checkquery);
   $rowmysql=mysqli_fetch_array($retmysql);
   $allcount = $rowmysql['allcount'];
   if($allcount == 0){ // Khong co trong vemuon table
    $querymysql = "insert into vemuon (CardNo, Depart, TrName, TrDateTime) values ($rowsqlsrv[CardNo], '$rowsqlsrv[Depart]', '$rowsqlsrv[TrName]', '$rowsqlsrv[TrDateTime]')";
    $retmysql = mysqli_query($conmysql,$querymysql);
   }else{
    //echo "not ok";
   }
  }

/************ Them nhung thang ve muon ma ko quet the ma bat dc tu camera *************************/
  $querymysql = "select l.user_id as userID, l.updated_at as TrDateTime, e.card_num as CardNo, c.id as CameraID, c.name as CameraName, p.full_name as TrName from log_dossier l, employee e, camera c, person p  where l.user_id = e.user_id and l.camera_id = c.id and p.id = l.user_id and c.id = 1 and l.updated_at >= date_add(date_sub(curdate(), interval 1 day), interval '17:15' HOUR_MINUTE) and l.updated_at <= date_add(date_sub(curdate(), interval 1 day), interval '23:59' HOUR_MINUTE) order by l.updated_at desc"; //camera cua chinh ra - camera.id = 1
  $retmysql = mysqli_query($conmysql,$querymysql);
  while ($rowmysql=mysqli_fetch_array($retmysql)) {
   $querymysql = "insert into vemuon (CardNo, Depart, TrName, TrDateTime, Camera) values ($rowsqlsrv[CardNo], '$rowsqlsrv[Depart]', '$rowsqlsrv[TrName]', '$rowsqlsrv[TrDateTime]', 'Camera')";
   $ret = mysqli_query($conmysql,$querymy);
  }

/************ Loai nhung thang ve som xong quay lai ma co trong bang ve som ******************/
  $querymysql = "delete from vesom where CardNo in (select CardNo from vemuon)";
  $ret = mysqli_query($conmysql,$querymysql);



  $querymysql = "select * from vesom";
  $retmysql = mysqli_query($conmysql,$querymysql);
?>
  <table>
<?php
  while ($rowmysql=mysqli_fetch_array($retmysql)) {?>
    <tr>
    <td><?php echo $rowmysql['CardNo'];?></td>
    <td><?php echo $rowmysql['TrName'];?></td>
    <td><?php echo $rowmysql['TrDateTime'];?></td>
    <td><?php echo $rowmysql['Depart'];?></td>
    </tr>
<?php
  }
?>
  </table>
<?php
}
?>
<a class="nav-link" href="export2.php" style="background-color:brown;margin-right:70px;position:absolute"><i class="fas fa-file-excel"></i><span>Export</span></a>

</body>
</html>

