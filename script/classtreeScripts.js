function highlightClassTree(classid, classReqs){
	$('#class'+classid).attr("className", "highlightedClassTree");
	for(x=0;x<classReqs.length;x++){
		if(!classReqs[x])
			continue;
		$('#class'+classReqs[x]).attr("className", "highlightedPrerequisite");
	}
}

function dehighlightClassTree(classid, classReqs){
	$('#class'+classid).attr("className", "normalClassTree");
	for(x=0;x<classReqs.length;x++){
		if(!classReqs[x])
			continue;
		$('#class'+classReqs[x]).attr("className", "normalClassTree");
	}
}

function removeClass(classid){
	//new Ajax.Updater('class'+classid+'RemoveDiv', 'curriculumRemoveClass.php', { method:'post', evalScripts: true, parameters: { classidnum: classid }});
	//alert("Trying to remove classid: " + classid);
	$.post("curriculumRemoveClass.php", { classidnum: classid }, function(data){ $("#class"+classid+"RemoveDiv").html(data); } );
}