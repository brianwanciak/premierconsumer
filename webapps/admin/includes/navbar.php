<?php


?>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#" style="display:none">Premier Consumer</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li <?php if($page == "index"){ ?>class="active"<?php } ?>><a href="index.php">Home</a></li>
              <li class="dropdown  <?php if($page == "leads"){ ?>class="active"<?php } ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Web Apps <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <?php if(USER_GROUP == 1 || USER_GROUP == 2){ ?><li><a href="leads.php">Submission Management</a></li><?php } ?>
                  <?php if(USER_GROUP == 1 || USER_GROUP == 4){ ?> 
                  <li><a href="content.php">Content Management</a></li>  
                  <?php } ?>
                  <!--
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                  -->
                </ul>
              </li>
              <?php if(USER_GROUP == 1){ ?>
              <li class="dropdown  <?php if($page == "users"){ ?>active<?php } ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administration <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="users.php">User Managment</a></li>
                  <!--
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                  -->
                </ul>
              </li>
              <?php } ?>
              <li><a href="/livesupport" target="_blank">Chat Console</a></li>
            </ul>
            <div id="user-menu">
                <div class="btn-group">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="icon-user"></i> <?php echo $_SESSION['user_fname']." ".$_SESSION['user_lname']; ?>
                <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                <!-- dropdown menu links -->
                	<li><a href="account.php">My Account</a></li>
                    <li><a href="javascript:void(0);" class="action-logout">Logout</a></li>
                </ul>
                </div>
                
                <div class="btn-group">
                	<button class="btn action-logout">Logout</button>
                </div>
               
            </div>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<?php if(USER_GROUP == 1 || USER_GROUP == 4){ 
	$contentPages = array("poll", "quiz", "newsletters", "content");
	if(in_array($page, $contentPages)){
?>    
<div class="sub-nav">

<ul>
	
  
  <li <?php echo ($page == "content") ? 'class="active"' : ""; ?>><a href="content.php">Content Management</a></li>
  <li <?php echo ($page == "quiz") ? 'class="active"' : ""; ?>><a href="quiz.php">Quiz Management</a></li>
  <li <?php echo ($page == "poll") ? 'class="active"' : ""; ?>><a href="poll.php">Poll Management</a></li>
  <li <?php echo ($page == "newsletters") ? 'class="active"' : ""; ?>><a href="newsletter.php?task=edit">Newsletter Generator</a></li>
   
</ul>

</div>
<?php } } ?>