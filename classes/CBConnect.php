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
class CBConnect implements CBConnectInterface
{

    public $cbapi;
    public $data_taker;
    public $logger;

    public function __construct(array $cbapi_settings)
    {
        
        $this->logger = new CBCLogger;

        global $cbc_data_taker;

        if (in_array('CBCDataTakerInterface', class_implements($cbc_data_taker))) $this->data_taker = $cbc_data_taker;
        else $this->data_taker = new CBCDataTaker(new CBConnectTable(DB_NAME));

        if (empty($cbapi_settings['url']) || empty($cbapi_settings['login']) || empty($cbapi_settings['key'])) $this->logger->log('Too few settings given to CBConnect.', 2);

        $this->cbapi = new ClientBaseAPI($cbapi_settings['url'], $cbapi_settings['login'], $cbapi_settings['key']);

    }

}
