// JavaScript Document

$(document).ready(function(){

	$(".action-logout").click(function(){window.location.href="login.php?logout=1";});
	
	$(".link-btn").click(function(){
		window.location = $(this).attr("rel");							  
	});
	
	$(".submit-btn").click(function(){
		$($(this).attr("rel")).submit();					  
	});
	
	$("#submission-save-btn").click(function(){
		if($("#status_id").val() == 3 && $("#client_id").val() == ""){
			$("#client_id").parents(".control-group").addClass("error");
			$("#error1").modal();
			return false;
		}
		$($(this).attr("rel")).submit();					  
	});
	
	$("a.link-del-btn, button.link-del-btn").click(function(){
		if(confirm("Are you sure you want to delete this entry?")){
			window.location = $(this).attr("rel");	
		}
	});
	
	$(".status-select li a").click(function(){
		var vals = $(this).attr("rel").split(';');	
		$(".status-select .btn.status-label").attr("class", "btn status-label "+vals[0]).html(vals[2]);		
		$(".status-select .btn.dropdown-toggle").attr("class", "btn dropdown-toggle "+vals[0]);	
		$("input#status_id").val(vals[1]);
	});
	
	$(".owner-select li a").click(function(){
		var vals = $(this).attr("rel").split(';');	
		$(".owner-select .btn.owner-label").html(vals[1]);		
		$("input#owner_id").val(vals[0]);
	});
	
	$(".user-filter-select li a").click(function(){
		var vals = $(this).attr("rel").split(';');	
		$(".user-filter-select .btn.user-filter-label").html(vals[1]);		
		$("input#var_filter_val").val(vals[0]);
		$("input#var_filter").val("user_id");
		$("input#freeSearch").val("");
		$("#leadsList").submit();
	});
	
	$(".status-filter-select li a").click(function(){
		var vals = $(this).attr("rel").split(';');	
		$(".status-filter-select .btn.status-filter-label").html(vals[1]);		
		$("input#var_filter_val").val(vals[0]);
		$("input#var_filter").val("status_id");
		$("input#freeSearch").val("");
		$("#leadsList").submit();
	});
	
	$("#onlyMyLeads").click(function(){
		if($(this).hasClass("disabled")){
			$("input#var_filter_val").val("");
			$("input#var_filter").val("");	
			$("input#freeSearch").val("");
			$("#leadsList").submit();
		}else{
			$("input#var_filter_val").val($(this).attr("rel"));
			$("input#var_filter").val("user_id");		
			$("input#freeSearch").val("");
			$("#leadsList").submit();
		}
	});
	
	$("#filterSearchSubmit").click(function(){
		filterSearchSubmit();
	});
	
	
	$(".filter-toggle").click(function(){
		 $("#filter-options").slideToggle(); 
		 if($("#filter-pos").val() == "closed"){$("#filter-pos").val("open"); $("#clearFilters").show(); }else{$("#filter-pos").val("closed"); $("#clearFilters").hide();}
	});
	
	$("#filterDateSubmit").click(function(){
		filterDateSubmit();
	});
	
	$("#filterDateClear").click(function(){
		$("#dp1, #dp2").val("");							  
	});
	
	$("#filterSearchValue").keypress(function(e){
		var code = null;
		code= (e.keyCode ? e.keyCode : e.which);
        if (code == 13){
			filterSearchSubmit();
		}

	});
	
	$("#dp1, #dp2").keypress(function(e){
		var code = null;
		code= (e.keyCode ? e.keyCode : e.which);
        if (code == 13){
			filterDateSubmit();
		}

	});
	
	
	
	
	
						   
});

function filterDateSubmit(){
	var d1 = new Date($("#dp1").val());
	var d2 = new Date($("#dp2").val());
	
	if(d2 - d1 < 0){
		$(".dateError").html("Your second date must be greater than the first.");
		$("#dp1, #dp2").parents(".control-group").addClass("error");
		$("#error2").modal();
		return false;
	}else{
		$("#leadsList").submit();
	}	
}

function filterSearchSubmit(){
	$("input#var_filter_val").val($("#filterSearchValue").val());
	$("input#var_filter").val($("#filterSearchField").val());	
	$("input#freeSearch").val("true");
	$("#leadsList").submit();	
}

function validateEmail(emailVal){	
    var pattern=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
    if(pattern.test(emailVal)){         
		return true;
    }else{   
		return false;
    }
}