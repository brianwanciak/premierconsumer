<?php
define('APP_CHECK',true);

$page = "content";

require_once("includes/functions.php");
require_once("includes/authentication.php");

if(USER_GROUP == 2){
	header("Location: leads.php");
}


$task = getVar("task");

if($task == "edit"){

	//$poll = new Poll(getVar("uid"));
	//$poll = $poll->poll;
	
}else{

	//$polls = new Polls();

}

function getCategories($section){
	$basePath = "../../content";
	$filename = (file_exists($basePath."/".$section."/en/categories.draft.xml")) ? "categories.draft.xml" : "categories.xml";
	$content = simplexml_load_file($basePath."/".$section."/en/".$filename) or die("Error: Cannot create object");
	return $content->children();
}



?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Content Management - ";
		require_once("includes/headlibs.php"); 
	?>
    <link href="css/skin-win8/ui.easytree.css" rel="stylesheet" media="screen">
    <link href="css/croppic.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="js/jquery.easytree.js"></script>
    <script type="text/javascript" src="js/content.js?v=3"></script>
    <script type="text/javascript" src="js/croppic.min.js"></script>
    <script src="ckeditor/ckeditor.js"></script>
  </head>
  <body>
  
  	

	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
        
    <?php if($task == "edit"){ ?>
    
		<?php require_once("includes/sitenav.php"); ?>
        <div style="width:860px; margin: 0 auto; margin-left:50px">
        <div class="row-fluid" style="margin-bottom:25px">
            <div class="span6">
                
                    
            </div>
            <div class="span6" align="right">                
                <button type="button" class="btn btn-small btn-warning publish" data-role="page-publish">Activate</button>         
                <button type="button" class="btn btn-small btn-danger publish" data-role="page-unpublish">De-activate</button>             
                <button type="button" class="btn btn-small" data-role="page-cancel">Cancel</button>
                <button type="button" class="btn btn-small" data-role="page-save">Save</button>
            </div>
                        
        </div>
        
        <div class="editorContent">
            
            <div class="row-fluid">
                <span class="span12">
                    <div class="edit-content-notice">
                        Use the content explorer on the left to select a page to edit
                    </div>    
                </span>
            </div>
        
        </div>
    	</div>
    <?php }elseif($task == "article-categories"){ ?>
    
    	<div class="row" style="margin-bottom:25px">
            <div class="span6">
                    
            </div>
            <div class="span6" align="right">                          
                <button type="button" class="btn btn-small" data-role="ac-cancel">Cancel</button>
                <button type="button" class="btn btn-small" data-role="ac-save">Save</button>
            </div>
                        
        </div>
        
        <div class="editorContent">
            
        </div>
        
        <script type="text/javascript">
			new ArticleCategories();
		</script>
    
    <?php }else{ ?>
    
    
       <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span4">
                    	<h3 class="table-title">Content Management</h3>
                    </div>
                    <div class="span8" align="right">
                    </div>
                </div>
             </div>
      </div>
      
      
       <div class="row">
        	<span class="span12">

            	<div class="content-options">
                	<h4>General</h4>
                	<div class="option">

                    	<a href="content.php?task=edit"><img src="images/edit-ico.png"/><span>Edit Content</span></a>
                    </div>
                    <div class="option">
                    	<a href="poll.php"><img src="images/list-ico.png"/><span>Manage Polls</span></a>
                    </div>
                    <div class="option">
                    	<a href="quiz.php"><img src="images/list-ico.png"/><span>Manage Quizzes</span></a>
                    </div>
                    <div class="option">
                    	<a href="newsletter.php?task=edit"><img src="images/book-ico.png"/><span>Newsletter Generator</span></a>
                    </div>
                </div>
            
            	<div class="content-options">
                	<h4>Articles</h4>
                	<div class="option">
                    	<div class="edit-panel">
                        	<form class="create-article-form">
                        	<table class="table table-bordered pc-table editor-content">
                            	<tr>
                                	<td>Name</td>
                                	<td><input type="text" name="name" /></td>
                            	</tr>
                        		<tr>
                                	<td>Select Category</td>
                                	<td><select name="category">
                                    <?php 
										$cats = getCategories("articles"); 
										for($i=0;$i<count($cats);$i++){
											echo '<option value="'.$cats[$i]->en.'">'.$cats[$i]->en.'</option>';;
										}	
									?>
                                    </select></td>
                            	</tr>
                                <tr>
                                	<td></td>
                                	<td><button class="btn btn-primary" data-role="create-article">Create Article</button> <button class="btn btn-default" data-role="close-panel">Cancel</button></td>
                            	</tr>
                        	</table>
                            </form>
                        </div>
                    	<a href="javascript:void(0);" class="create-article"><img src="images/article-ico.png"/><span>Add New Article</span></a>
                    </div>
                    <div class="option">
                    	<a href="content.php?task=article-categories"><img src="images/pages-ico.png"/><span>Article Categories</span></a>
                    </div>
                    
                </div>

                <?php if(USER_GROUP == 1){ ?>
                <div class="content-options">
                    <h4>Administrative</h4>
                    <div class="option">
                        <a href="https://gator3079.hostgator.com:2083/cpsess5665632711/frontend/x3/backup/wizard-fullbackup.html" target="_blank"><img src="images/backup-ico.png"/><span>Site Backup</span></a>
                    </div>
                    
                </div>
                <?php } ?>
            </span>
       </div>
	
      
      
      
      <?php } ?>
    	
      <?php require_once("includes/footer.php"); ?>
    
    </div>
    
    
    

  </body>
</html>



	
	

	
