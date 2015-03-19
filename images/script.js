//--------------------------------
//
function AddTag(tag_start,tag_end, el_id)
{
	if (document.getElementById(el_id).createTextRange && document.getElementById(el_id).caretPos)
	{
		var sel = document.selection.createRange();
		sel.text = tag_start + sel.text + tag_end;
	}
	else
		if(document.getElementById(el_id).selectionStart != undefined)
		{
			var sel_before, sel_after, sel, ta;
			ta = document.getElementById(el_id);
			sel_before = ta.value.substr(0, ta.selectionStart);
			sel = ta.value.substr(ta.selectionStart, ta.selectionEnd - ta.selectionStart);
			sel_after = ta.value.substr(ta.selectionEnd);
			ta.value = sel_before + tag_start + sel + tag_end + sel_after;
			ta.setSelectionRange(sel_before.length, sel_before.length + tag_start.length + sel.length + tag_end.length);
		}
		else 
			document.getElementById(el_id).value += tag_start + tag_end;
}
//--------------------
function storeCaret(text)
{ 
	if (text.createTextRange)
		text.caretPos = document.selection.createRange().duplicate();
}
function TAOnSelect(_eEvent,_sName)
{
	if ((_eEvent.type=="select")&&(_eEvent.srcElement.name==_sName))
	{
		g_oSelectionRange=document.selection.createRange();
		if (g_oSelectionRange!=null)
			if (g_oSelectionRange.text=="")	
				g_oSelectionRange=null;
		} 
		else 
			g_oSelectionRange=null;
}
//----------------------------
function AddText(text, el_id)
{
	var short_ = document.getElementById(el_id);
	if (short_ .createTextRange && short_ .caretPos)
	{
		var caretPos = short_ .caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
	}
	else
		if(short_ .selectionStart != undefined)
			{
				var sel_before = short_.value.substr(0, short_.selectionStart);
				var sel_after = short_.value.substr( short_.selectionEnd);
				short_.value = sel_before + text + sel_after;
				short_.setSelectionRange(sel_before.length + text.length, sel_before.length + text.length);
			}
		else
			document.getElementById(el_id) += text;  
}
//--------------------------------
function parseObj(obj)
{
	var objStr = '';
	for (prop in obj)
		objStr += 'Property: ' + prop + '; Type: ' + typeof(obj[prop]) + '; Value: '  + obj[prop] + '\n';
	alert("-----=ObjStr=-----\n"+objStr);
}
//--------------------------------
//   функция для целых чисел в полях ввода
function int_it(num, notnull)
{
	if(num=='-'||num=='0-')
	{
		return '-';
	} 
	else
	{ 
		return (parseInt(num)?parseInt(num):notnull);
	} 
}
//--------------------------------
//   функция для целых чисел в полях ввода
function cut_it(par, base)
{
		if(par!="")
		{
			par = parseInt(par);
			if(isNaN(par))
				par = 0;
			else
				par = parseInt(par%base);
		} 
		return par;
}
//--------------------------------
//  добавление пользователя ы группу
function user2group(uid, gid)
{
	$('#usingr').html("<img src='images/nprogresswait.gif' />");
	$('#usoutgr').html("");
	$.post("actions.php", { user2add: uid, groupid: gid},
		function(data){
			$('#usingr').html(data.ia);
			$('#usoutgr').html(data.ol);
		},'json');
}
//--------------------------------
//  обновление пользователя в редактировании задачи
function showgroupusers(gid)
{
	var suser = $('#tmaker').attr('value');
	$('#uslist').html("<img src='images/nprogresswait.gif' />");
	$.post("actions.php", { group4users: gid, seluser: suser },
		function(data){
			$('#uslist').html(data.rez);
		},'json');
}
//--------------------------------
//  изменения процента выполнения задачи исполнителем

function changetaskprogress(tex, taskid)
{
	var newproc = prompt(tex);
	if(newproc)
	{
		$.post("actions.php", { changetaskprogress: newproc, tid: taskid},
			function(data){
				if(data.rez)
				{
					workprogress = data.rez;
					paintprogressbars();
					$("#ctpl").html(data.rez+"%");
				}
			},'json');
		}
}
//--------------------------------
//
function paintprogressbars()
{
	var canvas = document.getElementById('timebar');
	if(canvas.getContext)
	{
		ctx = canvas.getContext('2d');
		ctx.fillStyle = 'rgb(100,100,100)';
		ctx.fillRect(0,0,400,18);
		ctx.fillStyle = 'rgb(100,100,100)';
		ctx.fillRect(0,20,400,40);
		var rcolor = (timeprogress<50)?(parseInt(255*timeprogress/50)):(255);
		var gcolor = (timeprogress>50)?(parseInt(255*(100-timeprogress)/50)):(255);
		var lwidth = parseInt(400*timeprogress/100);
		ctx.fillStyle = 'rgb('+(rcolor)+','+(gcolor)+',0)';
		ctx.fillRect(0,0,lwidth,18);
		ctx.fillStyle = 'rgb(0,255,0)';
		ctx.fillRect(0,20,(workprogress*400)/100,40);
		//ctx.fillStyle = 'rgb(0,0,0)';
		//ctx.font = "14pt Verdana";
		//ctx.fillText(timeprogress+"%", 220, 16);
	} 
}
//--------------------------------
function synclink(event)
{
	$('#addlink').attr('href', 'javascript: user2group('+$("#addusertogroup").attr('value')+','+$('#addlink').attr('tag')+');');
}
//--------------------------------
function show_edit_subtask_form(tid, sid, words)
{
	$("#stform").html("<tr><td><img src='images/nprogresswait.gif' /></td></tr>");
	if(sid>0)
	{
		$.post("actions.php", { showformsubtask: sid},
		function(data){
			if(data[0].sid)
			{
				rez = "<fieldset><legend class='b'>"+words[7]+"</legend><form>"+words[0]+": <input type='text' name='sname' value='"+data[0].sname+"' /> <span class='list_even'>"+words[1]+": <input type='text' name='stime' value='"+(data[0].stime/60)+"' /> "+words[2]+"</span><br />"+words[3]+": <input type='checkbox' name='sready'"+
				((data[0].sready==1)?" checked='checked'":"")+" /> <span class='list_even'><input type='button' value='"+words[4]+"' onclick='send_subtask_form(this.form);' /> <input type='button' value='"+words[5]+"' onclick='hide_edit_subtask_form();' /></span><input type='hidden' name='stask' value='"+Math.floor(tid)+"' /><input type='hidden' name='sid' value='"+Math.floor(data[0].sid)+"' /></form></fieldset>";
				$('#stform').html(rez);
			}
		},'json');
	}
	else
	{
		rez = "<fieldset><legend class='b'>"+words[6]+"</legend><form>"+words[0]+": <input type='text' name='sname' value='' /> <span class='list_even'>"+words[1]+": <input type='text' name='stime' /> "+words[2]+"</span><br />"+words[3]+": <input type='checkbox' name='sready' /> <span class='list_even'><input type='button' value='"+words[4]+"' onclick='send_subtask_form(this.form);' /> <input type='button' value='"+words[5]+"' onclick='hide_edit_subtask_form();' /></span><input type='hidden' name='stask' value='"+Math.floor(tid)+"' /><input type='hidden' name='sid' value='-1' /></form></fieldset>";
		$('#stform').html(rez);
	}
}
//--------------------------------
function hide_edit_subtask_form()
{
	$('#stform').html("");
}
//--------------------------------
function send_subtask_form(form)
{
	var _sname = form.sname.value;
	var _stime = form.stime.value;
	var _sready = form.sready.checked;
	var _sid = form.sid.value;
	var _stask = form.stask.value;
	$("#subtasklist").html("<tr><td><img src='images/nprogresswait.gif' /></td></tr>");
	$.post("actions.php", { editsubtask: _sid, sname: _sname, sname2: _sname[0], stime: _stime, sready: _sready, stask: _stask},
		function(data){
			if(data.rez)
			{
				$("#subtasklist").html(data.rez);
				hide_edit_subtask_form();
			}
		},'json');
}
//--------------------------------
function delete_subtask(sid, tid)
{
	$("#subtasklist").html("<tr><td><img src='images/nprogresswait.gif' /></td></tr>");
	$.post("actions.php", { deletesubtask: sid, stask: tid},
		function(data){
			if(data.rez)
			{
				$("#subtasklist").html(data.rez);
				hide_edit_subtask_form();
			}
		},'json');
}
//--------------------------------
var errstr = "";
var timeprogress = -1;
//--------------------------------
$(document).ready(function ()
{
	//
	$("table.form").each(function () {
        $("tr:nth-child(even)", this).addClass('list_odd');
        $("tr:nth-child(odd)", this).addClass('list_even');
	});
	//
	$("select#tgroup").change(function(){ showgroupusers(this.value); });
	//
	if(errstr!="")
	{
		$("#n1").removeClass('notice_hide').addClass('notice_show');
		$(".notice_in2").html("<p>&nbsp;</p><p>"+errstr+"</p><p>&nbsp;</p>");
		setTimeout(function() { $("#n1").removeClass('notice_show').addClass('notice_hide'); }, 5000);
	}
	//----------------------------
	// open/close card by cookie
	if(!$.cookie('openCard'))
	{
		$.cookie('openCard', 0, { expires: 100 });
	}
	if($.cookie('openCard')==0)
	{
		$('.openCard span').html('{'+$('.openCard span').attr('data-cardopen')+'}');
	}
	else
	{
		$('.openCard span').html('{'+$('.openCard span').attr('data-cardclose')+'}');
		$('.hint').show('fast');
	}
	$('.openCard span').click(function()
	{
		if($.cookie('openCard')==0)
        {
            $.cookie('openCard', 1, { expires: 100 });
            $('.openCard span').html('{'+$('.openCard span').attr('data-cardclose')+'}');
            $('.hint').show('fast');
        }
        else
        {
            $.cookie('openCard', 0, { expires: 100 });
            $('.openCard span').html('{'+$('.openCard span').attr('data-cardopen')+'}');
            $('.hint').hide('fast');
        }
	});
	//----------------------------
	//  отрисовка  timeprogress на canvas
	if(timeprogress>=0)
        paintprogressbars();
	//---------------------------
	$(".digit").keyup(function(event){this.value=int_it(this.value, 0);});
	$(".digit").blur(function(event){this.value=int_it(this.value, 0);});
	$(".digitnill").keyup(function(event){this.value=int_it(this.value, false);}).blur(function(event){this.value=int_it(this.value, false);});
	//
	$(".hour").keyup(function(event){this.value=cut_it(this.value, 24);}).blur(function(event){this.value=cut_it(this.value, 24);});
	//
	$(".minutes").keyup(function(event){this.value=cut_it(this.value, 60);}).blur(function(event){this.value=cut_it(this.value, 60);});
	//
	$(".day").keyup(function(event){this.value=cut_it(this.value, 32);}).blur(function(event){this.value=cut_it(this.value, 32);});
	//
	$("a.bI").click(function(){
		$(this).siblings("div.hint").slideToggle("fast");
	});
	//
	$(".notlink").click(function(){
		$(this).siblings("div.hint").toggle("fast");
	});
});

