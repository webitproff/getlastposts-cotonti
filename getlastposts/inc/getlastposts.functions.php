<?php
/**
 * Get Last Posts API
 *
 * getlastposts.functions.php - Functions for the Plugin getlastposts
 *
 * getlastposts plugin for CMF Cotonti (latest for today), PHP 8.4+, MySQL 8.0+
 * Date: Jun 6Th, 2026
 * Filename: getlastposts.functions.php
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
 
 
 
defined('COT_CODE') or die('Wrong URL.');


/**
 * Возвращает HTML-список последних сообщений форума.
 *
 * @param int          $count      Количество сообщений (по умолчанию 5)
 * @param string|array|false|null $category  Код категории, массив кодов,
 *                                           false / null / '' = все доступные категории
 * @param string       $template   Имя файла шаблона (по умолчанию 'getlastposts.sidebar')
 * @return string                  HTML-код списка сообщений
 */
function cot_forums_getLastPosts($count = 5, $category = null, $template = 'getlastposts.sidebar')
{
    // Проверяем, включён ли модуль «Форум». Если нет — возвращаем пустую строку,
    // чтобы не выводить ничего и не получить ошибок при обращении к таблицам форума.
    if (!cot_module_active('forums')) {
        return '';
    }

    // Регистрируем в объекте базы данных имена таблиц «forum_posts» и «forum_topics».
    // Это не подключает таблицы, а лишь сохраняет их имена, чтобы Cotonti знала,
    // какие реальные имена таблиц используются (с префиксом), и чтобы потом к ним
    // можно было обращаться как Cot::$db->forum_posts.
    // Делаем это на случай, если сам модуль forums ещё не зарегистрировал их в этом контексте.
    Cot::$db->registerTable('forum_posts');
    Cot::$db->registerTable('forum_topics');

    // Приводим $count к целому числу и гарантируем, что он не меньше 1.
    // max(1, ...) защищает от случайного нуля или отрицательного значения.
    $count = max(1, (int) $count);

    // Если $category — пустая строка, false или null, то считаем, что категории не заданы.
    // В этом случае присваиваем $category значение null (будет означать «все категории»).
    if ($category === '' || $category === false || $category === null) {
        $category = null;
    }
    // Поддержка передачи категорий через запятую в шаблоне: 'news,events,reviews'
    if (is_string($category) && $category !== '') {
        $category = array_map('trim', explode(',', $category));
    }
    // === БЛОК УЧЁТА ПРАВ ДОСТУПА ===
    // Проверяем настройку плагина 'rightscan' — нужно ли учитывать права доступа к категориям.
    if (Cot::$cfg['plugin']['getlastposts']['rightscan']) {
        // Получаем список категорий форума, к которым у текущего пользователя есть доступ на чтение.
        $authCategories = cot_authCategories('forums');
        // Если массив разрешённых категорий пуст, значит пользователь не может читать ни одну
        // категорию форума — возвращаем пустую строку.
        if (empty($authCategories['read'])) {
            return '';
        }
        // $allowed будет списком категорий, которые мы можем показать.
        $allowed = $authCategories['read'];
        // Если у пользователя нет права «читать все категории» (readAll === false),
        // то нужно отфильтровать $allowed, оставив только те, что запрошены в $category (если заданы).
        if (!$authCategories['readAll']) {
            // Если конкретные категории были переданы, приводим их к массиву.
            if ($category !== null) {
                $category = (array) $category;
                // array_intersect оставляет только те категории, которые есть и в $allowed,
                // и в $category (пересечение). Так мы не покажем то, что пользователь не имеет права читать.
                $allowed = array_intersect($allowed, $category);
            }
            // Если после фильтрации ничего не осталось — возвращаем пустую строку.
            if (empty($allowed)) {
                return '';
            }
        } else {
            // Пользователь имеет право читать все категории (readAll === true).
            if ($category !== null) {
                // Если переданы конкретные категории, будем показывать только их.
                $allowed = (array) $category;
            } else {
                // Иначе задаём пустой массив как признак «все категории» (позже не будем добавлять условие IN).
                $allowed = [];
            }
        }
    } else {
        // Если проверка прав отключена в настройках плагина.
        if ($category !== null) {
            // Ограничиваемся только переданными категориями.
            $allowed = (array) $category;
        } else {
            // Или все категории (пустой массив = без ограничения).
            $allowed = [];
        }
    }

    // === ФОРМИРОВАНИЕ УСЛОВИЯ WHERE ===
    $where = []; // Массив для частей условия WHERE
    // Если $allowed не пуст (т.е. есть конкретные разрешённые категории)
    if (!empty($allowed)) {
        // Отфильтровываем возможные «мусорные» значения: оставляем только строковые коды категорий,
        // которые не являются пустой строкой.
        $allowed = array_filter($allowed, function ($cat) {
            return is_string($cat) && $cat !== '';
        });
        // Если после фильтрации что-то осталось, добавляем условие IN.
        if (!empty($allowed)) {
            // Оборачиваем каждый код категории в кавычки и экранируем специальные символы.
            $quoted = array_map(function ($cat) {
                return Cot::$db->quote($cat);
            }, $allowed);
            // Собираем строку вида t.ft_cat IN ('cat1', 'cat2', ...)
            $where[] = 't.ft_cat IN (' . implode(', ', $quoted) . ')';
        }
    }

    // Исключаем темы, которые были перемещены в другие разделы (ft_movedto = 0 — тема не перемещена).
    $where[] = 't.ft_movedto = 0';
    // Получаем дополнительное условие для исключения приватных тем/разделов,
    // используя функцию модуля forums. Если условие не пустое, добавляем его в WHERE.
    $privateSQL = cot_forums_sqlExcludePrivateTopics('t');
    if ($privateSQL !== '') {
        $where[] = '(' . $privateSQL . ')';
    }

    // Если есть хотя бы одно условие, собираем из них строку WHERE с AND.
    $sqlWhere = '';
    if (!empty($where)) {
        $sqlWhere = 'WHERE ' . implode(' AND ', $where);
    }

    // Получаем реальные имена таблиц из зарегистрированных ранее.
    $t_posts = Cot::$db->forum_posts;
    $t_topics = Cot::$db->forum_topics;

    // Формируем окончательный SQL-запрос.
    // Выбираем идентификатор поста, идентификатор темы, id автора, имя автора,
    // дату создания, текст поста, заголовок темы, категорию темы и id темы (алиас topic_id).
    // Джойним таблицу постов с таблицей тем по идентификатору темы.
    // Применяем условие WHERE, сортируем по дате создания по убыванию, ограничиваем количество.
    $sql = "SELECT p.fp_id, p.fp_topicid, p.fp_posterid, p.fp_postername,
                   p.fp_creation, p.fp_text,
                   t.ft_title, t.ft_cat, t.ft_id AS topic_id
            FROM $t_posts AS p
            JOIN $t_topics AS t ON p.fp_topicid = t.ft_id
            $sqlWhere
            ORDER BY p.fp_creation DESC
            LIMIT $count";

    // Выполняем запрос и получаем все строки результата в виде массива ассоциативных массивов.
    $posts = Cot::$db->query($sql)->fetchAll();
    // Если посты не найдены, возвращаем пустую строку.
    if (empty($posts)) {
        return '';
    }

    // Создаём экземпляр XTemplate с файлом шаблона, расположенным в папке плагина.
    // cot_tplfile($template, 'plug') вернёт путь к файлу шаблона для плагина.
    $tpl = new XTemplate(cot_tplfile($template, 'plug'));
    $num = 0; // Счётчик для нумерации постов в шаблоне.

    // Перебираем все полученные сообщения.
    foreach ($posts as $post) {
        $num++; // Увеличиваем счётчик.

        // Определяем, разрешены ли BBCode в категории этого поста.
        // Сначала смотрим настройку для конкретной категории (cat_код),
        // если её нет — берём настройку по умолчанию (cat___default).
        $allowBBCodes = isset(Cot::$cfg['forums']['cat_' . $post['ft_cat']])
            ? Cot::$cfg['forums']['cat_' . $post['ft_cat']]['allowbbcodes']
            : Cot::$cfg['forums']['cat___default']['allowbbcodes'];

        // Парсим текст сообщения: преобразуем BBCode в HTML (если разрешено)
        // или просто обрабатываем.
        $parsedText = cot_parse($post['fp_text'], $allowBBCodes);
        // Удаляем все HTML-теги из обработанного текста, чтобы получить чистый текст для превью.
        $cleanPreview = strip_tags($parsedText);
        $maxLen = 200; // Максимальная длина превью в символах.
        // Если длина чистого текста превышает лимит, обрезаем её и добавляем '...'.
        if (mb_strlen($cleanPreview) > $maxLen) {
            $cleanPreview = mb_substr($cleanPreview, 0, $maxLen) . '...';
        }

        // Формируем URL для прямого перехода к посту (якорь #id).
        $postUrl   = cot_url('forums', 'm=posts&p=' . $post['fp_id'], '#' . $post['fp_id']);
        // Формируем URL к теме (список сообщений темы).
        $topicUrl  = cot_url('forums', 'm=posts&q=' . $post['topic_id']);
        // Строим цепочку хлебных крошек категории (без ссылок? false, false — уточнить).
        $catPath   = cot_breadcrumbs(cot_forums_buildpath($post['ft_cat'], false), false, false);
        // Формируем строку с именем пользователя (ссылка на профиль или просто текст).
        $poster    = cot_build_user($post['fp_posterid'], $post['fp_postername']);

        // Назначаем переменные для шаблона.
        $tpl->assign([
            'POST_ROW_ID'          => $post['fp_id'],           // ID поста
            'POST_ROW_TOPIC_ID'    => $post['topic_id'],       // ID темы
            'POST_ROW_URL'         => $postUrl,                // URL к посту
            'POST_ROW_TOPIC_URL'   => $topicUrl,               // URL к теме
            'POST_ROW_TITLE'       => htmlspecialchars($post['ft_title']), // Заголовок темы (экранированный)
            'POST_ROW_CAT_PATH'    => $catPath,                // Хлебные крошки категории
            'POST_ROW_POSTER'      => $poster,                 // Имя пользователя (со ссылкой)
            'POST_ROW_DATE'        => cot_date('datetime_medium', $post['fp_creation']), // Отформатированная дата
            'POST_ROW_DATE_STAMP'  => $post['fp_creation'],    // Unix-метка времени создания
            'POST_ROW_TEXT'        => $cleanPreview,           // Превью текста (обрезанное)
            'POST_ROW_ODDEVEN'     => cot_build_oddeven($num), // CSS-класс для чётности строки
            'POST_ROW_NUM'         => $num,                    // Порядковый номер поста
            'POST_ROW'             => $post,                   // Полные сырые данные поста на всякий случай
        ]);

        // Парсим блок MAIN.POST_ROW в шаблоне, используя назначенные переменные.
        $tpl->parse('MAIN.POST_ROW');
    }

    // Парсим главный блок MAIN, объединяя все POST_ROW.
    $tpl->parse('MAIN');
    // Возвращаем сгенерированный HTML из блока MAIN.
    return $tpl->text('MAIN');
}
