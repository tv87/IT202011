<?php
echo "Array: <br>\n";
$arr = [11, 12, 13, 14, 15, 16, 16, 17];
foreach($arr as $num)
{
	echo "$num <br>\n";
}
echo "Even Numbers: <br>\n";
foreach($arr as $num)
{	
	if($num % 2 == 0)
    {
    	echo "$num <br>\n";
    }
}
?>
