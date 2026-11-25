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
$bodyClass   = "apply-page";               // class cho thẻ <body>
$currentPage = "apply";                    // để menu "Apply" được tô sáng
 
// 2. Chèn phần đầu trang và menu
include "header.inc";
include "nav.inc";
?>

    <main id="apply-main" tabindex="-1">
      <section class="apply-hero" aria-labelledby="apply-title">
        <h1 id="apply-title">
          Bring your skills.<br /><span>Make learning better.</span>
        </h1>
        <p>
          Apply to join Lumina Learn and help create accessible, inclusive
          digital learning experiences.
        </p>
      </section>

      <div class="apply-layout">
        <aside class="apply-guide" aria-labelledby="apply-guide-title">
          <h2 id="apply-guide-title">Find your reference</h2>
          <p>Choose FE101 or UX102 in the form below.</p>
          <a class="apply-role" href="jobs.php#frontend-developer">
            <span class="apply-reference">FE101</span>
            <strong>Front-End Web Developer</strong>
            <span>Engineering · Full-time</span>
          </a>
          <a class="apply-role" href="jobs.php#ux-ui-designer">
            <span class="apply-reference">UX102</span>
            <strong>UX/UI Designer</strong>
            <span>Design &amp; UX · Full-time</span>
          </a>
        </aside>

        <section class="apply-form-panel">
          <div class="apply-form-heading">
            <h2 id="form-title">Job application</h2>
            <p id="form-instructions" style="font-weight: 700">
              All fields are required except Other skills and CV / Resume.
            </p>
          </div>

          <!-- ĐÃ ĐỔI ACTION SANG process_eoi.php VÀ THÊM novalidate -->
          <form
            class="apply-form"
            aria-labelledby="form-title"
            action="process_eoi.php"
            method="post"
            novalidate
          >
            <fieldset class="apply-fieldset">
              <legend>
                <span class="apply-step">01</span> Your chosen role
              </legend>
              <div class="apply-field">
                <label for="job-reference">Job reference number</label>
                <input
                  type="text"
                  id="job-reference"
                  name="job_reference"
                  class="apply-reference"
                  list="job-reference-options"
                  placeholder="Type or choose a job reference"
                />
                <datalist id="job-reference-options">
                  <option value="FE101" label="Front-End Web Developer"></option>
                  <option value="UX102" label="UX/UI Designer"></option>
                </datalist>
              </div>
            </fieldset>

            <fieldset class="apply-fieldset">
              <legend>
                <span class="apply-step">02</span> Personal details
              </legend>
              <div class="apply-form-grid">
                <div class="apply-field">
                  <label for="first-name">First name</label>
                  <input
                    type="text"
                    id="first-name"
                    name="first_name"
                    autocomplete="given-name"
                  />
                  <p class="apply-hint" id="first-name-help">
                    Up to 20 alphabetical characters.
                  </p>
                </div>
                <div class="apply-field">
                  <label for="last-name">Last name</label>
                  <input
                    type="text"
                    id="last-name"
                    name="last_name"
                    autocomplete="family-name"
                  />
                </div>
                <div class="apply-field apply-full-width">
                  <label for="date-of-birth">Date of birth</label>
                  <input
                    type="text"
                    id="date-of-birth"
                    name="date_of_birth"
                    placeholder="dd/mm/yyyy"
                    autocomplete="bday"
                  />
                </div>
              </div>
              <fieldset class="apply-choice-fieldset">
                <legend>Gender</legend>
                <div class="apply-choice-list">
                  <label class="apply-choice" for="gender-female">
                    <input type="radio" id="gender-female" name="gender" value="Female" />
                    <span>Female</span>
                  </label>
                  <label class="apply-choice" for="gender-male">
                    <input type="radio" id="gender-male" name="gender" value="Male" />
                    <span>Male</span>
                  </label>
                  <label class="apply-choice" for="gender-nonbinary">
                    <input type="radio" id="gender-nonbinary" name="gender" value="Non-binary" />
                    <span>Non-binary</span>
                  </label>
                  <label class="apply-choice" for="gender-undisclosed">
                    <input type="radio" id="gender-undisclosed" name="gender" value="Prefer not to say" />
                    <span>Prefer not to say</span>
                  </label>
                </div>
              </fieldset>
            </fieldset>

            <fieldset class="apply-fieldset">
              <legend><span class="apply-step">03</span> Address</legend>
              <div class="apply-form-grid">
                <div class="apply-field apply-full-width">
                  <label for="street-address">Street address</label>
                  <input
                    type="text"
                    id="street-address"
                    name="street_address"
                    autocomplete="address-line1"
                  />
                </div>
                <div class="apply-field apply-full-width">
                  <label for="suburb">Suburb / Town</label>
                  <input
                    type="text"
                    id="suburb"
                    name="suburb_town"
                    autocomplete="address-level2"
                  />
                </div>
                <div class="apply-field">
                  <label for="state">State / Territory</label>
                  <select id="state" name="state" autocomplete="address-level1">
                    <option value="" selected disabled>Select a state or territory</option>
                    <option value="VIC">VIC</option>
                    <option value="NSW">NSW</option>
                    <option value="QLD">QLD</option>
                    <option value="NT">NT</option>
                    <option value="WA">WA</option>
                    <option value="SA">SA</option>
                    <option value="TAS">TAS</option>
                    <option value="ACT">ACT</option>
                  </select>
                </div>
                <div class="apply-field">
                  <label for="postcode">Postcode</label>
                  <input
                    type="text"
                    id="postcode"
                    name="postcode"
                    autocomplete="postal-code"
                  />
                </div>
              </div>
            </fieldset>

            <fieldset class="apply-fieldset">
              <legend>
                <span class="apply-step">04</span> Contact details
              </legend>
              <div class="apply-form-grid">
                <div class="apply-field">
                  <label for="email">Email address</label>
                  <input
                    type="text"
                    id="email"
                    name="email"
                    autocomplete="email"
                    placeholder="name@example.com"
                  />
                </div>
                <div class="apply-field">
                  <label for="phone">Phone number</label>
                  <input
                    type="text"
                    id="phone"
                    name="phone"
                    autocomplete="tel"
                  />
                </div>
              </div>
            </fieldset>

            <fieldset class="apply-fieldset">
              <legend><span class="apply-step">05</span> Skills</legend>
              <fieldset class="apply-choice-fieldset">
                <legend>Core skill checklist</legend>
                <p class="apply-hint" id="skills-help">
                  Select your core skills.
                </p>
                <div class="apply-choice-list apply-checkbox-list">
                  <label class="apply-choice" for="skill-teamwork">
                    <input type="checkbox" id="skill-teamwork" name="skills[]" value="Communication and teamwork" />
                    <span>Communication and teamwork</span>
                  </label>
                  <label class="apply-choice" for="skill-problem-solving">
                    <input type="checkbox" id="skill-problem-solving" name="skills[]" value="Problem solving" />
                    <span>Problem solving</span>
                  </label>
                  <label class="apply-choice" for="skill-accessibility">
                    <input type="checkbox" id="skill-accessibility" name="skills[]" value="Accessible and user-centred design" />
                    <span>Accessible, user-centred design</span>
                  </label>
                </div>
              </fieldset>
              <div class="apply-field apply-other-skills">
                <label for="other-skills">
                  Other skills <span class="apply-optional">(optional)</span>
                </label>
                <textarea
                  id="other-skills"
                  name="other_skills"
                  rows="5"
                  placeholder="For example: HTML, CSS, JavaScript, Figma, user research or educational technology."
                ></textarea>
              </div>
            </fieldset>

            <div class="apply-submit-area">
              <button class="button button-primary" type="submit">
                Submit application
              </button>
            </div>
          </form>
        </section>
      </div>
    </main>

    <!-- Giữ nguyên Footer của mày (hoặc thay bằng <?php include 'footer.inc'; ?>) -->
    <footer class="site-footer" id="site-footer">
      <p class="copyright">© 2026 Lumina Learn. All rights reserved.</p>
    </footer>
  </body>
</html>