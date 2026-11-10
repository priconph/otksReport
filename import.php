<?php

$pollids  = "./Reference/VLB.txt";
$contents = file_get_contents($pollids);
$pollfields = explode('\n', $contents);
$x=0;
echo '<table border=1>';
while($pollfields[$x]!=""){
echo "<tr>";
echo "<td>".$pollfields[$x]."</td>";// Prints the value in first "cell"
echo "</tr>";
$x++;
}
echo '</table>';
}
?>