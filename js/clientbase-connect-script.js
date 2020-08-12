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
var cbc_settings_buffer = '';
var cbc_table_buffer = '';

function cbc_getter(key)
{
    if (key === 'settings') return cbc_settings_buffer;
    else if (key === 'table') return cbc_table_buffer;
    else return false;
}

function cbc_setter(key, value)
{
    if (key === 'settings') cbc_settings_buffer = value;
    else if (key === 'table') cbc_table_buffer = value;
}

function cbc_table_generate()
{
    const hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    const hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    var request = $.ajax({
        url: "/wp-json/clientbaseconnect/v1/table/get",
        method: "POST",
        data: {hash: hash_value, hash_key: hash_key},
        dataType: "json"
    });

    request.done(function(answer) {

        const main = document.querySelector('#clientbase-connect-main');

        const row = document.createElement('div');
        row.setAttribute('class', 'row');

        main.appendChild(row);

        const col_1 = document.createElement('div');
        col_1.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(col_1);

        const col_2 = document.createElement('div');
        col_2.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(col_2);

        let p = document.createElement('p');
        col_1.appendChild(p);

        const label = document.createElement('label');
        label.setAttribute('for', 'cbc_table_number');

        p.appendChild(label);
        label.innerHTML = 'Номер таблицы:';

        const input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.setAttribute('id', 'cbc_table_number');
        input.setAttribute('class', 'form-control');

        if (answer['code'] === 0) input.setAttribute('value', answer['data']);

        p.appendChild(input);

        p = document.createElement('p');

        main.appendChild(p);

        p.innerHTML = 'Поля таблицы:';

        cbc_fields_generate();

    });

    request.fail(function(jqXHR, textStatus) {
        console.log(jqXHR);
        document.querySelector('#clientbase-connect-status').innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
    });
}

function cbc_fields_generate()
{
    const hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    const hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    var request = $.ajax({
        url: "/wp-json/clientbaseconnect/v1/fields/get",
        method: "POST",
        data: {hash: hash_value, hash_key: hash_key},
        dataType: "json"
    });

    request.done(function(answer) {

        const main = document.querySelector('#clientbase-connect-main');

        const row = document.createElement('div');
        row.setAttribute('class', 'row');

        main.appendChild(row);

        const col_1 = document.createElement('div');
        col_1.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(col_1);

        const col_2 = document.createElement('div');
        col_2.setAttribute('class', 'col-xs-6 col-sm-6 col-md-6 col-lg-6');

        row.appendChild(col_2);

        var p;
        //var label;
        var input;

        if (answer['code'] === 0)
        {
            const fields = answer['data'].keys();

            for (let i = 0; i < fields.length; i++)
            {
                p = document.createElement('p');

                col_1.appendChild(p);

                input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('class', 'form-control');
                input.setAttribute('id', 'cbc-field-'+i);
                input.setAttribute('value', fields[i]);
                
                p.appendChild(input);
            }

            for (let i = 0; i < answer['data'].length; i++)
            {
                p = document.createElement('p');

                col_2.appendChild(p);

                input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('class', 'form-control');
                input.setAttribute('id', 'cbc-usermeta-'+i);
                input.setAttribute('value', answer['data'][i]);

                p.appendChild(input);

                if (answer['data'][i] == 'user_id') input.setAttribute('disabled', '');

            }
        }
        else
        {
            p = document.createElement('p');

            col_1.appendChild(p);

            input = document.createElement('input');
            input.setAttribute('type', 'text');
            input.setAttribute('class', 'form-control');
            input.setAttribute('id', 'cbc-field-0');

            p.appendChild(input);

            p = document.createElement('p');

            col_2.appendChild(p);

            input = document.createElement('input');
            input.setAttribute('type', 'text');
            input.setAttribute('class', 'form-control');
            input.setAttribute('id', 'cbc-usermeta-0');
            input.setAttribute('value', 'user_id');
            input.setAttribute('disabled', '');

            p.appendChild(input);
        }

        p = document.createElement('p');
        p.setAttribute('style', 'text-align: center;');

        main.appendChild(p);

        let button = document.createElement('button');
        button.setAttribute('type', 'button');
        button.setAttribute('class', 'btn btn-primary');

        p.appendChild(button);

        button.innerHTML = 'Сохранить';

        let span = document.createElement('span');

        p.appendChild(span);

        span.innerHTML = ' ';

        button = document.createElement('button');
        button.setAttribute('type', 'button');
        button.setAttribute('class', 'btn btn-secondary');

        p.appendChild(button);

        button.innerHTML = 'Вернуться';

    });

    request.fail(function(jqXHR, textStatus) {
        console.log(jqXHR);
        document.querySelector('#clientbase-connect-status').innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
    });
}

function cbc_settings_set()
{
    var main = document.querySelector('#clientbase-connect-main');
    var status = document.querySelector('#clientbase-connect-status');

    const url = document.querySelector('#cbc_settings_url').value;
    const login = document.querySelector('#cbc_settings_login').value;
    const key = document.querySelector('#cbc_settings_key').value;

    const hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    const hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    var save_button = document.querySelector('#cbc_settings_save_button');
    var back_button = document.querySelector('#cbc_settings_back_button');

    var is_set = cbc_settings_are_set;

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

            if (back_button.hasAttribute('disabled')) back_button.removeAttribute('disabled');

            //cbc_setter('settings', main.innerHTML);
            window.cbc_settings_buffer = main.innerHTML;

            if (is_set) status.innerHTML = 'Настройки соединения сохранены.';
            else {
                
                main.innerHTML = '';
                cbc_table_generate();
            
            }

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

    const main = document.querySelector('#clientbase-connect-main');
    const back_button = document.querySelector('#cbc_settings_back_button');

    if (cbc_settings_are_set === true)
    {
        if (back_button.hasAttribute('hidden')) back_button.removeAttribute('hidden');

        if (back_button.hasAttribute('disabled')) back_button.removeAttribute('disabled');

        //cbc_setter('settings', main.innerHTML);
        window.cbc_settings_buffer = main.innerHTML;
        main.innerHTML = '';
        cbc_table_generate();
    }

}