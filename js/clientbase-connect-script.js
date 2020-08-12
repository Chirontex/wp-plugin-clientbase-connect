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

function cbc_table_back()
{
    const main = document.querySelector('#clientbase-connect-main');
    const status = document.querySelector('#clientbase-connect-status');

    const inputs = main.getElementsByTagName('input');

    for (let i = 0; i < inputs.length; i++)
    {
        inputs[i].setAttribute('value', inputs[i].value);
    }

    cbc_table_buffer = main.innerHTML;
    main.innerHTML = cbc_settings_buffer;

    status.innerHTML = '';
}

function cbc_settings_back()
{
    const main = document.querySelector('#clientbase-connect-main');
    const url = document.querySelector('#cbc_settings_url');
    const login = document.querySelector('#cbc_settings_login');
    const key = document.querySelector('#cbc_settings_key');
    const status = document.querySelector('#clientbase-connect-status');

    url.setAttribute('value', url.value);
    login.setAttribute('value', login.value);
    key.setAttribute('value', key.value);

    cbc_settings_buffer = main.innerHTML;
    main.innerHTML = cbc_table_buffer;

    status.innerHTML = '';
}

function cbc_field_delete(number)
{
    const field = document.querySelector('#cbc_field_'+number);
    const usermeta = document.querySelector('#cbc_usermeta_'+number);
    const link = document.querySelector('#cbc_field_delete_'+number);
    const title = document.querySelector('#cbc_field_title_'+number);

    link.parentNode.removeChild(link);
    title.parentNode.removeChild(title);
    field.parentNode.remove();
    usermeta.parentNode.remove();
}

function cbc_field_create()
{
    const main = document.querySelector('#clientbase-connect-main');
    const rows = main.getElementsByClassName('row');
    const fields_row = rows[1];
    const fields_row_divs = fields_row.getElementsByTagName('div');

    let new_number = 1;

    while (document.querySelector('#cbc_field_'+new_number))
    {
        new_number = new_number + 1;
    }

    const a = document.createElement('a');
    a.setAttribute('href', 'javascript:void(0)');
    a.setAttribute('id', 'cbc_field_delete_'+new_number);
    a.setAttribute('onclick', 'cbc_field_delete('+new_number+');');

    fields_row_divs[0].appendChild(a);

    a.innerHTML = '<p>Удалить</p>';

    let p = document.createElement('p');

    fields_row_divs[0].appendChild(p);

    let input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('class', 'form-control');
    input.setAttribute('id', 'cbc_field_'+new_number);

    p.appendChild(input);

    p = document.createElement('p');
    p.setAttribute('id', 'cbc_field_title_'+new_number);

    fields_row_divs[1].appendChild(p);

    p.innerHTML = 'поле '+(new_number + 1);

    p = document.createElement('p');

    fields_row_divs[1].appendChild(p);

    input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('class', 'form-control');
    input.setAttribute('id', 'cbc_usermeta_'+new_number);

    p.appendChild(input);

}

function cbc_table_generate()
{
    const hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    const hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    document.querySelector('#clientbase-connect-status').innerHTML = 'Идёт загрузка, подождите...';

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

        const a = document.createElement('a');
        a.setAttribute('href', 'javascript:void(0)');
        a.setAttribute('id', 'cbc_field_add');
        a.setAttribute('onclick', 'cbc_field_create();');

        main.appendChild(a);

        a.innerHTML = '<p>Добавить поле</p>';

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
        var a;
        var input;

        if (answer['code'] === 0)
        {
            const fields = Object.keys(answer['data']);

            for (let i = 0; i < fields.length; i++)
            {
                
                if (answer['data'][fields[i]] == 'user_id')
                {
                    p = document.createElement('p');
                    p.setAttribute('id', 'cbc_field_delete_'+i);

                    col_1.appendChild(p);

                    p.innerHTML = 'Удаление невозможно';
                }
                else
                {
                    a = document.createElement('a');
                    a.setAttribute('href', 'javascript:void(0)');
                    a.setAttribute('id', 'cbc_field_delete_'+i);
                    a.setAttribute('onclick', 'cbc_field_delete('+i+');');

                    col_1.appendChild(a);

                    a.innerHTML = '<p>Удалить</p>';
                }

                p = document.createElement('p');

                col_1.appendChild(p);

                input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('class', 'form-control');
                input.setAttribute('id', 'cbc_field_'+i);
                input.setAttribute('value', fields[i]);
                
                p.appendChild(input);

                p = document.createElement('p');
                p.setAttribute('id', 'cbc_field_title_'+i);

                col_2.appendChild(p);

                p.innerHTML = 'поле '+(i + 1);

                p = document.createElement('p');

                col_2.appendChild(p);

                input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('class', 'form-control');
                input.setAttribute('id', 'cbc_usermeta_'+i);
                input.setAttribute('value', answer['data'][fields[i]]);

                p.appendChild(input);

                if (answer['data'][fields[i]] == 'user_id') input.setAttribute('disabled', '');
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
            input.setAttribute('id', 'cbc_usermeta_0');
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
        button.setAttribute('id', 'cbc_table_save_button');
        button.setAttribute('onclick', 'cbc_table_save();');

        p.appendChild(button);

        button.innerHTML = 'Сохранить';

        let span = document.createElement('span');

        p.appendChild(span);

        span.innerHTML = ' ';

        button = document.createElement('button');
        button.setAttribute('type', 'button');
        button.setAttribute('class', 'btn btn-secondary');
        button.setAttribute('id', 'cbc_table_back_button');
        button.setAttribute('onclick', 'cbc_table_back();');

        p.appendChild(button);

        button.innerHTML = 'Вернуться к соединению';

        document.querySelector('#clientbase-connect-status').innerHTML = '';

    });

    request.fail(function(jqXHR, textStatus) {
        console.log(jqXHR);
        document.querySelector('#clientbase-connect-status').innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
    });
}

function cbc_table_save()
{
    var status = document.querySelector('#clientbase-connect-status');
    var button_save = document.querySelector('#cbc_table_save_button');
    var button_back = document.querySelector('#cbc_table_back_button');

    var hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    var hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    button_save.innerHTML = 'Подождите...';

    if (!button_save.hasAttribute('disabled')) button_save.setAttribute('disabled', '');

    if (!button_back.hasAttribute('disabled')) button_back.setAttribute('disabled', '');

    status.innerHTML = 'Сохранение настроек таблицы...';

    var fields_request = $.ajax({
        url: "/wp-json/clientbaseconnect/v1/table/get",
        method: "POST",
        data: {hash: hash_value, hash_key: hash_key},
        dataType: "json"
    });

    fields_request.done(function(answer) {

        if (answer['code'] === 0)
        {
            status.innerHTML = 'Удаление старых настроек...';

            var delete_request = $.ajax({
                url: "/wp-json/clientbaseconnect/v1/table/massdelete",
                method: "POST",
                data: {hash: hash_value, hash_key: hash_key},
                dataType: "json"
            });

            delete_request.done(function(answer) {

                if (answer['code'] === 0) cbc_table_set();
                else
                {
                    button_save.innerHTML = 'Сохранить';

                    if (button_save.hasAttribute('disabled')) button_save.removeAttribute('disabled');

                    if (button_back.hasAttribute('disabled')) button_back.removeAttribute('disabled');

                    status.innerHTML = 'Ошибка при удалении старых настроек таблицы. Код: '+answer['code']+', сообщение: "'+answer['message']+'"';
                }
            });

            delete_request.fail(function(jqXHR, textStatus) {
                console.log(jqXHR);
                status.innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
            });
        }
        else cbc_table_set();

    });

    fields_request.fail(function(jqXHR, textStatus) {
        console.log(jqXHR);
        status.innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
    });
}

function cbc_table_set()
{
    var main = document.querySelector('#clientbase-connect-main');
    var status = document.querySelector('#clientbase-connect-status');
    var button_save = document.querySelector('#cbc_table_save_button');
    var button_back = document.querySelector('#cbc_table_back_button');

    const rows = main.getElementsByClassName('row');
    const fields_row = rows[1];
    const fields_row_divs = fields_row.getElementsByTagName('div');

    const fields = fields_row_divs[0].getElementsByTagName('input');
    const usermetas = fields_row_divs[1].getElementsByTagName('input');

    var fu = new Object;

    for (let i = 0; i < fields.length; i++)
    {
        fu[fields[i].value] = usermetas[i].value;
    }

    const table_number = document.querySelector('#cbc_table_number').value;

    var hash_key = document.querySelector('#cbc_csrf_hash_key').value;
    var hash_value = document.querySelector('#cbc_csrf_hash_value').value;

    status.innerHTML = 'Сохранение номера таблицы...';

    var table_request = $.ajax({
        url: "/wp-json/clientbaseconnect/v1/table/set",
        method: "POST",
        data: {hash: hash_value, hash_key: hash_key, table: table_number},
        dataType: "json"
    });

    table_request.done(function(answer) {

        if (answer['code'] === 0)
        {
            status.innerHTML = 'Сохранение полей таблицы...';

            var fields_request = $.ajax({
                url: "/wp-json/clientbaseconnect/v1/fields/set",
                method: "POST",
                data: {hash: hash_value, hash_key: hash_key, fields: fu},
                dataType: "json"
            });

            fields_request.done(function(answer) {

                if (answer['code'] === 0) status.innerHTML = 'Сохранение завершено.';
                else status.innerHTML = 'Сохранение не было завершено успешно. Код ошибки: '+answer['code']+', сообщение: "'+answer['message']+'"';

                button_save.innerHTML = 'Сохранить';

                if (button_save.hasAttribute('disabled')) button_save.removeAttribute('disabled');

                if (button_back.hasAttribute('disabled')) button_back.removeAttribute('disabled');

            });

            fields_request.fail(function(jqXHR, textStatus) {
                console.log(jqXHR);
                status.innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
            });
        }
        else
        {
            button_save.innerHTML = 'Сохранить';

            if (button_save.hasAttribute('disabled')) button_save.removeAttribute('disabled');

            if (button_back.hasAttribute('disabled')) button_back.removeAttribute('disabled');

            status.innerHTML = 'Ошибка. Код: '+answer['code']+', сообщение: "'+answer['message']+'"';
        }
    });

    table_request.fail(function(jqXHR, textStatus) {
        console.log(jqXHR);
        status.innerHTML = 'Ошибка AJAX-запроса. "'+textStatus+'"';
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

    if (!back_button.hasAttribute('disabled')) back_button.setAttribute('disabled', '');

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

        window.cbc_settings_buffer = main.innerHTML;
        main.innerHTML = '';
        cbc_table_generate();
    }

}
