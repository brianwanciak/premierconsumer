
function Quiz(uid){
	
	var quiz = {}
	var form = {
		cancelBtn : $("[data-role='quiz-cancel']"),
		saveBtn : $("[data-role='quiz-save']"),
		deleteBtn : $("[data-role='quiz-delete']"),
		publishBtn : $("[data-role='quiz-publish']"),
		unpublishBtn : $("[data-role='quiz-unpublish']"),
	}
	var templates = {
		answer : $("#answer-template").html(),
		question : $("#question-template").html()
	}
	
	var setDomVals = function(){
		quiz = {
			correctAnswer : $("[data-role='quiz-answer-correct']"),
			addAnswer : $("[data-role='add-answer']"),
			addQuestion : $("[data-role='add-question']"),
			deleteQuestion : $("[data-role='delete-question']"),
			deleteAnswer : $("[data-role='delete-answer']")
		}
	}
	
	var bindDynamicActions = function(){
		setDomVals();
		quiz.correctAnswer.unbind("click");
		quiz.addAnswer.unbind("click");
		quiz.deleteQuestion.unbind("click");
		quiz.deleteAnswer.unbind("click");
		
		quiz.correctAnswer.click(function(){
			$("tr", $(this).parents("table")).removeClass("success");
			$(this).parents("tr").addClass("success");					  
		});
		quiz.addAnswer.click(function(){
			$(this).parents("tr").before(templates.answer);	
			$("[data-role='quiz-answer-correct']", $(this).parents("table")).attr("name", "answer-" + $(this).parents("table").attr("data-question-id"));
			bindDynamicActions();
		});
		quiz.deleteQuestion.click(function(){
			if(confirm("Are you sure you want to delete this Question? This action cannot be undone.")){
				deleteItem($(this).attr("data-id"), "questions", $(this).parents("table"));
			}
		});
		quiz.deleteAnswer.click(function(){
			if(confirm("Are you sure you want to delete this Answer? This action cannot be undone.")){
				deleteItem($(this).attr("data-id"), "answers", $(this).parents("tr"));	
			}
		});
		
	}
	
	var deleteItem = function(id, table, domElem){
		if(id > 0){
			var key = $.now();
			var data = [{"id":id,"action":"delete","table":table}];	
			$("body").addClass("working");
			$.ajax({
				url: "process/quiz.process.php?key="+key,
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
			window.location.href = "quiz.php";							  
		});
		
		form.publishBtn.click(function(){
			publish();					  
		});
		
		form.unpublishBtn.click(function(){
			unpublish();					  
		});
		
		quiz.addQuestion.click(function(){
			$(this).before(templates.question);	
			$(".new-question", $(this).parent()).removeClass("new-question").attr("data-question-id", $.now()).attr("data-lang", $(this).attr("data-lang"));
			//$("[data-role='add-answer']", $(this).parents("table")).attr()
			bindDynamicActions();
		});
		
		form.deleteBtn.click(function(){
			if(confirm("WARNING! This will permenantly delete this Quiz and all contents. Are you sure?")){
				$("body").addClass("working");
				var key = $.now();
				var data = [{"id":$("#quiz_id").val(), "action":"deleteQuiz"}];
				$.ajax({
					url: "process/quiz.process.php?key="+key,
					type: "POST",
					data: JSON.stringify(data),
					contentType: "application/json",
					complete: function(jqXHR, status){
						window.location.href = "quiz.php";
						$("body").removeClass("working");
					}
				});
			}
		});
		
		form.saveBtn.click(function(){
			$("body").addClass("working");
			var key = $.now();
			var data = [{"id":$("#quiz_id").val(), 
						"enTitle": $(".quiz-en-title").val(), 
						"esTitle": $(".quiz-es-title").val(),
						"enDesc": $(".quiz-description").val(), 
						"esDesc": $(".quiz-description-es").val(),
						"relatedPoll": $(".related-poll").val(), 
						"relatedArticle": $(".related-article").val(),
						"image": $("#en-image").val(), 
						"esImage": $("#es-image").val(), 
						"esSameImage": $("#es-same-image").is(":checked"), 
						"action":"save"}];
			$(".admin-question").each(function(){
				item = {};
				item["id"] = $(this).attr("data-question-id");
				item["quiz_id"] = $("#quiz_id").val();
				item["question"] = $("[name='quiz-question']", $(this)).val();
				item["description"] = $(".quiz-question-description", $(this)).val(),
				item["lang"] = $(this).attr("data-lang");
				item["answers"] = [];
				$("tr.answer", $(this)).each(function(){
					var item2 = {}
					item2["id"] = $("[name='quiz-answer']", $(this)).attr("data-id");
					item2["answer"] = $("[name='quiz-answer']", $(this)).val();
					item2["is_correct"] = $("[data-role='quiz-answer-correct']", $(this)).is(':checked');
					item2["language"] = item["lang"];
					item["answers"].push(item2);
				});
				data.push(item);
				
			});
			$.ajax({
				url: "process/quiz.process.php?key="+key,
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
		var data = [{"id":$("#quiz_id").val(), "action":"publish"}];
		$.ajax({
			url: "process/quiz.process.php?key="+key,
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
		var data = [{"id":$("#quiz_id").val(), "action":"unpublish"}];
		$.ajax({
			url: "process/quiz.process.php?key="+key,
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
	
	var init = function(){
		quiz.id = uid;
		setDomVals();
		bindActions();
		bindDynamicActions();
	}
	
	init();
	
}