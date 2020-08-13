<?php
/**
 *    Client Base Connect
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
require_once plugin_dir_path(__FILE__).'clientbase-connect_functions.php';

foreach (['classes/interfaces', 'classes'] as $folder) {

    $cbc_path = plugin_dir_path(__FILE__).$folder;
    
    $cbc_load = opendir($cbc_path);

    while ($cbc_file = readdir($cbc_load)) {

        if (substr($cbc_file, -4) === '.php') require_once $cbc_path.'/'.$cbc_file;

    }

    closedir($cbc_load);

}
