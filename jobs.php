<?php 
require_once "settings.php";

$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$keyword = "";

if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

if ($keyword == "") {
    $sql = "SELECT * FROM jobs";
    $result = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT * FROM jobs
            WHERE job_title LIKE ?
            OR job_reference LIKE ?";

    $stmt = mysqli_prepare($conn, $sql);

    $search = "%" . $keyword . "%";

    mysqli_stmt_bind_param($stmt, "ss", $search, $search);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
}

if (!$result) {
    die("Query failed.");
}

$job_count = mysqli_num_rows($result);


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3933ad">
    <meta name="description" content="Explore front-end development and UX/UI design careers at Lumina Learn, an inclusive educational technology company.">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Lumina Learn">
    <meta property="og:title" content="Lumina Learn | Job Opportunities">
    <meta property="og:description" content="Explore front-end development and UX/UI design careers at Lumina Learn, an inclusive educational technology company.">
    <title>Lumina Learn | Job Opportunities</title>
    <link rel="icon" href="images/favicon.ico" sizes="any">
    <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
    <link rel="stylesheet" href="styles.css">
    
    <style>
        .jobs-page .job-reference { font-family: "Courier New", monospace; }
    </style>
</head>
<body class="jobs-page">

    <!-- Sticky navigation -->
<header class="site-header">
    <a aria-label="Lumina Learn home" class="brand" href="index.html#home">
    <span class="brand-mark">
     L </span>
    <span class="brand-name"> LUMINA <span>LEARN</span></span>
   </a>

    <!-- HOME -->
    <nav class="main-nav" aria-label="Main navigation">
        <div class="nav-dropdown">
            <a class="nav-link" href="index.php#home">Home</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" role="group" aria-label="Home sections">
                <a href="index.php#our-impact">Our Impact</a>
                <a href="index.php#career">Career</a>
                <a href="index.html#why-join-us">Why Join Us?</a>
            </div>
        </div>

        <!-- JOBS -->
        <div class="nav-dropdown">
            <a class="nav-link active" href="jobs.php" aria-current="page">Jobs</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" role="group" aria-label="Jobs sections">
                <a href="jobs.php#frontend-developer">Web Developer</a>
                <a href="jobs.php#ux-ui-designer">UX/UI Designer</a>
            </div>
        </div>

        <!-- ABOUT US -->
        <div class="nav-dropdown">
            <a class="nav-link" href="about.html">About Us</a>
            <span class="caret" aria-hidden="true">⌄</span>

            <div class="dropdown-menu" role="group" aria-label="About Us sections">
                <a href="about.html#our-project">Our Project</a>
                <a href="about.html#group-info">Get to Know</a>
                <a href="about.html#team-members">Meet Our Team</a>
                <a href="about.html#site-footer">Contact Us</a>
            </div>
        </div>
    </nav>

    <!-- HEADER ACTIONS -->
    <div class="header-actions">
        <a class="apply-button" href="apply.html">Apply</a>

        <a class="menu-icon" href="#site-footer" aria-label="Go to footer">
            <span></span>
            <span></span>
            <span></span>
        </a>
    </div>
</header>
    <!-- main content -->
    <main id="jobs-main" tabindex="-1">
        <section class="jobs-hero" aria-labelledby="jobs-title">
            <div class="jobs-hero-copy">
                <h1 id="jobs-title">Build a brighter<br><span>future of learning</span></h1>
                <p>Bring your skills to a team making digital education more accessible, inclusive and meaningful. Help us build a learning platform that works for everyone.</p>
                <a class="button button-primary" href="#open-roles">Explore available jobs <span aria-hidden="true">↓</span></a>
            </div>
        </section>
        <!-- open roles -->
        <section class="jobs-content section-anchor" id="open-roles" aria-labelledby="open-roles-title">
            <div class="jobs-heading">
                <div>
                    <h2 id="open-roles-title">Our open opportunities</h2>
                </div>
                <!-- php -->
                <p class="jobs-count" style="font-weight: 700;">
                    <?php echo $job_count; ?> open roles
                </p>
            </div>
            <!-- search form -->
            <form action="jobs.php" method="get" class="jobs-search">
                <label for="keyword">Search for a job:</label>
                <input type="search" id="keyword" name="keyword" placeholder="Enter job title or reference" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="button button-primary">Search</button>
            </form>
            
            <div class="jobs-layout">
                <!-- aside benefits and support -->
                <aside class="jobs-aside" aria-labelledby="jobs-aside-title">
                    <h2 id="jobs-aside-title">Why Lumina Learn?</h2>
                    <ul class="jobs-benefits">
                        <li><strong>Purposeful work</strong><span>Help remove barriers to digital education.</span></li>
                        <li><strong>Flexible collaboration</strong><span>Balance focused work with team days in Melbourne.</span></li>
                        <li><strong>Room to grow</strong><span>Learn through mentoring, feedback and professional development.</span></li>
                        <li><strong>Different perspectives</strong><span>Work with people who value inclusive design and shared success.</span></li>
                    </ul>
                    <section class="jobs-aside-note" aria-labelledby="adjustments-title">
                        <h3 id="adjustments-title">Need an adjustment?</h3>
                        <p>Contact our team to discuss an accessible application format or interview adjustment.</p>
                        <a href="mailto:info@luminalearn.com">info@luminalearn.com</a>
                    </section>
                </aside>
                <!-- job overview show basic infomation 1-->
                <div class="jobs-list jobs-overview" 
                    role = "group" aria-label="Available jobs">
                    <?php
                    if ($job_count == 0) {
                        echo "<p class='no-jobs'>No matching jobs found. Try another keyword.</p>";
                    }
                    ?>
                    <?php 
                    while ($job = mysqli_fetch_assoc($result)) {
                    ?>
                    <article class = "jobs-overview-card section-anchor"
                            id = "<?php echo htmlspecialchars($job['job_slug']); ?>" >
                        <p class = "section-kicker">
                            <?php echo htmlspecialchars($job['job_reference']); ?>
                        </p>

                        <h3>
                            <?php echo htmlspecialchars($job['job_title']); ?>
                        </h3>

                        <dl>
                            <div>
                                <dt>Location</dt>
                                <dd> <?php echo htmlspecialchars($job['location']); ?></dd>
                            </div>
                            
                            <div>
                                <dt>Work arrangement</dt>
                                <dd> <?php echo htmlspecialchars($job['work_arrangement']); ?></dd>
                            </div>

                            <div class = "jobs-overview-salary">
                                <dt>Salary</dt>
                                <dd> <?php echo htmlspecialchars($job['salary']); ?>
                                    <span>
                                        <?php echo htmlspecialchars($job['salary_note']); ?>
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <details class = "job-disclosure">
                            <summary>
                                <span class = "job-show-label">View role details</span>
                                <span class = "job-hide-label">Hide role details</span>
                                <span class = "job-disclosure-icon" aria-hidden="true"></span>
                            </summary>

                            <div class = "job-expanded-content">
                                <header class = "job-detail-header">
                                    <p class = "section-kicker">
                                        <?php echo htmlspecialchars($job['department']); ?>
                                    </p>

                                    <p class = "job-reference">
                                        Reference:
                                        <strong>
                                        <?php echo htmlspecialchars($job['job_reference']); ?>
                                        </strong>
                                    </p>

                                    <p>
                                        <?php echo htmlspecialchars($job['description']); ?>
                                    </p>
                                </header>

                                <dl class = "job-meta">
                                    <div>
                                        <dt>Salary</dt>
                                        <dd> <?php echo htmlspecialchars($job['salary']); ?>
                                            <?php echo htmlspecialchars($job['salary_note']); ?>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt>Reports to</dt>
                                        <dd> <?php echo htmlspecialchars($job['reports_to']); ?></dd>
                                    </div>

                                    <div>
                                        <dt>Location</dt>
                                        <dd> <?php echo htmlspecialchars($job['location']); ?></dd>
                                    </div>

                                    <div>
                                        <dt>Employment</dt>
                                        <dd> <?php echo htmlspecialchars($job['employment_type']); ?></dd>
                                    </div>
                                </dl>

                                <section class = "job-detail">
                                    <h4>Key responsibilities</h4>
                                    <ol>
                                        <?php  
                                        $items = explode("\n", $job['responsibilities']);
                                        foreach ($items as $item) {
                                           if (trim($item) !== '') {
                                               echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                           }
                                        }
                                        ?>
                                    </ol>
                                </section>

                                <section class = "job-detail">
                                    <h4>Essential requirements</h4>
                                    <ul>
                                        <?php  
                                        $items = explode("\n", $job['essential_requirements']);
                                        foreach ($items as $item) {
                                           if (trim($item) !== '') {
                                               echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                           }
                                        }
                                        ?>
                                    </ul>
                                </section>

                                <section class = "job-detail">
                                    <h4>Preferable requirements</h4>
                                    <ul>
                                        <?php  
                                        $items = explode("\n", $job['preferable_requirements']);
                                        foreach ($items as $item) {
                                           if (trim($item) !== '') {
                                               echo '<li>' . htmlspecialchars(trim($item)) . '</li>';
                                           }
                                        }
                                        ?>
                                    </ul>
                                </section>

                                <footer class = "job-detail-footer">
                                    <a class = "button button-primary" href = "apply.html">Apply for this role</a>
                                    <p> Use reference
                                        <strong class = "job-reference">
                                            <?php echo htmlspecialchars($job['job_reference']); ?>
                                        </strong>
                                        in your application form.
                                    </p>
                                </footer>
                            </div>
                        </details>

                    </article>
                    <?php } ?>
                </div>  
            </div>
                    <section class="jobs-application-steps section-anchor" aria-labelledby="application-steps-title">
                        <h2 id="application-steps-title">How to apply</h2>
                        <ol class="jobs-apply-steps" role="list">
                            <li><span class="jobs-step-number" aria-hidden="true">01</span><div><h3>Choose a role</h3><p>Read the details and note its reference: FE101 or UX102.</p></div></li>
                            <li><span class="jobs-step-number" aria-hidden="true">02</span><div><h3>Complete the form</h3><p>Enter the job reference, your details and skills. The CV field is optional.</p></div></li>
                            <li><span class="jobs-step-number" aria-hidden="true">03</span><div><h3>Review and submit</h3><p>Check your entries and correct any highlighted errors before submitting.</p></div></li>
                        </ol>
                        <div class="jobs-steps-footer">
                        <a class="text-link" href="apply.html">Open application form <span aria-hidden="true">↗</span></a>
                        </div>
                    </section>
        </section>

        <section class="jobs-inclusion" aria-labelledby="inclusion-title">
            <div>
                <h2 id="inclusion-title">Your perspective belongs here</h2>
                <p>We welcome applications from Aboriginal and Torres Strait Islander peoples, people with disability and people from diverse cultural backgrounds. We offer reasonable adjustments during recruitment and value the different experiences each person brings.</p>
            </div>
            <div>
                <h3>Acknowledgement of Country</h3>
                <p>We acknowledge the Traditional Custodians of the lands on which we work and learn. We pay our respects to Elders past and present and recognise the continuing connection of Aboriginal and Torres Strait Islander peoples to land, waters and community.</p>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="site-footer">
        <div class="footer-brand">
            <a class="brand brand-light" href="index.php#home">
                <span class="brand-mark">L</span>
                <span class="brand-name">LUMINA<span>LEARN</span></span>
            </a>
            <p>Building a Learning Platform for Everyone</p>
            <p>Making digital education accessible, inclusive and meaningful</p>
            
            <div class="footer-social">
                <a class="footer-social-icon" href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook (opens in a new tab)"><img src="images/social-facebook.svg" alt="" width="22" height="22"></a>
                <a class="footer-social-icon" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram (opens in a new tab)"><img src="images/social-instagram.svg" alt="" width="22" height="22"></a>
            </div>
        </div>
        <div class="footer-links">
            <h2>Quick Links</h2>
            <a href="index.html#home">Home</a>
            <a href="jobs.php" aria-current="page">Jobs</a>
            <a href="apply.html">Apply</a>
            <a href="about.html">About</a>
        </div>
        <div class="footer-resources">
            <h2>Project Resources</h2>
            
            <p>Jira Project: <a href="https://hyo2108.atlassian.net/jira/software/projects/SCRUM/boards/1" target="_blank" rel="noopener noreferrer">COS10026 Jira board</a></p>
            <p>GitHub Repository: <a href="https://github.com/NguyenHieu-P/PROJECT-WEB-COS10026" target="_blank" rel="noopener noreferrer">PROJECT-WEB-COS10026</a></p>
            <p>Live website: <a href="https://voliad.github.io/test-web-tech/" target="_blank" rel="noopener noreferrer">View Lumina Learn</a></p>
            <p>Email: <a href="mailto:info@luminalearn.com">info@luminalearn.com</a></p>
        </div>
        <p class="copyright">© 2026 Lumina Learn. All rights reserved.</p>
    </footer>
     <a aria-label="Message Lumina Learn on Messenger (opens in a new tab)" class="messenger-float" href="https://m.me/61596296752214" rel="noopener noreferrer" target="_blank">
    <img alt="" height="52" src="images/messenger-icon.svg" width="52"/>
    </a>
</body>
</html>
