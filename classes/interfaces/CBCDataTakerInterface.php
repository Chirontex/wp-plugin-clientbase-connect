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
interface CBCDataTakerInterface
{

    public function __construct(object $cbct);
    public function set_settings(string $url, string $login, string $key);
    public function get_settings();
    public function set_table(int $table);
    public function get_table();
    public function set_field(string $field, string $meta_entity);
    public function get_fields();
    public function delete_field(string $key);
    public function delete_whole_table();

}
