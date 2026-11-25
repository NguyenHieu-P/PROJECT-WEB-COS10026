<?php 
require_once "settings.php";

$pageTitle   = "Lumina Learn | Careers";  // tiêu đề trên tab trình duyệt
$bodyClass   = "jobs-page";               // class cho thẻ <body>
$currentPage = "jobs";                    // để menu "Home" được tô sáng

include "inc/header.inc";
include "inc/nav.inc";

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
                    <?php } ?> <!-- end while loop -->
                </div> <!-- end jobs-overview -->
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

    <?php
    include "inc/footer.inc";
    ?>
</body>
</html>
