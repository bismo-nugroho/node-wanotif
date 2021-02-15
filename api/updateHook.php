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

function getHook($source){
  $data = json_decode($source);
  
  

    $sqlunread="
    select *
      from
      tbl_chats
      where id = ".$data->id."
      limit 1 
        ";
  //echo  $sqlunread;
  
   $newdb = new ConDB("",$sqlunread);
  //$num_rows = mysql_num_rows($newdbunread->result);  
   $rows = mysql_fetch_array($newdb->result);
   //print_r($rows);
   
  if ($rows){
  
  $processed = "null";
  $sent = "null";
  $received = "null";
  
  if ($data->ack === 1 ){
    $processed = "'".date("Y-m-d H:i:s")."'";
  }else if ($data->ack === 2 ){
    if ($rows["processed"] === ""){
      $processed = "'".date("Y-m-d H:i:s")."'"; 
    }
     $sent = "'".date("Y-m-d H:i:s")."'"; 
  }else if ($data->ack === 3 ){
    if ($rows["processed"] === ""){
      $processed = "'".date("Y-m-d H:i:s")."'"; 
    }
    
     if ($rows["sent"] === ""){
      $sent = "'".date("Y-m-d H:i:s")."'"; 
    }
       
     $received = "'".date("Y-m-d H:i:s")."'";      
  }else{
     if ($rows["processed"] === ""){
      $processed = "'".date("Y-m-d H:i:s")."'"; 
    }
    
     if ($rows["sent"] === ""){
      $sent = "'".date("Y-m-d H:i:s")."'"; 
    }
       
     $received = "'".date("Y-m-d H:i:s")."'";    
  }
  
  $sql ="
  update tbl_chats
    set  ack = ".$data->ack.",
         chatid = '".$data->chat_id."',
         id_chat = '".$data->idchat."',
         processed = ".$processed.",
         sent = ".$sent.",
         received = ".$received."
      where id = ".$data->id."
        ";
  //echo  $sqlunread;
  
   $newdb = new ConDB("",$sql);    

   if ($newdb){
   }
    $data = (object)[];
    $data->status = "true";
    return json_encode($data);    
  }else{
    return '[{ "status" : "false" }]';
  }
 
 }
 
 echo   getHook($source);
 //echo "it works";
  
  ?>