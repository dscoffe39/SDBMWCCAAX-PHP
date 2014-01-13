<?php
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
//php function definitions


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
