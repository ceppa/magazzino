<?php

$emailPattern = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/";

$email1 = "gino.latino@hotmail.it";
$email2 = "ginas_ASDJAAAW$%$%&";

echo $email1 . " " . preg_match($emailPattern,$email1) . "<br><br>";
echo $email2 . " " . preg_match($emailPattern,$email2) . "<br><br>";
?>
