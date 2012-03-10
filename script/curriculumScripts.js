function loadCurriculum(curriculumid){
	//new Ajax.Updater('curriculum', 'curriculumManager.php', { method:'post', evalScripts: true, parameters: { curriculumid: curriculumid }});
	$.post("curriculumManager.php", { curriculumid: curriculumid }, function(data){ $("#curriculum").html(data); }, "html");
}

function displayDegrees(universityid){
	//new Ajax.Updater('degree', 'curriculumDegrees.php', { method:'post', evalScripts: true, parameters: { universityid: universityid }});
	$.post("curriculumDegrees.php", { universityid: universityid }, function(data){ $("#degree").html(data); }, "html");
}

function newCurriculum(curriculumid){
	//new Ajax.Updater('saveCurriculum', 'saveCurriculum.php', { method:'post', evalScripts: true, parameters: { curriculumid: curriculumid, curriculumname: $('curriculumname').value, universityid: $('chooseUniversity').value, degreeid: $('chooseDegree').value, defaultcurriculum: $('defaultcurriculum').value }});
	$.post("saveCurriculum.php", { curriculumid: curriculumid, curriculumname: $("#curriculumname").val(), 
														universityid: $("#chooseUniversity").val(), degreeid: $('#chooseDegree').val(), 
														defaultcurriculum: $("#defaultcurriculum").is(":checked") }, 
														function(data){ $("#saveCurriculum").html(data); }, "html");
}

function updateCurriculum(curriculumid){
	//new Ajax.Updater('saveCurriculum', 'saveCurriculum.php', { method:'post', evalScripts: true, parameters: { curriculumid: curriculumid, curriculumname: $('curriculumname').value, defaultcurriculum: $('defaultcurriculum').checked }});
	$.post("saveCurriculum.php", { curriculumid: curriculumid, curriculumname: $("#curriculumname").val(), 
														defaultcurriculum: $("#defaultcurriculum").is(":checked") }, 
														function(data){ $("#saveCurriculum").html(data); }, "html");
}

function loadCurriculumSelect(){
	//new Ajax.Updater('chooseCurriculumDiv', 'chooseCurriculum.php', { method:'post', evalScripts: true});
	$.post("chooseCurriculum.php", {}, function(data){ $("#chooseCurriculumDiv").html(data); }, "html");
}

function curriculumClassList(){
	//new Ajax.Updater('curriculumClassList', 'curriculumClassList.php', { method:'post', evalScripts: true});
	$.post("curriculumClassList.php",{}, function(data){ $("#curriculumClassList").html(data); }, "html");
}

function curriculumClassListByCriteria(universityid, degreeid){
	//new Ajax.Updater('curriculumClassList', 'curriculumClassList.php', { method:'post', evalScripts: true, parameters: { universityid: universityid, degreeid: degreeid}});
	$.post("curriculumClassList.php", { universityid: universityid, degreeid: degreeid}, function(data){ $("#curriculumClassList").html(data); }, "html" );
}

function loadCurriculumAddSelect(){
	//new Ajax.Updater('addCurriculumClassDiv', 'curriculumAddClassSelect.php', { method:'post', evalScripts: true});
	$.post("curriculumAddClassSelect.php",{}, function(data){ $("#addCurriculumClassDiv").html(data); }, "html");
}

function curriculumAddClass(){
	//new Ajax.Updater('addCurriculumClassTemp', 'curriculumAddClass.php', { method:'post', evalScripts: true, parameters: { classidnum: $('addCurriculumClass').value}});
	$.post("curriculumAddClass.php", { classidnum: $("#addCurriculumClass").val() }, function(data){ $("#addCurriculumClassTemp").html(data); }, "html");
}