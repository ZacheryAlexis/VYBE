<?php
header('Access-Control-Allow-Origin: *');
$make = $_GET['make'];
switch ($make) {
   case "buick":
       echo "Enclave,Lacrosse,Regal";
       break;
   case "chevy":
       echo "Camero,Corvette,Impala";
       break;
   case "dodge":
       echo "Challenger,Charger,Viper";
       break;
   case "ford":
       echo "Fusion,Mustang,Taurus";
       break;
    case "ferrari":
        echo "SF90,LaFerrari,F40";
        break;
    case "mercedes":
        echo "AMG GT, AMG ONE, AMG SLS";
        break;
}
