<?php
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
//php function definitions

function ccaaemail($subject, $message){
	return ccaemail("Webmaster", "...", $subject, $message);
}
function ccaemail($name, $email, $subject, $message){ //emails from general website
	$hmessage = $message;
	$hmessage = '
	<html>
	<head>
	  <title>SDBMWCCA Website</title>
	  <link rel="shortcut icon" href="..." />
	  <link rel="stylesheet" href=".." type="text/css" />
	</head>
	<body>
	  <div id="top-logo" >
		<img src="..." width="165" height="165" alt="San Diego BMW CCA logo" /><img src="..." width="700" height="165" alt="San Diego Bay with BMWs" />
	  </div>
		<div id="calendar">
		<p><strong>
		'.$hmessage.'
		</strong></p>
		</div>
		<div id="bottom-info">
		<table width="100%" border="0" cellpadding="5px">
		  <tr>
			<th scope="col"><a href="..." target="_new"><img src="..." alt="Like us on Facebook" /></a></th>
			<th scope="col"><a href="..." target="_new"><img src="..." alt="Follow sdbmw on Twitter" height="35"/></a></th>
		  </tr>
		</table>
	  </div>
		<div id="bottom-logo" >
		<img src="..." alt="" />
	  </div>
	</body>
	</html>
	';
	$args = array(
		'key' => '...',
		'message' => array(
			"html" => $hmessage,
			"text" => $message,
			"from_email" => "...",
			"from_name" => "...",
			"subject" => $subject,
			"to" => array(array("email" => $email, "name" => $name)),
			"track_opens" => true,
			"track_clicks" => true,
			"auto_text" => true
		)
	);

	$curl = curl_init('https://mandrillapp.com/api/1.0/messages/send.json' );
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($args));
	$response = curl_exec($curl);
	curl_close( $curl ); 

	if(strpos($response,"sent") != false)
		return true;
	else
		return false;
}

////////////////////////////////////DATABASE FUNCTIONS
//currently using mysqli, but all db interaction occurs via these functions, allowing any abstraction layer to be easily implemented. 
$Db_Link = NULL;

function Sql_Connect(){
	global $Db_Link;
	if(!isset($Db_Link))
	{
		$Db_Link = mysqli_connect(...);
		if(mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			die();
		}
	}
	return true;
}

function Sql_Query($query){
	global $Db_Link;
	$result = mysqli_query($Db_Link,$query);
	//$result = $Db_Link->prepare($query);
	if($result == FALSE)
	{
		$email = 'Error in this query: "'.$query.'"<br>Error: '. mysqli_error($Db_Link)." on page ".$_SERVER['PHP_SELF']."?".http_build_query($_GET);
		ccaaemail("SQL ERROR", $email);
		echo "An error has occurred, the administrator has been notified.";
		Sql_Disconnect();
		die();
	}
	return $result;
}

function Sql_Insert_Id(){
	global $Db_Link;
	return $Db_Link->insert_id;
}

function Sql_Affected_Rows(){
	global $Db_Link;
	return $Db_Link->affected_rows;
}

function Sql_Num_Rows($result){
	return $result->num_rows;
}

function Sql_Num_Field($result){
	return $result->field_count;
}

function Sql_Result($result, $num, $name){
	global $Db_Link;
	$result->data_seek($num);
	$dataset = $result->fetch_assoc();
	return $dataset[$name];
}

function Sql_Fetch_Row($result){
	global $Db_Link;
	return $result->fetch_row();
}

function Sql_CleanInput($input){
	global $Db_Link;
	$input = stripslashes($input);
	$input = $Db_Link->real_escape_string($input);
	return $input;
}

function Sql_Disconnect(){
	global $Db_Link;
	if(isset($Db_Link)){
		mysqli_close($Db_Link);
		$Db_Link = NULL;
	}
	return true;
}
?>
