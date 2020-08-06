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
class CBCUsersDataCollector implements CBCUsersDataCollectorInterface
{

    public $logger;

    public function __construct()
    {
        
        $this->logger = new CBCLogger;

    }

    public function get_users_ids(string $category = 'subscriber')
    {

        global $wpdb;
        global $table_prefix;

        $category = $wpdb->prepare("%s", $category);

        $select = $wpdb->get_results("SELECT t.user_id FROM ".DB_NAME.".".$table_prefix."usermeta AS t WHERE t.meta_key LIKE %capabilities% AND t.meta_value LIKE %".$category."%", ARRAY_A);

        if (is_array($select)) {

            if (count($select) > 0) {

                $result = [];

                foreach ($select as $value) {
                    
                    $result[] = $value['user_id'];

                }

            } else {

                $result = false;

                $this->logger->log('Empty result of DB query in CBCUsersDataCollector::get_users_ids().', 1);

            }

        } else {

            $result = false;

            $this->logger->log('DB query failed in CBCUsersDataCollector::get_users_ids().', 2);

        }

        return $result;

    }

}
