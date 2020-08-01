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
class CBConnectTable implements CBConnectTableInterface
{

    public $db;
    public $db_name;
    public $db_prefix;

    public function __construct(string $database, string $prefix = '')
    {
        
        global $table_prefix;

        if ($database === DB_NAME) {

            global $wpdb;

            $this->db = $wpdb;
            $this->db_name = DB_NAME;
            $this->db_prefix = $table_prefix;

        } else {

            $this->db_name = $database;

            $this->db = new wpdb(DB_USER, DB_PASSWORD, $this->db_name, DB_HOST);

            if (!empty($this->db->error)) wp_die($this->db->error);

            if (empty($prefix)) $this->db_prefix = $table_prefix;
            else $this->db_prefix = $prefix;

        }

    }

}
