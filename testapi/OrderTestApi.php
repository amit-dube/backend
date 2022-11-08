<?php

/**
* Create the class for testing different API
* through the curl, calling API
*/
class OrderTestApi{
	
	/**
	* Get all the CSV Order
	*/
	public function getCSVOrder(){
		
		$ch = curl_init('http://localhost/codeigniter4/');
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		 
		curl_close($ch);
		echo $data;
		
		
	}
	
	/**
	* Get the CSV order By id
	* @param id
	*/
	public function getCsvOrderById($id){
		
		$ch = curl_init("http://localhost/codeigniter4/getOrderById/$id");
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);		 
		curl_close($ch);
			
		echo $data;
		
		
	}
	
	
	/**
	* ADD the data in CSV
	* @param array
	*/
	public function addOrderToCSV($params){
	  $postData = '';
	  
	  $postData = $params;
	 
		$ch = curl_init();  
	 
		curl_setopt($ch,CURLOPT_URL,'http://localhost/codeigniter4/addOrderDataToCsv');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
	 
		$output=curl_exec($ch);
	 
		curl_close($ch);
		
		return $output;
			 
	}
	
	
	/**
	* Update the order in CSV
	* @param array
	*/
	public function updateOrderData($params){
	  $postData = '';
	  $output  = '';
	  $postData = $params;
	 $data_string = json_encode($postData);
	 //$data_string = json_encode(array($postData));

		//API URL
		$url="http://localhost/codeigniter4/editOrderDataToCsv";

		$ch = curl_init( $url );
		# Setup request to send json via PUT.
		/*$data_string = json_encode($params);
		 
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		# Send request.
		$result = curl_exec($ch);
		curl_close($ch);	
		return $result; */
		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);
		print_r ($result);
	}
	
	
	
	/**
	* Delete the recoed by id
	* @param
	*/
	
	public function deletaOrderData($params){
		
		$postData = '';
	  
		$postData = $params;
		//$ch = curl_init();  	 
	
		$data_string = json_encode(array("customer" =>$postData));

		$ch = curl_init("http://localhost/codeigniter4/deletaOrderData");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch);
		curl_close($ch);
		print_r ($result);
		
	}
	
	
} //Class end
?>