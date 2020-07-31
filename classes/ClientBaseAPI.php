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
class ClientBaseAPI
{

    public $url;
    public $login;
    public $key;

    public function __construct(string $url, string $login, string $key)
    {
        
        $this->url = $url;
        $this->login = $login;
        $this->key = $key;

    }

    protected function send_command_to_server(string $url, array $command)
    {

        $data = json_encode($command);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/json',
            'Content-length: '.strlen($data)
        ]);

        $answer = curl_exec($ch);

        $result = json_decode($answer, true);

        if (is_array($result)) return $result;
        else return $answer;

    }

    public function auth()
    {

        $request = $this->send_command_to_server($this->url.'api/auth/request', ['v' => '1.0', 'login' => $this->login, 'life_time' => 60]);

        if ($request['code'] === 0) {

            $auth = $this->send_command_to_server($this->url.'api/auth/auth', ['v' => '1.0', 'login' => $this->login, 'hash' => md5($request['salt'].$this->key)]);

            if ($auth['code'] === 0) $result = $auth['access_id'];
            else $result = false;

        } else $result = false;

        return $result;

    }

    public function crud(string $func, array $command)
    {

        if ($func === 'create' || $func === 'read' || $func === 'update' || $func === 'delete') {
            
            $request_uri = 'api/data/'.$func;

            $command['access_id'] = $this->auth();

            $result = $this->send_command_to_server($this->url.$request_uri, $command);
        
        } else $result = false;

        return $result;

    }

}
