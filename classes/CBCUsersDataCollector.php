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

    public function get_users_ids(array $categories = ['subscriber'])
    {

        global $wpdb;
        global $table_prefix;

        $where_categories = "";

        if (!empty($categories)) {

            foreach ($categories as $category) {

                $category = $wpdb->esc_sql($wpdb->esc_like($category));
                
                if (empty($where_categories)) $where_categories .= " AND (t.meta_value LIKE '%".$category."%'";
                else $where_categories .= " OR t.meta_value LIKE '%".$category."%'";

            }

            $where_categories .= ")";

        }

        $select = $wpdb->get_results("SELECT t.user_id FROM ".DB_NAME.".".$table_prefix."usermeta AS t WHERE t.meta_key LIKE '%capabilities%'".$where_categories, ARRAY_A);

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

    public function get_user_data(int $user_id, array $meta_entities)
    {

        global $wpdb;
        global $table_prefix;

        $user = $wpdb->get_results($wpdb->prepare("SELECT t.user_login, t.user_nicename, t.user_email, t.user_registered FROM ".DB_NAME.".".$table_prefix."users AS t WHERE t.ID = '%d'", $user_id), ARRAY_A);

        if (is_array($user)) {

            if (count($user) > 0) {

                $where_meta = '';

                $result = [];

                foreach ($meta_entities as $meta) {
                    
                    if (empty($where_meta)) $where_meta .= $wpdb->prepare('t.meta_key = "%s"', (string)$meta);
                    else $where_meta .= $wpdb->prepare(' OR t.meta_key = "%s"', (string)$meta);

                    if (isset($user[$meta])) $result[$meta] = $user[$meta];

                }

                $usermeta = $wpdb->get_results($wpdb->prepare("SELECT t.meta_key, t.meta_value FROM ".DB_NAME.".".$table_prefix."usermeta AS t WHERE t.user_id = '%d'", $user_id)." AND (".$where_meta.")", ARRAY_A);

                if (is_array($usermeta)) {

                    if (count($usermeta) > 0) {

                        foreach ($usermeta as $values) {
                            
                            if (array_search($values['meta_key'], $meta_entities) !== false) $result[$values['meta_key']] = $values['meta_value'];

                        }

                    } else $this->logger->log('User '.$user_id.' metadata did\'nt found while calling CBCUserDataCollector::get_user_data().', 1);

                } else {

                    if (empty($result)) $result = false;

                    $this->logger->log('DB usermeta query failure in CBCUserDataCollector::get_user_data().', 2);

                }

            } else {

                $result = false;

                $this->logger->log('User wasn\'t found while calling CBCUsersDataCollector::get_user_data().', 1);

            }
            
        } else {

            $result = false;

            $this->logger->log('DB users query failure in CBCUsersDataCollector::get_user_data().', 2);

        }

        return $result;

    }

}
