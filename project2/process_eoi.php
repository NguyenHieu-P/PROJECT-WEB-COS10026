<?php
// 1. Chặn truy cập trực tiếp bằng GET (không submit qua form)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: apply.php");
    exit();
}

// Hàm làm sạch dữ liệu (Sanitisation)
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// 2. Lấy và làm sạch dữ liệu từ POST
$job_ref     = isset($_POST["job_reference"]) ? sanitise_input($_POST["job_reference"]) : "";
$first_name  = isset($_POST["first_name"]) ? sanitise_input($_POST["first_name"]) : "";
$last_name   = isset($_POST["last_name"]) ? sanitise_input($_POST["last_name"]) : "";
$dob         = isset($_POST["date_of_birth"]) ? sanitise_input($_POST["date_of_birth"]) : "";
$gender      = isset($_POST["gender"]) ? sanitise_input($_POST["gender"]) : "";
$street      = isset($_POST["street_address"]) ? sanitise_input($_POST["street_address"]) : "";
$suburb      = isset($_POST["suburb_town"]) ? sanitise_input($_POST["suburb_town"]) : "";
$state       = isset($_POST["state"]) ? sanitise_input($_POST["state"]) : "";
$postcode    = isset($_POST["postcode"]) ? sanitise_input($_POST["postcode"]) : "";
$email       = isset($_POST["email"]) ? sanitise_input($_POST["email"]) : "";
$phone       = isset($_POST["phone"]) ? sanitise_input($_POST["phone"]) : "";
$other_skills = isset($_POST["other_skills"]) ? sanitise_input($_POST["other_skills"]) : "";

// Xử lý mảng skills[]
$skills_array = isset($_POST["skills"]) && is_array($_POST["skills"]) ? $_POST["skills"] : [];
$clean_skills = [];
foreach ($skills_array as $s) {
    $clean_skills[] = sanitise_input($s);
}

// 3. Server-side Validation
$errors = [];

// Job reference: đúng 5 ký tự chữ và số (hoặc khớp FE101 / UX102)
if (empty($job_ref)) {
    $errors[] = "Job reference number is required.";
} elseif (!preg_match("/^[a-zA-Z0-9]{5}$/", $job_ref)) {
    $errors[] = "Job reference number must be exactly 5 alphanumeric characters (e.g., FE101 or UX102).";
}

// First name: tối đa 20 ký tự chữ cái
if (empty($first_name)) {
    $errors[] = "First name is required.";
} elseif (!preg_match("/^[a-zA-Z\s]{1,20}$/", $first_name)) {
    $errors[] = "First name must contain up to 20 alphabetical characters only.";
}

// Last name: tối đa 20 ký tự chữ cái
if (empty($last_name)) {
    $errors[] = "Last name is required.";
} elseif (!preg_match("/^[a-zA-Z\s]{1,20}$/", $last_name)) {
    $errors[] = "Last name must contain up to 20 alphabetical characters only.";
}

// Date of birth: dd/mm/yyyy và tuổi từ 15 đến 80
if (empty($dob)) {
    $errors[] = "Date of birth is required.";
} elseif (!preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $dob, $matches)) {
    $errors[] = "Date of birth must be in dd/mm/yyyy format.";
} else {
    $day = (int)$matches[1];
    $month = (int)$matches[2];
    $year = (int)$matches[3];
    if (!checkdate($month, $day, $year)) {
        $errors[] = "Date of birth is not a valid calendar date.";
    } else {
        $birthDate = DateTime::createFromFormat('d/m/Y', $dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($birthDate > $today || $age < 15 || $age > 80) {
            $errors[] = "Applicant age must be between 15 and 80 years old.";
        }
    }
}

// Gender
$valid_genders = ["Female", "Male", "Non-binary", "Prefer not to say"];
if (empty($gender) || !in_array($gender, $valid_genders)) {
    $errors[] = "Please select a valid gender option.";
}

// Street address: tối đa 40 ký tự
if (empty($street)) {
    $errors[] = "Street address is required.";
} elseif (strlen($street) > 40) {
    $errors[] = "Street address must not exceed 40 characters.";
}

// Suburb / Town: tối đa 40 ký tự
if (empty($suburb)) {
    $errors[] = "Suburb/Town is required.";
} elseif (strlen($suburb) > 40) {
    $errors[] = "Suburb/Town must not exceed 40 characters.";
}

// State: 1 trong 8 bang của Úc
$valid_states = ["VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT"];
if (empty($state) || !in_array($state, $valid_states)) {
    $errors[] = "Please select a valid Australian state or territory.";
}

// Postcode: đúng 4 chữ số và khớp với State
if (empty($postcode)) {
    $errors[] = "Postcode is required.";
} elseif (!preg_match("/^\d{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
} elseif (in_array($state, $valid_states)) {
    $first_digit = $postcode[0];
    $state_postcode_rules = [
        "VIC" => ["3", "8"],
        "NSW" => ["1", "2"],
        "QLD" => ["4", "9"],
        "NT"  => ["0"],
        "WA"  => ["6"],
        "SA"  => ["5"],
        "TAS" => ["7"],
        "ACT" => ["0"]
    ];
    if (!in_array($first_digit, $state_postcode_rules[$state])) {
        $errors[] = "Postcode ($postcode) does not match the selected state ($state).";
    }
}

// Email
if (empty($email)) {
    $errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address format.";
}

// Phone: 8 đến 12 chữ số (hoặc khoảng trắng)
if (empty($phone)) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match("/^[0-9\s]{8,12}$/", $phone)) {
    $errors[] = "Phone number must contain 8 to 12 digits.";
}

// Core Skills: bắt buộc chọn ít nhất 1 (hoặc nếu form yêu cầu tick cả 3 thì đổi count($clean_skills) < 3)
if (empty($clean_skills)) {
    $errors[] = "Please select at least one core skill.";
}

// Phân tách từng skill để lưu vào các cột Skill1, Skill2, Skill3 trong DB
$skill1 = isset($clean_skills[0]) ? $clean_skills[0] : "";
$skill2 = isset($clean_skills[1]) ? $clean_skills[1] : "";
$skill3 = isset($clean_skills[2]) ? $clean_skills[2] : "";

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lumina Learn | Application Status</title>
  <link rel="stylesheet" href="styles/styles.css" />
  <style>
    .status-box {
      max-width: 720px;
      margin: 4rem auto;
      padding: 2.5rem;
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
    .status-box h1 { margin-bottom: 1rem; color: #3933ad; }
    .error-list { color: #b91c1c; background: #fef2f2; padding: 1.25rem 2rem; border-radius: 8px; margin: 1.5rem 0; }
    .success-card { color: #065f46; background: #ecfdf5; padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; }
    .eoi-badge { font-family: "Courier New", monospace; font-size: 1.4rem; font-weight: bold; color: #3933ad; }
  </style>
</head>
<body class="apply-page">
  <main class="status-box">
    <?php if (!empty($errors)): ?>
      <h1>Application Incomplete</h1>
      <p>Please fix the following errors and submit your application again:</p>
      <ul class="error-list">
        <?php foreach ($errors as $err): ?>
          <li><?php echo $err; ?></li>
        <?php endforeach; ?>
      </ul>
      <a class="button button-primary" href="apply.php">Go back to Application Form</a>

    <?php else: ?>
      <?php
      // 4. Kết nối Database (dùng file settings.php của nhóm)
      require_once("settings.php");
      $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

      if (!$conn) {
          echo "<h1>Database Connection Error</h1>";
          echo "<p>Unable to connect to the database. Please try again later.</p>";
      } else {
          // 5. Auto-create bảng eoi nếu chưa tồn tại
          $create_table_sql = "CREATE TABLE IF NOT EXISTS eoi (
              EOInumber INT AUTO_INCREMENT PRIMARY KEY,
              JobReferenceNumber VARCHAR(10) NOT NULL,
              FirstName VARCHAR(20) NOT NULL,
              LastName VARCHAR(20) NOT NULL,
              DateOfBirth VARCHAR(10) NOT NULL,
              Gender VARCHAR(20) NOT NULL,
              StreetAddress VARCHAR(40) NOT NULL,
              SuburbTown VARCHAR(40) NOT NULL,
              State VARCHAR(10) NOT NULL,
              Postcode VARCHAR(4) NOT NULL,
              Email VARCHAR(100) NOT NULL,
              Phone VARCHAR(15) NOT NULL,
              Skill1 VARCHAR(50),
              Skill2 VARCHAR(50),
              Skill3 VARCHAR(50),
              OtherSkills TEXT,
              Status ENUM('New', 'Current', 'Final') DEFAULT 'New'
          )";

          if (!mysqli_query($conn, $create_table_sql)) {
              echo "<h1>Database Error</h1><p>Could not initialize table: " . mysqli_error($conn) . "</p>";
          } else {
              // 6. Insert dữ liệu bằng Prepared Statement
              $insert_sql = "INSERT INTO eoi (
                  JobReferenceNumber, FirstName, LastName, DateOfBirth, Gender,
                  StreetAddress, SuburbTown, State, Postcode, Email, Phone,
                  Skill1, Skill2, Skill3, OtherSkills, Status
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')";

              $stmt = mysqli_prepare($conn, $insert_sql);
              mysqli_stmt_bind_param(
                  $stmt,
                  "sssssssssssssss",
                  $job_ref, $first_name, $last_name, $dob, $gender,
                  $street, $suburb, $state, $postcode, $email, $phone,
                  $skill1, $skill2, $skill3, $other_skills
              );

              if (mysqli_stmt_execute($stmt)) {
                  $eoi_number = mysqli_insert_id($conn);
                  ?>
                  <h1>Application Submitted!</h1>
                  <div class="success-card">
                    <p>Thank you, <strong><?php echo $first_name . " " . $last_name; ?></strong>. Your Expression of Interest has been recorded.</p>
                    <p>Your unique EOI Reference Number is: <span class="eoi-badge">#<?php echo $eoi_number; ?></span></p>
                    <p><strong>Applied Role:</strong> <?php echo $job_ref; ?> | <strong>Status:</strong> New</p>
                  </div>
                  <a class="button button-primary" href="index.php">Return to Home</a>
                  <?php
              } else {
                  echo "<h1>Submission Failed</h1><p>Error saving your application: " . mysqli_error($conn) . "</p>";
              }
              mysqli_stmt_close($stmt);
          }
          mysqli_close($conn);
      }
      ?>
    <?php endif; ?>
  </main>
</body>
</html>