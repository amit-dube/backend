<?php
require 'OrderTestApi.php';

/**
* Create the object of the class
*/
$obj = new orderTestApi();

/**
* Add the data in csv file
* @return Json
*/

$params = array(
   "name" => "Amit",
   "state" => "KAM",
   "zip" => "83277",
   "amount" => "12",
   "qty" => "7",
   "item" => "ASE38"
);
 
echo $result = $obj->addOrderToCSV($params);
$res = json_decode($result);


// Last inserted id
$id =$res->data;

/**
* A updating the order with the proper data
* @return Json
*/

$params = array(
	"id" => $id,
	"name" => "Amit",
	"state" => "MP",
	"zip" => "65603",
	"amount" => "18",
	"qty" => "5",
	"item" => "FDE8"
);
 
echo $obj->updateOrderData($params);

/**
 * A updating a order without proper data.
 * @return Json 
 */
 
 $params = array(
	"id" => $id,
	"name" => "Amit",
	"state" => "MP34",
	"zip" => "65M3",
	"amount" => "18",
	"qty" => "5",
	"item" => "FDE8"
);

echo $obj->updateOrderData($params);



/**
* Get the CSV Data by Id
 * @return Json
*/

$obj->getCsvOrderById($id);



/**
* Call the class function for delete the record
 * @return Json
*/

 $params = array(
	"id" => $id
	);

$obj->deletaOrderData($params);



/**
* Call the class funtion httpGetData 
* Show the csv Data
*/
$obj->getCSVOrder();



?>