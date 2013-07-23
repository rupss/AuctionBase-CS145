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
		<h3> Auctions </h3> 

		<table border="1" align="center">
			<tr>
				<th>Item ID</th>
				<th>Name</th>
				<th>Start Time</th>
				<th>End Time</th>
			</tr>

			<?php
				$time = getTime($db); 
				$query = "select ItemID, Name, Started, Ends from Item where Started < '".
				$time."'"; 
				$query = $query."order by Started;"; 
				$result = executeQuery($db, $query); 

				foreach ($result as $row)
				{
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
		?>

		</table>

	</center>
</html>

