<?php
/*
 * index.php - Trang chủ (Careers) của Lumina Learn
 * ----------------------------------------------------------
 * Trang được ghép từ 3 mảnh dùng chung:
 *   header.inc : phần <head> và thẻ mở <body>
 *   nav.inc    : thanh menu điều hướng
 *   footer.inc : footer, nút Zalo, thẻ đóng </body></html>
 * Phần ở giữa (<main>) là nội dung riêng của trang này.
 */
 
// 1. Khai báo thông tin của trang (header.inc và nav.inc sẽ dùng)
$pageTitle   = "Lumina Learn | Careers";  // tiêu đề trên tab trình duyệt
$bodyClass   = "home-page";               // class cho thẻ <body>
$currentPage = "home";                    // để menu "Home" được tô sáng
 
// 2. Chèn phần đầu trang và menu
include "inc/header.inc";
include "inc/nav.inc";
?>
 
    <main id="home-main">
        
        <!-- HOME -->
        <section class="hero" id="home">
            <div class="hero-copy">
                <p class="eyebrow">Building a Learning Platform for Everyone</p>
                <h1>WE ARE <span>HIRING!</span></h1>
                <p class="hero-description">
                    At Lumina Learn, we develop accessible and inclusive digital
                    education tools. Join our team and help transform online
                    learning into an equitable experience for all.
                </p>
                <div class="hero-actions">
                    <a class="button button-primary" href="jobs.php">Explore Jobs</a>
                    <a class="button button-secondary" href="about.php">About Us</a>
                </div>
            </div>

        </section>

                <!-- SLIDER -->

        <section class="impact-section" id="our-impact">
            <div class="image-slider" aria-label="Learning at Lumina Learn">
                
                <input type="radio" name="learning-slide" id="learning-slide-1" aria-label="Show image 1 of 3" checked>
                <input type="radio" name="learning-slide" id="learning-slide-2" aria-label="Show image 2 of 3">
                <input type="radio" name="learning-slide" id="learning-slide-3" aria-label="Show image 3 of 3">

                <div class="slider-window">
                    <div class="slider-track">

                        <img src="images/learning1.webp" alt="Two children using an online learning platform together on a laptop.">
                        <img src="images/learning2.webp" alt="A child learning online, with illustrated features for courses, personalised learning and progress tracking.">
                        <img src="images/learning3.webp" alt="A child celebrating a completed maths exercise, with illustrated learning progress charts.">
                    </div>
                         <!-- SLIDE 1 -->
                    <div class="slider-arrow arrow-one">
                        <label for="learning-slide-3" title="Previous image">‹</label>
                        <label for="learning-slide-2" title="Next image">›</label>
                    </div>
                        <!-- SLIDE 2 -->
                    <div class="slider-arrow arrow-two">
                        <label for="learning-slide-1" title="Previous image">‹</label>
                        <label for="learning-slide-3" title="Next image">›</label>
                    </div>
                        <!-- SLIDE 3 -->
                    <div class="slider-arrow arrow-three">
                        <label for="learning-slide-2" title="Previous image">‹</label>
                        <label for="learning-slide-1" title="Next image">›</label>
                    </div>
                </div>

                <div class="slider-dots" aria-label="Choose slide">
                    <label for="learning-slide-1" title="Show image 1"></label>
                    <label for="learning-slide-2" title="Show image 2"></label>
                    <label for="learning-slide-3" title="Show image 3"></label>
                </div>
            </div>

            <div class="impact-copy">
                <h2 id="impact-title">Making Learning Accessible to Everyone</h2>
                <p>
                    Education opens doors to new opportunities, but not everyone
                    has equal access to the tools and resources they need.
                </p>
                <p>
                    At Lumina Learn, we use technology and thoughtful design to
                    make online education more accessible, inclusive and engaging.
                    We believe digital learning should adapt to people, not the
                    other way around.
                </p>
                <p>
                    Our goal is to help learners overcome barriers, empower
                    educators and create better learning experiences for everyone.
                </p>
            </div>
        </section>

        <!-- SEARCH BAR -->

          <section id="find-role" class="home-role-browser">

            <!-- INTRO -->  
        
            <div class="home-role-intro">
                <h2 id="job-search-title">Find your next role.</h2>
                <p>Bring your skills to a team making learning more accessible.</p>
            </div>

            <!-- FORM --> 

            <form class="home-search-form" role="search" action="jobs.php#open-roles" method="get" aria-describedby="job-search-note">
                <label for="job-keyword">Job title or keyword</label>
                <div class="home-search-controls">
                    <input type="search" id="job-keyword" name="keywords" placeholder="e.g. front-end, design" maxlength="80">
                    <button class="button button-dark" type="submit">Search jobs <span aria-hidden="true">→</span></button>
                </div>
            </form>

            <!-- LINKS --> 

            <div class="home-role-links">
                <span>Explore our roles</span>
                <a href="jobs.php#frontend-developer">Front-End Web Developer <span aria-hidden="true">↗</span></a>
                <a href="jobs.php#ux-ui-designer">UX/UI Designer <span aria-hidden="true">↗</span></a>
            </div>
        </section>

        <!--CARREER PATH-->

        <section class="career-section" id="career">
            <div class="section-heading">
                <h2>Explore Your Career Path</h2>
                <p>
                    Different skills. One shared purpose. Discover how your
                    strengths can help make learning more inclusive.
                </p>
            </div>

            <div class="table-wrap">
                <table class="career-table">
                    <caption class="table-title">Career Opportunities at Lumina Learn</caption>
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Career Focus</th>
                            <th>Skills &amp; Interests</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <th scope="rowgroup" rowspan="2" class="department-cell">Design &amp; UX</th>
                                <td>Accessible interfaces</td>
                                <td>UI/UX design, prototyping</td>
                        </tr>
                        <tr>
                                <td>User research</td>
                                <td>Research, analysis</td>
                        </tr>
                    </tbody>
                    
                    <tbody>
                        <tr>
                            <th class="department-cell">Engineering</th>
                                <td colspan="2">Responsive learning platforms — HTML, CSS and programming</td>
                        </tr>
                        <tr>
                            <th class="department-cell">Education &amp; Content</th>
                                <td>Digital learning resources</td>
                                <td>Writing, content design</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <p class="table-note" style="font-weight: 600;">These are our career areas. <a href="jobs.php#open-roles">Visit Jobs to see current openings.</a></p>
        </section>

        <!---WHY JOIN US-->

        <section class="why-section" id="why-join-us">
            <div class="section-heading">
                <h2>WHY JOIN US?</h2>
            </div>

            <div class="benefit-grid">
                <article class="benefit-card">
                    <span class="benefit-number" aria-hidden="true">01</span>
                    <h3>Inclusive by Design</h3>
                    <p>Help create digital experiences that welcome learners of all abilities and backgrounds.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-number" aria-hidden="true">02</span>
                    <h3>Keep Growing</h3>
                    <p>Explore new ideas, develop your skills and learn through meaningful challenges.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-number" aria-hidden="true">03</span>
                    <h3>Create Real Impact</h3>
                    <p>Turn innovative ideas into practical solutions that improve digital education.</p>
                </article>

                <article class="benefit-card">
                    <span class="benefit-number" aria-hidden="true">04</span>
                    <h3>Grow Together</h3>
                    <p>Collaborate with people who value different perspectives and shared success.</p>
                </article>
            </div>
        </section>

        <section class="cta-section" id="call-to-action">
            <h2>Your Next Chapter Starts Here!</h2>
            <p>
                Bring your curiosity, creativity and passion for education to
                Lumina Learn. Together, we can help make learning more accessible
                for everyone.
            </p>
            <a class="button button-dark" href="jobs.php">View Job Descriptions Now!</a>
        </section>
    </main>
 
<?php
// 3. Chèn phần cuối trang (footer + nút Zalo + đóng thẻ)
include "inc/footer.inc";
?>
 