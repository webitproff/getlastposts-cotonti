

# Get Last Forum Posts — Cotonti plugin

Displays the latest forum posts anywhere on the site with a single function call in a template.  
Flexible configuration, filtering by categories, and custom templates for each block.

[![License](https://img.shields.io/badge/license-BSD-blue.svg)](LICENSE)

## Table of contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Function parameters](#function-parameters)
- [Usage examples](#usage-examples)
  - [Homepage block: all categories, default template](#homepage-block-all-categories-default-template)
  - [Inline in an article: only one category](#inline-in-an-article-only-one-category)
  - [Sidebar with a compact design](#sidebar-with-a-compact-design)
  - [Different templates for different sections](#different-templates-for-different-sections)
  - [Checking if the plugin is active](#checking-if-the-plugin-is-active)
- [Plugin settings](#plugin-settings)
- [How to create your own template](#how-to-create-your-own-template)
- [File structure](#file-structure)
- [Frequently asked questions](#frequently-asked-questions)
- [License](#license)
- [Author and support](#author-and-support)

## Features

- Output of any number of latest forum posts (default 5)
- Filtering by one, several, or all forum categories
- Configurable access right checks for categories (can be disabled)
- Flexible template system: create your own tpl file and specify it when calling the function
- Automatically hides moved and private topics
- Truncation of long messages to a defined length with an ellipsis appended
- Proper BBCode handling depending on the category settings

## Requirements

- **Cotonti** (up-to-date version recommended)
- **The `forums` module** must be installed and activated

## Installation

1. Download the plugin archive and extract it to the `plugins/getlastposts/` folder of your site.
2. Go to the Cotonti administration panel → **"Extensions"**.
3. Find **"Get Last Forum Posts"** in the list and click **"Install"**.
4. Done! The plugin is ready to be used in templates immediately.  
   If you manually update the plugin files, after replacing them perform **"Reinstall"** to refresh the database record.

## Quick start

The simplest call in any template:

```smarty
{PHP|cot_forums_getLastPosts(5)}
```

This line will output **5 latest forum posts** from all categories accessible to the current user, using the default template `getlastposts.sidebar.tpl`.

## Function parameters

The function `cot_forums_getLastPosts()` accepts three arguments:

```php
cot_forums_getLastPosts(
    int $count = 5,                                  // Number of posts
    mixed $category = null,                          // Categories: null / false / '' = all accessible
    string $template = 'getlastposts.sidebar'        // Template file name without the .tpl extension
)
```

- **`$count`** – an integer that determines how many latest posts will be loaded.
- **`$category`** – can be:
  - `null`, `false` or `''` – display posts from all categories accessible to the user (taking into account permissions if the check is enabled);
  - a string, e.g. `'news'` – only from the category with the code `news`;
  - an array of strings, e.g. `['news', 'articles']` – from several categories simultaneously.
- **`$template`** – the template file name from the `plugins/getlastposts/tpl/` folder. The default `'getlastposts.sidebar'` corresponds to the file `getlastposts.sidebar.tpl`.

## Usage examples

Let's look at several real-world scenarios.

### Homepage block: all categories, default template

Place the following code in the homepage template (`home.tpl` or `index.tpl`):

```smarty
<h3>Forum discussions</h3>
{PHP|cot_forums_getLastPosts(7)}
```

It will display 7 latest posts from all public forum categories. The output is styled using the standard `getlastposts.sidebar.tpl` template.

### Inline in an article: only one category

Suppose your site has a page dedicated to company news, and you want to show recent discussions from the `company-news` forum category. In the page template (`page.tpl`), insert the following at the desired location:

```smarty
<!-- IF {PHP|cot_plugin_active('getlastposts')} -->
<div class="card mt-4">
  <div class="card-header">Latest discussions</div>
  <div class="card-body">
    {PHP|cot_forums_getLastPosts(10, 'company-news')}
  </div>
</div>
<!-- ENDIF -->
```

If the `company-news` category is restricted to guests, the enabled "Consider access rights" setting will hide these posts from unauthorized users.

### Sidebar with a compact design

Sometimes the default design doesn't fit a narrow sidebar. Create a new template `getlastposts.compact.tpl` (see [How to create your own template](#how-to-create-your-own-template)) and call the plugin like this:

```smarty
{PHP|cot_forums_getLastPosts(5, null, 'getlastposts.compact')}
```

Now the block will use compact markup without extra spacing.

### Different templates for different sections

You can combine filtering and templates. For example, on the jobs page display discussions from the `jobs` category with one design, and in the blog — from `blog` with another:

**In the jobs template:**
```smarty
{PHP|cot_forums_getLastPosts(6, 'jobs', 'getlastposts.jobs')}
```

**In the blog template:**
```smarty
{PHP|cot_forums_getLastPosts(8, 'blog', 'getlastposts.blog')}
```

The template files `getlastposts.jobs.tpl` and `getlastposts.blog.tpl` must be created beforehand in the plugin folder.

### Checking if the plugin is active

It is recommended to always wrap the plugin call in an activity check to avoid errors if the plugin is disabled:

```smarty
<!-- IF {PHP|cot_plugin_active('getlastposts')} -->
    {PHP|cot_forums_getLastPosts(10, false, 'getlastposts.sidebar')}
<!-- ENDIF -->
```

## Plugin settings

Settings are located in the administration panel:  
**Administration → Extensions → Get Last Forum Posts → Configuration**

### Consider access rights for categories

- **Enabled (1)** – the plugin checks whether the current user can read the category. If not, posts from that category are not displayed. This is the default value.
- **Disabled (0)** – rights are not checked, all posts are displayed except those hidden by technical means (private topics, moved topics).

## How to create your own template

1. Go to the `plugins/getlastposts/tpl/` folder.
2. Copy the file `getlastposts.sidebar.tpl` and rename it, e.g., to `getlastposts.custom.tpl`.
3. Edit it to match your design. The basic template structure is:

```smarty
<!-- BEGIN: MAIN -->
  <h6>{PHP.L.getlastposts_title}</h6>
  <ul class="list-unstyled">
    <!-- BEGIN: POST_ROW -->
    <li class="bg-transparent">
      <a href="{POST_ROW_TOPIC_URL}">{POST_ROW_TITLE}</a>
      <br><small>{POST_ROW_POSTER}, {POST_ROW_DATE}</small>
      <p class="text-muted">{POST_ROW_TEXT}</p>
    </li>
    <!-- END: POST_ROW -->
    <!-- BEGIN: NO_POSTS -->
    <li>{PHP.L.getlastposts_none}</li>
    <!-- END: NO_POSTS -->
  </ul>
<!-- END: MAIN -->
```

4. Use the new template by specifying its name without `.tpl` in the third parameter of the function:  
   `{PHP|cot_forums_getLastPosts(5, null, 'getlastposts.custom')}`

### Available variables in POST_ROW

- `{POST_ROW_ID}` – post identifier
- `{POST_ROW_TOPIC_ID}` – topic identifier
- `{POST_ROW_URL}` – direct link to the post
- `{POST_ROW_TOPIC_URL}` – link to the topic
- `{POST_ROW_TITLE}` – topic title (escaped)
- `{POST_ROW_CAT_PATH}` – category breadcrumbs
- `{POST_ROW_POSTER}` – author name (with profile link if possible)
- `{POST_ROW_DATE}` – formatted date
- `{POST_ROW_DATE_STAMP}` – Unix timestamp
- `{POST_ROW_TEXT}` – truncated text preview without HTML tags
- `{POST_ROW_ODDEVEN}` – CSS class for row parity (`even`/`odd`)
- `{POST_ROW_NUM}` – sequential number starting from 1
- `{POST_ROW}` – raw data array of the post (for advanced modifications)

## File structure

```
plugins/getlastposts/
├── getlastposts.setup.php        # Setup file (database registration)
├── getlastposts.global.php       # Global hook, includes dependencies
├── inc/
│   └── getlastposts.functions.php # Main function cot_forums_getLastPosts()
├── tpl/
│   └── getlastposts.sidebar.tpl  # Default output template
└── lang/
    └── getlastposts.ru.lang      # Language strings (Russian)
```

## Frequently asked questions

### The plugin is installed but nothing is displayed

- Make sure the "Forums" module is active.
- Check whether there are any forum posts matching the specified categories and accessible to the user.
- Temporarily enable debugging in `config.php` (`$cfg['debug_mode'] = true;`) to see possible SQL errors.

### How to display posts from several categories?

Pass an array of category codes as the second parameter:

```smarty
{PHP|cot_forums_getLastPosts(10, ['news', 'events', 'reviews'])}
```

### Posts from a required category are not displayed

- Is the "Consider access rights" setting enabled? The current user may not have read access to that category.
- Check the exact spelling of the category code (it is case-sensitive and must match the code in the forum structure).
- Posts from private and hidden sections are excluded automatically.

### Can I use multiple blocks with different settings on the same page?

Yes, you can call the function as many times as you need with different parameters and templates.

## License

BSD License. Free distribution and modification provided the copyright is preserved.

## Author and support

- **Author:** webitproff  
- **Repository:** [github.com/webitproff/getlastposts-cotonti](https://github.com/webitproff/getlastposts-cotonti)  
- **Help and discussion:** [abuyfile.com/ru/forums/cotonti](https://abuyfile.com/ru/forums/cotonti)  

If you found a bug or have suggestions, create an [issue](https://github.com/webitproff/getlastposts-cotonti/issues) in the repository.

___

# Get Last Forum Posts — плагин для Cotonti

Выводит последние сообщения форума в любом месте сайта одним вызовом функции в шаблоне.
Гибкая настройка, фильтрация по категориям и свой шаблон для каждого блока.

[![License](https://img.shields.io/badge/license-BSD-blue.svg)](LICENSE)

## Оглавление

- [Возможности](#возможности)
- [Требования](#требования)
- [Установка](#установка)
- [Быстрый старт](#быстрый-старт)
- [Параметры функции](#параметры-функции)
- [Примеры использования](#примеры-использования)
  - [Блок на главной: все категории, стандартный шаблон](#блок-на-главной-все-категории-стандартный-шаблон)
  - [Врезка в статье: только одна категория](#врезка-в-статье-только-одна-категория)
  - [Сайдбар с компактным дизайном](#сайдбар-с-компактным-дизайном)
  - [Разные шаблоны для разных разделов](#разные-шаблоны-для-разных-разделов)
  - [Проверка активности плагина](#проверка-активности-плагина)
- [Настройки плагина](#настройки-плагина)
- [Как создать собственный шаблон](#как-создать-собственный-шаблон)
- [Структура файлов](#структура-файлов)
- [Часто задаваемые вопросы](#часто-задаваемые-вопросы)
- [Лицензия](#лицензия)
- [Автор и поддержка](#автор-и-поддержка)

## Возможности

- Вывод любого количества последних сообщений форума (по умолчанию 5)
- Фильтрация по одной, нескольким или всем категориям форума
- Настраиваемый учёт прав доступа к категориям (можно отключить)
- Гибкая система шаблонов: можно создать свой tpl-файл и указать его при вызове
- Автоматическое скрытие перемещённых и приватных тем
- Обрезка длинных сообщений до заданной длины с добавлением троеточия
- Корректная обработка BBCode в зависимости от настроек категории

## Требования

- **Cotonti** (актуальная версия)
- **Модуль «Форумы» (`forums`)** должен быть установлен и активирован

## Установка

1. Скачайте архив плагина и распакуйте его в папку `plugins/getlastposts/` вашего сайта.
2. Зайдите в админ-панель Cotonti → **«Расширения»**.
3. Найдите в списке **«Get Last Forum Posts»** и нажмите **«Установить»**.
4. Готово! Плагин сразу можно использовать в шаблонах.  
   Если вы вручную обновляли файлы плагина, после замены выполните **«Переустановить»**, чтобы обновить запись в базе данных.

## Быстрый старт

Самый простой вызов в любом шаблоне:

```smarty
{PHP|cot_forums_getLastPosts(5)}
```

Эта строка выведет **5 последних сообщений форума** из всех категорий, доступных текущему пользователю, используя стандартный шаблон `getlastposts.sidebar.tpl`.

## Параметры функции

Функция `cot_forums_getLastPosts()` принимает три аргумента:

```php
cot_forums_getLastPosts(
    int $count = 5,                                  // Количество сообщений
    mixed $category = null,                          // Категории: null / false / '' = все доступные
    string $template = 'getlastposts.sidebar'        // Имя файла шаблона без расширения .tpl
)
```

- **`$count`** – целое число, определяет, сколько последних сообщений будет загружено.
- **`$category`** – может быть:
  - `null`, `false` или `''` – вывести сообщения из всех категорий, доступных пользователю (с учётом прав, если проверка включена);
  - строкой, например `'news'` – только из категории с кодом `news`;
  - массивом строк, например `['news', 'articles']` – из нескольких категорий одновременно.
- **`$template`** – имя файла шаблона из папки `plugins/getlastposts/tpl/`. По умолчанию `'getlastposts.sidebar'` соответствует файлу `getlastposts.sidebar.tpl`.

## Примеры использования

Рассмотрим несколько реальных сценариев.

### Блок на главной: все категории, стандартный шаблон

Разместите в шаблоне главной страницы (`home.tpl` или `index.tpl`) код:

```smarty
<h3>Обсуждения на форуме</h3>
{PHP|cot_forums_getLastPosts(7)}
```

Будет показано 7 последних сообщений из всех публичных категорий форума. Оформление идёт через стандартный шаблон `getlastposts.sidebar.tpl`.

### Врезка в статье: только одна категория

Предположим, на сайте есть страница, посвящённая новостям компании, и вы хотите показать свежие обсуждения из форумной категории `company-news`. В шаблоне страницы (`page.tpl`) внутри нужного места вставьте:

```smarty
<!-- IF {PHP|cot_plugin_active('getlastposts')} -->
<div class="card mt-4">
  <div class="card-header">Последние обсуждения</div>
  <div class="card-body">
    {PHP|cot_forums_getLastPosts(10, 'company-news')}
  </div>
</div>
<!-- ENDIF -->
```

Если категория `company-news` закрыта для гостей, включённая настройка «Учитывать права» скроет эти сообщения от неавторизованных пользователей.

### Сайдбар с компактным дизайном

Иногда стандартный вид не подходит для узкой боковой колонки. Создайте новый шаблон `getlastposts.compact.tpl` (см. [Как создать собственный шаблон](#как-создать-собственный-шаблон)) и вызывайте плагин так:

```smarty
{PHP|cot_forums_getLastPosts(5, null, 'getlastposts.compact')}
```

Теперь блок будет использовать компактную вёрстку без лишних отступов.

### Разные шаблоны для разных разделов

Можно комбинировать фильтрацию и шаблоны. Например, на странице с вакансиями показываем обсуждения из категории `jobs` с одним дизайном, а в блоге — из `blog` с другим:

**В шаблоне вакансий:**
```smarty
{PHP|cot_forums_getLastPosts(6, 'jobs', 'getlastposts.jobs')}
```

**В шаблоне блога:**
```smarty
{PHP|cot_forums_getLastPosts(8, 'blog', 'getlastposts.blog')}
```

Файлы шаблонов `getlastposts.jobs.tpl` и `getlastposts.blog.tpl` должны быть заранее созданы в папке плагина.

### Проверка активности плагина

Рекомендуется всегда оборачивать вызов плагина в проверку его активности, чтобы избежать ошибок, если плагин будет отключён:

```smarty
<!-- IF {PHP|cot_plugin_active('getlastposts')} -->
    {PHP|cot_forums_getLastPosts(10, false, 'getlastposts.sidebar')}
<!-- ENDIF -->
```

## Настройки плагина

Настройки находятся в админ-панели:  
**Администрирование → Расширения → Get Last Forum Posts → Настройки**

### Учитывать права доступа к категориям

- **Включено (1)** – плагин проверяет, может ли текущий пользователь читать категорию. Если нет, сообщения из неё не выводятся. Это значение по умолчанию.
- **Выключено (0)** – права не проверяются, выводятся все сообщения, кроме скрытых техническими средствами (приватные темы, перемещённые топики).

## Как создать собственный шаблон

1. Перейдите в папку `plugins/getlastposts/tpl/`.
2. Скопируйте файл `getlastposts.sidebar.tpl` и переименуйте, например, в `getlastposts.custom.tpl`.
3. Отредактируйте его под свой дизайн. Основная структура шаблона:

```smarty
<!-- BEGIN: MAIN -->
  <h6>{PHP.L.getlastposts_title}</h6>
  <ul class="list-unstyled">
    <!-- BEGIN: POST_ROW -->
    <li class="bg-transparent">
      <a href="{POST_ROW_TOPIC_URL}">{POST_ROW_TITLE}</a>
      <br><small>{POST_ROW_POSTER}, {POST_ROW_DATE}</small>
      <p class="text-muted">{POST_ROW_TEXT}</p>
    </li>
    <!-- END: POST_ROW -->
    <!-- BEGIN: NO_POSTS -->
    <li>{PHP.L.getlastposts_none}</li>
    <!-- END: NO_POSTS -->
  </ul>
<!-- END: MAIN -->
```

4. Используйте новый шаблон, указав его имя без `.tpl` в третьем параметре функции:  
   `{PHP|cot_forums_getLastPosts(5, null, 'getlastposts.custom')}`

### Доступные переменные в POST_ROW

- `{POST_ROW_ID}` – идентификатор сообщения
- `{POST_ROW_TOPIC_ID}` – идентификатор темы
- `{POST_ROW_URL}` – прямая ссылка на сообщение
- `{POST_ROW_TOPIC_URL}` – ссылка на тему
- `{POST_ROW_TITLE}` – заголовок темы (экранирован)
- `{POST_ROW_CAT_PATH}` – хлебные крошки категории
- `{POST_ROW_POSTER}` – имя автора (с ссылкой на профиль, если возможно)
- `{POST_ROW_DATE}` – отформатированная дата
- `{POST_ROW_DATE_STAMP}` – Unix-метка времени
- `{POST_ROW_TEXT}` – обрезанное превью текста без HTML-тегов
- `{POST_ROW_ODDEVEN}` – CSS-класс чётности (`even`/`odd`)
- `{POST_ROW_NUM}` – порядковый номер, начиная с 1
- `{POST_ROW}` – полный массив данных сообщения (для продвинутых модификаций)

## Структура файлов

```
plugins/getlastposts/
├── getlastposts.setup.php        # Установочный файл (регистрация в БД)
├── getlastposts.global.php       # Хук global, подключает зависимости
├── inc/
│   └── getlastposts.functions.php # Основная функция cot_forums_getLastPosts()
├── tpl/
│   └── getlastposts.sidebar.tpl  # Шаблон вывода по умолчанию
└── lang/
    └── getlastposts.ru.lang      # Языковые строки (русский)
```

## Часто задаваемые вопросы

### Плагин установлен, но ничего не выводится

- Убедитесь, что модуль «Форумы» активен.
- Проверьте, есть ли в форуме сообщения, подходящие под указанные категории и доступные пользователю.
- Временно включите отладку в `config.php` (`$cfg['debug_mode'] = true;`), чтобы увидеть возможные SQL-ошибки.

### Как вывести сообщения из нескольких категорий?

Передайте массив кодов категорий во втором параметре:

```smarty
{PHP|cot_forums_getLastPosts(10, ['news', 'events', 'reviews'])}
```

### Не отображаются сообщения из нужной категории

- Включена ли настройка «Учитывать права доступа»? Возможно, текущий пользователь не имеет доступа на чтение этой категории.
- Проверьте точное написание кода категории (он чувствителен к регистру и должен совпадать с кодом в структуре форума).
- Сообщения из приватных и скрытых разделов исключаются автоматически.

### Могу ли я использовать несколько блоков с разными настройками на одной странице?

Да, вы можете вызывать функцию сколько угодно раз с разными параметрами и шаблонами.

## Лицензия

BSD License. Свободное распространение и модификация при условии сохранения копирайта.

## Автор и поддержка

- **Автор:** webitproff  
- **Репозиторий:** [github.com/webitproff/getlastposts-cotonti](https://github.com/webitproff/getlastposts-cotonti)  
- **Помощь и обсуждение:** [abuyfile.com/ru/forums/cotonti](https://abuyfile.com/ru/forums/cotonti)  

Если вы нашли ошибку или у вас есть предложения, создайте [issue](https://github.com/webitproff/getlastposts-cotonti/issues) в репозитории.
