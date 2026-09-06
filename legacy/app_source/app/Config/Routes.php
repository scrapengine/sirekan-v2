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
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function () {
    return view('/404/index');
});

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

$routes->get('/', 'Home::home');
$routes->get('/dashboard/countTicket(:any)', 'Home::countTicket/$1');
$routes->get('/dashboard/countTicket', 'Home::countTicket');
$routes->get('/dashboard/countFf', 'Home::countFf');
$routes->get('/dashboard/countOrderNow', 'Home::countOrderNow');
$routes->get('/dashboard/chart_data', 'Home::chart_data');
$routes->get('/dashboard/chart_data_month', 'Home::chart_data_month');
$routes->get('/dashboard/monthTicket', 'Home::monthTicket');
$routes->get('/dashboard/monthFf', 'Home::monthFf');
$routes->get('/dashboard/ticketNow', 'Home::ticketNow');
$routes->get('/dashboard/ffNow', 'Home::ffNow');
$routes->get('/dashboard/OntNodeb', 'Home::OntNodeb');
$routes->get('/dashboard/reportall', 'Home::reportAll');

//api
$routes->get('api/wan/nodeb', 'wan\nodeb::read');
$routes->get('api/wan/ont', 'wan\ont::ontTotal');
$routes->get('api/wan/ont/sn(:any)', 'wan\ont::searchSn');
$routes->get('api/additional/olt', 'additional\olt::read');
$routes->get('api/assurance(:any)', 'wan\assurance::searchTicket');





//user
$routes->get('/user', 'User::index');
$routes->get('/user/userlist', 'User::userlist', ['filter' => 'role:superadmin']);
$routes->get('/user/userlist/export', 'User::export', ['filter' => 'role:superadmin']);
$routes->post('/user/activate', 'User::activate', ['filter' => 'role:superadmin']);
$routes->post('/user/changeGroup', 'User::changeGroup', ['filter' => 'role:superadmin']);
$routes->get('/user/changePassword/(:any)', 'User::changePassword/$1');
$routes->post('/user/setPassword', 'User::setPassword');
$routes->get('/user/userlist/(:num)', 'User::detail/$1', ['filter' => 'role:superadmin']);
$routes->get('/test', 'User::test');
$routes->post('/user/update/(:num)', 'User::update/$1');
$routes->get('/admin', 'Admin::index', ['filter' => 'role:admin,superadmin']);
$routes->get('/admin/index', 'Admin::index', ['filter' => 'role:admin,superadmin']);
$routes->get('/admin/(:num)', 'Admin::detail/$1', ['filter' => 'role:admin,superadmin']);

//stisla
$routes->get('/home', 'Home::home');
// $routes->get('/wan/nodeb', 'Nodeb::index');
// $routes->addRedirect('/', 'home');
$routes->get('/wan/dummy', 'Dummy::index');
$routes->post('/wan/dummy', 'Dummy::store');
$routes->get('/wan/add', 'Dummy::add');
$routes->get('/wan/edit/(:num)', 'Dummy::edit/$1');
$routes->put('/wan/(:any)', 'Dummy::update/$1');
$routes->post('/wan/delete/(:segment)', 'Dummy::delete/$1');

//nodeb
$routes->get('wan/nodeb/trash', 'Wan\Nodeb::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/nodeb/restore/(:any)', 'Wan\Nodeb::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/nodeb/restore', 'Wan\Nodeb::restore', ['filter' => 'role:admin,superadmin']);
$routes->post('wan/nodeb/deletetrash/(:any)', 'Wan\Nodeb::deletetrash/$1');
$routes->post('wan/nodeb/deletetrash', 'Wan\Nodeb::deletetrash');
$routes->post('wan/nodeb/import', 'wan\nodeb::import');
$routes->get('wan/nodeb/listdatatrash', 'wan\nodeb::listDataTrash');
$routes->get('wan/nodeb/export', 'wan\nodeb::export');
$routes->get('wan/nodeb/listdata', 'wan\nodeb::listData');
$routes->get('wan/nodeb/(.*)/edit', 'wan\nodeb::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/nodeb/new', 'wan\nodeb::new', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/nodeb/detail/(:num)/(:any)', 'wan\nodeb::detailData/$1');
$routes->get('wan/nodeb/ukurnodeb', 'wan\nodeb::ukurNodeb');
$routes->get('wan/map', 'wan\nodeb::mapNodeb');
$routes->get('wan/surat(:any)', 'wan\nodeb::surat');

//olo
$routes->get('wan/olo/trash', 'Wan\olo::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/olo/restore/(:any)', 'Wan\olo::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/olo/restore', 'Wan\olo::restore', ['filter' => 'role:admin,superadmin']);
$routes->post('wan/olo/deletetrash/(:any)', 'Wan\olo::deletetrash/$1');
$routes->post('wan/olo/deletetrash', 'Wan\olo::deletetrash');
$routes->post('wan/olo/import', 'wan\olo::import');
$routes->get('wan/olo/listdatatrash', 'wan\olo::listDataTrash');
$routes->get('wan/olo/export', 'wan\olo::export');
$routes->get('wan/olo/listdata', 'wan\olo::listData');
$routes->get('wan/olo/(.*)/edit', 'wan\olo::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/olo/new', 'wan\olo::new', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/olo/detail/(:any)', 'wan\olo::detailData/$1');
$routes->get('wan/olo/ukurolo', 'wan\olo::ukurolo');
$routes->get('wan/map', 'wan\olo::mapolo');

//allnodeb
$routes->get('wan/allnodeb/trash', 'Wan\AllNodeb::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/allnodeb/restore/(:any)', 'Wan\AllNodeb::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/allnodeb/restore', 'Wan\AllNodeb::restore', ['filter' => 'role:admin,superadmin']);
$routes->post('wan/allnodeb/deletetrash/(:any)', 'Wan\AllNodeb::deletetrash/$1');
$routes->post('wan/allnodeb/deletetrash', 'Wan\AllNodeb::deletetrash');
$routes->post('wan/allnodeb/import', 'wan\Allnodeb::import');
$routes->get('wan/allnodeb/listdatatrash', 'wan\Allnodeb::listDataTrash');
$routes->get('wan/allnodeb/export', 'wan\Allnodeb::export');
$routes->get('wan/allnodeb', 'wan\AllNodeb::index');
$routes->get('wan/allnodeb/listdata', 'wan\Allnodeb::listData');
$routes->get('wan/allnodeb/(.*)/edit', 'wan\Allnodeb::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/allnodeb/new', 'wan\Allnodeb::new', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/allnodeb/detail/(:any)', 'wan\Allnodeb::detailData/$1');

//ont
$routes->post('wan/ont/deletetrash/(:any)', 'wan\ont::deletetrash/$1');
$routes->post('wan/ont/deletetrash', 'wan\ont::deletetrash/$1');
$routes->post('wan/ont/import', 'wan\ont::import');
$routes->get('wan/ont/trash', 'wan\ont::trash');
$routes->get('wan/ont/export_trash', 'wan\ont::export_trash');
$routes->get('wan/ont/restore/(:any)', 'wan\ont::restore/$1');
$routes->get('wan/ont/restore', 'wan\ont::restore');
$routes->get('wan/ont/export', 'wan\ont::export');
$routes->get('wan/ont/exportstock', 'wan\ont::exportStock');
$routes->get('wan/ont/exportstockolo', 'wan\ont::exportStockOlo');
$routes->get('wan/ont/exportinstalled', 'wan\ont::exportInstalled');
$routes->get('wan/ont/exportreturn', 'wan\ont::exportReturn');
$routes->get('wan/ont/listdatatrash', 'wan\ont::listDataTrash');
$routes->get('wan/ont/listdata', 'wan\ont::listData');
$routes->get('wan/ont/listdatastock', 'wan\ont::listDataStock');
$routes->get('wan/ont/listdatastockff', 'wan\ont::listDataStockFf');
$routes->get('wan/ont/listdatastockolo', 'wan\ont::listDataStockOlo');
$routes->get('wan/ont/listdatainstalled', 'wan\ont::listDataInstalled');
$routes->get('wan/ont/listdatareturn', 'wan\ont::listDataReturn');
$routes->post('wan/ont/type', 'wan\ont::ontType');


//fulfillment
$routes->post('wan/fulfillment/deletetrash/(:any)', 'wan\fulfillment::deletetrash/$1');
$routes->post('wan/fulfillment/deletetrash', 'wan\fulfillment::deletetrash');
$routes->post('wan/fulfillment/import', 'wan\fulfillment::import');
$routes->get('wan/fulfillment/trash', 'wan\fulfillment::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/fulfillment/export_trash', 'wan\fulfillment::export_trash');
$routes->get('wan/fulfillment/restore/(:any)', 'wan\fulfillment::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/fulfillment/restore', 'wan\fulfillment::restore', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/fulfillment/export', 'wan\fulfillment::export');
$routes->get('wan/fulfillment/listdatatrash', 'wan\fulfillment::listDataTrash');
$routes->get('wan/fulfillment/listdata', 'wan\Fulfillment::listData');
$routes->get('wan/fulfillment/detaildata', 'wan\Fulfillment::detailData');
$routes->get('wan/fulfillment/(.*)/edit', 'wan\Fulfillment::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/fulfillment/new', 'wan\Fulfillment::new', ['filter' => 'role:admin,superadmin']);

//assurance
$routes->post('wan/assurance/deletetrash/(:any)', 'wan\assurance::deletetrash/$1');
$routes->post('wan/assurance/deletetrash', 'wan\assurance::deletetrash');
$routes->post('wan/assurance/import', 'wan\assurance::import');
$routes->get('wan/assurance/trash', 'wan\assurance::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/assurance/export_trash', 'wan\assurance::export_trash');
$routes->get('wan/assurance/restore/(:any)', 'wan\assurance::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/assurance/restore', 'wan\assurance::restore', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/assurance/export', 'wan\assurance::export');
$routes->get('wan/assurance/listdatatrash', 'wan\assurance::listDataTrash');
$routes->get('wan/assurance/listdata', 'wan\assurance::listData');
$routes->get('wan/assurance/detaildata', 'wan\assurance::detailData');
$routes->get('wan/assurance/(.*)/edit', 'wan\assurance::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wan/assurance/new', 'wan\assurance::new', ['filter' => 'role:admin,superadmin']);


//ccanfulfillment
$routes->post('ccan/fulfillment/deletetrash/(:any)', 'ccan\fulfillment::deletetrash/$1');
$routes->post('ccan/fulfillment/deletetrash', 'ccan\fulfillment::deletetrash');
$routes->post('ccan/fulfillment/import', 'ccan\fulfillment::import');
$routes->get('ccan/fulfillment/trash', 'ccan\fulfillment::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/fulfillment/export_trash', 'ccan\fulfillment::export_trash');
$routes->get('ccan/fulfillment/restore/(:any)', 'ccan\fulfillment::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/fulfillment/restore', 'ccan\fulfillment::restore', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/fulfillment/export', 'ccan\fulfillment::export');
$routes->get('ccan/fulfillment/listdatatrash', 'ccan\fulfillment::listDataTrash');
$routes->get('ccan/fulfillment/listdata', 'ccan\Fulfillment::listData');
$routes->get('ccan/fulfillment/detaildata', 'ccan\Fulfillment::detailData');
$routes->get('ccan/fulfillment/(.*)/edit', 'ccan\Fulfillment::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/fulfillment/new', 'ccan\Fulfillment::new', ['filter' => 'role:admin,superadmin']);

//ccanassurance
$routes->post('ccan/assurance/deletetrash/(:any)', 'ccan\assurance::deletetrash/$1');
$routes->post('ccan/assurance/deletetrash', 'ccan\assurance::deletetrash');
$routes->post('ccan/assurance/import', 'ccan\assurance::import');
$routes->get('ccan/assurance/trash', 'ccan\assurance::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/assurance/export_trash', 'ccan\assurance::export_trash');
$routes->get('ccan/assurance/restore/(:any)', 'ccan\assurance::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/assurance/restore', 'ccan\assurance::restore', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/assurance/export', 'ccan\assurance::export');
$routes->get('ccan/assurance/listdatatrash', 'ccan\assurance::listDataTrash');
$routes->get('ccan/assurance/listdata', 'ccan\assurance::listData');
$routes->get('ccan/assurance/detaildata', 'ccan\assurance::detailData');
$routes->get('ccan/assurance/(.*)/edit', 'ccan\assurance::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('ccan/assurance/new', 'ccan\assurance::new', ['filter' => 'role:admin,superadmin']);

//wifiassurance
$routes->post('wifi/assurance/deletetrash/(:any)', 'wifi\assurance::deletetrash/$1');
$routes->post('wifi/assurance/deletetrash', 'wifi\assurance::deletetrash');
$routes->post('wifi/assurance/import', 'wifi\assurance::import');
$routes->get('wifi/assurance/trash', 'wifi\assurance::trash', ['filter' => 'role:admin,superadmin']);
$routes->get('wifi/assurance/export_trash', 'wifi\assurance::export_trash');
$routes->get('wifi/assurance/restore/(:any)', 'wifi\assurance::restore/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wifi/assurance/restore', 'wifi\assurance::restore', ['filter' => 'role:admin,superadmin']);
$routes->get('wifi/assurance/export', 'wifi\assurance::export');
$routes->get('wifi/assurance/listdatatrash', 'wifi\assurance::listDataTrash');
$routes->get('wifi/assurance/listdata', 'wifi\assurance::listData');
$routes->get('wifi/assurance/detaildata', 'wifi\assurance::detailData');
$routes->get('wifi/assurance/(.*)/edit', 'wifi\assurance::edit/$1', ['filter' => 'role:admin,superadmin']);
$routes->get('wifi/assurance/new', 'wifi\assurance::new', ['filter' => 'role:admin,superadmin']);


//olt
$routes->post('additional/olt/deletetrash/(:any)', 'additional\olt::deletetrash/$1');
$routes->post('additional/olt/deletetrash', 'additional\olt::deletetrash/$1');
$routes->post('additional/olt/import', 'additional\olt::import');
$routes->get('additional/olt/trash', 'additional\olt::trash');
$routes->get('additional/olt/export_trash', 'additional\olt::export_trash');
$routes->get('additional/olt/restore/(:any)', 'additional\olt::restore/$1');
$routes->get('additional/olt/restore', 'additional\olt::restore');
$routes->get('additional/olt/listdatatrash', 'Additional\Olt::listDataTrash');
$routes->get('additional/olt/export', 'additional\olt::export');
$routes->get('additional/olt/listdata', 'Additional\Olt::listData');

//metro
$routes->setDefaultController('Metro');
$routes->post('additional/metro/import', 'Additional\Metro::import');
$routes->post('additional/metro/deletetrash', 'additional\metro::deletetrash/$1');
$routes->post('additional/metro/deletetrash/(:any)', 'additional\metro::deletetrash/$1');
$routes->get('additional/metro/listdata', 'additional\Metro::listData');
$routes->get('additional/metro/export', 'additional\metro::export');
$routes->get('additional/metro/trash', 'additional\metro::trash');
$routes->get('additional/metro/export_trash', 'additional\metro::export_trash');
$routes->get('additional/metro/restore/(:any)', 'additional\metro::restore/$1');
$routes->get('additional/metro/restore', 'additional\metro::restore');
$routes->get('additional/metro/listdatatrash', 'Additional\Metro::listDataTrash');

//sto
$routes->setDefaultController('Sto');
$routes->post('additional/sto/import', 'Additional\sto::import');
$routes->get('additional/sto', 'Additional\Sto::index');
$routes->get('additional/sto/export', 'Additional\sto::export');
$routes->get('additional/sto/import', 'Additional\sto::import');
$routes->get('additional/sto/listdata', 'Additional\Sto::listData');
$routes->get('additional/sto/listdatatrash', 'Additional\Sto::listDataTrash');
$routes->get('additional/sto/trash', 'Additional\sto::trash');
$routes->post('additional/sto/deletetrash', 'additional\sto::deletetrash/$1');
$routes->post('additional/sto/deletetrash/(:any)', 'additional\sto::deletetrash/$1');
$routes->get('additional/sto/restore/(:any)', 'additional\sto::restore/$1');
$routes->get('additional/sto/restore', 'additional\sto::restore');

//naker
$routes->post('additional/naker/import', 'Additional\naker::import');
$routes->get('additional/naker', 'Additional\naker::index');
$routes->get('additional/naker/export', 'Additional\naker::export');
$routes->get('additional/naker/import', 'Additional\naker::import');
$routes->get('additional/naker/listdata', 'Additional\naker::listData');
$routes->get('additional/naker/listdatatrash', 'Additional\naker::listDataTrash');
$routes->get('additional/naker/trash', 'Additional\naker::trash');
$routes->post('additional/naker/deletetrash', 'additional\naker::deletetrash/$1');
$routes->post('additional/naker/deletetrash/(:any)', 'additional\naker::deletetrash/$1');
$routes->get('additional/naker/restore/(:any)', 'additional\naker::restore/$1');
$routes->get('additional/naker/restore', 'additional\naker::restore');

//ticket
$routes->get('ticket', 'Ticket::index');

//routes otomatis
$routes->resource('user');
$routes->resource('wan/nodeb');
$routes->resource('wan/allnodeb');
$routes->resource('wan/ont');
$routes->resource('wan/olo');
$routes->resource('wan/fulfillment');
$routes->resource('wan/assurance');
$routes->resource('ccan/fulfillment');
$routes->resource('ccan/assurance');
$routes->resource('wifi/fulfillment');
$routes->resource('wifi/assurance');
$routes->resource('additional/sto');
$routes->resource('additional/olt');
$routes->resource('additional/metro');
$routes->resource('additional/naker');


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
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
