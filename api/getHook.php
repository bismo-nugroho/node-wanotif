<?php
include("Connection.php");

function get_day(){
  if (intval(date("H"))>=0 && intval(date("H"))<=11)
   return "Good morning";
  else if  (intval(date("H"))>11 && intval(date("H"))<=15)
   return "Good afternoon";
  else if  (intval(date("H"))>15 && intval(date("H"))<=23)
   return "Good evening";     
}

function loadUnSend(){
    $sqlunread="
    select *
      from
      tbl_chats
      where ack is NULL
      and comm = 'OU'
      order by  id_chat
      limit 1 
        ";
  //echo  $sqlunread;
  
  $newdb = new ConDB("",$sqlunread);
  //$num_rows = mysql_num_rows($newdbunread->result);  
   $rows = mysql_fetch_array($newdb->result);
   //print_r($rows);
   
  if ($rows>0){
    //$rows = mysql_fetch_array($newdb->result);   
    $data = (object)[];
    $data->id = $rows["id"];
    $data->dest = $rows["dest"];
    $data->message = $rows["message"];
    $data->status = "true";
    echo json_encode($data);    
  }else{
  echo '[{ "status" : "false" }]';
  }
 
 }
 
 echo   loadUnSend();
 //echo "it works";
  
  ?>