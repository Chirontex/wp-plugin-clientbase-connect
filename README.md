# ClientBase Connect

**RU**

ClientBase Connect — это WordPress-плагин, предназначенный для экспорта данных в CRM-систему на платформе "Клиентская база". Нынешняя конфигурация плагина позволяет экпортировать данные только о пользователях и только в одну таблицу.

## Установка
0. Вам понадобятся WordPress 5+ и PHP 7+ на сервере, а также хотя бы базовые знания программиста о том, как работают WordPress и "Клиентская база".
1. Скачайте свежий релиз плагина в zip-архиве.
2. Установите плагин в WordPress с помощью соответствующего функционала в разделе "Плагины", либо скопировав содержимое плагина в папку <корневая директория WordPress>/wp-content/plugins/clientbase-connect. Если у вас нет папки clientbase-connnect в папке plugins (скорее всего, так и будет), то создайте её самостоятельно; если же эта папка уже есть, то проверьте, не устанавливаете ли вы **ClientBase Connect** во второй раз — если это так, то сначала удалите предыдущую установку (таблицу в БД тоже желательно уничтожить), в противном случае просто создайте папку с другим именем и скопируйте **ClientBase Connect** туда.

## Настройка
1. После установки и активации плагина у вас должен был появиться пункт **CB Connect** в сайдбаре админпанели. Вам нужно зайти в него.
2. Введите параметры подключения к API "Клиентской базы".
* Ключ доступа можно получить в настройках "Клиентской базы", раздел "Модули" — нужно зайти в модуль "API". Если ключа там нет — создайте его.
* Ключ всегда создаётся для определённого пользователя "Клиентской базы" — соответственно, его логин нужно ввести в настройках подключения в одноимённом поле.
* URL — это полный URL "Клиентской базы", который вы вводите в адресной строке браузера, чтобы в неё зайти.
3. После сохранения настроек перед вами откроются настройки таблицы — имеется в виду таблица в "Клиентской базе", в которую будет сохраняться информация о пользователях WordPress.
* Номер таблицы — это номер, который ей присвоила "Клиентская база" в своей БД, его можно увидеть в адресной строке, когда вы открываете эту таблицу.
* Левая колонка — это названия ячеек таблицы в "Клиентской базе", их нужно задавать __именно в таком виде, который они имеют в БД "Клиентской базы"__.
* Правая колонка — это названия полей и метаполей пользователей WordPress. __Их названия нужно задавать именно в таком виде, который они имеют в БД WordPress__. По умолчанию плагин добавляет поле user_id — это ID пользователя в БД WordPress. Это поле удалить нельзя — плагину нужно на что-то ориентироваться, чтобы проверять наличие пользователя в "Клиентской базе". Остальные поля вы можете добавлять и удалять свободно (исключение составляют лишь некоторые поля из таблицы users, которые явно не несут никакой нормальной ценности, например, поле с хэшами паролей). Например, если вам нужно добавить адреса электронной почты пользователей — добавьте поле user_email; если же вам нужно добавить какое-либо метаполе — указывайте его название так, как оно указано в поле meta_key в таблице usersmeta.
4. После сохранения настроек таблицы в "Клиентскую базу" начнут приходить данные обо всех регистрирующихся и изменяющих свои данные пользователях с уровнем прав "Подписчик" (если хотите это изменить, см. раздел "Разработчикам") — это же справедливо для пользователей, создаваемых и изменяемых в админпанели. Если вы хотите экспортировать уже имеющихся пользователей в "Клиентскую базу", то воспользуйтесь кнопкой **"Запустить массовую синхронизацию"**. Учтите: пока массовая синхронизация в процессе, страницу нельзя закрывать, иначе она прервётся!

## Возможные проблемы
1. Если после установки плагина у вас сразу открылись настройки таблицы — уничтожьте таблицу плагина в БД WordPress. Таблица плагина называется **clientbaseconnect_options**, перед названием обязательно стоит префикс, указанный в настройках WordPress (в файле wp-config.php).
2. Если пользовательский интерфейс плагина ведёт себя как-то странно (например, вы нажимаете на кнопку, но ничего не происходит и т.д.), то проверьте, включен ли JavaScript в вашем браузере. Если JavaScript включен, то проверьте версию браузера (чем свежее, тем лучше), а также файлы плагина — возможно вы не всё скопировали при установке.
3. В случае возникновения иных проблем в работе плагина — посмотрите в папке logs. Возможно, в ней есть логи с описанием ошибок, а у вас, возможно, есть достаточно смекалки и усидчивости, чтобы разобраться в корне вашей проблемы. У меня всё работает, проблема на вашей стороне. ¯\_(ツ)_/¯

## Разработчикам
Сейчас я просто перечислю вещи, которые надо знать, если очень хочется (или очень надо) дописать что-нибудь к плагину.

1. __$cbc_logger__ — глобальная переменная, экземпляр класса __CBCLogger__ (реализует логирование).
* Метод __CBCLogger::log()__ позволяет записать инфромацию в файл лога. Принимает два обязательных аругмента — строку, которую нужно записать в лог, и целое число с уровнем логирования. Уровень 0 и ниже — заметка, уровень 1 — предупреждение, уровень 2 и выше — ошибка.
* Свойство __CBCLogger::logging_level__ определяет, какого уровня информация будет записываться в логи. Например, если установлен уровень 2, то писаться будут только ошибки, и т.д.
2. __$cbc_data_taker__ — глобальная переменная, экземпляр класса __CBCDataTaker__. Через данный класс реализуется доступ к информации в таблице плагина, хранящейся в БД WordPress. Взаимодействие с таблицей плагина производится с помощью экземпляра класса __CBConnectTable__.
3. __$cbc_settings__ — глобальная переменная, хранит в себе настройки подключения к "Клиентской базе" (если сохранены в БД).
4. __$client_base_connect__ — глобальная переменная, экземпляр класса __CBConnect__. Данный класс реализует отправку данных о пользователях в "Клиенскую базу". Данные берутся через взаимодействие с экземпляром класса __CBCUsersDataCollector__ и отправляются экземпляром класса __ClientBaseAPI__.
5. В случае добавления новых API-маршрутов, ответы для callback-функций нужно генерировать с помощью функции __clientbaseconnect_results()__, она принимает один обязательный аргумент — целое число, обозначающее код ответа, и один необязательный — строку-сообщение, расшифровывающее код. Для кодов 0, -1, -2 и -3 сообщения заданы внутри самой функции, для остальных кодов сообщение можно и желательно задавать.