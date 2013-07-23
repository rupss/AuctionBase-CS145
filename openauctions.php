<?php # auctions.php - shows auctions that have a start time before the current time
  
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
		<p>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</p>
		<h3> Open Auctions </h3> 

          <table border="1" align="center">
        <?php
      $time = getTime($db); 
      $query = "select ItemID, Name, Started, Ends from Item where Started < '".
      $time."' and Ends > '".$time."' except select ItemID, Name, Started, Ends from Item where ItemID in (select A.ItemID from (select ItemID, max(Amount) as max_amount from Bid group by ItemID) B, (select ItemID, Buy_Price from Item) A where A.ItemID = B.ItemID and A.Buy_Price <= B.max_amount)"; 
   
      $result = executeQuery($db, $query); 

        if (count($result) == 0) {
          echo "No results found."; 
        }
        else {

           ?>
          <tr>
          <th>Item ID</th>
          <th>Name</th>
          <th>Start Time</th>
          <th>End Time</th>
          </tr>
        <?php
          foreach ($result as $row) {
            ?>
              <tr>
              <td>
                <a href="auction.php?id=<?= $row['ItemID'] ?>">
                <?= $row["ItemID"] ?>
                </a>    
              </td>
              <td>
                <?= $row["Name"] ?>
              </td>
              <td>
                <?= $row["Started"] ?>
              </td>
              <td>
                <?= $row["Ends"] ?>
              </td>
            </tr>
            <?php
          }
          $db = null; 
        }
        ?>

	</center>
</html>

