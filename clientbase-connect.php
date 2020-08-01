<?php
/**
 * Plugin Name: ClentBase Connect
 * Plugin URI: https://github.com/drnoisier/wp-plugin-clientbase-connect
 * Description: WordPress-плагин, предназначенный для экспорта данных о пользователях в CRM-систему на платформе "Клиентская база" .
 * Version: 0.25
 * Author: Дмитрий Шумилин
 * Author URI: mailto://dmitri.shumilinn@yandex.ru
 */
/**
 *    Copyright (C) 2020  Dmitry Shumilin (dmitri.shumilinn@yandex.ru)
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

require_once plugin_dir_path(__FILE__).'classes/ClientBaseAPI.php';
require_once plugin_dir_path(__FILE__).'classes/CBConnectTable.php';

define('CBAPI_CREATE', 'create');
define('CBAPI_READ', 'read');
define('CBAPI_UPDATE', 'update');
define('CBAPI_DELETE', 'delete');
