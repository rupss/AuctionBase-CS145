<?php
	
	function executeQuery($db, $query)
	{
		$return_value = array(); 

		try {
			$stmt = $db->prepare($query); 
			$stmt->execute(); 

			$result = $stmt->fetch(PDO::FETCH_ASSOC); 

			while ($result !== false) {
				array_push($return_value, $result); 
				$result = $stmt->fetch(PDO::FETCH_ASSOC); 

			}
			return $return_value; 
		}
		catch (PDOException $e) {
			print $e->getMessage(); 
			echo "Call failed.";
		}
		return false; 
	}

	function getTime($db) {
		$query = "select date_time from Time;"; 
		$result = executeQuery($db, $query); 
		return $result[0]["date_time"]; 
	}
?>