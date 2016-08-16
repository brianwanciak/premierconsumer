// JavaScript Document

$(document).ready(function(){
						   
	creatArticleForm();
	
});


function creatArticleForm(){
	
	var form = $(".create-article-form");
	
	var init = function(){
		
		form.submit(function(e){
        	e.preventDefault(e);
        });
		
		$(".create-article").click(function(){
			$(this).hide().parent().find(".edit-panel").fadeIn();
		});		
		$("[data-role='close-panel']").click(function(){
			var wrapper = $(this).parents(".option");
			wrapper.find(".edit-panel").hide();
			wrapper.find(".create-article").fadeIn();								 
		});	
		
		
		$("[data-role='create-article']").click(function(){
			var name = 	$("input[name='name']", form);	
			var cat = 	$("input[name='category']", form);	
			
			if(name.val() == ""){
				alert("Please fill in the Article Name");
				return;
			}
			else if(cat.val() == ""){
				alert("Please select an Article Category");
				return;	
			}
			else{
				createArticle(name, cat);	
			}
			
		});	
		
	}
	
	function createArticle(name, cat){
		$("body").addClass("working");	
		var data = form.serialize();
		$.ajax({
			url: "process/createNewArticle.process.php",
			type: "POST",
			data: data,
			complete: function(jqXHR, status){
				if(status == "success"){
					if(jqXHR.responseText == "exists"){
						alert("A page with that name already exists, please try again.");
					}else{
						window.location.href = "content.php?task=edit#"+jqXHR.responseText;
					}
				}
				$("body").removeClass("working");
			}
		});
	}
	
	init();
}


function buildSiteNav(){
	
	var menuWrap = $(".sitenav");
	//$(".sitenav .sitenav-content").html("");
	menuWrap.addClass("loading");
	$.ajax({
		url: "process/sitemenu.process.php?v="+$.now(),
		type: "GET",
		complete: function(jqXHR, status){
			if(status == "success"){
				$(".sitenav .sitenav-content").html(jqXHR.responseText);
			}
			$(".sitenav").removeClass("loading");
			$('#sitemenu').easytree({stateChanged: stateChanged});
		}
	});

	
	
	
}

function stateChanged(nodes, jsonString){
	
}


function SiteNav(){

	var navmenu = $("#sitemenu"),
		contentEditor = null;
	var nav = {
		
	}
	
	var bindEvents = function(){
		window.onhashchange = function(){
			var editor = new ContentEditor(window.location.hash.replace("#", ""));	
		}
		$(document).ready(function(){
			if(window.location.hash.replace("#", "") != ""){
				var editor = new ContentEditor(window.location.hash.replace("#", ""));	
			}
		});
	}
	
	var init = function(){
		bindEvents();	
	}

	init();
	
}


function ContentEditor(path){
	var path = path.replace(/\\/g, "/");
	var ce = {
		editorContent : $(".editorContent"),
		saveBtn : $("[data-role='page-save']"),
		cancelBtn : $("[data-role='page-cancel']"),
		publishBtn : $("[data-role='page-publish']"),
		unpublishBtn : $("[data-role='page-unpublish']"),
	}
	
	var bindEvents = function(){
		ce.cancelBtn.unbind("click");
		ce.saveBtn.unbind("click");
		ce.publishBtn.unbind("click");
		ce.unpublishBtn.unbind("click");
		
		ce.cancelBtn.click(function(){
			window.location.href = "content.php";					 
		});
		ce.saveBtn.click(function(){
			saveContent();					  
		});
		
		ce.publishBtn.click(function(){
			publish();					  
		});
		
		ce.unpublishBtn.click(function(){
			unpublish();					  
		});
	}
	
	var saveContent = function(){
		var key = $.now();
		$("body").addClass("working");
		var data = $('#contentForm').serialize();
		if(enContentMain){data += "&editor-contentPage=" + encodeURIComponent(enContentMain.getData());}
		if(esContentMain){data += "&es-editor-contentPage=" + encodeURIComponent(esContentMain.getData());}
		$.ajax({
			url: "process/contentEditor.process.php?task=save&key="+key+"&path="+path,
			type: "POST",
			data: data,
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
				}
				$("body").removeClass("working");
			}
		});
		
	}
	
	var loadContent = function(){
		var key = $.now();
		$("body").addClass("working");
		$.ajax({
			url: "process/contentEditor.process.php?task=edit&key="+key+"&path="+path,
			type: "GET",
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var publish = function(){
		var key = $.now();
		$("body").addClass("working");
		$.ajax({
			url: "process/contentEditor.process.php?task=publish&key="+key+"&path="+path,
			type: "GET",
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
					runManifest();
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var unpublish = function(){
		var key = $.now();
		$("body").addClass("working");
		$.ajax({
			url: "process/contentEditor.process.php?task=unpublish&key="+key+"&path="+path,
			type: "GET",
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
					runManifest();
				}
				$("body").removeClass("working");
			}
		});
	}
	
	
	var runManifest = function(){
		var key = $.now();
		var template = $("[name='template']").val();
		if(template.indexOf("article") >= 0){
			$.ajax({url: "process/articleManifest.process.php?key="+key, complete: function(jqXHR, status){
        	// possible code to track successful manifest creations
    		}});
		}else if(template.indexOf("category") >= 0){
			$.ajax({url: "process/articleManifest.process.php?key="+key, complete: function(jqXHR, status){
        	// possible code to track successful manifest creations
    		}});
		}else if(template.indexOf("calculator") >= 0){
			$.ajax({url: "process/calculatorManifest.process.php?key="+key, complete: function(jqXHR, status){
        	// possible code to track successful manifest creations
    		}});
		}else if(template.indexOf("video") >= 0){
			$.ajax({url: "process/videoManifest.process.php?key="+key, complete: function(jqXHR, status){
        	// possible code to track successful manifest creations
    		}});
		}
			
	}
	
	
	var init = function(){
		loadContent();
		bindEvents();
	}
	
	init();
	
}



function ArticleCategories(){
	
	var c = {
		editorContent : $(".editorContent"),
		saveBtn : $("[data-role='ac-save']"),
		cancelBtn : $("[data-role='ac-cancel']")
	}
	
	var bindEvents = function(){
		c.cancelBtn.unbind("click");
		c.saveBtn.unbind("click");
		
		c.cancelBtn.click(function(){
			window.location.href = "content.php";					 
		});
		c.saveBtn.click(function(){
			saveContent();					  
		});
		
	}
	
	var saveContent = function(){
		$("body").addClass("working");
		var data = $('#contentForm').serialize();
		$.ajax({
			url: "process/articleCategories.process.php?task=save",
			type: "POST",
			data: data,
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var loadContent = function(){
		$("body").addClass("working");
		$.ajax({
			url: "process/articleCategories.process.php?task=edit",
			type: "GET",
			complete: function(jqXHR, status){
				if(status == "success"){
					$(".editorContent").html(jqXHR.responseText);
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var init = function(){
		loadContent();
		bindEvents();
	}

	init();
}


function formDialog(message, type){
	$('body').prepend('<div class="formDialog '+type+'"><span>'+message+'</span></div>');
	$(".formDialog").css("margin-left", "-"+($(".formDialog").width()/2)+"px");
	window.setTimeout(function(){ $(".formDialog").fadeOut() }, 2000);
}



