<?php
define('APP_CHECK',true);

$page = "users";

require_once("includes/functions.php");
require_once("includes/authentication.php");

$msgSuccess = postVar("msgSuccess");
$msgError = postVar("msgError");
$redirect = false;
$task = getVar("task");
$data = false;

if($task == "save"){

	$data = $_POST['data'];
	$uid = postVar("uid");
	
	if($uid == 0){
		$redirect = "users.php";
		$check = getVal("user", "id", "email", $data['email']);
		if($check > 0){
			$msgError = "A user with that email already exists";
		}else{
			foreach($data as $field => $value){
				$arr[] = $field;
				$arr2[] = $value;
			}
			$tmpPass = strtolower(substr($data["fname"], 0, 1).$data["lname"]);
			$query = "INSERT INTO user (`".implode("`, `", $arr)."`, `password`) VALUES ('".implode("', '", $arr2)."', md5('".$tmpPass."'))";
			$result = dbQuery($query);
			if($result == 0){
				$msgSuccess = "New user has been added";
			}else{
				$msgError = "Could not add user at this time. If you continue to see this message, please contact your administrator.";
			}
		}
	}else{
		$redirect = "users.php";
		$query = "UPDATE user SET ";
		foreach($data as $field => $value){
			$arr[] = "`".$field."` = '".$value."'";
		}
		
		$query .= implode(", ", $arr);
		$query .= " WHERE id = ".$uid;
		$result = dbQuery($query);
		if($result == 0){
			$msgSuccess = "Your changes have been saved";
		}else{
			$msgError = "There was an error saving your changes. If you continue to see this message, please contact your administrator.";
		}
	}
	
 
	$query = "SELECT * FROM user ORDER BY group";
	$users = dbQuery($query, 1);
	
	
}else if($task == "edit"){


	$uid = getVar("uid");
	if(!$uid){$uid = 0;}
	$query = "SELECT * FROM user WHERE id=".$uid." LIMIT 1";
	$user = dbQuery($query, 2);
	$groups = getGroups();
	
	
}else if($task == "delete"){

	$redirect = "users.php";
	$uid = getVar("uid");
	if(!$uid){$uid = 0;}
	$query = "UPDATE user SET `password`='deleted', `group` = 3 WHERE id=".$uid." LIMIT 1";
	$result = dbQuery($query);
	if($result == 0){
		$msgSuccess = "User has been deleted";
	}else{
		$msgError = "Error deleting this entry.";
	}
	
}else if($task == "resetPass"){

	$uid = getVar("uid");
	$redirect = "users.php?task=edit&uid=".$uid;
	if(!$uid){$uid = 0;}
	$fname = getVal("user", "fname", "id", $uid);
	$lname = getVal("user", "lname", "id", $uid);
	$tmpPass = strtolower(substr($fname, 0, 1).$lname);
	$query = "UPDATE user SET `password` = md5('".$tmpPass."') WHERE id=".$uid;
	$result = dbQuery($query);
	if($result == 0){
		$msgSuccess = "User password has been reset";
	}else{
		$msgError = "Error resetting password";
	}
	
}else{
	$query = "SELECT * FROM user";
	$users = dbQuery($query, 1);
}






?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "User Management - ";
		require_once("includes/headlibs.php"); 
	?>

  </head>
  <body>
    
    <?php if($redirect){ ?>
    	<form method="post" action="<?php echo $redirect; ?>" id="redirectForm">
        	<input type="hidden" name="msgSuccess" value="<?php echo $msgSuccess;?>" />
            <input type="hidden" name="msgError" value="<?php echo $msgError;?>" />
        </form>
    	<script type="text/javascript">
			$("#redirectForm").submit();
		</script>
    <?php } ?>

	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    	<?php if($task == 'edit'){ ?>
        
         <div class="row">
    		<div class="span12">
            	<form action="users.php?task=save" method="post" id="userForm" class="pcForm" onSubmit="validateForm(this); return false;">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span5">
                    	<h3 class="table-title">My Account</h3>
                    </div>
                    <div class="span7" align="right">
                    	<?php if($uid != 0){ ?><button class="btn btn-mini btn-danger link-del-btn" type="button" rel="users.php?task=delete&uid=<?php echo $uid; ?>">Delete</button><?php } ?>
                        <button class="btn btn-small link-btn" type="button" rel="users.php">Cancel</button>
                        <button class="btn btn-small submit-btn" type="button" rel="#userForm">Save</button>
                    </div>
                </div>
                <?php if($msgSuccess){ ?>
                    	<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgSuccess; ?></div>
                <?php } ?>
                <?php if($msgError){ ?>
                    	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgError; ?></div>
                <?php } ?>
            	    <table class="table table-bordered pc-table">
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
                        	<td>Group</td>
                            <td>
                            	<div class="control-group">
                            	<select name="data[group]" id="group">
                                	<option value="0">---please select---</option>
                            		<?php
										
										foreach($groups as $group){
											$selected = "";
											if($group["id"] == $user["group"]){
												$selected = "selected='selected'";
											}
											echo '<option value="'.$group["id"].'" '.$selected.'>'.$group["group_name"].'</option>';
										}
									
									?>
                            	</select>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                        	<td>Password</td>
                            <td>
                            <?php if($uid != 0){ ?>
                            
                            <button class="btn btn-warning btn-small link-btn" onClick="javascript:void(0);return false;" rel="users.php?task=resetPass&uid=<?php echo $user["id"];?>">Reset Password</button></td>
                            <?php }else{ ?>
                            <p>A temporary password will be created with this format: "First Initial" + "Last Name" (i.e. John Smith would be 'jsmith')</p>
                            <?php } ?>
                        </tr>
                        
                        <input type="hidden" name="uid" value="<?php echo $uid; ?>" />
                        
    				</table>
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
				if($("#group").val() == 0){
					$("#group").after("<span class='help-inline'>*Please select a group</span>");
					$("#group").parent().addClass("error");
					err++;
				}
				if(!validateEmail($("#email").val())){
					$("#email").after("<span class='help-inline'>*Please enter a valid email address</span>");
					$("#email").parent().addClass("error");
					err++;
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
                	<div class="span5">
                    	<h3 class="table-title">User Management</h3>
                    </div>
                    
                    <div class="span7" align="right">
                    	
                            <button class="btn btn-success btn-small link-btn" type="button" rel="users.php?task=edit">+ New User</button>
                        
                    </div>
                </div>
                <?php if($msgSuccess){ ?>
                    	<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgSuccess; ?></div>
                <?php } ?>
                <?php if($msgError){ ?>
                    	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $msgError; ?></div>
                <?php } ?>
            	    <table class="table table-bordered pc-table">
   						<thead>
                        	<tr>
                        		<th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Group</th>
                                <th></th>
                        	</tr>
                        </thead>
                        <?php
						
							foreach($users as $user){
								$editUrl = "users.php?task=edit&uid=".$user["id"];
								$deleteUrl = "users.php?task=delete&uid=".$user["id"];
								echo "<tr>";
								echo "<td>".$user["id"]."</td>";
								echo "<td>".$user["fname"]."</td>";
								echo "<td>".$user["lname"]."</td>";
								echo "<td>".$user["email"]."</td>";
								echo "<td>".$user["username"]."</td>";
								$tmpGroup = getVal("group", "group_name", "id", $user["group"]);
								if($user["group"] == 3){$tmpGroup = "<span style='color:#ff0000'>".$tmpGroup."</span>";}
								echo "<td>".$tmpGroup."</td>";
								echo '<td><div class="btn-group"><a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action <span class="caret"></span></a><ul class="dropdown-menu"><li><a href="'.$editUrl.'">Edit</a></li><li><a href="javascript:void(0);" class="link-del-btn" rel="'.$deleteUrl.'">Delete</a></li></ul></div></td>';
								echo "</tr>";
							}
						
						?>
                        
    				</table>
            </div>
      </div>

        
        <?php } ?>
    	
      
		
      <?php require_once("includes/footer.php"); ?>
    
    </div>

  </body>
</html>