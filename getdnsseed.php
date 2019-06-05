<?php

//This program is ran every 15 minutes via Cron Job
//It grabs data from the exchange such as the total amount of CNs running and saves it into a file
//File 1 has all the cns

define("DEFAULT_SERVER","127.0.0.1"); //This is the CN server that we try to connect to
define("DEFAULT_PORT",55364); //CN Server port on mainnet

//Create a connection 
$fp = stream_socket_client('tcp://'.DEFAULT_SERVER.':'.DEFAULT_PORT,$errno,$errmsg);
if(!$fp){
	exit; //No connection could be made
}
stream_set_timeout($fp, 30); //Wait for 30 seconds before timing out

try{
	$cn_rows = array();
	$totalcn = 0;
	$numpts = 0;
	$page = 0;
	do{
		//Create a json query to the server
		set_time_limit(120); //Script will timeout after 2 minutes
		$query = [
		    "cn.method" => "cn.getlist",
		    "cn.response" => 0,
		    "cn.page"   => $page,
		];
		//Encode this array into json and send to server
		$json_string = json_encode($query);
		$data_length = strlen($json_string); //This will determine our datasize
		$data_length_int = pack("I",$data_length); //Convert the data string to a binary file (should be little endian, like NebliDex)
		fwrite($fp,$data_length_int);
		fwrite($fp,$json_string);

		//Now read the integer
		$data_length_int = fread($fp,4); //Read the binary integer returned
		$data_length = unpack("I",$data_length_int); //Get the actual data length
		if($data_length <= 0){
			throw new Exception("Failed to get response from server");
		}
		$json_string = fread($fp,$data_length[1]); //Get the returned information
		$response = json_decode($json_string,true);

		$numpts = (int)$response["cn.numpts"];

		foreach($response["cn.result"] as $cn_ip){
		    $cn_rows[$totalcn] = $cn_ip["cn.ip"];
		    $totalcn++;
		}

		$page++; //Go to next page
	}while($numpts > 0);

	if($totalcn > 0){
		shuffle($cn_rows); //Put the array in random order
		//Create a file and save this array data into it
		$file = fopen("cn_list.dat", "w");
		if(flock($file, LOCK_EX)){
			for($i = 0;$i < count($cn_rows);$i++){
				//Add each line
				fwrite($file,$cn_rows[$i]."\n");
			}
			fclose($file);
		}
	}
}catch(Exception $e){
	error_log("Failed to get NebliDex servers, error: ".$e);
}

if(isset($fp)){
	fclose($fp); //Close the stream when we are done
}

?>
