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
function clientbaseconnect_permission_check()
{

    global $cbc_logger;

        if (CBC_CSRF) {

            if (isset($_POST['hash']) && isset($_POST['hash_key'])) {

                if ($_SESSION[$_POST['hash_key']] === $_POST['hash']) $result = true;
                else {
                    
                    $result = false;

                    $cbc_logger->log('The hash did not match. A CSRF-attack may have occurred.', 1);
                
                }

            } else {
                
                $result = false;

                $cbc_logger->log('Hash arguments were not passed.', 2);
            
            }

        } else $result = true;

    return $result;

}

function clientbaseconnect_settings_set()
{

    global $cbc_logger;
    global $cbc_data_taker;

    if (isset($_POST['url']) && isset($_POST['login']) && isset($_POST['key'])) {

        if ($cbc_data_taker->set_settings(trim($_POST['url']), trim($_POST['login']), trim($_POST['key']))) $result = ['code' => 0, 'message' => 'Success.'];
        else {

            $result = ['code' => -2, 'message' => 'Database query failure.'];

            $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code -2: "Database query failure."', 2);

        }

    } else {
        
        $result = ['code' => -1, 'message' => 'Too few arguments for this request.'];

        $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code -1: "Too few arguments for this request."', 2);
    
    }

    return $result;

}

function clientbaseconnect_settings_get()
{

    global $cbc_logger;
    global $cbc_data_taker;

    $settings = $cbc_data_taker->get_settings();

    if ($settings) $result = ['code' => 0, 'message' => 'Success.', 'data' => $settings];
    else {

        $result = ['code' => -2, 'message' => 'Database query failure.'];

        $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code -2: "Database query failure."', 2);

    }

    return $result;

}
