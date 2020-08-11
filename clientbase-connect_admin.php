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
if (file_exists(BOOTSTRAP_CSS_DIR)) { ?><link type="text/css" rel="stylesheet" href="<?= plugin_dir_url(__FILE__) ?>/css/bootstrap.min.css"><?php }
else { ?><link type="text/css" rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"><?php }

if (file_exists(BOOTSTRAP_JS_DIR)) { ?><script type="text/javascript" src="<?= plugin_dir_url(__FILE__) ?>/js/bootstrap.min.js"></script><?php }
else { ?><script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script><?php }

if (file_exists(POPPER_DIR)) { ?><script type="text/javascript" src="<?= plugin_dir_url(__FILE__) ?>/js/popper.min.js"></script><?php }
else { ?><script type="text/javascript" src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script><?php }

if (file_exists(JQUERY_DIR)) { ?><script type="text/javascript" src="<?= plugin_dir_url(__FILE__) ?>/js/jquery-3.5.1.js"></script><?php }
else { ?><script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script><?php } ?>
<script type="text/javascript" src="<?= plugin_dir_url(__FILE__) ?>/js/clientbase-connect-script.js"></script>
<input type="hidden" id="cbc_csrf_hash_key" value="<?= $cbc_csrf_hash_key ?>">
<input type="hidden" id="cbc_scrf_hash_value" value="<?= $cbc_csrf_hash_value ?>">
<div class="container">
    <div class="row">
        <div class="col-xs-0 col-sm-0 col-md-4 col-lg-4"></div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <br />
            <h3 style="text-align: center;">Client Base Connect</h3>
            <br />
            <div id="clientbase-connect-main">
                <p>
                    <label for="cbc_settings_url">URL:</label><br>
                    <input type="text" class="form-control" id="cbc_settings_url" required="">
                </p>
                <p>
                    <label for="cbc_settings_login">Логин:</label><br>
                    <input type="text" class="form-control" id="cbc_settings_login" required="">
                </p>
                <p>
                    <label for="cbc_settings_key">Ключ доступа:</label><br>
                    <input type="text" class="form-control" id="cbc_settings_key" required="">
                </p>
                <p style="text-align: center;">
                    <button type="button" class="btn btn-primary">Сохранить</button>
                </p>
            </div>
        </div>
        <div class="col-xs-0 col-sm-0 col-md-4 col-lg-4"></div>
    </div>
</div>