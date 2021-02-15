<?php
require($pathdb); 
$dbconn = mysqli_connect($hostdb,$userdb,$passdb); 
//print_r($dbconn);

 //--generic wrapper
define ('LIKE','LIKE');


function mysql_connect($hostdb,$userdb,$passdb){
  global $dbconn;
  //print_r($dbconn);
  //if (!$dbconn){
	$dbconn = mysqli_connect($hostdb,$userdb,$passdb);
  //}
	//print_r($dbconn);
  return $dbconn;
}

function prnMsg($Msg,$Type='info', $Prefix=''){

	echo '<P>' . getMsg($Msg, $Type, $Prefix) . '</P>';

}//prnMsg

function getMsg($Msg,$Type='info',$Prefix=''){
	$Colour='';
	switch($Type){
		case 'error':
			$Class = 'error';
			$Prefix = $Prefix ? $Prefix : 'ERROR' . ' ' .'Message Report';
			break;
		case 'warn':
			$Class = 'warn';
			$Prefix = $Prefix ? $Prefix : 'WARNING' . ' ' . 'Message Report';
			break;
		case 'success':
			$Class = 'success';
			$Prefix = $Prefix ? $Prefix : 'SUCCESS' . ' ' . 'Report';
			break;
		case 'info':
		default:
			$Prefix = $Prefix ? $Prefix : 'INFORMATION' . ' ' .'Message';
			$Class = 'info';
	}
	return '<DIV class="'.$Class.'"><P><B>' . $Prefix . '</B> : ' .$Msg . '<P></DIV>';
}//getMsg

function IsEmailAddress($TestEmailAddress){

/*thanks to Gavin Sharp for this regular expression to test validity of email addresses */

	if (function_exists("preg_match")){
		if(preg_match("/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/", $TestEmailAddress)){
			return true;
		} else {
			return false;
		}
	} else {
		if (strlen($TestEmailAddress)>5 AND strstr($TestEmailAddress,'@')>2 AND (strstr($TestEmailAddress,'.co')>3 OR strstr($TestEmailAddress,'.org')>3 OR strstr($TestEmailAddress,'.net')>3 OR strstr($TestEmailAddress,'.edu')>3 OR strstr($TestEmailAddress,'.biz')>3)){
			return true;
		} else {
			return false;
		}
	}
}

Function ContainsIllegalCharacters ($CheckVariable) {

	if (strstr($CheckVariable,"'")
		OR strstr($CheckVariable,'+')
		OR strstr($CheckVariable,"\"")
		OR strstr($CheckVariable,'&')
		OR strstr($CheckVariable,"\\")
		OR strstr($CheckVariable,'"')){

		return true;
	} else {
		return false;
	}
}


function pre_var_dump(&$var){
	echo "<div align=left><pre>";
	var_dump($var);
	echo "</pre></div>";
}

class XmlElement {
  var $name;
  var $attributes;
  var $content;
  var $children;
};

 
//require_once ($PathPrefix .'includes/MiscFunctions.php');

//DB wrapper functions to change only once for whole application
function DB_query ($SQL,
		&$Conn,		
		$Transaction=false,
		$TrapErrors=true){

	global $PathPrefix;	
	$Result=mysqli_query($Conn, $SQL);
			
	if ($DebugMessage == '') {
		$DebugMessage = "The SQL that failed was";
	}
	$ErrMsg = DB_error_msg($Conn);
	
	if (DB_error_no($Conn) != 0 AND $TrapErrors==true){
		// msg to user, there is error in sql
		prnMsg($ErrorMessage.'<br>' . $ErrMsg,'error', 'Database Error');		
		// display error sql to screen
		prnMsg($DebugMessage. "<br>$SQL<br>",'error','Database SQL Failure');
		
		// get error sql for logfile
		$ErrorSQL = "INSERT INTO t_error (error_date, usrid, qry_str, err_msg) ".
					"VALUES (NOW(),'" .trim($_SESSION['UserId']). "', ".
					"'" .DB_escape_string($SQL). "', '" .DB_escape_string($ErrMsg). "')"; 
				
		// if type transcation==true than rollback
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL,$Conn);
			if (DB_error_no($Conn)!=0){
				prnMsg('Error Rolling Back Transaction', '', 'Database Rollback Error' );
			} 			
		} 
		
		// insert sql error to logfile
		mysqli_query($Conn, $ErrorSQL);
		
		exit;
		
	}
	return $Result;
}

function DB_fetch_row (&$ResultIndex) {
	$RowPointer=mysqli_fetch_row($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_assoc (&$ResultIndex) {
	$RowPointer=mysqli_fetch_assoc($ResultIndex);
	Return $RowPointer;
}

function DB_fetch_array (&$ResultIndex) {
	$RowPointer=mysqli_fetch_array($ResultIndex);
	Return $RowPointer;
}

function DB_data_seek (&$ResultIndex,$Record) {
	mysqli_data_seek($ResultIndex,$Record);
}

function DB_free_result (&$ResultIndex){
	mysqli_free_result($ResultIndex);
}

function DB_num_rows (&$ResultIndex){
	return mysqli_num_rows($ResultIndex);
}

function DB_affected_rows(&$ResultIndex){
	global $db;
	return mysqli_affected_rows($db);	
}

function DB_error_no(&$Conn){
	return mysqli_errno($Conn);
}

function DB_error_msg(&$Conn){
	return mysqli_error($Conn);
}

function DB_escape_string($String){
	global $dbconn;
	//return mysqli_real_escape_string($db, htmlentities($String));
	//return mysqli_real_escape_string($db, htmlentities($String, ENT_COMPAT, 'ISO-8859-1'));
	return mysqli_real_escape_string($dbconn, htmlspecialchars($String, ENT_COMPAT, 'ISO-8859-1'));		
}

function DB_show_tables(&$Conn){
	$Result = DB_query('SHOW TABLES',$Conn);
	Return $Result;
}

function DB_show_fields($TableName, &$Conn){
	$Result = DB_query("DESCRIBE $TableName",$Conn);
	Return $Result;
}

function DB_last_insert_id(&$Conn){
	return mysqli_insert_id($Conn);
}

function INTERVAL( $val, $Inter ){
		global $dbtype;
		return "\n".'INTERVAL ' . $val . ' '. $Inter."\n";
}

function DB_QuerySelect($FieldName, $TblName, $WhereClause) {
	$SQL = "SELECT $FieldName FROM $TblName $WhereClause ";
    $Result = DB_query($SQL, $db);
    return $Result;
}

//---------------


//rename_function('mysqli_fetch_array', 'original_mysqli_fetch_array');
//override_function('mysql_fetch_array', '&$ResultIndex', '	$RowPointer = mysqli_fetch_array($ResultIndex);return $RowPointer;  ');
//rename_function("__overridden__", 'dummy_feof');
function mysql_fetch_array(&$ResultIndex) {
	$RowPointer = mysqli_fetch_array($ResultIndex);
	return $RowPointer;  
}




function mysql_num_rows(&$ResultIndex){
	return mysqli_num_rows($ResultIndex);
}

function mysql_select_db($dbName,&$db) {
 return mysqli_select_db($db,$dbName);
 
}

function mysql_insert_id(){
  global $dbconn;
  return mysqli_insert_id($dbconn);
}

function mysql_errno(){
  global $dbconn;
	return mysqli_errno($dbconn);

}

function mysql_error(){
  global $dbconn;
  //echo "errors";
  return mysqli_error($dbconn);
}

function mysql_close(&$Conn){
  return mysqli_close($Conn);
  //return 1

}

function mysql_real_escape_string($str){
return DB_escape_string($str);
}

function split($chr,$str){
return explode($chr,$str);

}

//DB wrapper functions to change only once for whole application
function mysql_query ($SQL,
		&$Conn,		
		$Transaction=false,
		$TrapErrors=true){ 

	global $PathPrefix;	
	$Result=mysqli_query($Conn, $SQL);
			
	if ($DebugMessage == '') {
		$DebugMessage = "The SQL that failed was";
	}
  
	$ErrMsg = DB_error_msg($Conn);
	
	if (DB_error_no($Conn) != 0 AND $TrapErrors==true){
  echo "error";
		// msg to user, there is error in sql
		prnMsg($ErrorMessage.'<br>' . $ErrMsg,'error', 'Database Error');		
		// display error sql to screen
		prnMsg($DebugMessage. "<br>$SQL<br>",'error','Database SQL Failure');
		
		// get error sql for logfile
		$ErrorSQL = "INSERT INTO t_error (error_date, usrid, qry_str, err_msg) ".
					"VALUES (NOW(),'" .trim($_SESSION['UserId']). "', ".
					"'" .DB_escape_string($SQL). "', '" .DB_escape_string($ErrMsg). "')"; 
				
		// if type transcation==true than rollback
		if ($Transaction){
			$SQL = 'rollback';
			$Result = DB_query($SQL,$Conn);
			if (DB_error_no($Conn)!=0){
				prnMsg('Error Rolling Back Transaction', '', 'Database Rollback Error' );
			} 			
		} 
		
		// insert sql error to logfile
		//mysqli_query($Conn, $ErrorSQL);
		
		exit;
		
	}else{

  }
	return $Result;
}
