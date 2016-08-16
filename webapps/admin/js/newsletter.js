
function Newsletter(id){
	
	var newsletter = {};
	var form = {
		cancelBtn : $("[data-role='newsletter-cancel']"),
		saveBtn : $("[data-role='newsletter-save']"),
		deleteBtn : $("[data-role='newsletter-delete']"),
		publishBtn : $("[data-role='newsletter-publish']"),
		unpublishBtn : $("[data-role='newsletter-unpublish']"),
		elem : $("form.newsletter-form")
	}
	
	var bindActions = function(){
		form.cancelBtn.click(function(){
			window.location.href = "content.php";							  
		});
		
		
		
		form.saveBtn.click(function(){
			$("body").addClass("working");	
			var key = $.now();
			var data = form.elem.serialize();
			data += "&company_news=" + encodeURIComponent(companyNews.getData());
			data += "&educational_articles=" + encodeURIComponent(educationalArticles.getData());
			data += "&calc_of_the_month=" + encodeURIComponent(calcOfTheMonth.getData());
			data += "&company_news_es=" + encodeURIComponent(companyNewsES.getData());
			data += "&educational_articles_es=" + encodeURIComponent(educationalArticlesES.getData());
			data += "&calc_of_the_month_es=" + encodeURIComponent(calcOfTheMonthES.getData());
			$.ajax({
				url: "process/newsletter.process.php?key="+key,
				type: "POST",
				data: data,
				complete: function(jqXHR, status){
					if(status == "success"){
						$(".newsletter-results").show();
						$(".newsletter-results div").html(jqXHR.responseText);
					}
					$("body").removeClass("working");
				}
			});
		});
	}
	
	
	
	
	var init = function(){
		newsletter.id = id;
		bindActions();
	}
	
	init();
	
}