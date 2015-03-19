<?php
//---------------
$product['name'] = "ПроЕктор";
//-----------------
//* Links
$LF_links['news'] = 'Новости';
$LF_links['events'] = "События";
$LF_links['all_events'] = "Все события";
$LF_links['projects'] = "Проекты";
$LF_links['tasks'] = "Задачи";
$LF_links['mtasks'] = "Мои задачи";
$LF_links['users'] = "Пользователи";
$LF_links['profile'] = "Профиль пользователя";
$LF_links['mprofile'] = "Мой профиль";
$LF_links['about'] = "О 'ПроЕкторе'";
$LF_links['messages'] = "Сообщения";
$LF_links['admin'] = "Админцентр";
$LF_links['logout'] = "Выход";
$LF_links['groups'] = 'Группы';
$LF_links['editprofile'] = "Редактирование профиля";
$LF_links['inpms'] = "Входящие сообщения";
$LF_links['outpms'] = "Исходящие сообщения";
$LF_links['newpms'] = 'Непрочитанные сообщения';
$LF_links['newpm'] = 'Новое сообщение';
$LF_links['last_events'] = 'Ближайшие события';
$LF_links['files'] = 'Файлы';
$LF_links['myfiles'] = 'Мои файлы';
$LF_links['bugs'] = 'Багтрекер';
//----------------
$formz['login'] = "Логин";
$formz['pass'] = "Пароль";
$formz['sysin'] = "Вход в систему";
$LF_formz['submit'] = "Отправить";
$LF_formz['letsreg'] = "Зарегистрироваться";
$formz['registr'] = "Регистрация";
$formz['reqfields'] = "Поля, отмеченные <sup class='reqf'>*</sup> обязательны для заполнения";
$formz['spass'] = "Повтор пароля";
$formz['nick'] = "Никнейм";
$formz['name'] = "Имя";
$formz['sname'] = "Отчество";
$formz['surname'] = "Фамилия";
$formz['icq'] = "Номер ICQ";
$formz['email'] = "Email-адрес";
$LF_formz['jabber'] = "Jabber ID";
$formz['hphone'] = "Домашний телефон";
$formz['mphone'] = "Мобильный телефон";
$formz['country'] = "Страна";
$formz['city'] = "Город";
$formz['adress'] = "Домашний адрес";
$formz['usalready'] = "Пользователь с таким логином уже существует!";
$formz['notallf'] = "Заполнены не все обязательные для заполнения поля!";
$formz['logshort'] = "<em>".$formz['login']."</em> слишком короткий! Используйте не менее 3 символов.";
$formz['passshort'] = "<em>".$formz['pass']."</em> слишком короткий! Используйте не менее 6 символов.";
$formz['passdiff'] = "Поля <em>".$formz['pass']."</em> и <em>".$formz['spass']."</em> не совпадают!";
$formz['regready'] = "Регистрация успешно завершена.";
$formz['emact'] = "В ближайшее время на указанный вами email будет выслана ссылка активации акаунта.";
$formz['admact'] = "Ваша учётная запись требует активации администратором. На указанный вами email будет выслано сообщение об активации учётной записи, как только это произойдёт.";
$formz['icqdig'] = "<em>".$formz['icq']."</em> должен содержать от 5 до 9 цифр!";
$formz['activation_letter_subject'] = "Регистрация в системе управления проектами ".$COMPANY_NAME;
$formz['activation_letter_body'] = "Вы получили это письмо, т.к. кто-то заполнил регистрационную форму от вашего имени. Если это были не вы, то проигнорируйте данное сообщение, иначе воспользуйтесь ссылкой для активации вашей учётной записи:";
$formz['activation_letter_delim'] = "Ваши данные для входа:";
$LF_formz['wbregagrds'] = "С уважением, администратор сайта ".$COMPANY_NAME;
$LF_formz['plushour'] = "Укажите смещение в часах<br />относительно времени сервера<br />(текущее время сервера - ";
$LF_formz['savechanges'] = "Сохранить изменения";
$LF_formz['h'] = "ч";
$LF_formz['birthdate'] = 'Дата рождения';
$LF_formz['status'] = "Статус пользователя";
$LF_formz['ediem'] = "Редактировать e-mail, пароль";
$LF_formz['ediem_adm'] = "Редактировать e-mail, пароль, статус";
$LF_formz['npass'] = "Новый пароль";
$LF_formz['npass2'] = "Повтор нового пароля";
$LF_formz['oldpassreq'] = "Внимание! Для смены пароля или e-mail необходимо ввести ваш существующий пароль.";
$LF_formz['chavatar'] = "Изменить аватар";
$LF_formz['chplogo'] = "Изменить логотип";
$LF_formz['addtask'] = "Добавить задачу";
$LF_formz['edittask'] = "Редактировать задачу";
//--------------
$LF_mess[0] = "<span class='rse'>Нет пользователя с таким кодом активации.</span>";
$LF_mess[1] = "<span class='rwe'>Вы прошли активацию по email.</span>";
$LF_mess[2] = "<span class='rse'>Невозможно подключиться к базе данных!</span>";
$LF_mess[3] = "<span class='rse'>Неправильный логин или пароль!</span>";
$LF_mess[4] = "<span class='rse'>Ваш аккаунт заблокирован!</span>";
$LF_mess[5] = "<span class='rse'>Проверьте правильность введённых данных</span>";
$LF_mess[6] = "<span class='rse'>Нет такого пользователя!</span>";
$LF_mess[7] = "<span class='rse'>Недостаточно прав!</span>";
$LF_mess[8] = "<span class='rse'>Ошибка редактирования!</span>";
$LF_mess[9] = "<span class='rwe'>Изменения сохранены</span>";
$LF_mess[10] = "<span class='rse'>Неправильный пароль!</span>";
$LF_mess[11] = "<span class='rse'>Расхождение полей '".$LF_formz['npass']."' и '".$LF_formz['npass2']."'</span>";
$LF_mess[12] = "<span class='rse'>Недопустимые параметры файла</span>";
$LF_mess[13] = "<span class='rse'>Ничего не найдено</span>";
$LF_mess[14] = "<span class='rse'>Недопустимое расширение файла</span>";
$LF_mess[15] = "<span class='rse'>Недопустимый размер файла</span>";
$LF_mess[16] = "<span class='rwe'>Файл удалён</span>";
$LF_mess[17] = "<span class='rwe'>Файл добавлен</span>";
//--------------
$other['for'] = "для";
$other['powered_by'] = "Продукт by";
$other['logo'] = "Лого";
$LF_other['ret_index'] = "<a href='index.php'>Возврат на главную страницу</a>";
$LF_other['youlldontseethis'] = 'Вы не должны видеть это сообщение';
$other['error404'] = <<<ERRR
<h2 class='rse'>Ошибка 404</h2>
<p>Страница не найдена.<br />Возможно, она существовала ранее и была удалена, а возможно, что и не было её вовсе.<br />Bы можете вернуться на <a href="/{$SEC['proektor_path']}index.php">Главную</a> ПроЕктора</p>
ERRR;
$other['error403'] = <<<ERRR
<h2 class='rse'>Ошибка 403</h2>
<p>Доступ к странице запрещён.<br />Bы можете вернуться на <a href="/{$SEC['proektor_path']}index.php">Главную</a> ПроЕктора</p>
ERRR;
$LF_other['aloha'] = 'Привет';
$LF_other['now'] = 'Сейчас';
$LF_other['back'] = 'Вернуться';
$LF_other['my'] = 'Мой';
$LF_other['no'] = 'Нет';
$LF_other['Link'] = 'Ссылка';
$LF_other['timeline'] = 'Время выполнения';
$LF_other['sendamess'] = "Отправить сообщение";
$LF_other['editprofile'] = "Редактировать профиль";
$LF_other['addpro'] = "Добавить проект";
$LF_other['uonline'] = "Сейчас на сайте";
$LF_other['uptime'] = "Не проявляет активности в течении ";
$LF_other['registered'] = "Зарегистрирован";
$LF_other['lastactivity'] = "Последняя активность";
$LF_other['delete'] = "Удалить";
$LF_other['project'] = "Проект";
$LF_other['projectname'] = "Название проекта";
$LF_other['projectlogo'] = "Логотип проекта";
$LF_other['projectnew'] = "Новый проект";
$LF_other['lider'] = "Руководитель";
$LF_other['state'] = "Статус";
$LF_other['by'] = "от";
$LF_other['quitnosave'] = 'Выйти без сохранения';
$LF_other['project_start'] = "Начало проекта";
$LF_other['project_end'] = "Завершение проекта";
$LF_other['project_desc'] = "Описание проекта";
$LF_other['delproject'] = "Удалить проект";
$LF_other['project_files'] = "Файлы проекта";
$LF_other['project_tasks'] = "Задачи проекта";
$LF_other['project_roadmap'] = "Календарь задач";
$LF_other['discussion'] = "Обсуждение";
$LF_other['comments'] = "Комментарии";
$LF_other['comment'] = array("комментарий", "комментария", "комментариев");
$LF_other['noposts'] = "Нет сообщений";
$LF_other['to_discuss'] = "Поучаствовать в обсуждении";
$LF_other['editpost'] = "Редактировать сообщение";
$LF_other['added'] = "Добавлено";
$LF_other['author'] = 'Автор';
$LF_other['addnews'] = 'Добавить новость';
$LF_other['addevent'] = "Добавить событие";
$LF_other['edititems3'] = "Редактировать новость";
$LF_other['edititems4'] = "Редактировать событие";
$LF_other['editpro'] = "Редактировать проект";
$LF_other['editfile'] = "Редактировать файл";
$LF_other['rlydelpost'] = "Вы действительно хотите удалить сообщение?";
$LF_other['rlydelitems3'] = "Вы действительно хотите удалить новость?";
$LF_other['rlydelitems4'] = "Вы действительно хотите удалить событие?";
$LF_other['rlydelpro'] = "Вы действительно хотите удалить проект?";
$LF_other['rlydelfile'] = "Вы действительно хотите удалить файл?";
$LF_other['rlydelgroup'] = 'Вы действительно хотите удалить группу?\nУдаление группы возможно при условии, что в ней не состоит ни один пользователь.';
$LF_other['no_items3'] = "Нет ни одной новости";
$LF_other['no_items4'] = "Нет ни одного события";
$LF_other['allitems3'] = "Все новости";
$LF_other['allitems4'] = "Все события";
$LF_other['itemtitle3'] = "Заголовок новости";
$LF_other['itemtitle4'] = "Заголовок события";
$LF_other['event_end'] = "Событие состоялось";
$LF_other['event_for'] = "До события осталось";
$LF_other['evented'] = "состоится";
$LF_other['eventdate'] = "Дата события";
$LF_other['mstbenomuch'] = "файл должен быть размером не более";
$LF_other['openAll'] = 'Показать всё';
$LF_other['closeAll'] = 'Скрыть всё';

//* tasks
$LF_other['maker'] = 'Исполнитель';
$LF_other['taskname'] = 'Название задачи';
$LF_other['subtaskname'] = 'Название подзадачи';
$LF_other['taskdesc'] = 'Описание задачи';
$LF_other['task_start'] = 'Начало задачи';
$LF_other['task_end'] = 'Завершение задачи';
$LF_other['userstask'] = 'Задачи пользователя';
$LF_other['edittask'] = 'Редактировать задачу';
$LF_other['deltask'] = 'Удалить задачу';
$LF_other['editsubtask'] = 'Редактировать задачу';
$LF_other['delsubtask'] = 'Удалить задачу';
$LF_other['rlydeltask'] = 'Вы действительно хотите удалить задачу?';
$LF_other['newtask'] = 'Новая задача';
$LF_other['addmtask'] = 'Добавить подзадачу';
$LF_other['subtasks'] = 'Подзадачи';
$LF_other['subtasks_calendar'] = 'Календарь подзадач';
$LF_other['sub_tasks'] = 'Короткие подзадачи';
$LF_other['nosubtasks'] = 'Нет подзадач';
$LF_other['editsubtask'] = 'Редактировать подзадачу';
$LF_other['delsubtask'] = 'Удалить подзадачу';
$LF_other['mothertask'] = 'Основная задача';
$LF_other['mynewsubtask'] = 'Моя новая подзадача';
$LF_other['rlydelsubtask'] = 'Вы действительно хотите удалить подзадачу?';
$LF_other['subtaskeditform'] = array('Название подзадачи','Время выполнения','ч','Выполнено','Новая подзадача', 'Редактировать подзадачу');
$LF_other['newtaskdesc'] = 'Работать, работать и ещё раз работать...';
$LF_other['taskready'] = 'Готовность задачи';
$LF_other['taskgroup'] = 'Группа задачи';

//* bugs
$LF_other['bugs'] = 'Баги';
$LF_other['noBugs'] = 'Всё работает как часики - багов нет';
$LF_other['bugDetected'] = 'Новый баг';
$LF_other['bugState'] = array('Новый', 'Подтверждён', 'В обработке', 'Закрыт');
$LF_other['bugHeader'] = 'Заголовок бага';
$LF_other['bugDesc'] = 'Описание бага';
$LF_other['linkedTask'] = 'Связанная задача';
$LF_other['fullBugList'] = 'Полный список багов';
$LF_other['editBug'] = 'Редактировать баг';
$LF_other['deleteBug'] = 'Удалить баг';
$LF_other['rlyDelBug'] = 'Вы действительно хотите удалить баг?';
$LF_other['fixingBug'] = 'Исправление бага';
$LF_other['useExisting'] = 'Использовать существующую';

//* files
$LF_other['nofiles'] = 'Файлы не найдены';
$LF_other['task_files'] = 'Файлы задачи';
$LF_other['filename'] = 'Название файла';
$LF_other['download'] = 'Скачать';
$LF_other['filedesc'] = 'Описание файла';
$LF_other['usera'] = 'пользователя';
$LF_other['addfile'] = 'Добавить файл';
$LF_other['editfile'] = 'Редактировать файл';
$LF_other['file'] = 'Файл';
$LF_other['filesize'] = 'Размер';
$LF_other['allowext'] = 'допустимые расширения';
$LF_other['newfiledesc'] = 'Я был настолько ленив, чтобы придумывать описание для этого файла, что вам теперь придётся самим догадываться о характере его содержимого';
$LF_other['b'] = 'байт';
$LF_other['kb'] = 'Kб';
$LF_other['Mb'] = 'Mб';

//* groups
$LF_other['nogroup'] = 'Нет такой группы';
$LF_other['nogroups'] = 'Не найдено ни одной группы';
$LF_other['groupname'] = 'Название группы';
$LF_other['groupdesc'] = 'Описание группы';
$LF_other['addgroup'] = 'Добавление группы';
$LF_other['editgroup'] = 'Редактирование группы';
$LF_other['delgroup'] = 'Удаление группы';
$LF_other['usergroups'] = 'Группы пользователя';
$LF_other['groupcons'] = 'Состав группы';
$LF_other['editgroupcons'] = 'Редактирование состава группы';
$LF_other['selectuser'] = 'выбор пользователя';
$LF_other['adduser2group'] = 'Добавить пользователя в группу';
$LF_other['deluser2group'] = 'Убрать пользователя из группы';
$LF_other['groupicon'] = 'Иконка группы';
$LF_other['withoutgroup'] = 'без группы';
$LF_other['postlink'] = 'Ссылка на cообщение';
$LF_other['promptchangetaskprogress'] = 'Установите новое значение процентов выполнения задачи, 0-100';
$LF_other['latestposts'] = 'Последние комментарии';
$LF_other['save'] = 'Сохранить';
$LF_other['cansel'] = 'Отмена';
//----------------
$LF_PRO_STT[0] = 'Заморожен';
$LF_PRO_STT[1] = 'Активен';
$LF_PRO_STT[2] = 'Завершён';
//----------------
$LF_TASK_STT[0] = 'Приостановлена';
$LF_TASK_STT[1] = 'Выполняется';
$LF_TASK_STT[2] = 'Завершена';
//----------------
$LF_messz['from'] = 'От';
$LF_messz['for'] = 'Для';
$LF_messz['subj'] = 'Тема';
$LF_messz['body'] = 'Сообщение';
$LF_messz['reset'] = 'Очистить';
$LF_messz['send'] = 'Отправить';
$LF_messz['sended'] = 'Отправлено';
$LF_messz['nomssgz'] = 'Нет сообщений!';
$LF_messz['answer'] = 'Ответить';
$LF_messz['nosubj'] = 'Без темы';
$LF_messz['unred'] = 'Непрочитанное';
$LF_messz['red'] = 'Прочитанное';
$LF_messz['mail_newpm'] = array('Новое личное сообщение в системе "ПроЕктор"', 'Пользователь', 'отправил вам личное сообщение в системе "ПроЕктор" на сайте '.$COMPANY_NAME);
$LF_messz['notice'] = array();
$LF_messz['notice']['taskfail'] = array('Закончился срок выполнения задачи', 'Довожу до вашего сведения, что пользователем', 'по состоянию на текущий момент не была выполнена задача');
$LF_messz['notice']['tasknotice'] = array('Напоминание о задаче', 'Напоминаю, что у пользователя', 'по состоянию на текущий момент осталось менее', 'для выполнения задачи');
$LF_messz['notice']['tasknew'] = array('Добавлена новая задача', 'Спешу сообщить, что пользователю', 'была добавлена новая задача');
$LF_messz['notice']['bestregards'] = 'С уважением, робот сайта';
//----------------
$LF_time['year'] = array("год", "года", "лет");
$LF_time['month'] = array("месяц", "месяца", "месяцев");
$LF_time['day'] = array("день", "дня", "дней");
$LF_time['hour'] = array("час", "часа", "часов");
$LF_time['decade'] = array("декада", "декады", "декад");
$LF_time['minute'] = array("минута", "минуты", "минут");
$LF_time['mon_long'] = array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", 'сентябрь', 'октябрь', "ноябрь", "декабрь");
$LF_time['day_short'] = array("вс", "пн", "вт", "ср", "чт", "пт", "сб");
$LF_time['mon_short'] = array('янв', 'фев', 'мар', 'апр', "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек");
//----------------
$LF_STATS[0] = "Заблокированный по e-mail и администратором";
$LF_STATS[1] = "Заблокированный по e-mail";
$LF_STATS[2] = "Заблокированный администратором";
$LF_STATS[3] = "Пользователь";
$LF_STATS[4] = "Модератор";
$LF_STATS[5] = "Администратор";
/*
$LF_STATS[0] = "Double blocked by Email and by Administrator";
$LF_STATS[1] = "Blocked by Email";
$LF_STATS[2] = "Blocked by Administrator";
$LF_STATS[3] = "User";
$LF_STATS[4] = "Moderator";
$LF_STATS[5] = "Administrator";*/
//----------------
$LF_install['head'] = 'Установка "'.$product['name'].'"';
$LF_install['motto'] = '<p>Добро пожаловать!</p><p>С помощью этой страницы вы сможете установить "'.$product['name'].'" на свой сервер.</p>';
$LF_install['nodbconnect'] = 'Ошибка при подключении к базе данных.';
$LF_install['complete']=<<<LOL
<p>Система "ПроЕктор" успешно установлена<br />Для того, чтобы начать работать с системой, сделайте следующее:<ul>
<li>В файле "config.php" отредактируйте переменную безопасносного хранения паролей \$SEC['seed']. Присвойте её любое значение. Категорически не рекомендуется менять её значение после того, как в системе будет зарегистрирован хотя бы один пользователь.</li>
<li>Зарегистрируйтесь на странице <a href='registration.php'>Регистрация</a>. Первый зарегистрированный пользователь автоматически получает права администратора.</li>
<li>В файле "config.php" вы можете настроить под себя и другие параметры - "скрытую" регистрацию, параметры активизации пользователей и прочее.</li>
<li>Удалите файл "install.php" прямо сейчас.</li>
</ul></p>
LOL;
$LF_install['enterconnectpars'] = 'Введите параметры подключения к базе данных';
$LF_install['dbname'] = 'Имя базы данных';
$LF_install['dbifexist'] = 'если базы с таким именем не существует, то система создаст новую базу';
$LF_install['hostname'] = 'Имя хоста (чаще всего это <b>localhost</b>)';
$LF_install['tabprefix'] = 'Префикс для таблиц базы данных';
$LF_install['tabprefixdesc'] = 'заполните это поле, если используете одну базу данных для нескольких скриптов';
$LF_install['dbuser'] = 'Имя пользователя базы данных';
$LF_install['dbpassword'] = 'Пароль пользователя базы данных';
$LF_install['retstep1'] = 'Некоторые данные не были введены. Помните, что все поля обязательны для заполнения.<br /><a href="?step=1">Вернуться к шагу 1</a>';
?>