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
function clientbaseconnect_results(int $code, string $message = '')
{

    $result = ['code' => $code];

    if ($code < 1) {

        switch ($code) {
            case 0:
                $result['message'] = 'Success.';
                break;

            case -1:
                $result['message'] = 'Too few arguments for this request.';
                break;

            case -2:
                $result['message'] = 'Database query failure.';
                break;

            case -3:
                $result['message'] = 'Bad arguments.';
                break;
            
            default:
                $result['message'] = 'Unknown code.';
                break;
        }

    } else {

        if (empty($message)) $result['message'] = 'Unknown code.';
        else $result['message'] = $message;

    }

    return $result;

}

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

        if ($cbc_data_taker->set_settings(trim($_POST['url']), trim($_POST['login']), trim($_POST['key']))) $result = clientbaseconnect_results(0);
        else {

            $result = clientbaseconnect_results(-2);

            $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

        }

    } else {
        
        $result = clientbaseconnect_results(-1);

        $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);
    
    }

    return $result;

}

function clientbaseconnect_settings_get()
{

    global $cbc_logger;
    global $cbc_data_taker;

    $settings = $cbc_data_taker->get_settings();

    if ($settings) {

        $result = clientbaseconnect_results(0);

        $result['data'] = $settings;

    } else {

        $result = clientbaseconnect_results(-2);

        $cbc_logger->log('/clientbaseconnect/v1/settings/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_table_set()
{

    global $cbc_logger;
    global $cbc_data_taker;

    if (isset($_POST['table'])) {

        if ($_POST['table'] > 0) {

            if ($cbc_data_taker->set_table($_POST['table'])) $result = clientbaseconnect_results(0);
            else {

                $result = clientbaseconnect_results(-2);

                $cbc_logger->log('clientbaseconnect/v1/table/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

            }

        } else {

            $result = clientbaseconnect_results(1, 'Incorrect table number.');

            $cbc_logger->log('clientbaseconnect/v1/table/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

        }

    } else {

        $result = clientbaseconnect_results(-1);

        $cbc_logger->log('clientbaseconnect/v1/table/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_table_get()
{

    global $cbc_logger;
    global $cbc_data_taker;

    $table = $cbc_data_taker->get_table();

    if ($table) {
        
        $result = clientbaseconnect_results(0);

        $result['data'] = $table;
    
    } else {

        $result = clientbaseconnect_results(-2);

        $cbc_logger->log('clientbaseconnect/v1/table/get — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_table_massdelete()
{

    global $cbc_logger;
    global $cbc_data_taker;

    if ($cbc_data_taker->delete_whole_table()) $result = clientbaseconnect_results(0);
    else {

        $result = clientbaseconnect_results(-2);

        $cbc_logger->log('clientbaseconnect/v1/table/massdelete — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_fields_set()
{

    global $cbc_logger;
    global $cbc_data_taker;

    if (isset($_POST['fields'])) {

        if (is_array($_POST['fields']) && !empty($_POST['fields'])) {

            $set_result = true;

            foreach ($_POST['fields'] as $key => $value) {
                
                $set_result = $set_result and $cbc_data_taker->set_field((string)$key, (string)$value);

            }

            if ($set_result) $result = clientbaseconnect_results(0);
            else $result = clientbaseconnect_results(-2);

        } else {

            $result = clientbaseconnect_results(-3);

            $cbc_logger->log('clientbaseconnect/v1/fields/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

        }

    } else {

        $result = clientbaseconnect_results(-1);

        $cbc_logger->log('clientbaseconnect/v1/fields/set — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_fields_get()
{

    global $cbc_logger;
    global $cbc_data_taker;

    $data = $cbc_data_taker->get_fields();

    if ($data) {

        $result = clientbaseconnect_results(0);

        $result['data'] = $data;

    } else {

        $result = clientbaseconnect_results(-2);

        $cbc_logger->log('clientbaseconnect/v1/fields/get — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_fields_delete()
{

    global $cbc_logger;
    global $cbc_data_taker;

    if (isset($_POST['key'])) {

        if ($cbc_data_taker->delete_field((string)$_POST['key'])) $result = clientbaseconnect_results(0);
        else {

            $result = clientbaseconnect_results(-2);

            $cbc_logger->log('clientbaseconnect/v1/fields/delete — answer code '.$result['code'].': "'.$result['message'].'"', 2);

        }

    } else {

        $result = clientbaseconnect_results(-1);

        $cbc_logger->log('clientbaseconnect/v1/fields/delete — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}

function clientbaseconnect_users_ids()
{

    global $cbc_logger;

    $data_collector = new CBCUsersDataCollector;

    if (is_array($_POST['categories'])) $ids = $data_collector->get_users_ids($_POST['categories']);
    else $ids = $data_collector->get_users_ids();

    if ($ids) {

        $result = clientbaseconnect_results(0);
        $result['data'] = $ids;

    } else {

        $result = clientbaseconnect_results(-2);

        $cbc_logger->log('clientbaseconnect/v1/data/get_ids — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }
    
    return $result;

}

function clientbaseconnect_user_create()
{

    global $cbc_logger;
    global $cbc_data_taker;
    global $client_base_connect;

    if (in_array('CBConnectInterface', class_implements($client_base_connect))) {

        if (isset($_POST['user_id'])) {

            $data_collector = new CBCUsersDataCollector;

            $fields = $cbc_data_taker->get_fields();

            if ($fields) {

                $user_data = $data_collector->get_user_data((int)$_POST['user_id'], $fields);

                if ($user_data) {

                    $row_create = $client_base_connect->row_create($user_data);

                    if ($row_create) {

                        $result = clientbaseconnect_results(0);
                        $result['data'] = $row_create;

                    } else {

                        $result = clientbaseconnect_results(2, 'Data transfer to CRM failed.');

                        $cbc_logger->log('clientbaseconnect/v1/data/create_user — answer code '.$result['code'].': "'.$result['message'].'"', 2);

                    }

                } else {

                    $result = clientbaseconnect_results(-2);

                    $cbc_logger->log('clientbaseconnect/v1/data/create_user — answer code '.$result['code'].' while taking user data: "'.$result['message'].'"', 2);

                }

            } else {

                $result = clientbaseconnect_results(-2);

                $cbc_logger->log('clientbaseconnect/v1/data/create_user — answer code '.$result['code'].' while query fields: "'.$result['message'].'"', 2);

            }

        } else {

            $result = clientbaseconnect_results(-1);

            $cbc_logger->log('clientbaseconnect/v1/data/create_user — answer code '.$result['code'].': "'.$result['message'].'"', 2);

        }

    } else {

        $result = clientbaseconnect_results(6, 'Some kind of bad magic is happened with CBConnect object. Looks like it wasn\'t created when it must.');

        $cbc_logger->log('clientbaseconnect/1/data/create_user — answer code '.$result['code'].': "'.$result['message'].'"', 2);

    }

    return $result;

}
