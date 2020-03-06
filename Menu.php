<!-- <div class="scrollmenu">
    <a href="Default.php">Home</a>
    <a href="NewEntry.php">New Trigger</a>
    <a href="Configure.php">Configure devices</a>
    <a href="#about">About</a>
</div> -->


<?php 

    $tmeInfo=StorageManager::LoadTimeInfo();

?>


<nav class="navbar navbar-expand-sm navbar-light" style="background-color:#FF6600;color:white;margin-bottom:25px">
  <!-- <img src="openhab-logo.png" class="img-fluid" alt="Responsive image"> -->
  <!-- <a class="navbar-brand" href="Default.php" style="color:white;padding-left: 30px;">Home</a>
  <a class="navbar-brand" href="Default.php" style="color:white;">cal</a>
  <a class="navbar-brand" href="Default.php" style="color:white;">Test</a> -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

  <div class="navbar-brand"><img src="openhab-logo-lt.png" class="img-fluid" alt="Responsive image"></div>
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0" >
    <li class="nav-item">
        <a class="nav-item nav-link" href="Default.php" style="color:white;font-size:20px">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-item nav-link" href="Calendar.php" style="color:white;font-size:20px;">Calendar</a>
      </li>
      <li class="nav-item">
      <a class="nav-item nav-link" href="Devices.php" style="color:white;font-size:20px;">Devices</a>
      </li>
    </ul>

    <span class="navbar-text" style="color:white;">
    
          <div>        
            <i class="fas fa-sun"></i>
            <em>&nbsp; <?php echo $tmeInfo->GetFormattedSunrise() ?> &nbsp; &nbsp;</em></div>
          <div>
            <i class="fas fa-moon"></i>
            <em>&nbsp; <?php echo $tmeInfo->GetFormattedSunset() ?> </em>
          </div>
    
    </span>

  </div>
  
</nav>

