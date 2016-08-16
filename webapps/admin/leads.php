<?php
define('APP_CHECK',true);

$page = "leads";

require_once("includes/functions.php");
require_once("includes/authentication.php");

if(USER_GROUP == 4){
	header("Location: index.php");
}


$msgSuccess = postVar("msgSuccess");
$msgError = postVar("msgError");
$redirect = false;
$task = getVar("task");

if($task == "edit"){

	$uid = getVar("uid");
	if(!$uid){$uid = 0;}
	$query = "SELECT *, DATE_FORMAT(DATE_ADD(`date`, interval 1 hour), '%m/%d/%Y  %l:%i %p') as submission_date, DATE_FORMAT(`last_updated`, '%m/%d/%Y') as last_updated_date FROM leads WHERE id=".$uid." LIMIT 1";
	$lead = dbQuery($query, 2);

	$lead["owner"] = ($lead['user_id'] == 0) ? "Not Assigned" : getUser($lead['user_id']);
	$lead["last_updated_by"] = ($lead['last_updated_by'] == 0) ? "n/a" : getUser($lead['last_updated_by']);
	$lead["site"] = ($lead['language'] == "spanish") ? "librededeudas.com" : "premierconsumer.org";
	$lead["form"] = getVal("forms", "name", "id", $lead["form_id"]);
	
	

}else if($task == "delete"){

	$redirect = "leads.php";
	$uid = getVar("uid");
	if(!$uid){$uid = 0;}
	$query = "DELETE FROM leads WHERE id=".$uid." LIMIT 1";
	$result = dbQuery($query);
	if($result == 0){
		$msgSuccess = "Lead has been deleted";
	}else{
		$msgError = "Error deleting this lead";
	}
	
}else if($task == "assign"){

	$redirect = "leads.php";
	$uid = getVar("uid");

	$query = "UPDATE leads SET `user_id` = ".$_SESSION['user_id'].", `last_updated_by` = ".$_SESSION['user_id'].", `status_id` = 3 WHERE id=".$uid." LIMIT 1";
	$result = dbQuery($query);
	if($result == 0){
		$msgSuccess = "Lead has been assign to you";
	}else{
		$msgError = "Error assigning lead";
	}
	
}else{

	if($task == "save"){

		$data = $_POST['data'];
		$uid = postVar("uid");
		
		$data["last_updated_by"] = $_SESSION['user_id'];
		
		//check for saving as duplicate without assigning user, then assign current user
		if($data["status_id"] == 6 && $data["user_id"] == 0){
			$data["user_id"] = $_SESSION['user_id'];
		}
		
		$redirect = "leads.php";
		$query = "UPDATE leads SET ";
		foreach($data as $field => $value){
			$arr[] = "`".$field."` = '".$value."'";
		}
		
		
		$query .= implode(", ", $arr);
		$query .= ", last_updated = NOW() WHERE id = ".$uid;
		$result = dbQuery($query);
		if($result == 0){
			$msgSuccess = "Your changes have been saved";
		}else{
			$msgError = "There was an error saving your changes. If you continue to see this message, please contact your administrator.";
		}
	
	}

	////************** Pagination

	$display = 50;
	$filter = false;
	$direction = 'DESC';
	$page = 1;
	$changeDir = 0;
	$filterVal = false;
	$orderBy = "id";
	$freeSearch = false;
	$date1 = "";
	$date2 = "";
	$latestIdPrev = (isset($_POST['latestId'])) ? $_POST['latestId'] : 0;

	
	if(isset($_POST['display'])){$display = $_POST['display'];}
	if(isset($_POST['filter'])){$filter = $_POST['filter'];}
	if(isset($_POST['direction'])){$direction = $_POST['direction'];}
	if(isset($_POST['page'])){$page = $_POST['page'];}
	if(isset($_POST['changeDir'])){$changeDir = $_POST['changeDir'];}
	if(isset($_POST['filterVal'])){$filterVal = $_POST['filterVal'];}
	if(isset($_POST['orderBy'])){$orderBy = $_POST['orderBy'];}
	if(isset($_POST['freeSearch'])){$freeSearch = $_POST['freeSearch'];}
	
	if(isset($_POST['date1'])){$date1 = $_POST['date1'];}
	if(isset($_POST['date2'])){$date2 = $_POST['date2'];}
	$start = ($page * $display) - $display;
	
	$leads = new Leads($start, $display, $filter, $direction, $changeDir, $filterVal, $orderBy, $freeSearch, $date1, $date2);
	
	$latestId = $leads->latestId;
	
	$newLeadAlert = ($latestIdPrev != 0 && $latestIdPrev < $latestId) ? true : false;
	
	$pagination = new Pagination($page, $display, $leads->totalCount);
	
	
	////************** End Pagination

}



?>

<!DOCTYPE html>
<html>
  <head>
	<?php 
		$pageTitle = "Lead Management - ";
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
    
    <?php if($newLeadAlert){ ?>
    	
        <div id="newLeads" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Alert</h3>
          </div>
          <div class="modal-body">
            <div class="alert alert-success" style="margin-bottom:0px">You have new leads ready to be viewed</div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Okay</button>
          </div>
        </div>
        <script type="text/javascript">
			$("#newLeads").modal();
		</script>
        
        
    <?php } ?>
    
       
	<?php require_once("includes/navbar.php"); ?>
    
	<div class="container">
    
    	<?php if($task == 'edit'){ ?>
        
         <div class="row">
         	<form action="leads.php?task=save" method="post" id="leadForm" class="pcForm">
    		<div class="span6" style="overflow:hidden">
            	
            	<div class="row" style="margin-bottom:15px">
                	<div class="span6">
                    	<h3 class="table-title">Submission Details </h3>
                    	
                    </div>
                </div>
            	    <table class="table table-bordered pc-table">
   						<tr>
                        	<td>Name</td>
                            <td><?php echo $lead["name"]; ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?php echo $lead["email"]; ?></td>
                        </tr>
                        <tr>
                            <td>Best Time to Contact</td>
                            <td><?php echo $lead["best_time_to_contact"]; ?></td>
                        </tr>
                        <tr>
                            <td>Total Debt</td>
                            <td><?php echo $lead["total_debt"]; ?></td>
                        </tr>
                        <tr>
                            <td>Home Phone</td>
                            <td><?php echo $lead["h_phone"]; ?></td>
                        </tr>
                        <tr>
                            <td>Work Phone</td>
                            <td><?php echo $lead["w_phone"]; ?></td>
                        </tr>
                        <tr>
                            <td>Cell Phone</td>
                            <td><?php echo $lead["c_phone"]; ?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td><?php echo $lead["address"]; ?></td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td><?php echo $lead["city"]; ?></td>
                        </tr>
                        <tr>
                            <td>Zip</td>
                            <td><?php echo $lead["zip"]; ?></td>
                        </tr>
                        <tr>
                            <td>Comments</td>
                            <td><?php echo $lead["comments"]; ?></td>
                        </tr>
                        <tr>
                            <td>IP Address</td>
                            <td><?php echo $lead["ip"]; ?></td>
                        </tr>
                        <tr>
                            <td>Refererrer</td>
                            <td><?php echo $lead["referrer"]; ?></td>
                        </tr>
                        <tr>
                            <td>Who Is</td>
                            <td><a href="<?php echo $lead["whois"]; ?>" target="_blank">WhoIs Link</a></td>
                        </tr>
                        <tr>
                        	<?php
			$leadEmailCopyText = str_replace("</td>", "\r\n", $lead["email_body"]);
			$leadEmailCopyText = str_replace("</tr>", "\r\n", $leadEmailCopyText);
			$leadEmailCopyText = strip_tags($leadEmailCopyText);
					
	  ?>
      <div id="hidden_email_body" style="display:none;"><?php echo $leadEmailCopyText; ?></div>  
        
      
                            <td style="vertical-align:top"><a href="javascript:void(0)" style="font-size:12px; position:absolute; margin-top:45px" class="copyBtn2">Copy Lead</a>Email Body </td>
                            <td class="emailBody"><?php echo $lead["email_body"]; ?></td>
                        </tr>
                       
    				</table>
                    
            </div>
            <div class="span6">
                <div class="row" style="margin-bottom:15px">
                	<div class="span6">
                    <div class="row">
                        <div class="span2">
                            <h3 class="table-title">Administration</h3>
                        </div>
                    	<div class="span4" align="right">
							<?php if($uid != 0 && USER_GROUP == 1){ ?><button class="btn btn-mini btn-danger link-del-btn" type="button" rel="leads.php?task=delete&uid=<?php echo $uid; ?>">Delete</button><?php } ?>
                            <button class="btn btn-small link-btn" type="button" rel="leads.php">Cancel</button>
                            <button class="btn btn-small" id="submission-save-btn" type="button" rel="#leadForm">Save</button>
                        </div>
                        </div>
                    </div>
                </div>
                    <table class="table table-bordered pc-table">
                    	<tr>
                            <td>Client ID</td>
                            <td><div class="control-group"><input class="input-xlarge" type="text" id="client_id" name="data[client_id]" value="<?php echo $lead["client_id"]; ?>"></div></td>
                        </tr>
                        <tr>
                        	<td>Status</td>
                            <td>
                            	<?php echo renderStatus($lead['status_id'], "edit"); ?>
                            </td>
                        </tr>
                         <tr>
                        	<td>Form</td>
                            <td>
                            	<?php echo $lead["form"]; ?>
                            </td>
                        </tr>
                         <tr>
                        	<td>Website</td>
                            <td>
                            	<?php echo $lead["site"]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Assigned To</td>
                            <td><div class="control-group"><?php echo renderOwner($lead["user_id"], "edit"); ?></div></td>
                        </tr>
                        <tr>
                        	<td>Submitted On</td>
                            <td><?php echo $lead["submission_date"]; ?></td>
                        </tr>
                        <tr>
                        	<td>Last Updated</td>
                            <td><?php echo ($lead['last_updated_date'] == "00/00/0000") ? "never" : $lead['last_updated_date'];?></td>
                        </tr>
                        <tr>
                            <td>Last Updated By</td>
                            <td><div class="control-group"><?php echo $lead["last_updated_by"]; ?></div></td>
                        </tr>
                        
                        <tr>
                        	<td>Notes</td>
                            <td><div class="control-group"><textarea style="width:96%; height: 150px" name="data[notes]"><?php echo $lead["notes"]; ?></textarea></div></td>
                        </tr>
                    </table>
            </div>
            
            <input type="hidden" value="<?php echo $uid; ?>" name="uid" /> 
            </form>
      </div>
      
      <div id="error1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Error</h3>
          </div>
          <div class="modal-body">
            <div class="alert alert-error">Please enter a valid 5-digit Client ID</div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Okay</button>
          </div>
        </div>
        
        
      
      
      <script type="text/javascript" src="js/zclip.js"></script>
      <script type="text/javascript">
	  	$(document).ready(function(){
		
		 $('a.copyBtn2').zclip({
			path:'js/ZeroClipboard.swf',
			copy:$('#hidden_email_body').html()
		 });
		});

	  </script>

        
<!-- --------------------------------------------------END EDIT HTML-------------------------------------------------- -->       
        <?php }else{ ?>
        
         <div class="row">
    		<div class="span12">
            	<div class="row" style="margin-bottom:15px">
                	<div class="span4">
                    	<h3 class="table-title">Submission Management</h3>
                    </div>
                    
                    <div class="span8" align="right">
                    	
                     
                    </div>
                </div>
                <?php if($msgSuccess){ ?>
                    	<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">x</button><?php echo $msgSuccess; ?></div>
                <?php } ?>
                <?php if($msgError){ ?>
                    	<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">x</button><?php echo $msgError; ?></div>
                <?php } ?>
                
                <form action="leads.php" method="post" name="leadsList" id="leadsList">
                	<div id="filter-options">
                    <?php include_once("includes/filter-bar.php"); ?>
                    </div>
            	    <table class="table table-bordered table-hover pc-table">
                    	<thead>
                            <tr>
                            	<th>#</th>
                                <th>Client ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Submission Date</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                         </thead>
                            <?php
							
							if($leads->totalCount > 0){
								foreach($leads->leads as $lead){
									$deleteUrl = "leads.php?task=delete&uid=".$lead["id"];
									$assignUrl = "leads.php?task=assign&uid=".$lead["id"];
									
									$editUrl = "javascript:void(0)";
									
									$canEdit = false;
									
									if($lead["status_id"] == 3){
										if(($lead["user_id"] == $_SESSION['user_id']) || ($_SESSION["user_group"] == 1)){
											$canEdit = true;
											$editUrl = "leads.php?task=edit&uid=".$lead["id"];
										}
									}else{
										$canEdit = true;
										$editUrl = "leads.php?task=edit&uid=".$lead["id"];
									}
																		
									
									echo "<tr>";
									echo "<td><a href='".$editUrl."'>".$lead["id"]."</a></td>";
									echo "<td>".$lead["client_id"]."</td>";
									echo "<td>".$lead["name"]."</td>";
									echo "<td>".$lead["email"]."</td>";
									echo "<td>".$lead["submission_date"]."</td>";
									echo "<td>".$lead["owner"]."</td>";
									echo "<td>".$lead["status"]."</td>";
									echo '<td><div class="btn-group"><a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action <span class="caret"></span></a><ul class="dropdown-menu">';
									
									if($canEdit){
										echo '<li><a href="'.$editUrl.'">Edit</a></li><li><a href="'.$assignUrl.'">Assign to Me</a></li>';
										if(USER_GROUP == 1){ 
											echo '<li><a href="javascript:void(0);" class="link-del-btn" rel="'.$deleteUrl.'">Delete</a></li>';
										}
									}else{
										echo '<li><a href="javascript:void(0);">No Actions Available</a></li>';
									}
                                    echo '</ul></div></td>';
									echo "</tr>";
								}
							}else{
								echo '<tr><td colspan="8"><div class="alert alert-error">Sorry, no results found</div></td></tr>';
							}
							
							?>
                       <tr>
                            <td colspan="8">
                              <div id="l-pagination" align="center">
                                <p>Display</p>
                                <select id="l-display" onChange="setDisplay(this.options[this.selectedIndex].value)">
                                    <option value="10" <?php if($display == 10){ echo 'selected="selected"'; } ?>>10</option>
                                    <option value="10" <?php if($display == 20){ echo 'selected="selected"'; } ?>>20</option>
                                    <option value="50" <?php if($display == 50){ echo 'selected="selected"'; } ?>>50</option>
                                    <option value="100" <?php if($display == 100){ echo 'selected="selected"'; } ?>>100</option>
                                </select>
                                <?php  echo $pagination->html; ?>
                              </div>
                            </td>
                         </tr>
                        
    				</table>
                    <input type="hidden" value="<?php echo $leads->display ;?>" name="display" id="var_display" />
                    <input type="hidden" value="<?php echo $page ;?>" name="page" id="var_page" />
                    <input type="hidden" value="<?php echo $changeDir ;?>" name="changeDir" id="var_changeDir" />
                    <input type="hidden" value="<?php echo $leads->orderBy ;?>" name="orderBy" id="var_order_by" />
                    <input type="hidden" value="<?php echo $leads->filter ;?>" name="filter" id="var_filter" />
                    <input type="hidden" value="<?php echo $freeSearch ;?>" name="freeSearch" id="freeSearch" />
                    <input type="hidden" value="<?php echo $leads->direction ;?>" name="direction" id="var_direction" />
                    <input type="hidden" value="<?php echo $leads->filterVal ;?>" name="filterVal" id="var_filter_val" />
                    <input type="hidden" value="<?php echo $latestId ;?>" name="latestId" id="latestId" />
    			</form>
            </div>
      </div>
      
      <div id="error2" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Error</h3>
          </div>
          <div class="modal-body">
            <div class="alert alert-error dateError"></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Okay</button>
          </div>
        </div>
    
      
      
      
      <script type="text/javascript">
		 var time = new Date().getTime();
		 $(document.body).bind("click", function(e) {
			 time = new Date().getTime();
		 });
	
		 function refresh() {
			 if(new Date().getTime() - time >= 60000) 
				 $("#leadsList").submit();
			 else 
				 setTimeout(refresh, 10000);
		 }
	
		 setTimeout(refresh, 10000);
	</script>
      
      <script type="text/javascript">
	  

		function setOrder(val){
			$("#var_orderBy").val(val);
			$("#var_changeDir").val(1);
			$("#leadsList").submit();
		}
		function setDisplay(val){
			$("#var_display").val(val);
			$("#var_changeDir").val(0);
			$("#leadsList").submit();
		}
		function setPage(val){
			$("#var_page").val(val);
			$("#var_changeDir").val(0);
			$("#leadsList").submit();
		}
		function setDirection(val){
			$("#var_direction").val(val);
			$("#var_changeDir").val(0);
			$("#leadsList").submit();
		}
	
	
	</script>

        
        <?php } ?>
    	
      
		
      <?php require_once("includes/footer.php"); ?>
    
    </div>
    
    
    

  </body>
</html>


<?php


class Leads{

	function __construct($start, $display, $filter, $direction, $changeDir, $filterVal, $orderBy, $freeSearch, $date1, $date2){
		
		$this->start = $start;
		$this->display = $display;
		$this->filter = $filter;
		$this->filterVal = $filterVal;
		$this->orderBy = $orderBy;
		$this->direction = $direction;
		$this->changeDir = $changeDir;
		$this->freeSearch = $freeSearch;
		$this->date1 = $date1;
		$this->date2 = $date2;
		$this->latestId = $this->getLatestId();
		$this->query = $this->buildQuery();
		$this->leads = $this->getLeads();

	}
	
	
	function getLeads(){
	
		$rows = dbQuery($this->query, 1);

		if(empty($rows)){
			return false;
		}else{
			for($i=0; $i<count($rows); $i++){
				//$rows[$i]['status'] = getVal("status", "title", "id", $rows[$i]['status_id']);
				$rows[$i]['status'] = renderStatus($rows[$i]['status_id'], "");
				$rows[$i]['owner'] = ($rows[$i]['user_id'] == 0) ? "Not Assigned" : getUser($rows[$i]['user_id']);
			}
			
		}
		
		return $rows;
	
	}
	
	function getLatestId(){
		$query = "SELECT `id` FROM leads ORDER BY `date` DESC LIMIT 1";
		$lead = dbQuery($query, 2);
		return $lead["id"];
	}
	
	
	function buildQuery(){
	
		if($this->changeDir == 1){
			if($this->direction == 'ASC'){
				$this->direction = 'DESC';
			}else{
				$this->direction = 'ASC';
			}
		}
		
		$queryCount = "SELECT count(*) as totalCount FROM leads";
		
		$query = "SELECT `id`, `name`, DATE_FORMAT(DATE_ADD(`date`, interval 1 hour), '%m/%d/%Y %l:%i %p') as submission_date, `user_id`, `status_id`, `client_id`, `email` FROM leads";
		
		// WHERE BUILDER ***************************************
		
		//echo "Filter: ".$this->filter."<br />Value: ".$this->filterVal;
		$filterCheck = " WHERE";
		if($this->filter && $this->filter != ""){
			$filterCheck = " AND";
			if($this->freeSearch){
				if(strpos($this->filter, ";")){
					$fields = explode(";", $this->filter);
					foreach($fields as $f){
						$queryArr[] = "`".$f."` LIKE '%".$this->filterVal."%'";
					}
					$where = " WHERE ".implode(" OR ", $queryArr);
				}else{
					if($this->filter == "all"){
						$all_array = array("name", "h_phone", "c_phone", "w_phone", "email", "client_id");
						foreach($all_array as $arr_val){
							$tmp_where[] = "`".$arr_val."` LIKE '%".$this->filterVal."%'";
						}
						$where = " WHERE ".implode(" || ", $tmp_where);
					}else{
						$where = " WHERE `".$this->filter."` LIKE '%".$this->filterVal."%'";
					}
					
				}
			}else{
				$where = " WHERE `".$this->filter."` = '".$this->filterVal."'";
			}
			$query .= $where;
			$queryCount .= $where;
		}

		if($this->date1 != "" && $this->date2 != ""){
			$date1 = explode("/", $this->date1);
			$date1 = $date1[2]."-".$date1[0]."-".$date1[1];
			$date2 = explode("/", $this->date2);
			$date2 = $date2[2]."-".$date2[0]."-".$date2[1];
			$dateQuery = " `date` BETWEEN CAST('".$date1."' AS DATE) AND CAST('".$date2."' AS DATE)";
			$query .= $filterCheck.$dateQuery;
			$queryCount .= $filterCheck.$dateQuery;
			
		}
		
		
		// END WHERE BUILDER ***************************************
		
		
		$query .= " ORDER BY ".$this->orderBy." ".$this->direction;
		
		$query .= " LIMIT ".$this->start.",".($this->display);
		$totalCount = dbQuery($queryCount, 2);
		$this->totalCount = $totalCount['totalCount'];
		//echo $query;
		return $query;
	}
	
	

}



class Pagination{
	
	function __construct($page, $display, $count){
		
		$this->page = $page;
		$this->display = $display;
		$this->count = $count;
		
		$this->html = $this->getPagination();
	}
	
	function getPagination(){
	
	
		$this->pages = ceil($this->count / $this->display);
		
		if($this->pages > 15){$this->pages = 15;}
		
		$html = '<div class="btn-group">';
		
		if($this->page > 1){
			$html .= '<button class="btn" onclick="setPage(\''.($this->page-1).'\')">< Prev</button>';
		}
		
		for($i=1; $i <= $this->pages; $i++){
			if($i == $this->page){
				$html .= '<button class="btn disabled">'.$i.'</button> ';
			}else{
				$html .= '<button class="btn" href="javascript:void(0);" onclick="setPage(\''.$i.'\')">'.$i.'</button> ';
			}
			
		}
		
		if($this->page < $this->pages){
			$html .= '<button class="btn" onclick="setPage(\''.($this->page+1).'\')">Next ></button>';
		}
		
		$html .= '</div>';
		
		return $html;
	}


}



?>