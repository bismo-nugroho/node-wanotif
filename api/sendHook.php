<?php
include("Connection.php");

$source = $_GET["source"];

function get_day(){
  if (intval(date("H"))>=0 && intval(date("H"))<=11)
   return "Good morning";
  else if  (intval(date("H"))>11 && intval(date("H"))<=15)
   return "Good afternoon";
  else if  (intval(date("H"))>15 && intval(date("H"))<=23)
   return "Good evening";     
}


function listMenu($data,$msgs){
  $msg = explode(" ",$data->message);
  
  if (count($msg) > 0 ){
    $response = "List Menu".PHP_EOL;
    $response .= "--------------------".PHP_EOL;
    $response .= "/listtask  : List Task ".PHP_EOL;   
    $response .= "/appr [task_id] : Approve Task ".PHP_EOL;   
    $response .= "/restart  : Restart BOT ".PHP_EOL;  
    $response .= "--------------------".PHP_EOL; 
    $response .= "Silahkan masukkan pilihan anda ".PHP_EOL;
    return $response;
  }
  return "";
}

function approveTask($data,$msgs){
  $msg = explode(" ",$data->message);
  $resposese = "";
  
  
  if ( count($msg) < 2 ){
    $response = "Format Approve salah".PHP_EOL;
    $response .= "-----------------------------".PHP_EOL;
    $response .= "Format Approve adalah /appr [12345]".PHP_EOL;
    $response .= "Silahkan ketik kembali ".PHP_EOL;
  }else{
    $response = "";
  }

  
  return $response;
}

function responseAI($data,$msg){
  $msg = $data->message;
  $response = "";
  $body = trim(strtolower($msg));


  $sqlunread="
  select *
    from
    tbl_response
    where parameter = '".$body."'
    limit 1 
      ";
//echo  $sqlunread;

$newdb = new ConDB("",$sqlunread);
//$num_rows = mysql_num_rows($newdbunread->result);  
 $rows = mysql_fetch_array($newdb->result);

 if ($rows){
  return  $rows["response"];
 }else{
   return "";
 }

}


function sendHook($source){
  $data = json_decode($source);
  
  $msg = explode(" ",$data->message);
  
  $response = "";
  
  
  switch (strtolower($msg[0])) {
    case 'menus':
       $response =  listMenu($data,$msg);    
       break;
    case '/appr':
       $response = approveTask($data,$msg); 
       break;
    case 3:
       $var3 = 'Monthly';
       break;
    case 4:
    case 5:
       $var3 = 'Quarterly';
       break;
    default:
       $response = responseAI($data,$msg);
       break;      
}

  
  $dest = explode("@",$data->idchat)[0];  
  
  $sql ="
   insert into tbl_chats(`id_user`,`id_chat`,`dest`,`sendtype`,`ack`,`comm`,`message`,`submitted`)
   VALUES ('','".$data->idchat."','".$dest."','person','1','IN','".$data->message."','".date("Y-m-d H:i:s")."')";
  //echo  $sql;  
   $newdb = new ConDB("",$sql);    
   if ($newdb){
    $data = (object)[];
    $data->status = "true";
    $data->response = $response;
    return json_encode($data);    
  }else{
    return '[{ "status"   : "false",
               "response" : "" 
            }]';
  }
 
 }
 
 echo   sendHook($source);
 //echo "it works";
