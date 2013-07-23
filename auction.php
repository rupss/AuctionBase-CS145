<?php # auctions.php - shows auctions that have a start time before the current time
  
  include ('./sqlitedb.php');
?>

<html>
	<head>
		<title>AuctionBase</title>
	</head>

	<center>


	<?php 
  	include ('./navbar.html');
  	include ('./dbfuncs.php'); 
  	setlocale(LC_MONETARY, "en_US");

  	$ItemID = $_GET["id"]; 
  	$bidderID = $_POST["bidderID"]; 
  	$amount = floatval($_POST["amount"]);
  	$isOpen = TRUE; 

  	$query = "select * from Item where ItemID = ".$ItemID.";"; 
  	$result = executeQuery($db, $query); 

  	$startTime = $result[0]["Started"]; 
  	$endTime = $result[0]["Ends"]; 
  	$currTime = getTime($db); 
  	$buy_price = $result[0]["Buy_Price"]; 

 

  	$maxBidQuery = "select max(Amount) as max_amount from Bid where ItemID = ".$ItemID.";"; 
  	$maxBidResult = executeQuery($db, $maxBidQuery); 

  	if ($startTime < $currTime && $endTime > $currTime) {
  		$isOpen = TRUE; 
  	}
  	else {
  		$isOpen = FALSE; 
  	}

  	if ($isOpen == TRUE && $maxBidResult) {
  		$maxBidAmount = $maxBidResult[0]["max_amount"]; 
  		if ($buy_price && $maxBidAmount >= $buy_price) {
  			$isOpen = FALSE; 
  		}
  	
  	}

  	if ($isOpen == FALSE) {
  		if ($bidderID || $amount) {
  			?> 
  			<p style="color: red; ">
  				Error: Cannot bid on a closed auction. 
  			</p>
  		<?php 
  		}
  	}
  	else if ($bidderID && $isOpen == TRUE) {

  		$time = getTime($db); 
  		$latestBidTimeQuery = "select max(Time) as max_time from (select * from Bid where ItemID = ".$ItemID.");"; 
  		$latestBidTimeResult = executeQuery($db, $latestBidTimeQuery); 
  		$latestBidTime = $latestBidTimeResult[0]["max_time"]; 

  		if ($time <= $latestBidTime) {
  			?> 
  			<p style="color: red; ">
  				Error: New bid time must be after the most recent bid. 
  			</p>
  		<?php 
  		}

  		else {

  		$validIDQuery = "select * from Bidder where userID = '".$bidderID."';"; 
  		$result = executeQuery($db, $validIDQuery); 
  		
  		if ($result) {

  			$sellerQuery = "select sellerID from Item where ItemID = ".$ItemID.";"; 
  			$sellerResult = executeQuery($db, $sellerQuery); 
  			$sellerID = $sellerResult[0]["sellerID"]; 

  			if ($sellerID == $bidderID) {
  				?> 
  				<p style="color: red; ">
  					Error: Seller cannot bid on their own item.
  				</p>
  			<?php 

  			}

  			else {

  			//check to make sure that amount is greater than the previous amount

  			$validAmountQuery = "select ItemID, userID, Time, max(Amount) as max_amount from Bid where ItemID = ".$ItemID.";"; 
  			$result = executeQuery($db, $validAmountQuery); 

  			$maxAmount = $result[0]["max_amount"]; 

  			if ($amount != 0 && $amount > $maxAmount) {

  				$firstBidResult = executeQuery($db, "select First_Bid from Item where ItemID = ".$ItemID.";"); 
  				$firstBid = $firstBidResult[0]["First_Bid"]; 

  				if ($amount < $firstBid) {
  					?> 
  					<p style="color: red; ">
  						Error: Bid amount must be greater than $<?= money_format("%n",$firstBid) ?>
  					</p>
  				<?php 
  				}

  				else {

  				executeQuery($db, "begin transaction; "); 
  				$insertBidQuery = "insert into Bid values(". $ItemID. ", '".$bidderID."', '".getTime($db)."', '".$amount."'); "; 
  				$insertBidResult = executeQuery($db, $insertBidQuery); 
  				$updateNumBidsQuery = "update Item set Number_of_Bids = Number_of_Bids + 1 where ItemID = ".$ItemID.";"; 
  				$updateNumBidsResult = executeQuery($db, $updateNumBidsQuery); 

  				if ($insertBidResult === false || $updateNumBidsResult === false) {
  					executeQuery($db, "rollback; "); 
  				}
  				else {
  					executeQuery($db, "commit; "); 

  					//update Currently in Item so that Currently = $amount

  					$updateCurrentlyQuery = "update Item set Currently = ".$amount." where ItemID = ".$ItemID.";"; 
  					executeQuery($db, $updateCurrentlyQuery); 
  				}



 
  			}
  		}
  			else {
  				?> 
  				<p style="color: red; ">
  					Error: Invalid amount entered. Amount must be numeric and greater than all previous bids amounts. 
  				</p>
  			<?php 
  			}

  			
  		}
  	}
   
  		else {
  			?> 
  				<p style="color: red; ">
  					Error: Invalid Bidder ID entered.
  				</p>
  			<?php 
  		}
  	}
  		
  	}

  	?>
	<h3> Single Auction </h3> 
  	<?php
	
    if ($bidderID == NULL && $amount && $isOpen == true) {
        ?> 
          <p style="color: red; ">
            Error: Enter a valid bidder ID. 
          </p>
        <?php 
    }

    if ($isOpen == true && $bidderID == NULL && $_POST["amount"] && is_numeric($_POST["amount"]) == false) {
      ?> 
          <p style="color: red; ">
            Error: Amount must be numeric and a valid bidder ID is necessary. 
          </p>
        <?php 
    }

	if ($ItemID) {
		$query = "select * from Item where ItemID = '".$ItemID."';"; 
		$result = executeQuery($db, $query); 

		if ($result) {
			$row = $result[0]; 
			?>
			Started: <?= $row["Started"] ?> <br>
			Ends: <?= $row["Ends"] ?> <br>
			Description: <?= $row["Description"] ?> <br>
			<?php

			$query = "select * from Bid where ItemID = '".$ItemID."' order by Time;"; 
			$result = executeQuery($db, $query); 
			?>
			<br><br>
			<p style="font-size: 17px;">
				<b>Bids</b>
			</p>
			<table>
				<tr> 
					<th> Bidder </th>
					<th> Amount </th>
					<th> Time </th>
				</tr>
			<?php

			foreach ($result as $row) {
			?>
				<tr>
					<td> <?= $row["userID"] ?> </td>
					<td> $<?= money_format("%n", $row["Amount"]) ?> </td>
					<td> <?= $row["Time"] ?> </td>
				</tr>
			<?php
			}
			

			?>
			
				<tr>
					<form action="auction.php?id=<?= $ItemID ?>" method="POST">
						<td> <input type="text" name="bidderID"> </td>
						<td> <input type="text" name="amount"> </td>
						<td> <input type="submit" name="submit" value="Submit"> </td>
					</form>
				</tr>
			</table>

			<?php


  			$maxBidQuery = "select max(Amount) as max_amount from Bid where ItemID = ".$ItemID.";"; 
  			$maxBidResult = executeQuery($db, $maxBidQuery); 

  			if ($isOpen == TRUE && $maxBidResult) {
  				$maxBidAmount = $maxBidResult[0]["max_amount"]; 
  		
  				if ($buy_price && $maxBidAmount >= $buy_price) {
  					$isOpen = FALSE; 
  				}
  	
  			}


			if ($isOpen == FALSE) {


				$winningBidQuery = "select userID, max(Amount) as max_amount from Bid where ItemID =".$ItemID." group by ItemID;"; 
				$winResult = executeQuery($db, $winningBidQuery); 

				if ($result) {
					?>
						<p style="color:blue;">
							Closed. AUCTION WINNER: <b><?= $winResult[0]["userID"] ?></b>
						</p>

					<?php
				}
				else {
					?>
						<p style="color:blue;">
							Closed. No Auction Winner.
						</p>
					<?php
				}
			}
			$db = null; 

		}
		else {
			?> 
  				<p style="color: red; ">
  					ERROR: Invalid Item ID. Try again.
  				</p>
  			<?php 
		}
	}
	else {
		?> 
  			<p style="color: red; ">
  				ERROR: Select a valid auction entry.
  			</p>
  		<?php 
	}

  if ($isOpen == true) {
    ?>
    <p style="color:blue;">
        This auction is open and taking bids! 
    </p>
    <?php
  }

	?>

	</center>
</html>