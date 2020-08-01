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

    public function create_table()
    {

        return $this->db->query("CREATE TABLE IF NOT EXISTS `".$this->db_prefix."clientbaseconnect_options` (`ID` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, `option_key` CHAR(50) NOT NULL, `option_value` CHAR(100) NULL, PRIMARY KEY (`ID`), UNIQUE INDEX `option_key` (`option_key`)) COLLATE='utf8mb4_unicode_ci' AUTO_INCREMENT=0");

    }

    public function insert(string $key, string $value)
    {

        return $this->db->insert($this->db_prefix.'clientbaseconnect_options', ['option_key' => $key, 'option_value' => $value]);

    }

    public function update(string $key, string $value)
    {

        return $this->db->update($this->db_prefix.'clientbaseconnect_options', ['option_value' => $value], ['option_key' => $key]);

    }

    public function delete(string $key, string $value = '')
    {

        $where['option_key'] = $key;

        if (!empty($value)) $where['option_value'] = $value;

        return $this->db->delete($this->db_prefix.'clientbaseconnect_options', $where);

    }

    public function select(string $query = '', $output_type = null)
    {

        if (empty($query)) $query = null;

        if (empty($output_type)) return $this->db->get_results($query);
        else {

            if ($output_type === OBJECT || $output_type === OBJECT_K || $output_type === ARRAY_A || $output_type === ARRAY_N) return $this->db->get_results($query, $output_type);
            else return $this->db->get_results($query);

        }

    }

}
