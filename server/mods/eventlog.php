<?php
class EventLog {
	//Constants
	const SECURITY_EVENT="Security";
	const SERVER_EVENT="Server";
	const CLIENT_EVENT="Client";
	const CLIENTHOST_EVENT="Client Host";
	
	//New log entry
	static function  addEntry($link, $type, $text, $hide_ip=false){
		mysqli_query($link,"INSERT INTO LOG (TIMESTAMP, IP, TYPE,ENTRY) VALUES ('".date("U")."','".($hide_ip?"-":$_SERVER['REMOTE_ADDR'])."','$type','$text')");
	}
	
	//Return log entry
	static function  getEntry($link, $id){
		$res=mysqli_fetch_row(mysqli_query($link,"SELECT * FROM LOG WHERE ID=$id"));
		return $res?$res:null;
	}
	
	//Clear entire log
	static function clear($link){
		mysqli_query($link, "TRUNCATE TABLE LOG");
	}
	static function oldest($link){
		mysqli_query($link, "TRUNCATE TABLE LOG");
	}
};

?>