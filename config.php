<?php
$begin_script = microtime();
//----------------------
// debug
error_reporting(E_ALL);
//----------------------
//  НАСТРОЙКИ БЕЗОПАСНОСТИ
/* Установите код для регистрации, если хотите ограничить круг лиц, для которых
регистрация будет доступна. В этом случае они могут зарегистрироваться, воспрользовавшись ссылкой
http://your_site/ProEktor_folder/registration.php?code=YOUR_CODE_HERE
Оставьте переменную пустой, если хотите, чтобы регистрация была доступна для всех */
$SEC['registration_code'] = "caw";
//  Активация пользователей по email (защита от роботов)
$SEC['check_email'] = false;
//  Шум - любые символы, делающие уникальным хеш в ссылке активации по email
$SEC['noise'] = "u8";
//  Активация пользователей администратором 
$SEC['check_admin'] = false;
//  Шум - любые символы, делающие уникальным хеш пароля пользователя
//  НЕ МЕНЯТЬ после того, как кто-то уже зарегистрировался
$SEC['seed'] = "snk-caw";
// путь к каталогу с ПроЕктором относительно корня сайта
$SEC['proektor_path'] = ""; // like "/projector"/propolygon
//-----------------------
// имя компании
$COMPANY_NAME = "Lasos zabavki";
// язык системы
$MY_LOCALE = "ru"; // ru, en
include("languages/".$MY_LOCALE.".php");
// спрашивать отчество при регистрации
$ASK_SNAME = true;
// год основания компании
$SINCE_YEAR = "2000";
// заголовки страниц
$PAGES_TITLE = $product['name']." ".$other['for']." ".$COMPANY_NAME;
// время, в иечение которого пользователь считается онлайн (в секундах)
$SONLINE = 500;
// адрес администратора сайта (от его имени будут отправляться письма пользователям)
$ADMINSEMAIL = 'bret.snk@gmail.com';
//------------------------
//  тэги, доступные для парсинга
$TAGS = array('b', 'i', 'tt', 'u', 's', 'pre');
//  тэги, доступные для парсинга
$SMILES = array(':)', ':(', ';)', '%-6');
//  автоматически преобразовывать URL в ссылки
$PARSE_URL = true;
//  время, в течении которого можно редавтировать свой пост, cek
$POST_EDIT_TIME = 600;
//  количество постов на странице (не редактировать это число, когда первые посты же появились!)
$POSTS_ON_PAGE = 10;
//  количество новостей на странице
$NEWS_ON_PAGE = 5;
//  количество букв в краткой форме новости
$SYMBOLS_SHORT_NEWS = 400;
// размер загружаемого аватара в байтах, не более
$AV_MAX_WEIGHT = 6100;
// размер загружаемого лого проекта в байтах, не более
$PLOGO_MAX_WEIGHT = 12200;
// начало недели: 0 - с воскресенья, 1 - с понедельника
$WEEK_BEGIN_DAY = 1;  //гы-гы
// допустимые расширения закачиваемых файлов
$ALLOW_EXTENSIONS2 = array("image/gif", "image/png", "image/jpeg", "application/zip", "application/x-rar-compressed", "application/7z", "application/x-tar", "application/force-download");
// допустимые расширения закачиваемых файлов - 2
$ALLOW_EXTENSIONS = array("gif", "png", "jpeg", "jpg", "zip", "rar", "7z", "tar", "gz");
// размер загружаемого файла в байтах, не более
$FILE_MAX_WEIGHT = 1024*1024*12;
// время напоминания о истекающем сроке задания, с
$TASK_NOTICE_TIME = 24*60*60;
// иконка группы по умолчанию (base64)
$DEFAULT_GROUP_ICON = "R0lGODlhEgASAIABAAAAAAAAACH5BAEAAAEALAAAAAASABIAAAIuhI+Jwe0fzFPA"."SUvb1fp0iFXMBnqjWJoRGrJr6r4Q+YE2bdftHC9TnJMFW5RiAQA7";
// максимальная длина слова в блоке отображения последних постов, симв
$MAX_W0RD_LENGTH = 21;
//----------------------
// ПАРАМЕТРЫ БАЗЫ ДАННЫХ
//  не трогать этот блок!!!
include("connection.php");
if(!isset($DBASE['adress'])||!isset($DBASE['login'])||!isset($DBASE['pass'])||!isset($DBASE['name']))
{
 // адрес 
 $DBASE['adress'] = "localhost";
 // логин
 $DBASE['login'] = "login";
 // пароль
 $DBASE['pass'] = "pass";
 // имя
 $DBASE['name'] = "name";
}
//------------------------
//   файлы, открытые для не3арегистрированных пользователей
$VISIBLE_FILES = array("/index.php", "/registration.php", "/install.php", "/error404.php");
//------------------------
error_reporting(E_ALL);
?>