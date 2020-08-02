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

    public function __construct(object $cbct)
    {

        if (in_array('CBConnectTableInterface', class_implements($cbct))) $this->cbct = $cbct;
        else $this->cbct = new CBConnectTable(DB_NAME);

    }

    public function set_settings(string $url, string $login, string $key)
    {

        if ($this->cbct->insert('url', $url) && $this->cbct->insert('login', $login) && $this->cbct->insert('key', $key)) return true;
        else return ($this->cbct->update('url', $url) && $this->cbct->update('login', $login) && $this->cbct->update('key', $key));

    }

    public function get_settings()
    {

        $select = $this->cbct->select("SELECT t.option_key, t.option_value FROM ".$this->cbct->db_name.".".$this->cbct->db_prefix."clientbaseconnect_options AS t WHERE t.option_key = 'url' OR t.option_key = 'login' OR t.option_key = 'key'", ARRAY_A);

        if ($select) {

            $result = [];

            foreach ($select as $values) {
                
                $result[$values['option_key']] = $values['option_value'];

            }

            if (count($result) < 3) $result = false;

        } else $result = false;

        return $result;

    }

}
