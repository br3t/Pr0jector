<?php
//-----------------------
/** 
* Перевод окончания существительного в человеческую форму в зависимости от числительного
* @param $number int число чего-либо
* @param $titles array варинаты написания для количества 1, 2 и 5
* @return string строка цифрой и с нужным окончанием
*/
function human_plural_form($number, $titles, $isNO){
$cases = array (2, 0, 1, 1, 1, 2);
if($isNO!=''&&$number==0)
 return $isNO." ".$titles[2];
else 
 return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}
//-----------------------
/** 
* Принудительно добавляет пробелы в слово, которое длиннее "нормы"
* @param $w0rd слово для преобразования
* @param $len максимальная длина слова
* @return string слово, разбитое на части
*/
function short_words($w0rd)
{
 global $MAX_W0RD_LENGTH;
 $len = $MAX_W0RD_LENGTH;
 $rezstr = "";
 while(strlen($w0rd)>$len)
 {
  $rezstr .= mb_substr($w0rd, 0, $len, 'utf-8')." ";
  $w0rd = mb_substr($w0rd, $len, strlen($w0rd), 'utf-8');
 }
 $rezstr .= $w0rd;
 return $rezstr;
}
//-----------------------
/** 
* Преобразование bb-кодов текста в html-тэги
* @param $tex текст для преобразования
* @return string html-текст без бб-кодов
*/
function parse_post($tex)
{
 global $TAGS, $SMILES, $PARSE_URL;
 foreach($TAGS as $i)
 {
  $pat = '/(\['.$i.'\])(.*?)(\[\/'.$i.'\])/';
  $pat2 = '<'.$i.'>\\2</'.$i.'>';
  $tex = preg_replace($pat, $pat2, $tex);
 }
 if($PARSE_URL)
 {
  $tex = preg_replace('/(\[a=http:\/\/)(.*?)(\])(.*?)(\[\/a\])/', '<a href="http://\\2" target="_blank"><img src="images/icons/http.gif" title="http" />\\4</a>', $tex);
  $tex = preg_replace('/(\[a=ftp:\/\/)(.*?)(\])(.*?)(\[\/a\])/', '<a href="ftp://\\2" target="_blank"><img src="images/icons/ftp.gif" title="ftp" />\\4</a>', $tex);
  $tex = preg_replace('/(\[img\]http\:\/\/)(.*?)(\.)(jpg|jpeg|gif|png)(\[\/img\])/', '<img src="http://\\2.\\4" />', $tex);
 }
 $tex = nl2br($tex);
 $tex = preg_replace('/(\[hint=)(.*?)(\])(.*?)(\[\/hint\])/', '<span><span class="notlink">{ \\2 }</span><div class="hint">\\4</div></span>', $tex);
 $tex = preg_replace_callback('/(\[user=)([0-9]{1,4})(\])/', create_function('$par', 'return gen_user_link(array_map("infiltrtext", user_exist($par[2])));'), $tex);
 $tex = str_replace("[br]", "<br />",  $tex);
 for($i=0; $i<count($SMILES); $i++)
  $tex = str_replace($SMILES[$i], " <img src='images/smiles/s".$i.".gif' />",  $tex);
 return $tex;
}
//-----------------------
/** 
* Удаление всех bb-кодов из текста
* @param $tex текст для преобразования
* @return string текст без bb-кодов
*/
function unparse_post($tex)
{
 global $TAGS, $SMILES, $PARSE_URL;
 foreach($TAGS as $i)
 {
  $pat = '/(\['.$i.'\])(.*?)(\[\/'.$i.'\])/';
  $pat2 = '\\2';
  $tex = preg_replace($pat, $pat2, $tex);
 }
 if($PARSE_URL)
 {
  $tex = preg_replace('/(\[a=http:\/\/)(.*?)(\])(.*?)(\[\/a\])/', '\\4', $tex);
  $tex = preg_replace('/(\[a=ftp:\/\/)(.*?)(\])(.*?)(\[\/a\])/', '\\4', $tex);
  $tex = preg_replace('/(\[img\]http\:\/\/)(.*?)(\.)(jpg|jpeg|gif|png)(\[\/img\])/', '[img]', $tex);
 }
 $tex = nl2br($tex);
 $tex = preg_replace('/(\[hint=)(.*?)(\])(.*?)(\[\/hint\])/', '{ \\2 }', $tex);
 $tex = preg_replace_callback('/(\[user=)([0-9]{1,4})(\])/', create_function('$par', ' $u=array_map("infiltrtext", user_exist($par[2])); return $u["nick"];'), $tex);
 $tex = str_replace("[br]", "<br />",  $tex);
 return $tex;
}
?>