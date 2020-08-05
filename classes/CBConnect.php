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

        $this->cbapi = new ClientBaseAPI((string)$cbapi_settings['url'], (string)$cbapi_settings['login'], (string)$cbapi_settings['key']);

    }

    public function row_create(array $data, bool $cals = true)
    {

        $table = $this->data_taker->get_table();

        if ($table) {

            $fields = $this->data_taker->get_fields();

            if ($fields) {

                $command = [
                    'table_id' => $table,
                    'cals' => $cals,
                    'data' => ['row' => []]
                ];

                foreach ($fields as $field => $meta_entity) {
                    
                    $command['data']['row'][(string)$field] = (string)$data[$meta_entity];

                }

                $create = $this->cbapi->crud(CBAPI_CREATE, $command);

                if ($create['code'] === 0) $result = $create['id'];
                else {

                    $result = false;

                    $this->logger->log('Row creation failed. CRM answer code: '.$create['code'].'. CRM answer message: "'.$create['message'].'"', 2);

                }

            } else {

                $result = false;

                $this->logger->log('Problems with gettings fields in CBConnect:row_create().', 2);

            }

        } else {
            
            $result = false;

            $this->logger->log('Problems with getting table number in CBConnect::row_create().', 2);
        
        }

        return $result;

    }

    public function row_read(array $conditions, bool $cals = true, array $sort = ['id' => 'ASC'], int $start = 0, int $limit = 1000000)
    {

        $table = $this->data_taker->get_table();

        if ($table) {

            if ($start < 0) $start = 0;

            if ($limit < 1) $limit = 1000000;

            if (empty($conditions)) $conditions = ['status' => ['term' => '=', 'value' => 0, 'union' => 'AND']];

            $command = [
                'table_id' => $table,
                'cals' => $cals,
                'fields' => [
                    'row' => []
                ],
                'filter' => ['row' => $conditions],
                'sort' => ['row' => $sort],
                'start' => $start,
                'limit' => $limit
            ];

            $read = $this->cbapi->crud(CBAPI_READ, $command);

            if ($read['code'] === 0) $result = $read['data'];
            else {

                $result = false;

                $this->logger->log('Row reading failed. CRM answer code: '.$read['code'].'. CRM answer message: "'.$read['message'].'"', 2);

            }

        } else {

            $result = false;

            $this->logger->log('Problems with getting table number in CBConnect::row_read().', 2);

        }

        return $result;

    }

}
