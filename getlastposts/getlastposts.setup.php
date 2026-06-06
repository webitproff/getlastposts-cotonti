<?php
/* ====================
[BEGIN_COT_EXT]
Code=getlastposts
Name=Get Last Forum Posts
Category=custom
Description=Вывод последних сообщений форума в любом месте сайта через {PHP|cot_forums_getLastPosts()}
Version=1.3.1
Date=2026-06-06
Author=webitproff
Copyright=(c) webitproff 2026
Notes=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=forums
Requires_plugins=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
rightscan=01:radio::1:Учитывать права доступа к категориям
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL');



/**
 * getlastposts.setup.php - Register data in $db_core and $db_config. Setup & Config File for the Plugin getlastposts
 *
 * getlastposts plugin for CMF Cotonti (latest for today), PHP 8.4+, MySQL 8.0+
 * Date: Jun 6Th, 2026
 * Filename: getlastposts.setup.php
 *
 * Source: https://github.com/webitproff/getlastposts-cotonti
 * Demo: https://abuyfile.com/
 *
 * @package getlastposts
 * @version 1.3.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff
 * @license BSD
 */
/**
Разбор полей:

    Code: Уникальный код плагина, в данном случае featuredproducts.
    Name: Название плагина, например, Featured Products in Market.
    Category: Категория, к которой относится плагин.
    Description: Описание плагина, например, 
    Version: Версия плагина, например, 1.0.0.
    Date: Дата выпуска текущей версии плагина, например, 2025-02-27.
    Author: Автор плагина. Здесь можно указать ваше имя или компанию.
    Copyright: Авторские права, например, ваше имя или название вашей компании.
    Notes: Лицензия плагина. В данном случае BSD License.
    SQL: Если плагин использует SQL-таблицы, то укажите путь к SQL-скрипту. Если нет, оставьте пустым.
    Auth_guests: (Auth_guests=R) Права доступа для гостей, например, R — доступ только для чтения.
    Lock_guests: (Lock_guests=WA) Лок (лок - даже админ не поправит в админке) для гостей, например, 12345A — защищает от несанкционированного доступа.
    Auth_members: (Auth_members=RW) Права доступа для зарегистрированных пользователей, например, RW — чтение и запись.
    Lock_members: (Lock_members=A )Лок для зарегистрированных пользователей, например, 12345A.
    Recommends_modules: Модули, которые рекомендуется использовать с плагином (если применимо).
    Recommends_plugins: Плагины, которые рекомендуется использовать с плагином (если применимо).
    Requires_modules: Модули, которые необходимы для работы плагина. В данном случае, page, так как плагин работает со статьями.
    Requires_plugins: Плагины, которые необходимы для работы плагина (если применимо). Если нет, оставьте пустым.

 */ 


/* 
**Структура файлов плагина (текущая версия):**
```
plugins/getlastposts/
├── getlastposts.setup.php         // Setup file (database registration)
├── getlastposts.global.php        // Global hook, includes dependencies
├── inc/
│   └── getlastposts.functions.php // Main function cot_forums_getLastPosts()
├── tpl/
│   └── getlastposts.sidebar.tpl   // Default output template
└── lang/
    ├── getlastposts.en.lang.php   // Английский языковой файл
    └── getlastposts.ru.lang.php       // Language strings (Russian)
```
 */
 