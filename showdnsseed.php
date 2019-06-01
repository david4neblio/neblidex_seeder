<?php

//This is the file that displays a list of CNs that is queried from another file
//When a user first connects to NebliDex, they will come here to get the seed list, then they will query NebliDex CNs for more seed lists
//This will be used to compile a list of reliable CNs

//It is also used to return the IP address of the remote host

$version = 1; //This is the API version

$dbpath = ""; //Path to the relevant data

if(!empty($_GET["api"])){
	//This is a simple API
	$request = strtolower($_GET["api"]);
	if(!empty($_GET["v"])){
		$version = (int)$_GET["v"];
	}

	if($version == 1){
		if($request == "my_remote_ip"){
			echo $_SERVER['REMOTE_ADDR'];
		}
	}else{
		exit;
	}
}else{
	//No API used, just show the CN list
	EchoCNList($dbpath);
}

function EchoCNList($path){
	//This function will display the entire list of nodes to anyone requesting it
	$cn_list = array();
	$cn_num = 0;
	if(!file_exists($path."cn_list.dat")){ //File doesn't exist
		return;
	}
	$file = fopen($path."cn_list.dat", "r");
	if($file) {
	    while (($line = fgets($file)) !== false) {
	        $cn_list[$cn_num] = trim($line);
	        $cn_num++; //Read all the CNs running
	    }
	    fclose($file);
	}

	//Now show it to the user
	$seed_list = [
	    "cn_seed" => $cn_list  //List of CNs
	];
	echo json_encode($seed_list);
}

?>
