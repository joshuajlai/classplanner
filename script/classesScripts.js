function loadClassList(){
	//new Ajax.Updater('classList', 'classlist.php', { method:'post', evalScripts: true, parameters: { school: ''+$('chooseSchool').value }});
	//$('classDetails').innerHTML="";
	$.post("classlist.php", { school: $("#chooseSchool").val() }, function(data){ $("#classList").html(data); }, "html");
	$("#classDetails").html("");
	
}

function changeAlpha(character){
	//new Ajax.Updater('classList', 'classlist.php', { method:'post', evalScripts: true, parameters: { alphabet: character}});
	//$('classDetails').innerHTML="";
	$.post("classlist.php", { alphabet: character }, function(data){ $("#classList").html(data); }, "html");
	$("#classDetails").html("");
}

function updateAlpha(character, classid){
	//new Ajax.Updater('classList', 'classlist.php', { method:'post', evalScripts: true, parameters: { alphabet: character, classidnum: classid}});
	$.post("classlist.php", { alphabet: character, classidnum: classid }, function(data){ $("#classList").html(data); }, "html");
}

function loadClassDetails(classid){
	highlightRow(classid);
	//new Ajax.Updater('classDetails', 'classdetails.php', { method: 'post', evalScripts: true, parameters: { classidnum: classid }});
	$.post("classdetails.php", { classidnum: classid }, function(data){ $("#classDetails").html(data); }, "html");
}

function loadPrerequisites(classid){
	//new Ajax.Updater('classPrerequisites', 'classprerequisites.php', { method: 'post', evalScripts: true, parameters: {  classidnum: classid}});
	$.post("classprerequisites.php", { classidnum: classid }, function(data){ $("#classPrerequisites").html(data); }, "html");
}

function saveClass(){
	var classSymbol = $('#classSymbol').val();
	var className = $('#className').val();
	//new Ajax.Updater('errors', 'saveCustomClass.php', { method: 'post', evalScripts: true, parameters: { classSymbol: classSymbol, className: className }});
	$.post("saveCustomClass.php", { classSymbol: classSymbol, className: className }, function(data){ $("#errors").html(data); }, "html");
}

function addClass(){
	//new Ajax.Updater('classDetails', 'classdetails.php', { method: 'post', evalScripts: true, parameters: { classidnum: 'new' }});
	$.post("classdetails.php", { classidnum: 'new' }, function(data){ $("#classDetails").html(data); }, "html");
}

function removePrerequisite(classid, prerequisiteid){
	//new Ajax.Updater('classPrerequisites', 'classprerequisites.php', { method: 'post', evalScripts: true, parameters: {  classidnum: classid, remove: prerequisiteid}});
	$.post("classprerequisites.php", { classidnum: classid, remove: prerequisiteid }, function(data){ $("#classPrerequisites").html(data); }, "html");
}

function addPrerequisite(classid, prerequisiteid){
	//new Ajax.Updater('classPrerequisites', 'classprerequisites.php', { method: 'post', evalScripts: true, parameters: {  classidnum: classid, add: prerequisiteid}});
	$.post("classprerequisites.php", {   classidnum: classid, add: prerequisiteid }, function(data){ $("#classPrerequisites").html(data); }, "html");
}

function updateClassOffering(classid, sequence, value){
	//new Ajax.Updater('classOffering', 'classOffering.php', { method: 'post', evalScripts: true, parameters: { classidnum: classid, sequence: sequence, value: value }});
	$.post("classOffering.php", { classidnum: classid, sequence: sequence, value: value }, function(data){ $("#classOffering").html(data); }, "html");
}

function deleteClass(){
	//new Ajax.Updater('errors', 'deleteClass.php', { method: 'post', evalScripts: true, parameters: {}});
	$.post("deleteClass.php", {}, function(data){ $("#errors").html(data); }, "html");
}