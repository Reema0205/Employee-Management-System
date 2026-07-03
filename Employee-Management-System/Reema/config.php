<?php
$connection = mysqli_connect("localhost","root","","ems");

if(!$connection){
    die("died");
}
else{
    echo "failed";
}

?>