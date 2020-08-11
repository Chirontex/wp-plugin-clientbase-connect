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
class CBCDataTaker implements CBCDataTakerInterface
{

    public $cbct;
    public $logger;

    public function __construct(object $cbct)
    {

        if (in_array('CBConnectTableInterface', class_implements($cbct))) $this->cbct = $cbct;
        else $this->cbct = new CBConnectTable(DB_NAME);

        $this->logger = new CBCLogger;

    }

    public function set_settings(string $url, string $login, string $key)
    {

        if ($this->get_settings()) {

            if ($this->cbct->update('url', $url) === false) $result_url = false;
            else $result_url = true;

            if ($this->cbct->update('login', $login) === false) $result_login = false;
            else $result_login = false;

            if ($this->cbct->update('key', $key) === false) $result_key = false;
            else $result_key = false;
        
        } else {

            if ($this->cbct->insert('url', $url) === false) $result_url = false;
            else $result_url = true;

            if ($this->cbct->insert('login', $login) === false) $result_login = false;
            else $result_login = false;

            if ($this->cbct->insert('key', $key) === false) $result_key = false;
            else $result_key = false;

        }

        $result = $result_url and $result_login and $result_key;

        return $result;

    }

    public function get_settings()
    {

        $select = $this->cbct->select("SELECT t.option_key, t.option_value FROM ".$this->cbct->db_name.".".$this->cbct->db_prefix."clientbaseconnect_options AS t WHERE t.option_key = 'url' OR t.option_key = 'login' OR t.option_key = 'key'", ARRAY_A);

        if (count($select) === 3) {
            
            $result = $this->get_results_sorter($select);

            if (!$result) $this->logger->log('Something is wrong with CBCDataTaker::get_settings().', 2);
        
        } else $result = false;

        return $result;

    }

    public function set_table(int $table)
    {

        if ($this->cbct->insert('table', (string)$table)) return true;
        else return $this->cbct->update('table', (string)$table);

    }

    public function get_table()
    {

        $select = $this->cbct->select("SELECT t.option_value FROM ".$this->cbct->db_name.".".$this->cbct->db_prefix."clientbaseconnect_options AS t WHERE t.option_key = 'table' LIMIT 1", ARRAY_A);

        if (is_array($select)) {

            if (empty($select)) {
                
                $result = false;

                $this->logger->log('Table number isn\'t specified in DB.', 1);
            
            } else $result = (int)$select[0]['option_value'];

        } else {
            
            $result = false;

            $this->logger->log('Something is wrong with CBCDataTaker::get_table().', 2);
        
        }

        return $result;

    }

    public function set_field(string $field, string $meta_entity)
    {

        if ($field === 'id' || $field === 'user_id' || $field === 'add_time' || $field === 'status' || substr($field, 0, 1) === 'f') {

            if ($this->cbct->insert($field, $meta_entity)) return true;
            else return $this->cbct->update($field, $meta_entity);

        } else return false;

    }

    public function get_fields()
    {

        $select = $this->cbct->select("SELECT t.option_key, t.option_value FROM ".$this->cbct->db_name.".".$this->cbct->db_prefix."clientbaseconnect_options AS t WHERE t.option_key = 'id' OR t.option_key = 'user_id' OR t.option_key = 'add_time' OR t.option_key = 'status' OR t.option_key LIKE 'f%'", ARRAY_A);

        if (is_array($select)) {

            if (empty($select)) $result = false;
            else $result = $this->get_results_sorter($select);

        } else {
            
            $result = false;

            $this->logger->log('Something is wrong with CBCDataTaker::get_fields().', 2);
        
        }

        return $result;

    }

    public function delete_field(string $key)
    {

        return $this->cbct->delete($key);

    }

    public function delete_whole_table()
    {

        $result = $this->cbct->delete('table');

        if ($result) {

            $fields = $this->get_fields();

            if ($fields) {

                foreach ($fields as $key => $value) {
                    
                    $result = $result and $this->cbct->delete($key);

                }

            } else $result = false;

        }

        return $result;

    }

    protected function get_results_sorter(array $get_results)
    {

        $result = [];

        foreach ($get_results as $values) {
            
            if (array_key_exists('option_key', $values) && array_key_exists('option_value', $values)) {

                $result[$values['option_key']] = $values['option_value'];

            } else {

                $result = false;
                break;

            }

        }

        return $result;

    }

}
