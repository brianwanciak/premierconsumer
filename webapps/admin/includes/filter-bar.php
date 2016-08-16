<table class="table pc-table no-border">
<tr class="no-border  no-padding">      
	<td class="no-border no-padding">
    
    
 
            <div class="btn-toolbar">
                <div class="btn-group"><button class="btn <?php if($filter == "user_id" && $filterVal == $_SESSION['user_id']){echo "disabled";} ?>" id="onlyMyLeads" type="button" rel="<?php echo $_SESSION['user_id']; ?>">Only My Leads</button></div>
                <div class="btn-group">
					<?php 
						$userIdVal = ($filter == "user_id") ? $filterVal : 0;
						echo userFilter($userIdVal); 
					?>
                </div>
                <div class="btn-group">
					<?php 
						$statusIdVal = ($filter == "status_id") ? $filterVal : 0;
						echo statusFilter($statusIdVal); 
					?>
                </div>
                <div class="btn-group">
                	<button class="btn btn-small btn-inverse link-btn" id="clearFilters" type="button" rel="leads.php">Clear Filters</button>
                </div>
            </div>
     
     </td>
 </tr>
 <tr>
     <td class="no-border no-padding">
            <div align="left" style="margin-top:15px">
            <table class="table table-bordered auto-width pc-table no-margin">
                <tr> 
                    <td>Search</td>
                    <td>
                    	<select id="filterSearchField">
                        	<option value="all">---search all fields---</option>
                        	<option value="name" <?php if($freeSearch && $filter == "name"){echo "selected='selected'";}?>>Name</option>
                            <option value="email" <?php if($freeSearch && $filter == "email"){echo "selected='selected'";}?>>Email</option>
                            <option value="h_phone;c_phone;w_phone" <?php if($freeSearch && $filter == "h_phone;c_phone;w_phone"){echo "selected='selected'";}?>>Phone</option>
                            <option value="client_id" <?php if($freeSearch && $filter == "client_id"){echo "selected='selected'";}?>>Client ID</option>
				<option value="ip" <?php if($freeSearch && $filter == "ip"){echo "selected='selected'";}?>>IP Address</option>                        
</select>
                    </td>
                    <td>for</td>
                    <td><input type="text" id="filterSearchValue" class="span2" style="width:300px" value="<?php if($freeSearch){echo $filterVal;} ?>"></td>
                    <td><button class="btn btn-primary" id="filterSearchSubmit" onclick="return false;">Submit</button></td>
                </tr>
            </table>
            </div>
    </td>       
</tr>      
<tr>
     <td class="no-border no-padding">
            <div align="left" style="margin-top:15px">
            <table class="table table-bordered auto-width pc-table no-margin">
                <tr> 
                    <td>Date Range</td>
                    <td>
                    	<div class="control-group"><input type="text" id="dp1" data-date-format="mm/dd/yyyy" name="date1" value="<?php echo $date1; ?>" class="span2 datepicker"></div>
                    </td>
                    <td>to</td>
                    <td><div class="control-group"><input type="text" id="dp2" data-date-format="mm/dd/yyyy" name="date2" value="<?php echo $date2; ?>" class="span2 datepicker"></div></td>
                    
                    <td><button class="btn" id="filterDateClear" onclick="return false;">Clear</button></td>
                    <td><button class="btn btn-primary" id="filterDateSubmit" onclick="return false;">Submit</button></td>
                </tr>
            </table>
            </div>
    </td>       
</tr>      
</table>


<script type="text/javascript">
	$('#dp1, #dp2').datepicker().on('changeDate', function(ev){
		$("div.datepicker").hide();
	});
</script>