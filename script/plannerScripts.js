function calculateSchedule(){
	//$("#scheduleTable").load("classManager.php", { maxClasses: $("#maxClasses").val() });
	$.post("classManager.php", { maxClasses: $("#maxClasses").val() }, function(data){ $("#scheduleTable").html(data); }, "html");
}
