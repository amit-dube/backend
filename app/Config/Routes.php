<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('OrderController');
$routes->setDefaultMethod('getorderList');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/', 'OrderController::getorderList');
$routes->post('/addOrderDataToCsv', 'OrderController::addOrderDataToCsv');
//$routes->get('/addOrderDataToCsv/', 'OrderController::addOrderDataToCsv');
$routes->post("/editOrderDataToCsv", "OrderController::editOrderDataToCsv");
//$routes->get("/editOrderDataToCsv", "OrderController::editOrderDataToCsv");


//$routes->delete("/deletaOrderData/(:num)", "OrderController::deletaOrderData/$1");
$routes->post("/deletaOrderData", "OrderController::deletaOrderData");
//$routes->get("/deletaOrderData", "OrderController::deletaOrderData");
$routes->get("/getOrderById/(:num)", "OrderController::getOrderById/$1");
$routes->get("/getLastOrderCsvId", "OrderController::getLastOrderCsvId");


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have deletaDataaccess to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
