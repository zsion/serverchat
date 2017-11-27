
function $(id) {
    return typeof id == "string" ? document.getElementById(id) : id;
}

function isUndefined(variable) {
	return typeof variable == 'undefined' ? true : false;
}

function in_array(needle, haystack) {
	if(typeof needle == 'string' || typeof needle == 'number') {
		for(var i in haystack) {
			if(haystack[i] == needle) {
					return true;
			}
		}
	}
	return false;
}

function GetObjByE(e) {
  if (isUndefined(e)) e = window.event;
  var Obj = document.all ? e.srcElement : e.target;

  return Obj;
}

function select_deselectAll (formname, elm, group) {
	var frm = document.forms[formname];
	
    for (i=0; i<frm.length; i++) {
        if (elm.attributes['checkall'] != null && elm.attributes['checkall'].value == group) {
            if (frm.elements[i].attributes['checkme'] != null && frm.elements[i].attributes['checkme'].value == group)
              frm.elements[i].checked = elm.checked;
        } else if (frm.elements[i].attributes['checkme'] != null && frm.elements[i].attributes['checkme'].value == group) {
            if(frm.elements[i].checked == false) {
                frm.elements[1].checked = false;
            }
        }
    }
}

function setCookie (name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*3600*1000));
		var expires = "; expires="+date.toGMTString();
	}else{
		var expires = "";
	}

	document.cookie = name+"="+value+expires+"; path=/";
}

function removeCookie(name) {
  document.cookie = name + "=; expires=Fri, 31 Dec 1988 23:59:59 GMT;";
}

/////////ajax函数
function ajax(url, callback, updating, loading, format, method) {
	if(!method) method = "POST";
	
	url += (url.indexOf("?")+1) ? "&" : "?";
	url += ajaxpending;

	jx.bind({
		"url":url,
		"onSuccess":callback,
		"onError":function(status){
			alert('ajax error!');
		},
		"format":format,
		"method":method,
		"update":updating,
		"loading":loading
	});

	return false;
}

//获取IP地址归属地
function iplocation(gid, ip) {
	var obj = $('ip_'+gid);
	if(obj) {
		if(obj.innerHTML != '') {
			if(obj.style.display == 'block'){
				obj.style.display = 'none';
			}else{
				obj.style.display = 'block';
			}
		}else{
			ajax("support.php?act=iplocation&ip="+ip, function(data){showlocation(gid, data);});
		}
	}
}

//显示IP地址归属地
function showlocation(gid, data) {
	var obj = $('ip_'+gid);
	if(obj) {
		obj.style.display = 'block';
		obj.innerHTML = data;
	}
}

//添加事件函数
function _attachEvent(obj, evt, func, eventobj) {
	eventobj = !eventobj ? obj : eventobj;
	if(obj.addEventListener) {
		obj.addEventListener(evt, func, false);
	} else if(eventobj.attachEvent) {
		obj.attachEvent('on' + evt, func);
	}
}
