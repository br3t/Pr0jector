<?php
//---------------
$product['name'] = 'ProJector';
//-----------------
// Links
$LF_links['news'] = "News";
$LF_links['events'] = "Events";
$LF_links['all_events'] = "All Events";
$LF_links['projects'] = "Projects";
$LF_links['tasks'] = "Tasks";
$LF_links['mtasks'] = "My tasks";
$LF_links['users'] = "Members";
$LF_links['profile'] = "profile";
$LF_links['mprofile'] = "My Profile";
$LF_links['about'] = "About 'ProJector'";
$LF_links['messages'] = "Messages";
$LF_links['admin'] = "Admin";
$LF_links['logout'] = "Logout";
$LF_links['groups'] = 'Groups';
$LF_links['editprofile'] = "Edit profile";
$LF_links['inpms'] = "Incoming";
$LF_links['outpms'] = "Outgoing message";
$LF_links['newpms'] = "Unread messages";
$LF_links['newpm'] = "New Message";
$LF_links['last_events'] = "Upcoming Events";
$LF_links['files'] = 'Files';
$LF_links['myfiles'] = 'My files';
//----------------
$formz['login'] = "Login";
$formz['pass'] = "password";
$formz['sysin'] = "Logon";
$LF_formz['submit'] = "Send";
$LF_formz['letsreg'] = "Register";
$formz['registr'] = "Registration";
$formz['reqfields'] = "Fields marked <sup class='reqf'> * </sup> are required";
$formz['spass'] = "Repeat password";
$formz['nick'] = "Nickname";
$formz['name'] = "Name";
$formz['sname'] = "First name";
$formz['surname'] = "Last name";
$formz['icq'] = "ICQ number";
$formz['email'] = "Email";
$LF_formz['jabber'] = "Jabber ID";
$formz['hphone'] = "Phone";
$formz['mphone'] = "Mobile phone";
$formz['country'] = "Country";
$formz['city'] = "City";
$formz['adress'] = "Address";
$formz['usalready'] = "This username already exists!";
$formz['notallf'] = "not filled in all required fields";
$formz['logshort'] = "<em>". $formz['login']."</em> is too short! Use at least 3 characters.";
$formz['passshort'] = "<em>". $formz['pass']."</em> is too short! Use at least 6 characters.";
$formz['passdiff'] = "Fields <em>". $formz['pass']."</em> and <em> ". $formz['spass']."</em> do not match!";
$formz['regready'] = "Registration completed successfully.";
$formz['emact'] = "In the near future to your email address will be sent a link to activate the account's.";
$formz['admact'] = "Your account requires activation by the administrator. To your email address will be sent a message to activate your account as soon as it happens.";
$formz['icqdig'] = "<em>". $formz['icq']."</em> must contain from 5 to 9 digits!";
$formz['activation_letter_subject'] = "Register at project's management system ". $COMPANY_NAME;
$formz['activation_letter_body'] = "You are receiving this email because someone filled out a registration form on your behalf. If it were not for you, then ignore this message, or click the link to activate your account:";
$formz['activation_letter_delim'] = "Your login information:";
$LF_formz['wbregagrds'] = "Yours, a site administrator". $COMPANY_NAME;
$LF_formz['plushour'] = "Specify the offset in hours relative to the time server <br /> (the current server time -";
$LF_formz['savechanges'] = "Save changes";
$LF_formz['h'] = "h";
$LF_formz['birthdate'] = 'Birthday';
$LF_formz['status'] = "User Status";
$LF_formz['ediem'] = "Edit e-mail, password";
$LF_formz['ediem_adm'] = "Edit e-mail, password, status";
$LF_formz['npass'] = "New password";
$LF_formz['npass2'] = "Repeat new password";
$LF_formz['oldpassreq'] = "Warning: To change your password or e-mail you must enter your current password.";
$LF_formz['chavatar'] = "Edit userpic";
$LF_formz['chplogo'] = "Change the logo";
$LF_formz['addtask'] = "Add Task";
$LF_formz['edittask'] = "Edit this task";
//--------------
$LF_mess[0] = "<span class='rse'>No user with the activation code.</span>";
$LF_mess[1] = "<span class='rwe'>You've been activated by email.</span>";
$LF_mess[2] = "<span class='rse'>Unable to connect to database!</span>";
$LF_mess[3] = "<span class='rse'>Invalid login or password!</span>";
$LF_mess[4] = "<span class='rse'>your account blocked!</span>";
$LF_mess[5] = "<span class='rse'>Check the entered data</span>";
$LF_mess[6] = "<span class='rse'>No such user!</span>";
$LF_mess[7] = "<span class='rse'>Insufficient rights!</span>";
$LF_mess[8] = "<span class='rse'>Error editing!</span>";
$LF_mess[9] = "<span class='rwe'>Changes saved</span>";
$LF_mess[10] = "<span class='rse'>Wrong password!</span>";
$LF_mess[11] = "<span class='rse'>Divergence fields '".$LF_formz['npass']."' and '".$LF_formz['npass2']."'</span>";
$LF_mess[12] = "<span class='rse'>Invalid file settings</span>";
$LF_mess[13] = "<span class='rse'>Nothing found</span>";
$LF_mess[14] = "<span class='rse'>Invalid file extension</span>";
$LF_mess[15] = "<span class='rse'>Invalid file size</span>";
$LF_mess[16] = "<span class='rwe'>File removed</span>";
$LF_mess[17] = "<span class='rwe'>File added</span>";
//--------------
$other['for'] = "for";
$other['powered_by'] = "product by";
$other['logo'] = "Logo";
$LF_other['ret_index'] = "<a href='index.php'>Return to the home page</a>";
$LF_other['youlldontseethis'] = 'You should not see this message';
$other['error404'] = <<<ERRR
<h2 class='rse'>Error 404</h2>
<p>Page not found.<br />Perhaps it existed previously and was removed, and perhaps that was not it at all.<br /> Hearing can return to ProJector's <a href="/{$SEC['proektor_path']}index.php">Home</a></p>
ERRR;
$other['error403'] = <<<ERRR
<h2 class='rse'>Error 403</h2>
<p>Access denied.<br />Hearing can return to ProJector's <a href="/{$SEC['proektor_path']}index.php">Home</a></p>
ERRR;
$LF_other['aloha'] = 'Hello';
$LF_other['now'] = 'Now';
$LF_other['back'] = 'Back';
$LF_other['my'] = 'My';
$LF_other['no'] = 'No';
$LF_other['timeline'] = 'Time';
$LF_other['sendamess'] = "Send message";
$LF_other['editprofile'] = "Edit profile";
$LF_other['addpro'] = "Add project";
$LF_other['uonline'] = "Online";
$LF_other['uptime'] = "Do not active for the";
$LF_other['registered'] = "Registered";
$LF_other['lastactivity'] = "Last activity";
$LF_other['delete'] = "Delete";
$LF_other['project'] = "Project";
$LF_other['projectname'] = "Project name";
$LF_other['projectlogo'] = "Project logo";
$LF_other['projectnew'] = "New project";
$LF_other['lider'] = "Manager";
$LF_other['state'] = "Status";
$LF_other['by'] = "by";
$LF_other['quitnosave'] = 'Quit without saving';
$LF_other['project_start'] = "Project start";
$LF_other['project_end'] = "Project end";
$LF_other['project_desc'] = "Project description";
$LF_other['delproject'] = "Delete project";
$LF_other['project_files'] = "Project files";
$LF_other['project_tasks'] = "Objectives";
$LF_other['discussion'] = "Discussion";
$LF_other['comments'] = "Comments";
$LF_other['comment'] = array("comment", "comment", "comment");
$LF_other['noposts'] = "No message";
$LF_other['to_discuss'] = "Add comment";
$LF_other['editpost'] = "Edit post";
$LF_other['added'] = "Added";
$LF_other['author'] = "Author";
$LF_other['addnews'] = "Add news";
$LF_other['addevent'] = "Add Event";
$LF_other['edititems3'] = "Edit news";
$LF_other['edititems4'] = "Edit event";
$LF_other['editpro'] = "Edit project";
$LF_other['editfile'] = "Edit file";
$LF_other['rlydelpost'] = "Are you sure you want to delete this message?";
$LF_other['rlydelitems3'] = "Are you sure you want to delete the article?";
$LF_other['rlydelitems4'] = "Are you sure you want to delete the event?";
$LF_other['rlydelpro'] = "Are you sure you want to delete the project?";
$LF_other['rlydelfile'] = "Are you sure you want to delete the file?";
$LF_other['rlydelgroup'] = 'Are you sure you want to delete a group?\nGroup deleting is possible provided that there is not any user.';
$LF_other['no_items3'] = "There is no news";
$LF_other['no_items4'] = "There are no events";
$LF_other['allitems3'] = "All News";
$LF_other['allitems4'] = "All Events";
$LF_other['itemtitle3'] = "title of the news";
$LF_other['itemtitle4'] = "Event Title";
$LF_other['event_end'] = "Event end";
$LF_other['event_for'] = "Before the events left";
$LF_other['evented'] = "will take place";
$LF_other['eventdate'] = "Event date";
$LF_other['mstbenomuch'] = "file must be no larger than";
$LF_other['maker'] = 'Executor';
$LF_other['taskname'] = 'Task name';
$LF_other['subtaskname'] = 'Name of subtasks';
$LF_other['taskdesc'] = 'Task description';
$LF_other['task_start'] = 'Task start';
$LF_other['task_end'] = 'Task end';
$LF_other['userstask'] = 'User`s tasks';
$LF_other['edittask'] = 'Edit task';
$LF_other['deltask'] = 'Delete Task';
$LF_other['editsubtask'] = 'Edit subtask';
$LF_other['delsubtask'] = 'Delete subtask';
$LF_other['rlydeltask'] = 'Are you sure you want to delete a task?';
$LF_other['newtask'] = 'New task';
$LF_other['addmtask'] = 'Add subtask';
$LF_other['subtasks'] = 'subtasks';
$LF_other['sub_tasks'] = 'Short subtasks';
$LF_other['nosubtasks'] = 'No sub';
$LF_other['editsubtask'] = 'Edit subtask';
$LF_other['delsubtask'] = 'Delete subtask';
$LF_other['mothertask'] = 'main objective';
$LF_other['mynewsubtask'] = 'My new sub-task';
$LF_other['rlydelsubtask'] = 'Are you sure you want to delete the subtask?';
$LF_other['subtaskeditform'] = array('name subproblems', 'Time', 'h', 'Done', 'New subtask', 'Edit subtask');
$LF_other['newtaskdesc'] = 'Work, work and work again ...';
$LF_other['taskready'] = 'Task`s progress';
$LF_other['taskgroup'] = 'Task`s group';
$LF_other['nofiles'] = 'No files found';
$LF_other['task_files'] = 'Task`s files';
$LF_other['filename'] = 'Filename';
$LF_other['download'] = 'Download';
$LF_other['filedesc'] = 'File description';
$LF_other['usera'] = 'user';
$LF_other['addfile'] = 'Add file';
$LF_other['editfile'] = 'Edit file';
$LF_other['file'] = 'File';
$LF_other['filesize'] = 'Size';
$LF_other['allowext'] = 'Allowed extensions';
$LF_other['newfiledesc'] = 'I was so lazy to come up with a description for this file, you now will have to guess about the nature of its contents';
$LF_other['b'] = 'bytes';
$LF_other['kb'] = 'Kb';
$LF_other['Mb'] = 'Mb';
$LF_other['nogroup'] = 'No such group';
$LF_other['nogroups'] = 'Could not find any groups';
$LF_other['groupname'] = 'Group name';
$LF_other['groupdesc'] = 'Group description';
$LF_other['addgroup'] = 'Add group';
$LF_other['editgroup'] = 'Edit group';
$LF_other['delgroup'] = 'Delete group';
$LF_other['usergroups'] = 'Groups';
$LF_other['groupcons'] = 'Group members';
$LF_other['editgroupcons'] = 'Editing of the group';
$LF_other['selectuser'] = 'Select user';
$LF_other['adduser2group'] = 'Add user to group';
$LF_other['deluser2group'] = 'Remove user from group';
$LF_other['groupicon'] = 'Group icon';
$LF_other['withoutgroup'] = 'no group';
$LF_other['postlink'] = 'Link to the post';
$LF_other['promptchangetaskprogress'] = 'Set the new value per task, 0-100';
$LF_other['latestposts'] = 'Recent comments';
$LF_other['save'] = 'Save';
$LF_other['cansel'] = 'Cancel';
//----------------
$LF_PRO_STT[0] = 'Paused';
$LF_PRO_STT[1] = 'Active';
$LF_PRO_STT[2] = 'Completed';
//----------------
$LF_TASK_STT[0] = 'Paused';
$LF_TASK_STT[1] = 'Running';
$LF_TASK_STT[2] = 'Completed';
//----------------
$LF_messz['from'] = 'From';
$LF_messz['for'] = 'To';
$LF_messz['subj'] = 'Subject';
$LF_messz['body'] = 'Message';
$LF_messz['reset'] = 'Reset';
$LF_messz['send'] = 'Send';
$LF_messz['sended'] = 'Sent';
$LF_messz['nomssgz'] = 'No message';
$LF_messz['answer'] = 'Reply';
$LF_messz['nosubj'] = 'No Subject';
$LF_messz['unred'] = 'unread';
$LF_messz['red'] = 'read';
$LF_messz['mail_newpm'] = array ('new private message in the "ProJector"', 'User', 'has sent you a private message in the "ProJector"-system on the site '. $COMPANY_NAME);
$LF_messz['notice'] = array ();
$LF_messz['notice']['taskfail'] = array('expired task', 'I bring to your notice that the user', 'as of the date the task was not completed');
$LF_messz['notice']['tasknotice'] = array ('reminders', 'Remember, that the user', 'at the moment there are less than', 'for the task');
$LF_messz['notice']['tasknew'] = array ('Added a new task', 'I hasten to inform you that the user', 'added a new task');
$LF_messz['notice']['bestregards'] = 'Regards, SiteRobot';
//----------------
// $LF_time ['years'] = "years";
$LF_time['year'] = array("year", "year", "years");
// $LF_time ['months'] = "months";
$LF_time['month'] = array("month", "month", "months");
// $LF_time ['days'] = "Days";
$LF_time['day'] = array ("day", "day", "days");
// $LF_time ['hours'] = "hours";
$LF_time['hour'] = array ("hour", "hour", "hours");
// $LF_time ['minutes'] = "Minutes";
$LF_time['minute'] = array ("minute", "minute", "minutes");
$LF_time['mon_long'] = array ("january", "february", "march", "april", "may", "june", "july", "august", 'September', 'october', 'november', 'december');
$LF_time['day_short'] = array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa");
$LF_time['mon_short'] = array('Jan', 'Feb', 'Mar', 'Apr', "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
//----------------
$LF_STATS[0] = "Blocked by Email and by Administrator";
$LF_STATS[1] = "Blocked by Email";
$LF_STATS[2] = "Blocked by Administrator";
$LF_STATS[3] = "User";
$LF_STATS[4] = "Moderator";
$LF_STATS[5] = "Administrator";
//----------------
$LF_install['head'] = 'Installing "'. $product['name'].'"';
$LF_install['motto'] = '<p>Welcome!</p><p>On this page, you can install "'. $product['name'].'" on your server.</p>';
$LF_install ['nodbconnect'] = 'Error connecting to database.';
$LF_install ['complete']=<<< LOL
<p>System "ProJector" has been successfully installed.<br />To begin working with the system, do the following: <ul>
<li> The file "config.php" edit variable sequirity store passwords \$SEC['seed']. Assign it any value. Is not recommended to change its value after the system will be registered at least one user.</li>
<li>Register by page <a href='registration.php'>Register</a>. The first registered user automatically gets Administrator privileges.</li>
<li>The file "config.php" you can customize and other parameters - the "hidden" registration, the parameters of activation of users and so on.</li>
<li>Delete the file "install.php" right now. </li>
</ul></p>
LOL;
$LF_install['enterconnectpars'] = 'Enter the settings for connecting to the database';
$LF_install['dbname'] = 'Database name';
$LF_install['dbifexist'] = 'if the database with that name does not exist, the system creates a new database';
$LF_install['hostname'] = 'The host name (usually <b>localhost</b>)';
$LF_install['tabprefix'] = 'Prefix for tables in the database';
$LF_install['tabprefixdesc'] = 'fill this field if you use one database for several scripts';
$LF_install['dbuser'] = 'Database username';
$LF_install['dbpassword'] = 'The user password database';
$LF_install['retstep1'] = 'Some data has not been introduced. Remember that all fields are required.<br /><a href="?step=1">Return to Step 1</a>';
?>