<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\OrderModel;
use Codeigniter\Controller;

/**
* CORS-compliant method.  
* It will allow any GET, POST, DELETE or OPTIONS requests from any origin.
*/
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");

class OrderController extends ResourceController
{
	use ResponseTrait;
	/**
     * 
	 * getorderList
	 * Open a CSV file readonly mode
	 * Read all the CSV file record row by row and store in array
     * @return Json
     *  
     */
	 
	public function getorderList(){
		
		
		$filename = RELATIVEFILEPATH; // RELATIVEFILEPATH relative file path of csv filename define app/Constant.php
		
		$order_model = new OrderModel(); // Create the object of Model class
		
		//check if file exists , if not throws an exception. with Model class object $order_model
		$order_model->checkFileExist($filename);
		
		//A function using an exception should be in a "try" block
		try{
			/**
			* CSV file open readonly mode
			* Read the data and return in array
			*/
			$file = fopen($filename, 'r');
			$headers = fgetcsv($file);		//fgetcsv() function parses a line from an open file	
				$data = [];  				// Create a empty array for storing reading csv data and return 
				while(($row = fgetcsv($file)) !== false){
					$item = [];
					foreach ($row as $key => $value)
					$item[$headers[$key]] = $value ?: null;
					$data[] = $item;
				}		
				fclose($file); // Closed the file after reading completed
				
				return $this->respond($data);
				
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message.
			$error_response = $e->getMessage();
			
			/**
			* Used for Codeigniter generic function.
			* @param $error_response
			* @return Json
			*/		
			
			return $this->fail($error_response);
		}			
	}
	
	/**
     *  
	 * getOrderById
	 * Open a CSV file readonly mode
	 * Read the CSV file record by id
     * @return json
     *  
     */
	
	public function getOrderById($id){
		
		$data = '';
		
		$order_model = new OrderModel(); // Create the object of Model class
		
		// Call the Class model function for read the specific record by id
		$data = $order_model->getOrderCsvById($id);
		
		//A function using an exception should be in a "try" block
		try{
			if($data!=''){
				
				return $this->respond($data);
				
			}else{	
				$response = [
				'status' => 204,
				'error' => true,
				'message' => "Sorry, No data be found"			
				];
			return $this->respondCreated($error_response);			
				
			}
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message with code.
			 $error_response = $e->getMessage();				 
			
			/**
			* Used for Codeigniter generic function.
			* @param $error_response
			* @return Json
			*/
			
			return $this->fail($error_response);
		}
	}
	
	/**
     * 
	 * addOrderDataToCsv 
	 * Add record in CSV file with new record id
     * @param array format (name,state,zip,amount,qtym)
	 * @return Json
     */
	Public function addOrderDataToCsv(){	
			
			//Get the Post Data
			$data = [
				'name' => $this->request->getVar('name'),
				'state' => $this->request->getVar('state'),
				'zip' => $this->request->getVar('zip'),
				'amount' => $this->request->getVar('amount'),
				'qty' => $this->request->getVar('qty'),
				'item' => $this->request->getVar('item'),
				];
		
		// Call validate method to check the Order Data validation
		$errors_response = $this->validatingOrderData($data);
		
		if (!empty($errors_response)) {				
											
				$response = [
				'status' => 402,
				'error' => true,
				'message' => $errors_response, // Get the error message				
			];
			return $this->respondCreated($response);
		}
		//A function using an exception should be in a "try" block
		try{
				$order_model = new OrderModel(); // Create the object of Model class				

				
				/**
				* Call the Model class method to save the record in new row
				* @param $data array 
				*/
				$res = $order_model->saveOrderToCSV($data); 
				
				if(isset($res['st'])){
					$response = [
						'data' => $res['id'],
						'status'   => 201,
						'error'    => false,
						'messages' => 'Order created successfully'
					];
										
					return $this->respondCreated($response);
				}else{
					$response = [
						'status'   => 304,
						'error'    => true,
						'messages' => "Failed to create order data"
					];
			return $this->respondCreated($error_response);
				}			
			
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message.
			$error_response = $e->getMessage();
			
			/**
			* Used for Codeigniter generic function.
			* @param $error_response
			* @return Json
			*/
			
			return $this->fail($error_response);
		}

	}
	
		
	/**
     * 
	 * editOrderDataToCsv
	 * Edit a specific record in CSV file
     * @param array format (name,state,zip,amount,qtym)
     * return json
     */
	public function editOrderDataToCsv(){
		$errors_response ='';
		
		// php://input read-only stream that allows you to read raw data from the request body
		$putData = (array) json_decode(file_get_contents("php://input"), true);
		//print_r($putData); exit;
		// Call validate method to check the form field validation
		$errors_response = $this->validatingOrderData($putData);
		if (!empty($errors_response)) {
			
				$response = [
				'status' => 402,
				'error' => true,
				'message' => $this->validator->getErrors() // Get the error message				
				];
				return $this->respondCreated($response);				
			}
			
		//A function using an exception should be in a "try" block
		try{
			
				$order_model = new OrderModel(); // Create the object of Model class
				 
				 
				
				$data = [
				'id' => $putData['id'],
				'name' => $putData['name'],
				'state' => $putData['state'],
				'zip' => $putData['zip'],
				'amount' => $putData['amount'],
				'qty' => $putData['qty'],
				'item' => $putData['item'],
				];
								
				/**
				* Call the Model class updateOrderToCSV method 
				* @param $data array 
				*/
				$res = $order_model->updateOrderToCSV($data); 
				
				if(isset($res)){
					$response = [
						'status'   => 200,
						'error'    => false,
						'messages' => 'Order updated successfully'
						];
					/**
					 * Used after successfully creating a new resource.
					 * @param array
					 * @return Response
					 */
					return $this->respondCreated($response);
				}else{
					$response = [
						'status'   => 304,
						'error'    => true,
						'messages' => 'Failed to update order data'
						];
					return $this->respondCreated($response);					
				}			
			
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message.
			$error_response = $e->getMessage();
			
			/**
			* Used for Codeigniter generic  function.
			* @param $error_response
			* @return Json
			*/
			
			return $this->fail($error_response);
		}		
	}
	
	/**
     * 
	 * deletaOrderData
	 * Delete single or Multiple order row in CSV file
     * @return json response
     *
     */
	public function deletaOrderData()
	{		
				
		$order_model = new OrderModel(); // Create the object of Model class
		//A function using an exception should be in a "try" block
		try{
			
			$data = (array) json_decode(file_get_contents("php://input"), true);
			
				foreach($data as $order){
					if (array_key_exists("id", $order)) {
						$res = $order_model->deleteOrderToCsvById($order['id']);
					}
				}
					
				if($res){
					$response = [
						'status' => 200,
						"error" => false,
						'messages' => 'Order deleted successfully',
						'data' => []
					];
					
					/**
					 * Used after successfully creating a new resource.
					 * @param array
					 * @return Response
					 */
					return $this->respondCreated($response);
				} else {
				$response = [
						'status' => 304,
						"error" => true,
						'messages' => '"Failed to delete order data"',
						'data' => []
					];
					
					/**
					 * Used after successfully creating a new resource.
					 * @param array
					 * @return Response
					 */
					return $this->respondCreated($response);
			}		
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message.
			$error_response = $e->getMessage();
						
			/**
			* Used for Codeigniter generic  function.
			* @param $error_response
			* @return Json
			*/
			return $this->fail($error_response);
		}
	}
	
	
	
	/**
	* getLastOrderCsvId
	* Get the last inserted id of the CSV file
	* return a single record
	*/
	
	public function getLastOrderCsvId(){
		
		$order_model = new OrderModel(); // Create the object of Model class
		//A function using an exception should be in a "try" block
		try{
			$last_id = $order_model->getLastCsvOrderId();
				//echo "last_id".$last_id; exit;
				$last_id = $last_id?$last_id+1 : 1; //last inserted id increment by 1 or return 1;
			
				$response = [
						'data' => $last_id,  
						'status'   => 200,
						'error'    => false,
						'messages' => 'Fetch the last order id increment by 1'
					];					
					
					/**
					 * Used after successfully creating a new resource.
					 * @param array
					 * @return Response
					 */
					return $this->respondCreated($response);
			
		} catch (\Exception $e) { //Catch block retrieves an exception information
			
			//Gets the Exception message, Returns the Exception message.
			$error_response =  $e->getMessage();
			
			/**
			* Used for Codeigniter generic  function.
			* @param $error_response
			* @return Json
			*/
			return $this->fail($error_response);
		}
	}
	
	
	/**
	* validatingOrderData
	* check allowed characters and numbers are specified for indivisual fields
	* checks if any field is mandatory or not
	* @return array
	*/
	
	function validatingOrderData($data_param){
		$rules = [
			"name" => "required|min_length[3]|alpha_space",
			"state" => "required|min_length[2]|alpha",
			"zip" => "required|numeric",
			"amount" => "required|numeric",
			"qty" => "required|numeric",
			"item" => "required",			
		];

		 if($this->validate($rules)){
		$data = [				
				'name' => $data_param['name'],
				'state' => $data_param['state'],
				'zip' => $data_param['zip'],
				'amount' => $data_param['amount'],
				'qty' => $data_param['qty'],
				'item' => $data_param['item'],
				];
		 }else{
             $error = $this->validator->getErrors();
			return $error;
		 }
		
	}
	
	
	
	/*function validatingOrderData(){
		$rules = [
			"name" => "required|min_length[3]|alpha_space",
			"state" => "required|min_length[2]|alpha",
			"zip" => "required|numeric",
			"amount" => "required|numeric",
			"qty" => "required|numeric",
			"item" => "required",			
		];
		
		$messages = [
			"name" => [
				"required" => "Name is required"
			],			
			"state" => [
				"required" => "State is required"
			],
			"zip" => [
				"required" => "Zip is required"
			],
			"amount" => [
				"required" => "Amount is required"
			],
			"qty" => [
				"required" => "Qty is required"
			],
			"item" => [
				"required" => "Item is required"
			],
		];
		
		return $this->validate($rules, $messages);
	}*/
	
		
} // Class
