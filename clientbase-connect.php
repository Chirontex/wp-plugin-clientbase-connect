<?php
/**
 * Plugin Name: ClentBase Connect
 * Plugin URI: https://github.com/drnoisier/wp-plugin-clientbase-connect
 * Description: WordPress-плагин, предназначенный для экспорта данных о пользователях в CRM-систему на платформе "Клиентская база" .
 * Version: 0.75
 * Author: Дмитрий Шумилин
 * Author URI: mailto://dr.noisier@yandex.ru
 */

use function PHPSTORM_META\registerArgumentsSet;

/**
 *    Copyright (C) 2020  Dmitry Shumilin (dr.noisier@yandex.ru)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once plugin_dir_path(__FILE__).'classes/interfaces/ClientBaseAPIInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBConnectTableInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBCDataTakerInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBCLoggerInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBConnectInterface.php';
require_once plugin_dir_path(__FILE__).'classes/interfaces/CBCUsersDataCollectorInterface.php';

require_once plugin_dir_path(__FILE__).'classes/ClientBaseAPI.php';
require_once plugin_dir_path(__FILE__).'classes/CBConnectTable.php';
require_once plugin_dir_path(__FILE__).'classes/CBCDataTaker.php';
require_once plugin_dir_path(__FILE__).'classes/CBCLogger.php';
require_once plugin_dir_path(__FILE__).'classes/CBConnect.php';
require_once plugin_dir_path(__FILE__).'classes/CBCUsersDataCollector.php';

require_once plugin_dir_path(__FILE__).'clientbase-connect_functions.php';

define('CBAPI_CREATE', 'create');
define('CBAPI_READ', 'read');
define('CBAPI_UPDATE', 'update');
define('CBAPI_DELETE', 'delete');

if (!defined('BOOTSTRAP_CSS_DIR')) define('BOOTSTRAP_CSS_DIR', plugin_dir_path(__FILE__).'css/bootstrap.min.css');

if (!defined('BOOTSTRAP_JS_DIR')) define('BOOTSTRAP_JS_DIR', plugin_dir_path(__FILE__).'js/bootstrap.min.js');

if (!defined('POPPER_DIR')) define('POPPER_DIR', plugin_dir_path(__FILE__).'js/popper.min.js');

if (!defined('JQUERY_DIR')) define('JQUERY_DIR', plugin_dir_path(__FILE__).'js/jquery-3.5.1.js');

if (!file_exists(BOOTSTRAP_CSS_DIR)) file_put_contents(BOOTSTRAP_CSS_DIR, file_get_contents('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css'));

if (!file_exists(BOOTSTRAP_JS_DIR)) file_put_contents(BOOTSTRAP_JS_DIR, file_get_contents('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js'));

if (!file_exists(POPPER_DIR)) @file_put_contents(POPPER_DIR, file_get_contents('https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js'));

if (!file_exists(JQUERY_DIR)) file_put_contents(JQUERY_DIR, file_get_contents('https://code.jquery.com/jquery-3.5.1.js'));

$cbc_logger = new CBCLogger;

session_start(['name' => 'clientbase_connect_session']);

$cbc_csrf_session_status = session_status();

switch ($cbc_csrf_session_status) {
    case PHP_SESSION_ACTIVE:
        
        define('CBC_CSRF', true);

        $cbc_csrf_hash_key = 'key_'.time();
        $cbc_csrf_hash_value = htmlspecialchars(password_hash('zfgsw4tergzfdga4yz0zd943423sdsdg3', PASSWORD_DEFAULT));

        $_SESSION[$cbc_csrf_hash_key] = $cbc_csrf_hash_value;

        break;

    default:

        define('CBC_CSRF', false);

        if ($cbc_csrf_session_status === PHP_SESSION_NONE) $cbc_logger->log('An error occurred while creating the session.', 2);
        else $cbc_logger->log('Sessions are disabled.', 0);

        break;
    
}

$cbc_data_taker = new CBCDataTaker(new CBConnectTable(DB_NAME));

add_action('rest_api_init', function() {

    register_rest_route('clientbaseconnect/v1/settings', '/set', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_settings_set',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/settings', '/get', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_settings_get',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/table', '/set', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_table_set',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/table', '/get', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_table_get',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/table', '/massdelete', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_table_massdelete',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/fields', '/set', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_fields_set',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/fields', '/get', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_fields_get',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/fields', '/delete', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_fields_delete',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/data', '/get_ids', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_users_ids',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

    register_rest_route('clientbaseconnect/v1/data', '/create_user', [
        'methods' => 'POST',
        'callback' => 'clientbaseconnect_user_create',
        'permission_callback' => 'clientbaseconnect_permission_check'
    ]);

});

$cbc_settings = $cbc_data_taker->get_settings();

if ($cbc_settings) $client_base_connect = new CBConnect($cbc_settings);

add_action('admin_menu', function() {

    add_menu_page('Client Base Connect', 'Client Base Connect', 8, plugin_dir_path(__FILE__).'clientbase-connect_admin.php');

});
