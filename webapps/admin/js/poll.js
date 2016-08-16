
function Poll(uid){
	
	var poll = {}
	var form = {
		cancelBtn : $("[data-role='poll-cancel']"),
		saveBtn : $("[data-role='poll-save']"),
		deleteBtn : $("[data-role='poll-delete']"),
		publishBtn : $("[data-role='poll-publish']"),
		unpublishBtn : $("[data-role='poll-unpublish']"),
	}
	var templates = {
		answer : $("#answer-template").html(),
		question : $("#question-template").html()
	}
	
	var setDomVals = function(){
		poll = {
			addAnswer : $("[data-role='add-answer']"),
			deleteAnswer : $("[data-role='delete-answer']")
		}
	}
	
	var bindDynamicActions = function(){
		setDomVals();
		poll.addAnswer.unbind("click");
		poll.deleteAnswer.unbind("click");
		
		poll.addAnswer.click(function(){
			$(this).parents("tr").before(templates.answer);	
			bindDynamicActions();
		});

		poll.deleteAnswer.click(function(){
			if(confirm("Are you sure you want to delete this Answer? This action cannot be undone.")){
				deleteItem($(this).attr("data-id"), "poll_answers", $(this).parents("tr"));	
			}
		});
	}
	
	var deleteItem = function(id, table, domElem){
		if(id > 0){
			var key = $.now();
			var data = [{"id":id,"action":"delete","table":table}];	
			$("body").addClass("working");
			$.ajax({
				url: "process/poll.process.php?key="+key,
				type: "POST",
				data: JSON.stringify(data),
				contentType: "application/json",
				complete: function(jqXHR, status){
					if(status == "success"){
						domElem.fadeOut("slow", function(){$(this).remove()});	
					}
					$("body").removeClass("working");
				}
			});
		
		}else{
			domElem.fadeOut("slow", function(){$(this).remove()});	
		}
		
	}
	
	var bindActions = function(){
		form.cancelBtn.click(function(){
			window.location.href = "poll.php";							  
		});
		
		form.publishBtn.click(function(){
			publish();					  
		});
		
		form.unpublishBtn.click(function(){
			unpublish();					  
		});
		
		form.deleteBtn.click(function(){
			if(confirm("WARNING! This will permenantly delete this poll and all contents. Are you sure?")){
				$("body").addClass("working");
				var key = $.now();
				var data = [{"id":$("#poll_id").val(), "action":"deletePoll"}];
				$.ajax({
					url: "process/poll.process.php?key="+key,
					type: "POST",
					data: JSON.stringify(data),
					contentType: "application/json",
					complete: function(jqXHR, status){
						window.location.href = "poll.php";
						$("body").removeClass("working");
					}
				});
			}
		});
		
		form.saveBtn.click(function(){
			$("body").addClass("working");
			var key = $.now();
			var data = [{"id":$("#poll_id").val(), 
						"enTitle": $(".poll-en-title").val(), 
						"esTitle": $(".poll-es-title").val(), 
						"enDesc": $(".poll-description").val(), 
						"esDesc": $(".poll-description-es").val(),
						"relatedQuiz": $(".related-quiz").val(), 
						"relatedArticle": $(".related-article").val(),
						"image": $("#en-image").val(), 
						"esImage": $("#es-image").val(), 
						"esSameImage": $("#es-same-image").is(":checked"), 
						"action":"save"}];
			$(".admin-question").each(function(){
				item = {};
				item["id"] = $(this).attr("data-question-id");
				item["poll_id"] = $("#poll_id").val();
				item["question"] = $("[name='poll-question']", $(this)).val();
				item["lang"] = $(this).attr("data-lang");
				item["answers"] = [];
				$("tr.answer", $(this)).each(function(){
					var item2 = {}
					item2["id"] = $("[name='poll-answer']", $(this)).attr("data-id");
					item2["answer"] = $("[name='poll-answer']", $(this)).val();
					item2["language"] = item["lang"];
					item["answers"].push(item2);
				});
				data.push(item);
				
			});
			$.ajax({
				url: "process/poll.process.php?key="+key,
				type: "POST",
				data: JSON.stringify(data),
				contentType: "application/json",
				complete: function(jqXHR, status){
					
					if (window.location.href.indexOf("&uid") > -1) {
						var location = window.location.href;
					}else{
						var location = window.location.href+"&uid="+jqXHR.responseText;	
					}
					
					window.location.href = location;
					//alert(JSON.stringify(jqXHR.responseText));	
					$("body").removeClass("working");
				}
			});
			//alert(JSON.stringify(data));
			
		});
	}
	
	var publish = function(){
		$("body").addClass("working");
		var key = $.now();
		var data = [{"id":$("#poll_id").val(), "action":"publish"}];
		$.ajax({
			url: "process/poll.process.php?key="+key,
			type: "POST",
			data: JSON.stringify(data),
			contentType: "application/json",
			complete: function(jqXHR, status){
				if(status == "success"){
					form.unpublishBtn.show();
					form.publishBtn.hide();
					$(".draftDisclaimer").hide();
					//runManifest();
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var unpublish = function(){
		$("body").addClass("working");
		var key = $.now();
		var data = [{"id":$("#poll_id").val(), "action":"unpublish"}];
		$.ajax({
			url: "process/poll.process.php?key="+key,
			type: "POST",
			data: JSON.stringify(data),
			contentType: "application/json",
			complete: function(jqXHR, status){
				if(status == "success"){
					form.unpublishBtn.hide();
					form.publishBtn.show();
					$(".draftDisclaimer").show();
					//runManifest();
				}
				$("body").removeClass("working");
			}
		});
	}
	
	var checkForNew = function(){
		if($(".admin-question").length == 1){
			$(".accordion-inner").append(templates.question);
			$(".new-question", $(".accordion-inner.english")).removeClass("new-question").attr("data-question-id", $.now()).attr("data-lang", "english");
			$(".new-question", $(".accordion-inner.es")).removeClass("new-question").attr("data-question-id", $.now()).attr("data-lang", "spanish");
		}
	}
	
	var init = function(){
		poll.id = uid;
		checkForNew();
		setDomVals();
		bindActions();
		bindDynamicActions();
	}
	
	init();
	
}