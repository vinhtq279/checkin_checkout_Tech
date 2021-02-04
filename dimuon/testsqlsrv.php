<?php
$serverName = "172.24.104.161\\MSSQLSERVER, 1433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>"DoorDataDB", "UID"=>"sa", "PWD"=>"Nsi@ansv");
$consqlsrv = sqlsrv_connect( $serverName, $connectionInfo);

if( $consqlsrv ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>
