<?php
//$con=mysqli_connect("dbtit", "root", "example", "ermsdb");
$con=mysqli_connect("10.2.1.29", "attendance_ereception", "123456aA@", "vntech");

if(mysqli_connect_errno()){
 echo "Connection Fail".mysqli_connect_error();
}else{
  echo "Connection established.<br />";
}
?>
