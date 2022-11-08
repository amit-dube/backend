<?php 
namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
	
	/**
	* 
	* readOrderCsvToArray
	* Read the CSV file records
	* Reads all the rows of csv and store into an array
	* @param relative path of csv filename
	* return array
	*
	*/
	
	function readOrderCsvToArray($file_name){
		//check if file exists , if not throws an exception.
		$this->checkFileExist($file_name);
		$file = fopen($file_name, 'r');
			$headers = fgetcsv($file);
			$data = [];
			while (($row = fgetcsv($file)) !== false){
				$item = [];
				foreach ($row as $key => $value)
				$item[$headers[$key]] = $value ?: null;
				$data[] = $item;
			}
			fclose($file);
			return $data;
	}
	
	
	/**
	* 
	* getOrderCsvById
	* Read the CSV data by ID
	* Store the specific CSV record in json
	* @param id
	* return json array
	*/
	function getOrderCsvById($id){
		$status = false;
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv file define app/Constant.php
		
		//check if file exists , if not throws an exception.
		$this->checkFileExist($filename);
			
		//Read data in csv file
		$data = $this->readOrderCsvToArray($filename);
		
		$json_arr[]='';
			if(count($data)>0){
				foreach($data as $subKey => $subArray){
					if($subArray['id'] == $id){
						if(count($data)>0){
						$json_arr = array(
						'id'=> $subArray['id'] ?? '0',

						'name'=> $subArray['name'] ?? '0',

						'state'=> $subArray['state'] ?? '0', 

						'zip'=> $subArray['zip'] ?? '0',

						'amount'=> $subArray['amount'] ?? '0',

						'qty'=> $subArray['qty'] ?? '0', 

						'item'=> $subArray['item'] ?? '0'
						);
						return $json_arr;										 
						}
					}
				}
			}
		
		return $status;
	}
	
	
	/**
	*
	*
	* saveOrderToCSV
	* Save the data in csv as new row
	* push new array data exiting array and write into csv file
	* @param array
	*/
	function saveOrderToCSV($newdata){
		$status = false; // populating the error message in controller by defulat false
		
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv file define app/Constant.php
		
		//check if file exists , if not throws an exception.
		$this->checkFileExist($filename);
		
		//Read the file
		$data = $this->readOrderCsvToArray($filename);
		
		// Genrate the next row id
		$last_record=end($data);
		if(!empty($last_record)){
			$newid = $last_record['id']+1;
		}else{
			$newid = 1;
		}
		
		$newarr=array('id'=>$newid);
		$newdata = array_merge($newarr,$newdata);		
		$data[] = $newdata;
		
		//Add the new record in CSV 
		$datalabel = array("id","name","state","zip","amount","qty","item");	
		foreach($data as $subKey => $subArray){
				// Loop through file pointer and a line
				$output = fopen($filename, 'w');  
				fputcsv($output, $datalabel); 
				if(count($data)>0){
					foreach ($data as $record){			
						fputcsv($output, $record);
					}
				}else{
					//$record = array("No Records Found");
					fputcsv($output, $record);
				}
				fclose($output);
				$status=true;
			}		
		
		$ret_result = array('id'=>$newid, 'st'=>$status);
		return $ret_result;
	}
	
	/**
	* 
	* updateOrderToCSV
	* Update the record in csv file
	* read order data from csv file , compare csv row-id with given id and
    * re-assign new array to selected row
	* @param array $edit_data
	*/
	
	function updateOrderToCSV($edit_data){
		$status = false; // populating the error message in controller by defulat false
		
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv file define app/Constant.php
		
		//check if file exists , if not throws an exception.
		$this->checkFileExist($filename);
			
		//Read the file
		$data = $this->readOrderCsvToArray($filename);
		
		if(count($data)>0){
			// Update the record in specific row compate the csv row id edit field id 
			foreach($data as $subKey => $subArray){
				if($subArray['id'] == $edit_data['id']){					
					$data[$subKey]['id'] =  $edit_data['id'];
					$data[$subKey]['name'] =  $edit_data['name'];
					$data[$subKey]['state'] =  $edit_data['state'];
					$data[$subKey]['zip'] =  $edit_data['zip'];
					$data[$subKey]['amount'] =  $edit_data['amount'];
					$data[$subKey]['qty'] =  $edit_data['qty'];
					$data[$subKey]['item'] =  $edit_data['item'];
				}
			}
			
			$datalabel = array("id","name","state","zip","amount","qty","item");
			$output = fopen($filename, 'w');
			fputcsv($output, $datalabel); 
			foreach ($data as $record){
				fputcsv($output, $record);
			}
			fclose($output);
			$status=true;
		}
			return $status;

	}
	
    /**
     * deleteOrderToCsvById
     * read order data from csv file , compare row-id with given id and
     * splice/remove the array from existing order data
     * @param  $id
     * @return json
     */
	public function deleteOrderToCsvById($id){
		$status = false; // populating the error message in controller by defulat false
		
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv file define app/Constant.php
		
		//check if file exists , if not throws an exception.
		$this->checkFileExist($filename);
		
		//Read the file
		$data = $this->readOrderCsvToArray($filename);
				
		$datalabel = array("id","name","state","zip","amount","qty","item");		
		foreach($data as $subKey => $subArray){
			if($subArray['id'] == $id){
				unset($data[$subKey]); //Delete the exiting order by id
				
				// Loop through file pointer and a line
				$output = fopen($filename, 'w');
				fputcsv($output, $datalabel); 
				if(count($data)>0){
					foreach ($data as $record){
						fputcsv($output, $record);
					}					
				}
				fclose($output);
				$status=true;
			}
		}
		return $status;
	}
	
	/**
	*getLastCsvOrderId
	*Get the last inserted record in the CSV file
	*@param Relative file path
	*@return array
	*/
	public function getLastCsvOrderId(){
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv file define app/Constant.php
		$last_row ='';
		//check if file exists , if not throws an exception.
		$this->checkFileExist($filename);
		$data = $this->readOrderCsvToArray($filename);
		$last_row = (end($data)); 
		$last_id='';
		if(!empty($last_row)){
			$last_id = $last_row['id'];
		}else{
			$last_id=0;
		}
		return $last_id; 
	}
	
	/**
     * checkFileExist
     * @param  string $csvFilePath
     * @return void
     */
    public function checkFileExist(string $csvFilePath)
    {
        try {
            if(!file_exists($csvFilePath)){             
				$response = [						
						'status'   => 404,
						'error'    => false,
						'messages' => "File does not exists"
					];	
					return $this->respondCreated($response);
				
            }
        } catch (\Exception $e) {
            
			exit($e->getMessage());
        }
    }
	
	
} // Class End 