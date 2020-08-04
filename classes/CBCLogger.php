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
class CBCLogger implements CBCLoggerInterface
{

    public $logs_dir;

    public function __construct()
    {
        
        $this->logs_dir = plugin_dir_path(__FILE__).'logs/';

    }

    public function log(string $message, int $level)
    {

        if ($level < 1) $level_string = 'NOTICE';
        elseif ($level === 1) $level_string = 'WARNING';
        else $level_string = 'ERROR';

        $log_time = time();
        $log_name = $log_time.'.log';

        if (file_exists($this->logs_dir.$log_name)) $log_content = file_get_contents($this->logs_dir.$log_name)."\n";
        else $log_content = '';

        return file_put_contents($this->logs_dir.$log_name, $log_content.date('Y-m-d H:i:s', $log_time).' + '.time() - $log_time.' || '.$level_string.': '.$message);

    }

}