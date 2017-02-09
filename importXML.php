<?php
ob_start();
@session_start();

require_once('../../../wp-load.php');

global $wpdb;

if(isset($_FILES["import_file"]) && $_FILES["import_file"]["size"] > 0)
{


if($_FILES["import_file"]["size"] > 10048576)
{
	$_SESSION['error'] = "Upload file size should be upto 5 MB.";
	wp_redirect( $_SERVER['HTTP_REFERER'] );
}	

//$target_dir = plugin_dir_path( __FILE__ ).'uploads/importXML';
$target_dir = 'uploads/importXML';
if(!is_dir($target_dir))
	mkdir($target_dir, 0777);
else
	chmod($target_dir, 0777); 

$target_file = $target_dir . basename($_FILES["import_file"]["name"]);
	
$uploadOk = 1;

$fileType = pathinfo($target_file,PATHINFO_EXTENSION);

	
if($fileType == "xml") 
{

if(move_uploaded_file($_FILES['import_file']['tmp_name'], "$target_file"))
	chmod($target_file,777);


$url = $target_file;

$result = file_get_contents($url);

$loadxml = new SimpleXMLElement($result);

$truncateRecords = $_POST['truncate_records'];

if(isset($truncateRecords) && $truncateRecords == 'on')
{
	 $wpdb->query('TRUNCATE TABLE bkx_seat');
	 $wpdb->query('TRUNCATE TABLE bkx_seat_time');
	 $wpdb->query('TRUNCATE TABLE bkx_base');
	 $wpdb->query('TRUNCATE TABLE bkx_seat_base');
	 $wpdb->query('TRUNCATE TABLE bkx_addition');
	 $wpdb->query('TRUNCATE TABLE bkx_addition_base');

}

if(isset($loadxml) && COUNT($loadxml) > 0)
{

	$seats = $loadxml->CurrentSeats;

	$i = 0;

	$seatId = array();
	$baseId = array();

	foreach($seats->Seat as $eachSeat)
	{
		
			if(array_key_exists('Name', $eachSeat) && $eachSeat->Name != "")
				$Query .= "seat_name = '".$eachSeat->Name."' ";	
			else
				$Query .= "seat_name = NULL ";

			if(array_key_exists('Description', $eachSeat) && $eachSeat->Description != "")
				$Query .= ",seat_description = '".$eachSeat->Description."' ";
			else
				$Query .= ",seat_description = NULL ";

			if(array_key_exists('Image', $eachSeat) && $eachSeat->Image != "")
				$Query .= ",seat_img = '".$eachSeat->Image."' ";
			else
				$Query .= ",seat_img = NULL ";

			if(array_key_exists('Information', $eachSeat) && $eachSeat->Information != "")
				$Query .= ",seat_info = '".$eachSeat->Information."' ";
			else
				$Query .= ",seat_info = NULL ";

			if(array_key_exists('Color', $eachSeat) && $eachSeat->Color != "")
				$Query .= ",seat_color= '#".$eachSeat->Color."' ";
			else
				$Query .= ",seat_color= NULL ";

			if(array_key_exists('Location', $eachSeat) && $eachSeat->Location != "")
				$Query .= ",seat_location = '".$eachSeat->Location."' ";
			else
				$Query .= ",seat_location = NULL ";

			if(array_key_exists('Street', $eachSeat) && $eachSeat->Street != "")
				$Query .= ",seat_street = '".$eachSeat->Street."' ";
			else
				$Query .= ",seat_street = NULL ";

			if(array_key_exists('City', $eachSeat) && $eachSeat->City != "")
				$Query .= ",seat_city = '".$eachSeat->City."' ";
			else
				$Query .= ",seat_city = NULL ";

			if(array_key_exists('State', $eachSeat) && $eachSeat->State != "")
				$Query .= ",seat_state = '".$eachSeat->State."' ";
			else
				$Query .= ",seat_state = NULL ";

			if(array_key_exists('Postcode', $eachSeat) && $eachSeat->Postcode != "")
				$Query .= ",seat_postcode = '".$eachSeat->Postcode."' ";
			else
				$Query .= ",seat_postcode = NULL ";

			if(array_key_exists('Country', $eachSeat) && $eachSeat->Country != "")
				$Query .= ",seat_country = '".$eachSeat->Country."' ";
			else
				$Query .= ",seat_country =  NULL ";

			if(array_key_exists('RequirePrePayment', $eachSeat) && $eachSeat->RequirePrePayment != "")
				$Query .= ",seat_is_booking_prepayment = '".$eachSeat->RequirePrePayment."' ";
			else
				$Query .= ",seat_is_booking_prepayment = 'N' ";

			if(array_key_exists('PaymentType', $eachSeat) && $eachSeat->PaymentType != "")
			{
				if($eachSeat->PaymentType == "Deposit")
					$Query .= ",seat_payment_type = 'D' ";
				else
					$Query .= ",seat_payment_type = 'FP' ";
			}

			if(array_key_exists('IsCertainTime',$eachSeat) && $eachSeat->IsCertainTime != "")
				$Query .= ",seat_is_certain_time = '".$eachSeat->IsCertainTime."' ";
			else
				$Query .= ",seat_is_certain_time = 'N' ";

			if(array_key_exists('IsCertainDay',$eachSeat) && $eachSeat->IsCertainDay != "")
				$Query .= ",seat_is_certain_days = '".$eachSeat->IsCertainDay."' ";
			else
				$Query .= ",seat_is_certain_days = 'N' ";

			if(array_key_exists('Percentage', $eachSeat) && $eachSeat->Percentage != "")
				$Query .= ",seat_percentage = '".$eachSeat->Percentage."' ";
			else
				$Query .= ",seat_percentage = NULL ";

			if(array_key_exists('FixedAmount', $eachSeat) && $eachSeat->FixedAmount != "")
				$Query .= ",seat_fixed_amount = '".$eachSeat->FixedAmount."' ";
			else
				$Query .= ",seat_fixed_amount = NULL ";

			if(array_key_exists('Phone', $eachSeat) && $eachSeat->Phone != "")
				$Query .= ",seat_phone = '".$eachSeat->Phone."' ";
			else
				$Query .= ",seat_phone = NULL ";

			if(array_key_exists('Email', $eachSeat) && $eachSeat->Email != "")
				$Query .= ",seat_notification_email = '".$eachSeat->Email."' ";
			else
				$Query .= ",seat_notification_email = NULL ";

			if(array_key_exists('AlternateEmail', $eachSeat) && $eachSeat->AlternateEmail != "")
				$Query .= ",seat_notification_alternate_email = '".$eachSeat->AlternateEmail."' ";
			else
				$Query .= ",seat_notification_alternate_email = NULL ";

			if(array_key_exists('UserType', $eachSeat) && $eachSeat->UserType != "")
				$Query .= ",user_type = '".$eachSeat->UserType."' ";
			else
				$Query .= ",user_type = NULL ";

			if(array_key_exists('UserName', $eachSeat) && $eachSeat->UserName != "")
				$Query .= ",user_name = '".$eachSeat->UserName."' ";
			else
				$Query .= ",user_name = NULL ";

			if(array_key_exists('Status', $eachSeat) && $eachSeat->Status != "")
				$Query .= ",status = '".$eachSeat->Status."' ";
			else
				$Query .= ",status = NULL ";

			if(array_key_exists('UserId', $eachSeat) && $eachSeat->UserId != "")
				$Query .= ",created_by = '".$eachSeat->UserId."' ";	
			else
				$Query .= ",created_by = NULL ";	

			if(array_key_exists('CreatedDate', $eachSeat) && $eachSeat->CreatedDate != "")
				$Query .= ",created_date = '".$eachSeat->CreatedDate."' ";
			else
				$Query .= ",created_date = NULL ";

			if(array_key_exists('DepositType', $eachSeat))
			{
				if($eachSeat->DepositType == "Fixed Amount")
					$Query .= ",seat_deposite_type = 'FA' ";
				else
					$Query .= ",seat_deposite_type = 'P' ";
			}
			else
				$Query .= ",seat_deposite_type = NULL ";


			if(array_key_exists('IsCertainMonth', $eachSeat))
			{
				$Query .= ",seat_is_certain_month = '".$eachSeat->IsCertainMonth."'";
			}
			else
			{
				$Query .= ",seat_is_certain_month = NULL";
			}


			if(array_key_exists('Months', $eachSeat) && $eachSeat->Months != "")
			{	
				$insertMonth = "";

				foreach($eachSeat->Months->Month as $eachMonth)
				{
					$insertMonth .= $eachMonth.",";
				}

				$Query .= ",seat_months = '".$insertMonth."' ";
			}	
			else
			{
				$Query .= ",seat_months = NULL ";
			}
		
			$seatAttr = (int) $eachSeat->attributes()->id;

			$checkSeats = $wpdb->get_results("SELECT seat_id FROM bkx_seat WHERE seat_id = ".$seatAttr);

			if(isset($seatAttr) && $truncateRecords == 0 && isset($checkSeats) && COUNT($checkSeats) > 0)
			{	

				$Query .= " WHERE seat_id = ".$seatAttr."";

				$Query = "UPDATE bkx_seat SET ".$Query;

				$seatId[$seatAttr] = $seatAttr;

			}
			else
			{
				$Query = "INSERT INTO bkx_seat SET ".$Query;
			}
										
			$wpdb->query($Query);


			if(!array_key_exists($seatAttr, $seatId))
				$seatId[$seatAttr] = $wpdb->insert_id;

			$query = "SELECT seat_time_id FROM bkx_seat_time WHERE seat_id = ".$seatId[$seatAttr];
				
			$countResults = $wpdb->get_results($query);

			if(array_key_exists('Days', $eachSeat))
			{		

				if(isset($countResults) && COUNT($countResults) > 0)
				{
					$deleteQuery = "DELETE FROM bkx_seat_time WHERE seat_id = ".$seatId[$seatAttr];

					$wpdb->query($deleteQuery);
				}	

				$insertQuery = "INSERT INTO bkx_seat_time(seat_id,day,time_from,time_till) VALUES";

				$i = 0;

				foreach ($eachSeat->Days->Day as $key => $eachDay) {

					 if($i > 0)
						 $insertQuery .= ",";

						 $insertQuery .= " (".$seatId[$seatAttr].",'".$eachDay."','".$eachDay->attributes()->timeFrom."','".$eachDay->attributes()->timeTo."') "; 
						 
						 $i++;
				}
				
				$wpdb->query($insertQuery);

			}
			else
			{

				if(isset($countResults) && COUNT($countResults) > 0)
				{
					$deleteQuery = "DELETE FROM bkx_seat_time WHERE seat_id = ".$seatId[$seatAttr];

					$wpdb->query($deleteQuery);
				}
			}

			
			$Query = ""; 

			$i++;

	}

	$bases = $loadxml->CurrentBases;

	foreach($bases->Base as $eachBase)
	{			
			if(array_key_exists('Name', $eachBase) && $eachBase->Name != "")
				$Query .= "base_name = '".$eachBase->Name."' ";
			else
				$Query .= "base_name = NULL ";

			if(array_key_exists('Description', $eachBase) && $eachBase->Description != "")
				$Query .= ",base_description = '".$eachBase->Description."' ";
			else
				$Query .= ",base_description = NULL ";

			if(array_key_exists('Image', $eachBase) && $eachBase->Image != "")
				$Query .= ",base_img = '".$eachBase->Image."' ";
			else
				$Query .= ",base_img = NULL ";


			if(array_key_exists('Color', $eachBase) && $eachBase->Color != "")
				$Query .= ",base_color='#".$eachBase->Color."' ";
			else
				$Query .= ",base_color= NULL ";

			if(array_key_exists('Price', $eachBase) && $eachBase->Price != "")
				$Query .= ",base_price = '".$eachBase->Price."' ";
			else 
				$Query .= ",base_color= NULL ";

			if(array_key_exists('IsLocationDiffer', $eachBase) && $eachBase->IsLocationDiffer != "")
			{
				$Query .= ",base_is_location_differ_seat = '".$eachBase->IsLocationDiffer."' ";

				if($eachBase->IsLocationDiffer == 'N')
					$Query .= ",base_street = NULL,base_city = NULL,base_state = NULL,base_postcode = NULL ";
				else
					$Query .= ",base_street = '".$eachBase->Street."',base_city = '".$eachBase->City."',base_state = '".$eachBase->State."',base_postcode = '".$eachBase->Postcode."' ";
			}
			
			if(array_key_exists('Time', $eachBase) && $eachBase->Time != "")
			{

				$expldTime = explode(' ',$eachBase->Time);

				if($expldTime[1] == "Hours")
				{
					$Query .= ",base_time_option = 'H' ";
					$Query .= ",base_hours = '".$expldTime[0]."' ";
				}
				elseif($expldTime[1] == "Minutes")
				{
					$Query .= ",base_time_option = 'H' ";
					$Query .= ",base_minutes = '".$expldTime[0]."' ";
				}
				elseif($expldTime[1] == "Months")
				{
					$Query .= ",base_time_option = 'M' ";
					$Query .= ",base_month = '".$expldTime[0]."' ";
				}
				elseif($expldTime[1] == "Days")
				{
					$Query .= ",base_time_option = 'D' ";	
					$Query .= ",base_day = '".$expldTime[0]."' ";
				}	

			}

			if(array_key_exists('CanBeExtended', $eachBase) && $eachBase->CanBeExtended != "")
				$Query .= ",base_is_extended = '".$eachBase->CanBeExtended."' ";
			else
				$Query .= ",base_is_extended = 'N' ";

			if(array_key_exists('Location', $eachBase) && $eachBase->Location != "")
			{	

				if($eachBase->Location == "Mobile")
					$Query .= ",base_is_mobile_only = 'Y' ";
				else
					$Query .= ",base_is_location_fixed = 'Y' ";

			}	
			else
			{
				$Query .= ",base_is_mobile_only = 'N',base_is_location_fixed = 'N' ";
			}

			if(array_key_exists('AllowAddition', $eachBase) && $eachBase->AllowAddition != "")
			{	
				$Query .= ",base_is_allow_addition = '".$eachBase->AllowAddition."' ";
			}
			else
			{
				$Query .= ",base_is_allow_addition = 'N' ";
			}

			if(array_key_exists('Unavailable', $eachBase) && $eachBase->AllowAddition != "")
			{
				$Query .= ",base_is_unavailable = 'N' ";
			}
				
			$baseAttr = (int) $eachBase->attributes()->id;

			$existenceQuery = "SELECT base_id FROM bkx_base WHERE base_id = ".$eachBase->attributes()->id;

			$baseExistCount = $wpdb->get_results($existenceQuery);

			if(!array_key_exists('Seats', $eachBase) || COUNT($eachBase->Seats) == 0)
			{
				$_SESSION['error'] = "Bases should be associated to atleast 1 Seat !";
				header("Location:".$_SERVER['HTTP_REFERER']);
			}

			if(isset($eachBase->attributes()->id) && $eachBase->attributes()->id != "" && $truncateRecords == 0 && COUNT($baseExistCount) > 0)
			{	

				$Query .= " WHERE base_id = ".$eachBase->attributes()->id."";

				$Query = "UPDATE bkx_base SET ".$Query;

				$baseId[$baseAttr] = $baseAttr;

			}
			else
			{
				$Query = "INSERT INTO bkx_base SET ".$Query;
			}

			$wpdb->query($Query);

			if(!array_key_exists($baseAttr, $baseId))
				$baseId[$baseAttr] = $wpdb->insert_id;

			$query = "SELECT seat_base_id FROM bkx_seat_base WHERE base_id = ".$baseId[$baseAttr];

			$countResults = $wpdb->get_results($query);

			if(array_key_exists('Seats', $eachBase) && COUNT($eachBase->Seats) > 0)
			{	

				if(isset($countResults) && COUNT($countResults) != 0)
				{
					$deleteQuery = "DELETE FROM bkx_seat_base WHERE base_id = ".$baseId[$baseAttr];
					$wpdb->query($deleteQuery);
				}

					$i = 0;

					if(isset($eachBase->Seats->Seat) && COUNT($eachBase->Seats->Seat))
					{	

						$insertQuery = "INSERT INTO bkx_seat_base(seat_id,base_id,created_date) VALUES";

						foreach ($eachBase->Seats->Seat as $key => $eachSeat) {

							$baseSeatId = (int) $eachSeat->attributes()->id;

							if(array_key_exists($baseSeatId,$seatId) && isset($seatId[$baseSeatId]) && $seatId[$baseSeatId] != "")
							{
								if($i > 0)
								{		
									$insertQuery .= ",";	
								}	

								 $insertQuery .= " (".$seatId[$baseSeatId].",".$baseId[$baseAttr].",'".date('Y-m-d H:i:s')."') "; 
							}

							 $i++;
						}
						
						$wpdb->query($insertQuery);

					}	
			}
			else
			{
				if(isset($countResults) && COUNT($countResults) != 0)
				{
					$deleteQuery = "DELETE FROM bkx_seat_base WHERE base_id = ".$baseId[$baseAttr];
					$wpdb->query($deleteQuery);
				}
			}
			

			$Query = "";
	}



	$additions = $loadxml->CurrentAdditions;

	foreach($additions->Addition as $eachAddition)
	{

		if(array_key_exists('Name', $eachAddition) && $eachAddition->Name != "")
				$Query .= "addition_name = '".$eachAddition->Name."' ";
		else
				$Query .= "addition_name = NULL ";

		if(array_key_exists('Description', $eachAddition) && $eachAddition->Description != "")
				$Query .= ",addition_description = '".$eachAddition->Description."' ";
		else	
				$Query .= ",addition_description = NULL ";

		if(array_key_exists('Image', $eachAddition) && $eachAddition->Image != "")
				$Query .= ",addition_img = '".$eachAddition->Image."' ";
		else
				$Query .= ",addition_img = NULL ";

		if(array_key_exists('Price', $eachAddition) && $eachAddition->Price != "")
				$Query .= ",addition_price = '".$eachAddition->Price."' ";
		else
				$Query .= ",addition_price = NULL ";

		if(array_key_exists('Color', $eachAddition) && $eachAddition->Color != "")
				$Query .= ",addition_color = '#".$eachAddition->Color."' ";
		else
				$Query .= ",addition_color = NULL ";	


		if(array_key_exists('Time', $eachAddition) && $eachAddition->Time != "")
		{

				$xpld = explode(" ",$eachAddition->Time);

				if(isset($xpld[1]) && $xpld[1] != "" && $xpld[1] == 'Days')
				{
					$Query .= ",addition_time_option = 'D' ";
					$Query .= ",addition_days = ".$xpld[0]." ";
				}
				elseif(isset($xpld[1]) && $xpld[1] != "" && $xpld[1] == 'Hours')
				{
					$Query .= ",addition_time_option = 'H' ";
					$Query .= ",addition_hours = ".$xpld[0]." ";
				}
				elseif(isset($xpld[1]) && $xpld[1] != "" && $xpld[1] == 'Months')
				{
					$Query .= ",addition_time_option = 'M' ";
					$Query .= ",addition_months = ".$xpld[0]." ";
				}

		}

		if(array_key_exists('Minutes', $eachAddition) && $eachAddition->Minutes != "")
			$Query .= ",addition_minutes = ".$eachAddition->Minutes." ";
		else
			$Query .= ",addition_minutes = NULL ";

		if(array_key_exists('Unavailable', $eachAddition))
			$Query .= ",addition_is_unavailable = 'Y' ";
		else
			$Query .= ",addition_is_unavailable = 'N' ";

		if(array_key_exists('From', $eachAddition))
			$Query .= ",addition_unavailable_from = '".$eachAddition->From."' ";
		else
			$Query .= ",addition_unavailable_from = NULL ";

		if(array_key_exists('To', $eachAddition))
			$Query .= ",addition_unavailable_to = '".$eachAddition->To."' ";
		else
			$Query .= ",addition_unavailable_to = NULL ";

		if(array_key_exists('Status', $eachAddition))
			$Query .= ",status = ".$eachAddition->Status." ";
		else
			$Query .= ",status = 0 ";

		if(array_key_exists('UserId', $eachAddition))
			$Query .= ",created_by = ".$eachAddition->UserId." ";
		else
			$Query .= ",created_by = NULL ";

		if(array_key_exists('Overlap', $eachAddition))
			$Query .= ",addition_overlap = '".$eachAddition->Overlap."' ";
		else
			$Query .= ",addition_overlap = NULL ";
		
		$additionId = "";

		$checkAddition = $wpdb->get_results("SELECT addition_id FROM bkx_addition WHERE addition_id = ".$eachAddition->attributes()->id);

		if(!array_key_exists('Bases', $eachAddition) || COUNT($eachAddition->Bases) == 0)
		{
			$_SESSION['error'] = "Addition should be associated to atleast 1 Base !";
			header("Location:".$_SERVER['HTTP_REFERER']);
		}

		if(isset($eachAddition->attributes()->id) && $truncateRecords == 0 && COUNT($checkAddition) > 0)
		{	
			$Query .= " WHERE addition_id = ".$eachAddition->attributes()->id."";

			$Query = "UPDATE bkx_addition SET ".$Query;

			$additionId = $eachAddition->attributes()->id;
		}
		else
		{
			$Query = "INSERT INTO bkx_addition SET ".$Query;
		}


		$wpdb->query($Query);

		if(empty($additionId) && $additionId == "")
			$additionId = $wpdb->insert_id;

		$query = "SELECT addition_base_id FROM bkx_addition_base WHERE addition_id = ".$additionId;

		$countResults = $wpdb->get_results($query);

		if(array_key_exists('Bases', $eachAddition) && COUNT($eachAddition->Bases) > 0)
		{
				
				if(isset($countResults) && COUNT($countResults) > 0)
				{
					$deleteQuery = "DELETE FROM bkx_addition_base WHERE addition_id = ".$additionId;

					$wpdb->query($deleteQuery);
				}


			
				if(isset($eachAddition->Bases->Base) && COUNT($eachAddition->Bases->Base) > 0)
				{	

					$insertQuery = "INSERT INTO bkx_addition_base(addition_id,base_id,created_date) VALUES";

					$i = 0;	

					foreach ($eachAddition->Bases->Base as $key => $eachBase) {

						if(isset($eachBase->attributes()->id) && $eachBase->attributes()->id != "" && $eachBase->attributes()->id != 0)
						{	

							$baseAdditionAttr = (int) $eachBase->attributes()->id;

							if(array_key_exists($baseAdditionAttr, $baseId))
							{		
								 if($i > 0)
								  $insertQuery .= ",";

								$insertQuery .= " (".$additionId.",".$baseId[$baseAdditionAttr].",'".date('Y-m-d H:i:s')."') "; 


							}

							$i++;
						}

					}
					
					$wpdb->query($insertQuery);
				}
		}	
		else
		{
				if(isset($countResults) && COUNT($countResults) > 0)
				{
					$deleteQuery = "DELETE FROM bkx_addition_base WHERE addition_id = ".$additionId;

					$wpdb->query($deleteQuery);
				}
		}

		$Query = "";

	}
	

}

	$_SESSION['success'] =	'File imported successfully !';
}
else
{
	$_SESSION['error'] = 'Only XML file is valid for importing data !';
}

}

unlink($target_file);

ob_clean();
header("Location:".$_SERVER['HTTP_REFERER']);
