1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
<?php
/*echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
echo "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890<br>";*/
echo "Flush test";
$count = 10;
//ob_implicit_flush(true);
//ini_set('output_buffering',1);
    	while($count-->0){
    		print "<br>Count was ".$count;
    		//flush();
    		ob_flush();
    		sleep(1);
    	}
    	//die("");// No script file
?>
xxx
</body>
</html>