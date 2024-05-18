<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
/* AUTH START */
$routes->add('/login', 'Auth\Auth::login');
$routes->add('/logout', 'Auth\Auth::logout');
/* AUTH END */

$routes->add('/super-admin', 'SuperAdmin\SuperAdmin::index');
$routes->add('/super-admin/add-clients', 'SuperAdmin\SuperAdminSetting::addSuperAdminClients');
$routes->add('/super-admin/view-clients', 'SuperAdmin\SuperAdmin::showClient');
$routes->add('/super-admin/edit-client/(:num)', 'SuperAdmin\SuperAdminSetting::updateClient/$1');
$routes->add('/super-admin/getClientName', 'SuperAdmin\SuperAdmin::getClientName');
$routes->add('/super-admin/getClientEmail', 'SuperAdmin\SuperAdmin::getClientEmail');
$routes->add('/super-admin/getClientPhone', 'SuperAdmin\SuperAdmin::getClientPhone');
$routes->add('/super-admin/add-user', 'SuperAdmin\SuperAdminSetting::superAdminAddUser');
$routes->add('/super-admin/edit-user/(:num)', 'SuperAdmin\SuperAdminSetting::superAdminUpdateUser/$1');
$routes->add('/super-admin/view-user', 'SuperAdmin\SuperAdmin::superAdminShowUsers');
$routes->add('/super-admin/getUserEmail', 'SuperAdmin\SuperAdmin::getUserEmail');
$routes->add('/super-admin/getUserPhone', 'SuperAdmin\SuperAdmin::getUserPhone');
$routes->add('/super-admin/add-state', 'SuperAdmin\SuperAdminSetting::addStateData');
$routes->add('/super-admin/edit-states-data/(:num)', 'SuperAdmin\SuperAdminSetting::updateStatesData/$1');
$routes->add('/super-admin/show-states', 'SuperAdmin\SuperAdmin::showStatesInformation');
$routes->add('/super-admin/add-area', 'SuperAdmin\SuperAdminSetting::addArea');
$routes->add('/super-admin/getUpozila', 'SuperAdmin\SuperAdminSetting::getUpozila');
$routes->add('/super-admin/change-password', 'SuperAdmin\SuperAdminSetting::changePassword');
$routes->add('/super-admin/delete/(:any)/(:num)/(:num)/(:any)/(:any)', 'SuperAdmin\SuperAdmin::superAdmindeleteData/$1/$2/$3/$4/$5');
$routes->add('/super-admin/act-dec/(:any)/(:num)/(:num)/(:any)/(:any)', 'SuperAdmin\SuperAdmin::superAdminActDecData/$1/$2/$3/$4/$5');
/* super-admin end  */

