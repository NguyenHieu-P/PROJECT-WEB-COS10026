<?php
// $current_page comes from header.inc: home, jobs, about or apply.
// The matching link gets class "active" and aria-current="page".
$current_page = isset($current_page) ? $current_page : "home";
?>
<nav aria-label="Main navigation" class="main-nav">
    <div class="nav-dropdown">
     <a <?php echo $current_page === "home" ? 'aria-current="page" ' : ''; ?>class="nav-link<?php echo $current_page === "home" ? ' active' : ''; ?>" href="index.php#home">
      Home
     </a>
     <span aria-hidden="true" class="caret">
      ⌄
     </span>
     <div aria-label="Home sections" class="dropdown-menu" role="group">
      <a href="index.php#our-impact">
       Our Impact
      </a>
      <a href="index.php#career">
       Career
      </a>
      <a href="index.php#why-join-us">
       Why Join Us?
      </a>
     </div>
    </div>
    <!-- JOBS -->
    <div class="nav-dropdown">
     <a <?php echo $current_page === "jobs" ? 'aria-current="page" ' : ''; ?>class="nav-link<?php echo $current_page === "jobs" ? ' active' : ''; ?>" href="jobs.html">
      Jobs
     </a>
     <span aria-hidden="true" class="caret">
      ⌄
     </span>
     <div aria-label="Jobs sections" class="dropdown-menu" role="group">
      <a href="jobs.html#frontend-developer">
       Web Developer
      </a>
      <a href="jobs.html#ux-ui-designer">
       UX/UI Designer
      </a>
     </div>
    </div>
    <!-- ABOUT US -->
    <div class="nav-dropdown">
     <a <?php echo $current_page === "about" ? 'aria-current="page" ' : ''; ?>class="nav-link<?php echo $current_page === "about" ? ' active' : ''; ?>" href="about.html">
      About Us
     </a>
     <span aria-hidden="true" class="caret">
      ⌄
     </span>
     <div aria-label="About Us sections" class="dropdown-menu" role="group">
      <a href="about.html#our-project">
       Our Project
      </a>
      <a href="about.html#group-info">
       Get to Know
      </a>
      <a href="about.html#team-members">
       Meet Our Team
      </a>
      <a href="about.html#site-footer">
       Contact Us
      </a>
     </div>
    </div>
   </nav>
   <!-- HEADER ACTIONS -->
   <div class="header-actions">
    <a <?php echo $current_page === "apply" ? 'aria-current="page" ' : ''; ?>class="apply-button<?php echo $current_page === "apply" ? ' active' : ''; ?>" href="apply.php">
     Apply
    </a>
   </div>
  </header>
