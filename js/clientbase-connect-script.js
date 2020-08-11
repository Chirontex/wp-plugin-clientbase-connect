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
function cbc_settings_set()
{
    var main = document.querySelector('#clientbase-connect-main');
    var status = document.querySelector('#clientbase-connect-status');

    var cbc_settings_buffer = '';

    var cbc_table_buffer = 'test';

    const url = document.querySelector('#cbc_settings_url').value;
    const login = document.querySelector('#cbc_settings_login').value;
    const key = document.querySelector('#cbc_settings_key').value;

    const hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    const hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    var save_button = document.querySelector('#cbc_settings_save_button');
    var back_button = document.querySelector('#cbc_settings_back_button');

    save_button.innerHTML = 'Подождите...';

    if (!save_button.hasAttribute('disabled')) save_button.setAttribute('disabled', '');

    var request = $.ajax({
        url: "/wp-json/clientbaseconnect/v1/settings/set",
        method: "POST",
        data: {
            hash: hash_value,
            hash_key: hash_key,
            url: url,
            login: login,
            key: key
        },
        dataType: "json"
    });

    request.done(function(answer) {

        if (answer['code'] === 0)
        {

            save_button.innerHTML = 'Сохранить';

            if (save_button.hasAttribute('disabled')) save_button.removeAttribute('disabled');

            if (back_button.hasAttribute('hidden')) back_button.removeAttribute('hidden');

            cbc_settings_buffer = main.innerHTML;
            main.innerHTML = cbc_table_buffer;

            status.innerHTML = 'test';
        }
        else status.innerHTML = answer['code']+': "'+answer['message']+'"';

    });

    request.fail(function(jqXHR, textStatus) {

        console.log(jqXHR);

        status.innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';

    });
}

function cbc_settings_check()
{

    if (cbc_settings_set === true)
    {
        cbc_settings_buffer = main.innerHTML;
    }
}