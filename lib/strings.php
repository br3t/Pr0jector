<?php
//-----------------------
/** 
* ������� ��������� ���������������� � ������������ ����� � ����������� �� �������������
* @param $number int ����� ����-����
* @param $titles array �������� ��������� ��� ���������� 1, 2 � 5
* @return string ������ ������ � � ������ ����������
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
* ������������� ��������� ������� � �����, ������� ������� "�����"
* @param $w0rd ����� ��� ��������������
* @param $len ������������ ����� �����
* @return string �����, �������� �� �����
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
* �������������� bb-����� ������ � html-����
* @param $tex ����� ��� ��������������
* @return string html-����� ��� ��-�����
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
* �������� ���� bb-����� �� ������
* @param $tex ����� ��� ��������������
* @return string ����� ��� bb-�����
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