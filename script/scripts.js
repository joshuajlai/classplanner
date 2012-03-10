function highlightRow(rowname){
	deHighlightRow();
	if($('#' + rowname)){
		$('#' + rowname).attr("originalcolor", $('#' + rowname).css("backgroundColor"));
		$('#' + rowname).css("backgroundColor", "#1199ff");
		this.highlightedrow = rowname;
	}
}

function deHighlightRow(){
	if(this.highlightedrow && $(this.highlightedrow)){
		$("#" + this.highlightedrow).css("backgroundColor", $('#' + this.highlightedrow).attr("originalcolor"));
	}
}

function updateAccount(){
	//new Ajax.Updater('ajaxresponse', 'saveAccount.php', { method:'post', evalScripts: true, parameters: { username: $('username').value, password1: $('password1').value, password2: $('password2').value }});
	$.post("saveAccount.php", { username: $('#username').val(), password1: $('#password1').val(), password2: $('#password2').val() }, function(data){ $("#ajaxresponse").html(data);}, "html");
}

function loadTerms(){
	//new Ajax.Updater('terms', 'loadTerms.php', { method: 'post', evalScripts: true });
	$.post("loadTerms.php", {}, function(data){ $("#terms").html(data); }, "html");
}

function deleteTerm(termsequence){
	//new Ajax.Updater('termUpdate', 'updateTerm.php', { method: 'post', evalScripts: true, parameters: { del: termsequence }});
	$.post("updateTerm.php", { del: termsequence }, function(data){ $("#termUpdate").html(data); }, "html");
}

function updateStartingTerm(termsequence){
	//new Ajax.Updater('termUpdate', 'updateTerm.php', { method: 'post', evalScripts: true, parameters: { startingterm: termsequence }});
	$.post("updateTerm.php", { startingterm: termsequence }, function(data){ $("#termUpdate").html(data); }, "html");
}

function addTerm(){
	//new Ajax.Updater('termUpdate', 'updateTerm.php', { method: 'post', evalScripts: true, parameters: { addterm: $('term.new').value }});
	$.post("updateTerm.php", { addterm: $("#newTerm").val() }, function(data){ $("#termUpdate").html(data); }, "html");
}

function editTerm(sequence){
	$('#displayTerm'+sequence).hide();
	$('#editTerm'+sequence).show();
	$('#editButton'+sequence).hide();
	$('#saveButton'+sequence).show();
}

function updateTerm(sequence){
	//new Ajax.Updater('termUpdate', 'updateTerm.php', { method: 'post', evalScripts: true, parameters: { sequence: sequence, updateTerm: $('term'+sequence).value } });
	$.post("updateTerm.php", { sequence: sequence, updateTerm: $("#term" + sequence).val() }, function(data){ $("#termUpdate").html(data); }, "html");
}
