<?php
define('APP_CHECK',true);

$page = "account";

require_once("includes/functions.php");
require_once("includes/authentication.php");

$msgSuccess = false;
$msgError = false;
$task = getVar("task");
$data = false;

if($task == "save"){
	$data = $_POST['data'];
	$password = $_POST['password1'];
	$cp = $_POST['cp'];
	$query = "UPDATE user SET ";
	foreach($data as $field => $value){
		$arr[] = $field." = '".$value."'";
	}
	if($cp == 1){
		$arr[] = "password = md5('".$password."')";
	}
	$query .= implode(", ", $arr);
	$query .= " WHERE id = ".USER_ID;
	$result = dbQuery($query);
	if($result == 0){
		$msgSuccess = "Your changes have been saved";
	}else{
		$msgError = "There was an error saving your changes. If you continue to see this message, please contact your administrator.";
	}
}

$query = "SELECT * FROM user WHERE id = ".USER_ID." LIMIT 1";
$user = dbQuery($query, 2);






?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "My Account - ";
		require_once("includes/headlibs.php"); 
	?>
  </head>
  <body>
    

	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    	<?php if($task == 'edit'){ ?>
        
         <div class="row">
    		<div class="span12">
            	<form action="account.php?task=save" method="post" id="accountForm" class="pcForm" onSubmit="validateForm(this); return false;">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span10">
                    	<h3 class="table-title">My Account</h3>
                    </div>
                    <div class="span2" align="right">
                        <button class="btn btn-small link-btn" type="button" rel="account.php">Cancel</button>
                        <button class="btn btn-small submit-btn" type="button" rel="#accountForm">Save</button>
                    </div>
                </div>
            	    <table class="table table-bordered">
   						<tr>
                        	<td>First Name</td>
                            <td><div class="control-group"><input class="input-xlarge" type="text" id="fname" name="data[fname]" value="<?php echo $user["fname"]; ?>"></div></td>
                        </tr>
                        <tr>
                        	<td>Last Name</td>
                            <td><div class="control-group"><input class="input-xlarge" type="text" id="lname" name="data[lname]" value="<?php echo $user["lname"]; ?>"></div></td>
                        </tr>
                        <tr>
                        	<td>Email</td>
                            <td><div class="control-group"><input class="input-xlarge" type="text" id="email" name="data[email]" value="<?php echo $user["email"]; ?>"></div></td>
                        </tr>
                        <tr>
                        	<td>Username</td>
                            <td><div class="control-group"><input class="input-xlarge" type="text" id="username" name="data[username]" value="<?php echo $user["username"]; ?>"></div></td>
                        </tr>
                        <tr>
                        	<td>Password</td>
                            <td><div class="control-group"><input class="input-xlarge" type="password" id="password1" name="password1" value=""></div></td>
                        </tr>
                        <tr>
                        	<td>Re-enter Password</td>
                            <td><div class="control-group"><input class="input-xlarge" type="password" id="password2" name="password2" value=""></div></td>
                        </tr>
    				</table>
                    	<input type="hidden" value="0" name="cp" id="cp" />
                    </form>
            </div>
      </div>
      
      <script type="text/javascript">
	  		function validateForm(form){
				var err = 0;
				$("span.help-inline").remove();
				$(".control-group.error").removeClass("error");
				if($("#fname").val() == ""){
					$("#fname").after("<span class='help-inline'>*Field cannot be blank</span>");
					$("#fname").parent().addClass("error");
					err++;
				}
				if($("#lname").val() == ""){
					$("#lname").after("<span class='help-inline'>*Field cannot be blank</span>");
					$("#lname").parent().addClass("error");
					err++;
				}
				if($("#username").val() == ""){
					$("#username").after("<span class='help-inline'>*Field cannot be blank</span>");
					$("#username").parent().addClass("error");
					err++;
				}
				if(!validateEmail($("#email").val())){
					$("#email").after("<span class='help-inline'>*Please enter a valid email address</span>");
					$("#email").parent().addClass("error");
					err++;
				}
				if($("#password1").val() != "" || $("#password2").val() != ""){
					if($("#password1").val() != $("#password2").val()){
						$("#password1, #password2").after("<span class='help-inline'>*Passwords do not match</span>");
						$("#password1, #password2").parent().addClass("error");
						err++;
					}else{
						$("#cp").val(1);
					}
				}
				if(err == 0){
					form.submit();
				}
			}
	  </script>

        
        
        <?php }else{ ?>
        
         <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span10">
                    	<h3 class="table-title">My Account</h3>
                    </div>
                    
                    <div class="span2" align="right">
                    	
                        <button class="btn btn-small link-btn" type="button" rel="account.php?task=edit">Edit Profile</button>
                    </div>
                </div>
                <?php if($msgSuccess){ ?>
                    	<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgSuccess; ?></div>
                <?php } ?>
                <?php if($msgError){ ?>
                    	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgError; ?></div>
                <?php } ?>
            	    <table class="table table-bordered">
   						<tr>
                        	<td>First Name</td>
                            <td><?php echo $user["fname"]; ?></td>
                        </tr>
                        <tr>
                        	<td>Last Name</td>
                            <td><?php echo $user["lname"]; ?></td>
                        </tr>
                        <tr>
                        	<td>Email</td>
                            <td><?php echo $user["email"]; ?></td>
                        </tr>
                        <tr>
                        	<td>Username</td>
                            <td><?php echo $user["username"]; ?></td>
                        </tr>
                        <tr>
                        	<td>Password</td>
                            <td>******** [<a href="account.php?task=edit">change</a>]</td>
                        </tr>
    				</table>
            </div>
      </div>

        
        <?php } ?>
    	
      
		
      <?php require_once("includes/footer.php"); ?>
    
    </div>

  </body>
</html>