<?php

$pathdb =  __DIR__ . "/coninfo.php";
$pathwrap =  __DIR__ . "/dbwrapper.php";

include($pathwrap);

class ConDB
{

  var $dbs = "wa";
  var $result;
  var $db;
  var $debug = 0;

  function ConDB($dbs, $qstr)
  {
    require("coninfo.php");
    $result;
    $db;
    $debug = 0;
    $this->db = mysql_connect($hostdb, $userdb, $passdb);
    //print_r($this->db);
    if (($this->db))
      $oke = 1;
    else {
      if ($this->debug == 0)
        die("Error " . mysql_errno() . " : " . mysql_error());
    }
    //mysql_select_db("fpdappscpb",$this->db);
    mysql_select_db($dbs, $this->db);
    if (($this->result = @mysql_query($qstr, $this->db))) {
      return;
    } else {
      if ($this->debug == 0)
        die("Error " . mysql_errno() . " : " . mysql_error());
    }
  }

  function close()
  {
    mysql_close($this->db);
  }
}
