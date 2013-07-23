<?php # currtime.php - show current time
  
  include ('./sqlitedb.php');
?>

<html>
<head>
<title>AuctionBase</title>
</head>

<?php 
  include ('./navbar.html');
  include ('./dbfuncs.php'); 
?>

<center>
<h3> Current Time </h3> 

<?php
  $query = "select date_time from Time";
  $row = executeQuery($db, $query); 
  if ($row == false) 
  {
    echo "Selecting time failed"; 
  }
  else 
  {
    echo "Current time is: ".htmlspecialchars($row[0]["date_time"]);
  }

  $db = null;

?>

</center>
</html>

