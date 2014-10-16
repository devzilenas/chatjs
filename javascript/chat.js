/**
 * This is a Chat.
 *
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @version 0.0.7
 */

function gbid(name) {
	return document.getElementById(name);
}

function hide(id) {
	f = gbid(id);
	if(f !== null) f.style.display = "none";
}

function show(id) {
	f = gbid(id);
	if(f !== null) f.style.display = "block";
}
function isHidden(id) {
	f = gbid(id);
	return f !== null && 'none' === f.style.display;
}

/**
 * Library for different simple functions.
 */
function Lib() {}
/**
 * Checks whether variable exists in array. Checks with === .
 *
 * @param {Object} v
 *
 * @param {Array} arr
 *
 * @returns {Boolean}
 */
Lib.contains = function(v, arr) {
	var i   = 0;
	var has = false; 
	for(i = 0; !has || i < arr.length; i++) 
		if(arr[i] === v) has = true;
	return has;
}
/**
 * Uppercases first character of the string.
 *
 * @param {String} $str String to uppercase.
 *
 * @returns {String}
 */
Lib.ucFirst = function(str) {
	return str.charAt(0).toUpperCase() + str.substr(1);
}

/**
 * Returns object class name.
 *
 * @param {Object} o
 *
 * @returns {String}
 */
Lib.uok = function(o) {
	return Lib.ucFirst(Object.keys(o)[0]);
}

/**
 * Generates random integer between 0 and 2^(32-1).
 *
 * @returns {Integer}
 */
Lib.rint = function() {
	return Math.floor(Math.random()*Math.pow(2,32-1));
}

/**
 * Adds timeout to remove element with id after ms seconds passed.
 * 
 * @param {String} id
 *
 * @param {Integer} ms
 */
Lib.tRemove = function(id, ms) {
	window.setTimeout(function() {
		var e = gbid(id);
		if('undefined' !== typeof e)
			e.parentNode.removeChild(e);
		},
		ms);
}

/**
 * Adds message to 'msgs' div.
 */
Lib.m = function(s) {
	msgs = gbid('msgs');
	msgs.innerHTML = msgs.innerHTML + s;
}

/**
 * Adds info message to msgs box.
 * 
 * @param {String} msg
 */
Lib.info = function(msg) {
	var r  = Lib.rint();
	var id = 'info_'+r; 
	Lib.m('<span class="info" id="'+id+'">'+msg+'</span>');
	Lib.tRemove(id,WindowManager.max_msg_duration());
}

/**
 * Adds error message to msgs box.
 * 
 * @param {String} msg
 */
Lib.error = function(msg) {
	var r  = Lib.rint();
	var id = 'error_'+r; 
	Lib.m('<span class="error" id="'+id+'">'+msg+'</span>');
	Lib.tRemove(id,WindowManager.max_msg_duration());
}

/**
 * Convert string form json object to object of Classname.
 * 
 * @param {Object} $tjson Json object.
 *
 * @param {String} $classname Class name of the object to create.
 *
 * @returns {Object}
 */
Lib.funcFromJsonO = function(tjson, classname) { 
	var o   = new window[classname](); 
	var ks  = Object.keys(tjson);
	var i   = 0;
	var key = ''; 
	for(i = 0; i<ks.length; i++) {
		key = ks[i];
		if('undefined' === typeof o["set_"+key]) {//if doesn't have setter
			o["m_"+key] = tjson[key];
		} else {
			o["set_"+key](tjson[key]);
		}
	}
	return o;
}

/**
 * Creates HttpObject.
 *
 * @returns {Object}
 */
Lib.makeHttpObject = function() {
	try { 
		return new XMLHttpRequest();
	}
	catch (error) {}

	try {
		return new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(error) {}

	try {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	catch (error) {}

	throw new Error("Could not create HTTP request object.");
}

/**
 * Makes request.
 */
Lib.request = function(ho, url, postdata, success, failure) {
	var async = true;
	if(null !== postdata) {
		ho.open("POST", url, async);
		ho.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ho.send(postdata);
	} else {
		ho.open("GET", url, async);
		ho.send(null);
	}
	ho.onreadystatechange = function() {
		if(ho.readyState == 4) {
			if(success && ho.status == 200) {
				success(ho.responseText);
			} else if(failure) {
				failure(ho.status, ho.statusText);
			}
		}
	}
}

function Jsonp() { } 
/**
 * This validation regexp is from RFC 4627 Chapter 6
 */
Jsonp.secureObjConvert = function(str) {
	return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(str.replace(/"(\\.|[^"\\])*"/g, ''))) && eval('(' + str + ')'); 
}

/**
 * Makes object from json string.
 *
 * @param {String} str Json string response (usualy it is from server).
 *
 * @returns {Array}
 */
Jsonp.asjobjs = function(str) {

	var jobjs   = this.secureObjConvert(str);
	var cl      = Lib.uok(jobjs);
	var tjsonos = jobjs[cl]; 
	var objs    = new Array();
	var tjsono; //temporary
	var i = 0;

	for(i = 0; i<tjsonos.length; i++) {
		tjsono = tjsonos[i];
		objs.push(window[cl]['fromJsonO'](tjsono, cl));
	}
	return objs; 
}

/**
 * Request.
 */
function Req() { }

/**
 * Setter for url.
 *
 * @param {String} $url
 */
Req.prototype.setUrl = function(url) {
	this.m_url = url;
}
Req.prototype.url = function() {
	return this.m_url;
}
Req.prototype.setSuccess = function(success) {
	this.m_success = success;
}
Req.prototype.success = function() {
	return this.m_success;
}
Req.prototype.setFailure = function(failure) {
	this.m_failure = failure;
}
Req.prototype.failure = function() {
	return this.m_failure;
}
Req.prototype.post_data = function() {
	return this.m_post_data;
}
Req.prototype.setPostData = function(pd) {
	this.m_post_data = pd;
}
Req.prototype.isPost = function() {
	return typeof this.m_post_data !== 'undefined';
}

/**
 * Execute request.
 *
 * @private
 *
 * @param {Object} $ho HttpObject to execute request through.
 */
Req.prototype.execute = function(ho) {
	if(this.isPost()) {
		Lib.request(ho, this.url(), this.post_data(), this.success(), this.failure());
	} else {
		Lib.request(ho, this.url(), null, this.success(), this.failure());
	}
}

/**
 * Requests manager. 
 *
 * Puts all requests to the queue. Executes requests one after another.
 */
function XR() {
	this.m_requests = new Array();
	this.m_current  = null; // current request
	this.m_ho       = Lib.makeHttpObject();
}

XR.prototype.eventGotResponse = function(response) {
	if(response.success()) {
		xr.executedSuccessfully();
	} else {
		xr.executedUnsuccessfully();
	}
}
/**
 * Method to request executed successfully.
 */
XR.prototype.executedSuccessfully = function() {
	this.setCurrent(null);
}

/**
 * Method to tell that request executed unsuccessfully.
 */
XR.prototype.executedUnsuccessfully = function() {
	this.setCurrent(null);
}

/**
 * Returns requests.
 *
 * @returns {Array}
 */
XR.prototype.requests = function() {
	return this.m_requests;
}

/**
 * Gets next request from queue.
 *
 * @private
 * 
 * @returns {Object}
 */
XR.prototype.setCurrentNext = function() {
	if(this.requests().length > 0) {
		//Take one request from queue.
		r = this.requests().shift();
		//make it current request.
		this.setCurrent(r);
		return this.current();
	}
}

/**
 * Tells whether is executing current request.
 */
XR.prototype.isExecuting = function() {
	return this.current() !== null;
}

/**
 * Executes next request from queue.
 *
 * @private
 */
XR.prototype.executeNext = function() {
	if(!this.isExecuting() && this.requests().length > 0) {
		this.setCurrentNext();
		this.current().execute(this.ho());
	}
} 
XR.prototype.current = function() {
	return this.m_current;
} 
XR.prototype.setCurrent = function(r) {
	this.m_current = r;
} 
XR.prototype.ho = function() {
	return this.m_ho;
}

/**
 * Adds POST request to the end of queue.
 */
XR.prototype.addPost = function(url, postdata, success, failure) {
	r = new Req();
	r.setUrl(url);
	r.setSuccess(success);
	r.setFailure(failure);
	r.setPostData(postdata);
	this.addIfNotExists(r);
}

/**
 * Adds GET request to the end of queue.
 */
XR.prototype.addRequest = function(url, success, failure) {
	r = new Req();
	r.setUrl(url);
	r.setSuccess(success);
	r.setFailure(failure);
	this.addIfNotExists(r);
}
/**
 * Checks if has such request.
 *
 * @param {Request} $rtc Request to check for.
 *
 * @returns {Boolean}
 */
XR.prototype.hasRequest = function(rtc) {
	var i   = 0;
	var r   = null;
	var has = false;
	for(i = 0; i<xr.requests().length; i++) {
		r = xr.requests()[i];
		if(rtc.url() == r.url()) has = true;
		if(has) break;
	}
	return has;
}
/**
 * Add request if not exists in queue.
 *
 * @param {Req} r
 */
XR.prototype.addIfNotExists = function(r) {
	if(!xr.hasRequest(r))
		xr.requests().push(r);
}

/**
 * What to do on each iteration.
 */
XR.prototype.pulse = function() {
	xr.executeNext();
}

xr = new XR();
window.setInterval(xr.pulse, 1000);

/**
 * Data returned from api.
 */
function ApiData() {
	this.m_dbobjs   = new Object(); //use Object for Associative arrays.
	this.m_response = null; 
}

/**
 * Setter for m_response.
 *
 * @param {Object} $response
 */
ApiData.prototype.set_response = function(response) {
	this.m_response = response;
}

/**
 * Getter for m_response.
 *
 * @returns {Object}
 */
ApiData.prototype.response = function() {
	return this.m_response;
}

/**
 * Gives dbobjs by class name.
 *
 * @param {String} cn Class name of objects.
 *
 * @returns {Array}
 */
ApiData.prototype.dbobjsbycn = function(cn) {
	var o   = this.dbobjs();
	return ('undefined' === typeof o[cn]) ? new Array() : o[cn];
}

/**
 * Getter for m_dbobjs.
 */
ApiData.prototype.dbobjs = function() {
	return this.m_dbobjs;
} 

/**
 * Adds dbobjs.
 *
 * @param {Array} $dbobjs
 *
 * @param {String} $cn Class name of objects.
 */
ApiData.prototype.set_dbobjs = function(dbobjs, cn) {
	this.m_dbobjs[cn] = dbobjs;
}

/**
 * Returns object of class ApiData.
 *
 * @param {String} str Data string.
 *
 * @returns {Object}
 */
ApiData.toObj = function(str) {
	// Parse string.
	var apijobj = Jsonp.secureObjConvert(str); //object from server data
	var ad = new ApiData();

	//Database objects
	if('undefined' !== typeof apijobj.DbObjs) { 
		var dbobjs = apijobj.DbObjs;
		var classnames = Object.keys(dbobjs);
		var i = 0;
		var j = 0;
		// With each class extract it's objects
		for(i = 0; i < classnames.length; i++) {
			var cn   = classnames[i];
			var objs = new Array();
			for(j = 0; j < dbobjs[cn].length; j++ )
				objs.push(window[cn]['fromJsonO'](dbobjs[cn][j], cn)); 
			ad.set_dbobjs(objs, cn);
		}
	}
	
	//Response
	ad.set_response(Response.fromJsonO(apijobj.Response, "Response")); 
	return ad;
}

/**
 * Response.
 */
function Response() {}
Response.fromJsonO = Lib.funcFromJsonO;

/**
 * Getter for m_success.
 * 
 * @returns {Boolean}
 */
Response.prototype.success = function() {
	return this.m_success;
}

/**
 * Getter for m_message.
 *
 * @returns {String}
 */
Response.prototype.message = function() {
	return this.m_message;
}

/**
 * TALK
 */
function Talk() {}
Talk.fromJsonO = Lib.funcFromJsonO;
/**
 * Getters for Talk.
 */
Talk.prototype.id = function() {
	return this.m_id;
}
Talk.prototype.created_on = function() {
	return this.m_created_on;
}
Talk.prototype.created_on_d = function() {
	return new Date(Date.parse(this.m_created_on));
}
Talk.prototype.what = function() {
	return this.m_what;
}
Talk.prototype.talker_nickname = function() {
	return this.m_talker_nickname;
}
/**
 * TALKER
 */
function Talker() { }
Talker.fromJsonO = Lib.funcFromJsonO;

/**
 * Getters for Talker.
 */
Talker.prototype.id = function() {
	return this.m_id;
}
Talker.prototype.nickname = function() {
	return this.m_nickname;
}

/**
 * Event for click on "change nickname".
 * @event
 */
function WindowManager() {}

/**
 * Returns maximum duration for message before it disappears.
 */
WindowManager.max_msg_duration = function() {
	return 5000;
}

WindowManager.prototype.eventChangeNicknameClicked = function() {
	if(isHidden(this.formSetNicknameId())) {
		//Show form to set nickname.
		show(this.formSetNicknameId());
		this.inputSetNickname().focus();
	} else {
		hide(this.formSetNicknameId());
	}
}

/**
 * Focuses on the input field.
 */
WindowManager.prototype.keepfocus = function() {
	if(this.chatBoxInput() !== document.activeElement)
		this.chatBoxInput().focus();
}

/**
 * Event for talkers list item click.
 *
 * @event 
 *
 * @param {Integer} talker_id
 */
WindowManager.prototype.eventTalkerListItemClicked = function(talker_nickname) {
	this.talkTo(talker_nickname);
}

/**
 * Writes nickname to say box.
 *
 * @private
 *
 * @param string $nickname
 */
WindowManager.prototype.talkTo = function(nickname) { 
	var cbi = this.chatBoxInput();
	cbi.value = nickname + ": "+ cbi.value;
	cbi.value = cbi.value; //place cursor at the end of field
	wm.keepfocus();
}

/**
 * Event for chat talks list item select.
 *
 * @event
 * 
 * @param integer talk_id
 *
 * @return void
 */
WindowManager.prototype.eventChatTalksListItemClicked = function(talk_id) {
	alert('Talk with id '+talk_id+' clicked!');
}

/**
 * Tells that current talker data has "changed".
 *
 * @event
 */
WindowManager.prototype.eventTalkerChanged = function() {
	// Set nickname field with nickname of talker.
	wm.inputNickname().value = cm.talker().nickname();

	//must reload talkers list since talker changed.
	gw.loadTalkers();
}

/**
 * Tells that current chat changed. Must regenerate contents.
 *
 * @event
 */
WindowManager.prototype.init = function() {
	wm.clearTalksBox()  ;
	wm.clearTalkersBox();
	gw.currentTalker()  ;
	gw.loadNewTalks()   ;
	gw.loadTalkers()    ; 
}

/**
 * User says something (i.e. user submits chat box).
 *
 * @event
 */
WindowManager.prototype.chatBoxSubmitted = function() {
	gw.talkerSay(this.chatBoxInput().value);
	this.chatBoxInput().value = '';
	this.keepfocus();
	return false;
}

/**
 * Event for nickname set form submit.
 *
 * @event
 */
WindowManager.prototype.setNicknameSubmit = function() { 
	var new_nickname = this.inputNickname().value;
	gw.talkerSetNickname(new_nickname);
	hide(this.formSetNicknameId());
}

/**
 * Name of the chat talks box.
 *
 * @private
 */
WindowManager.prototype.chatTalksBoxId = function() {
	return 'chat_talks_box';
}

/**
 * Name of the talkers list.
 *
 * @private
 *
 * @returns {String}
 */
WindowManager.prototype.talkersListBoxId = function() {
	return 'talkers_list_box';
}

/**
 * Id of the nickname form.
 *
 * @private
 *
 * @returns {String}
 */
WindowManager.prototype.formSetNicknameId = function() {
	return 'form_set_nickname';
}

/**
 * Id of the nickname input field.
 *
 * @private
 *
 * @returns {String}
 */
WindowManager.prototype.inputSetNicknameId = function() {
	return 'input_talker_nickname';
}

/**
 * Name of the chat box.
 *
 * @private
 *
 * @returns {String}
 */
WindowManager.prototype.formChatBoxId = function() {
	return 'form_chat_box';
}

/**
 * Name of the say button.
 *
 * @returns {String}
 */
WindowManager.prototype.buttonSayId = function() {
	return 'button_say';
}

/**
 * Returns name of the chat box input.
 *
 * @private
 *
 * @returns {String}
 */
WindowManager.prototype.chatBoxInputId = function() {
	return 'chat_box_input';
}

/**
 * Returns nickname input.
 */
WindowManager.prototype.inputNickname = function() {
	return gbid(this.inputSetNicknameId());
}

/**
 * Returns say button.
 *
 * @returns {Object}
 */
WindowManager.prototype.buttonSay = function() {
	return gbid(this.buttonSayId());
}

/**
 * Returns chat box input.
 *
 * @returns {Object}
 */
WindowManager.prototype.chatBoxInput = function() {
	return gbid(this.chatBoxInputId());
}

/**
 * Returns input of "set nickname".
 * 
 * @private
 *
 * @returns {Object}
 */
WindowManager.prototype.inputSetNickname = function() {
	return gbid(this.inputSetNicknameId());
}

/**
 * Returns talkers box.
 *
 * @returns {Object}
 */
WindowManager.prototype.talkersListBox = function() {
	return gbid(this.talkersListBoxId());
}

/**
 * Returns chat talks box.
 *
 * @returns {Object}
 */
WindowManager.prototype.chatTalksBox = function() {
	return gbid(this.chatTalksBoxId());
}

/**
 * Returns chat box.
 *
 * @returns {Object}
 */
WindowManager.prototype.formChatBox = function() {
	return gbid(this.formChatBoxId());
}

/**
 * Returns forms from page.
 *
 * @returns {Array}
 */
WindowManager.prototype.forms = function() {
	return new Array(this.formSetNicknameId(), this.formChatBoxId());
}

/**
 * Sets contents of talkers list box.
 *
 * @private
 */
WindowManager.prototype.setTalkersListBox = function(contents) {
	this.talkersListBox().innerHTML = contents;
}

/**
 * Shows chat box.
 *
 * @returns void
 */
WindowManager.prototype.showChatBox = function() {
	show(this.formChatBoxId());
}

/**
 * Rebuilds list: sets talkers list with contents.
 */
WindowManager.prototype.reBuildTalkersList = function() {
	this.setTalkersListBox(this.buildTalkersList()); 
}

/**
 * Clears talks box.
 *
 * @private
 */
WindowManager.prototype.clearTalksBox = function() {
	this.chatTalksBox().innerHTML = '';
}

/**
 * Clears talkers box.
 *
 * @private
 */
WindowManager.prototype.clearTalkersBox = function() {
	this.talkersListBox().innerHTML = '';
}

/**
 * Builds chat talks list from talks.
 *
 * @param {Array} talks Talks to add.
 *
 */
WindowManager.prototype.reBuildTalksList = function(talks) {
	wm.updateTalksList(talks);
	//scrollbar to the end
	var ctb = this.chatTalksBox();
	ctb.scrollTop = ctb.scrollHeight;
}

/**
 * Updates talks list with talks.
 */
WindowManager.prototype.updateTalksList = function(talks) {
	this.chatTalksBox().innerHTML = this.chatTalksBox().innerHTML + this.buildChatTalks(talks);
}

/**
 * Builds html for talkers list.
 *
 * @returns {String}
 */
WindowManager.prototype.buildTalkersList = function() {
	var out     = '<b>Talkers</b>';
	var talkers = cm.talkers(); 
	var ctalker = cm.talker();
	var talker;
	var i = 0;
	for(i = 0; i < talkers.length; i++) {
		talker = talkers[i];
		out = out + '<span class="talker clickable" onclick="wm.eventTalkerListItemClicked(\''+talker.nickname()+'\')"> * ' + ( talker.id() != ctalker.id() ? talker.nickname() : '<b>'+talker.nickname()+'</b>') + '</span>'; 
	}
	return out;
}

/**
 * Builds html for chat talks list.
 *
 * @returns {String}
 */
WindowManager.prototype.buildChatTalks = function(talks) { 
	var out   = '';
	var talk;
	var i = 0;
	var tstr = '';
	for(i=0; i<talks.length; i++) {
		talk = talks[i];
		tstr = talk.created_on_d().getHours() + ':' + talk.created_on_d().getMinutes();
		out = out + '<span class="talk clickable" onclick="wm.eventChatTalksListItemClicked('+talk.id()+')"><i>@'+tstr+'</i>,'+talk.talker_nickname()+' said <b>'+talk.what()+"</b></span>";
	} 
	return out;
}

/**
 * Hides all my forms.
 *
 * @private
 */
WindowManager.prototype.hideAllForms = function hideAllForms() {
	var i  = 0;
	var fs = this.forms();
	for(i=0;i<fs.length;i++) 
		hide(fs[i]);
} 

/**
 * Exchanges data with server. Works only with current chat. 
 */
function Gateway() {} 

/**
 * Registers request to set talker nickname.
 */
Gateway.prototype.talkerSetNickname = function(nickname) {
	postdata = 'talker[nickname]='+encodeURIComponent(nickname);
	xr.addPost('api.php?talker&set_nickname', postdata, gw.eventTalkerNicknameChanged);
}

/**
 * Registers request to get current talker.
 */
Gateway.prototype.currentTalker = function() {
	xr.addRequest("api.php?talker&current", gw.eventGotCurrentTalker);
}

/**
 * Registers request to get talkers.
 *
 * @returns {Array}
 */
Gateway.prototype.loadTalkers = function(chat) {
	xr.addRequest('api.php?talkers', gw.eventGotTalkers);
}

/**
 * Registers request to get talks for chat.
 */
Gateway.prototype.loadNewTalks = function() {
	xr.addRequest('api.php?talks&last_talk_id='+cm.lastTalkId(), gw.eventGotNewTalks);
}

/**
 * Registers request to post what is said by talker.
 *
 * @param {String} what
 */
Gateway.prototype.talkerSay = function(what) {
	postdata = 'talk[what]='+encodeURIComponent(wm.chatBoxInput().value);
	xr.addPost('api.php?say', postdata, gw.eventTalkerSaid);
}

/**
 * Event on talker nickname changed.
 * 
 * @event
 */
Gateway.prototype.eventTalkerNicknameChanged = function(str) {
	var ad = ApiData.toObj(str);
	xr.eventGotResponse(ad.response());
	gw.currentTalker(); //reload talker information from server
}

/**
 * Event on current talker.
 *
 * @event
 */
Gateway.prototype.eventGotCurrentTalker = function(str) {
	var ad = ApiData.toObj(str);
	xr.eventGotResponse(ad.response());
	var talkers = ad.dbobjsbycn("Talker");
	if(talkers.length > 0)
		cm.set_talker(talkers[0]);
}

/**
 * Event on talkers words.
 *
 * @event
 */
Gateway.prototype.eventTalkerSaid = function(str) {
	//m('Talker said:'+str);
	xr.executedSuccessfully();
}

/**
 * Event on talkers from server.
 *
 * @event 
 */
Gateway.prototype.eventGotTalkers = function(str) {
	var ad = ApiData.toObj(str);
	xr.eventGotResponse(ad.response());
	var talkers = ad.dbobjsbycn("Talker");
	// Inform ChatManager of talkers. 
	cm.eventGotTalkers(talkers);
}

/**
 * Event on talks from server.
 *
 * @event
 */
Gateway.prototype.eventGotNewTalks = function(str) {
	var ad = ApiData.toObj(str);
	xr.eventGotResponse(ad.response());
	var talks  = ad.dbobjsbycn("Talk");
	//Inform ChatManager of new talks.
	cm.eventGotNewTalks(talks);
}

/**
 * Manages current chat. Works with Gateway.
 */
function ChatManager() {
	this.m_talker  = null; //current talker
	this.m_talks   = new Array();
	this.m_talkers = new Array();
}

/**
 * Returns current talker.
 *
 * @returns {Object}
 */
ChatManager.prototype.talker = function() {
	return this.m_talker;
}

/**
 * Sets current talker.
 * 
 * @param {Talker} talker
 */
ChatManager.prototype.set_talker = function(talker) {
	this.m_talker = talker;
	wm.eventTalkerChanged(); // inform that talker "changed"
}

/**
 * Event on talkers.
 */
ChatManager.prototype.eventGotTalkers = function(talkers) {
	// Set talkers and rebuild talkersList
	this.setTalkers(talkers);
	this.reloadTalkersList();
	wm.reBuildTalkersList();
}

/**
 * Event on new talks.
 */
ChatManager.prototype.eventGotNewTalks = function(talks) {
	//add talks and rebuild talkslist
	var talks_added = this.addTalks(talks);
	wm.reBuildTalksList(talks_added);
}

/**
 * Gets talkers and puts them to talkers list.
 */
ChatManager.prototype.reloadTalkersList = function() {
	cm.fromTalkers(this.talkers()); //builds talkers list from talkers
}

/**
 * Setter for m_talks.
 *
 * @param {Array} $talks
 */
ChatManager.prototype.setTalks = function(talks) {
	this.m_talks = talks;
}
/**
 * Clears talks.
 *
 */
ChatManager.prototype.clearTalks = function() {
	this.m_talks = new Array();
}
/**
 * Getter for m_talks.
 *
 * @returns {Array}
 */
ChatManager.prototype.talks = function() {
	return this.m_talks;
}

/**
 * Setter for m_talkers.
 *
 * @param {Array} $talkers
 */
ChatManager.prototype.setTalkers = function(talkers) {
	this.m_talkers = talkers;
}

/**
 * Getter for m_talkers.
 * 
 * @return array
 */
ChatManager.prototype.talkers = function() {
	return this.m_talkers;
}

ChatManager.prototype.addTalker = function(talker) {
	if(!this.hasTalker(talker))
		this.m_talkers.push(talker);
}

/**
 * Makes talkers list from talkers.
 * 
 * @param {Array} talkers
 */
ChatManager.prototype.fromTalkers = function(talkers) {
	var i = 0;
	if(Array.isArray(talkers)) {
		for(i = 0; i<talkers.length; i++) {
			t = talkers[i];
			cm.addTalker(t);
		} 
	}
}

/**
 * Tells whether list has talker in it.
 *
 * @param Talker talker
 */
ChatManager.prototype.hasTalker = function(talker) {
	var talkers = this.talkers();
	var i   = 0;
	var has = false; 
	for(i = 0; i < talkers.length; i++) {
		if(talkers[i].id() == talker.id()) has = true;
		if(has) break;
	}
	return has;
}

/**
 * Gets id of last talk stored.
 *
 * @returns {Integer}
 */
ChatManager.prototype.lastTalkId = function() {
	talks     = this.talks();
	var i     = 0;
	var maxId = 0;
	for(i = 0; i < talks.length; i++) {
		if(maxId < talks[i].id()) maxId = talks[i].id();
	}
	return maxId;
}

ChatManager.prototype.hasTalk = function(talk) {
	var talks = this.talks();
	var i     = 0;
	var has   = false;
	for(i = 0; i < talks.length; i++) {
		if(talks[i].id() == talk.id()) has = true;
		if(has) break;
	}
	return has;
}
/**
 * Adds talks.
 *
 * @param {Array} $talks
 *
 * @returns {Array} Added talks.
 */
ChatManager.prototype.addTalks = function(talks) {
	var i = 0;
	var ret = new Array();
	for(i = 0; i < talks.length; i++) {
		if(this.addTalk(talks[i])) {
			ret.push(talks[i]); 
		}
	}
	return ret;
}

/**
 * Add talk.
 *
 * @param {Talk} $talk
 *
 * @returns {Boolean} True if talk added.
 */
ChatManager.prototype.addTalk = function(talk) {
	var has = this.hasTalk(talk);
	if(!has) this.talks().push(talk); 
	return !has;
}

/**
 * GLOBALS
 */
var wm  = new WindowManager(); // manages what to show to user
var gw  = new Gateway();       // manages chat data exchange with server
var cm  = new ChatManager();   // manages chat 
var jp  = new Jsonp();

//Hide all forms that are not needed instantly.
wm.hideAllForms();
//Show what is needed.
wm.showChatBox();

// Init ChatManager
wm.init(); 

function pulse() { if(null != cm.talker()) gw.loadNewTalks(); }
window.setInterval(pulse, 3000);

//Focus on the chat input field.
wm.keepfocus();

