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

    if (is_object($cbc_logger)) $logger = $cbc_logger;
    else $logger = new CBCLogger;

        if (CBC_CSRF) {

            if (isset($_POST['hash']) && isset($_POST['hash_key'])) {

                if ($_SESSION[$_POST['hash_key']] === $_POST['hash']) $result = true;
                else {
                    
                    $result = false;

                    $logger->log('The hash did not match. A CSRF-attack may have occurred.', 1);
                
                }

            } else {
                
                $result = false;

                $logger->log('Hash arguments were not passed.', 2);
            
            }

        } else $result = true;

    return $result;

}
