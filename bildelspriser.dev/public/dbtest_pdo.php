<?php 
    $connection = mysql_connect("mysql1082.servage.net", 
        "bdp_prod", 
        "m1bveVWPolo13"); 

    mysql_select_db("bdp_prod", $connection); 

	$res = mysql_query("select * from car_makes",$connection);
	$ar = mysql_fetch_assoc($res);
	print_r($ar);

?> 