<?php
include "connect.php";

function getallcategoriesdata($con) {
    global $siteprefix;  
    $query = "SELECT * FROM {$siteprefix}categories WHERE parent_id IS NULL";
    $result = mysqli_query($con, $query);

    if ($result) {
        $categoryData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categoryData[] = $row;
        }
        return $categoryData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getallprofessiondata($con) {
    global $siteprefix;  
    $query = "SELECT * FROM {$siteprefix}profession WHERE parent_id IS NULL";
    $result = mysqli_query($con, $query);

    if ($result) {
        $professionData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $professionData[] = $row;
        }
        return $professionData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getallspecializationsdata($con) {
    global $siteprefix;  
    $query = "SELECT * FROM {$siteprefix}specialization WHERE parent_id IS NULL";
    $result = mysqli_query($con, $query);

    if ($result) {
        $specializationData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $specializationData[] = $row;
        }
        return $specializationData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getalleventsdata($con) {
    global $siteprefix;  
    $query = "SELECT * FROM {$siteprefix}event_types";
    $result = mysqli_query($con, $query);

    if ($result) {
        $eventData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $eventData[] = $row;
        }
        return $eventData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}

function getsubcategoriesbyparents($con, $parentIds) {
    global $siteprefix;

    // convert parent_ids (comma-separated) into int array
    $ids = array_map('intval', explode(",", $parentIds));
    $idsList = implode(",", $ids);

    // only fetch subcategories that belong to those parent_ids
    $query = "SELECT * FROM {$siteprefix}categories WHERE parent_id IN ($idsList)";
    $result = mysqli_query($con, $query);

    if ($result) {
        $subcategoryData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subcategoryData[] = $row;
        }
        return $subcategoryData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getprofessionbyparents($con, $parentIds) {
    global $siteprefix;

    // convert parent_ids (comma-separated) into int array
    $ids = array_map('intval', explode(",", $parentIds));
    $idsList = implode(",", $ids);

    // only fetch subcategories that belong to those parent_ids
    $query = "SELECT * FROM {$siteprefix}profession WHERE parent_id IN ($idsList)";
    $result = mysqli_query($con, $query);

    if ($result) {
        $professionData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $professionData[] = $row;
        }
        return $professionData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function getspecializationbyparents($con, $parentIds) {
    global $siteprefix;

    // convert parent_ids (comma-separated) into int array
    $ids = array_map('intval', explode(",", $parentIds));
    $idsList = implode(",", $ids);

    // only fetch subcategories that belong to those parent_ids
    $query = "SELECT * FROM {$siteprefix}specialization WHERE parent_id IN ($idsList)";
    $result = mysqli_query($con, $query);

    if ($result) {
        $subcategoryData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $specializationData[] = $row;
        }
        return $specializationData;
    } else {
        return ['error' => mysqli_error($con)];
    }
}


function registerVendorEndpoint($postData, $filesData) {
    global $con, $siteprefix,$siteurl, $siteName, $siteMail;
    $messages = '';

    // Sanitize input
    $title       = mysqli_real_escape_string($con, $postData['title']);
    $firstName   = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName  = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName    = mysqli_real_escape_string($con, $postData['last_name']);
    $dob         = mysqli_real_escape_string($con, $postData['dob']);
    $gender      = mysqli_real_escape_string($con, $postData['gender']);
    $nationality = mysqli_real_escape_string($con, $postData['nationality']);
    $languages   = mysqli_real_escape_string($con, $postData['languages']);
    $businessName          = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness    = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName             = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone                 = mysqli_real_escape_string($con, $postData['phone']);
    $website               = mysqli_real_escape_string($con, $postData['website']);
    $email                 = mysqli_real_escape_string($con, $postData['email']);
    $stateResidence        = mysqli_real_escape_string($con, $postData['state_residence']);
    $address               = mysqli_real_escape_string($con, $postData['address']);

    $facebook  = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter   = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin  = mysqli_real_escape_string($con, $postData['linkedin']);
    $password = $_POST['password'];
    $retypePassword = $_POST['retypePassword'];
    $passwordhash=hashPassword($password);

        // Combine first and last name
$fullName = trim($firstName . ' ' . $lastName);

// Replace spaces with hyphens and convert to lowercase
$baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $fullName), '-'));

// Start with the cleaned slug
$alt_title = $baseSlug;
$counter = 1;

// Ensure the alt_title is unique
while (true) {
    $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "users WHERE slug = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $alt_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        break; // alt_title is unique
    }

    // Append counter to baseSlug if not unique
    $alt_title = $baseSlug . '-' . $counter;
    $counter++;
}
// Handle multiple categories
    $categoryId = '';
    if (isset($postData['category']) && is_array($postData['category'])) {
        $escapedCategories = array_map(function($cat) use ($con) {
            return mysqli_real_escape_string($con, $cat);
        }, $postData['category']);
        $categoryId = implode(",", $escapedCategories);
    }

    // Handle multiple subcategories
    $subcategoryId = '';
    if (isset($postData['subcategory']) && is_array($postData['subcategory'])) {
        $escapedSubcategories = array_map(function($subcat) use ($con) {
            return mysqli_real_escape_string($con, $subcat);
        }, $postData['subcategory']);
        $subcategoryId = implode(",", $escapedSubcategories);
    }
    $services       = mysqli_real_escape_string($con, $postData['services']);
    $experience     = mysqli_real_escape_string($con, $postData['experience_years']);

    $coverage      = isset($postData['coverage']) ? mysqli_real_escape_string($con, implode(",", $postData['coverage'])) : '';
    $onsite         = mysqli_real_escape_string($con, $postData['onsite']);
    $availability   = mysqli_real_escape_string($con, $postData['availability']);
    $consent        = isset($postData['consent']) ? 1 : 0;
    $user_type      ="vendor";


        // ✅ Check if email already exists
    $checkEmail = "SELECT id FROM {$siteprefix}users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($con, $checkEmail);
    if ($result && mysqli_num_rows($result) > 0) {
        $messages .= generateMessage("Email already exists. Please use a different email.", "red");
        return array('status' => 'error', 'messages' => $messages);
    }

    // File uploads
    $targetDir = "../uploads/";

 // Photo
$photoFile = null;
if (!empty($filesData["photo"]["name"])) {
    $photoFile = uniqid() . '_' . basename($filesData["photo"]["name"]);
    $photoPath = $targetDir . $photoFile;
    move_uploaded_file($filesData["photo"]["tmp_name"], $photoPath);
}

// Business logo
$logoFile = null;
if (!empty($filesData["business_logo"]["name"])) {
    $logoFile = uniqid() . '_' . basename($filesData["business_logo"]["name"]);
    $logoPath = $targetDir . $logoFile;
    move_uploaded_file($filesData["business_logo"]["tmp_name"], $logoPath);
}

// Portfolio (multiple)
$portfolioFiles = [];
if (!empty($filesData["portfolio"]["name"][0])) {
    foreach ($filesData["portfolio"]["name"] as $key => $fileName) {
        $portfolioFile = uniqid() . '_' . basename($fileName);
        $portfolioPath = $targetDir . $portfolioFile;
        if (move_uploaded_file($filesData["portfolio"]["tmp_name"][$key], $portfolioPath)) {
            $portfolioFiles[] = $portfolioFile; // ✅ save only filename
        }
    }
}
$portfolioCSV = !empty($portfolioFiles) ? implode(",", $portfolioFiles) : '';

if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/\d/', $password)) {

         $messages = generateMessage("Password must be at least 8 characters and include upper-case, lower-case, and numbers.", "red");
        return array('status' => 'error', 'messages' => $messages);
  }	
  //check if password match									
  else if ($password !== $retypePassword ){
        $messages = generateMessage("Ooops! Password do not match.", "red");
        return array('status' => 'error', 'messages' => $messages);
  }


    // SQL insert
    $sql = "INSERT INTO {$siteprefix}users 
            (title, first_name, middle_name, last_name, photo, dob, gender, nationality, languages,
             business_name, registered_business_name, owner_name, business_logo, portfolio,
             phone, website, email, state_residence, address,
             facebook, twitter, instagram, linkedin,
             category_id, subcategory_id, services, experience_years,
             coverage, onsite, availability, consent, status, user_type, subscription_status,password,slug)
            VALUES
            ('$title', '$firstName', '$middleName', '$lastName', '$photoFile', '$dob', '$gender', '$nationality', '$languages',
             '$businessName', '$registeredBusiness', '$ownerName', '$logoFile', '$portfolioCSV',
             '$phone', '$website', '$email', '$stateResidence', '$address',
             '$facebook', '$twitter', '$instagram', '$linkedin',
             '$categoryId', '$subcategoryId', '$services', '$experience',
             '$coverage', '$onsite', '$availability', '$consent', 'pending','$user_type', 'inactive','$passwordhash','$alt_title')";

   if (mysqli_query($con, $sql)) {
    // Get the last inserted user ID
    $userId = mysqli_insert_id($con);

    // Generate a verification token
    $verificationToken = bin2hex(random_bytes(16));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 day"));

    // Insert into email_verifications table
    $sqlVerified = mysqli_query($con, "INSERT INTO {$siteprefix}email_verifications (user_id, token, expires_at, verified) 
        VALUES ($userId, '$verificationToken', '$expires_at', 0)");

    // Build verification link
    $verificationLink = "$siteurl/verify.php?id=$userId&token=$verificationToken&action=verifyemail";

    // Prepare email
    $emailMessage = "
    Thank you for registering as a vendor on {$siteName}.<br>
    Please verify your email by clicking the link below:<br><br>
    <a href='{$verificationLink}'>{$verificationLink}</a><br><br>
    This link will expire in 24 hours.
    ";

    $emailSubject = "Verify Your Email for {$siteName}";

    // Send email
    sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

    $messages .= generateMessage("Vendor registered successfully! Please check your email to verify.", "green");
    return array('status' => 'success', 'messages' => "Vendor registered successfully! Please check your email to verify.");
} else {
    $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
    return array('status' => 'error', 'messages' => $messages);
}

}


function registerTherapistEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;
    $messages = '';

    // Sanitize personal data
    $title        = mysqli_real_escape_string($con, $postData['title']);
    $firstName    = mysqli_real_escape_string($con, $postData['first_name']);
    $middleName   = mysqli_real_escape_string($con, $postData['middle_name']);
    $lastName     = mysqli_real_escape_string($con, $postData['last_name']);
    $dob          = mysqli_real_escape_string($con, $postData['dob']);
    $gender       = mysqli_real_escape_string($con, $postData['gender']);
    $nationality  = mysqli_real_escape_string($con, $postData['nationality']);
    $languages    = mysqli_real_escape_string($con, $postData['languages']);

    // Contact & business
    $businessName       = mysqli_real_escape_string($con, $postData['business_name']);
    $registeredBusiness  = mysqli_real_escape_string($con, $postData['registered_business_name']);
    $ownerName           = mysqli_real_escape_string($con, $postData['owner_name']);
    $phone               = mysqli_real_escape_string($con, $postData['phone']);
    $website             = mysqli_real_escape_string($con, $postData['website']);
    $email               = mysqli_real_escape_string($con, $postData['email']);
    $state               = mysqli_real_escape_string($con, $postData['state']);
    $lga                 = mysqli_real_escape_string($con, $postData['lga']);
    $address             = mysqli_real_escape_string($con, $postData['address']);

    // Socials
    $facebook  = mysqli_real_escape_string($con, $postData['facebook']);
    $twitter   = mysqli_real_escape_string($con, $postData['twitter']);
    $instagram = mysqli_real_escape_string($con, $postData['instagram']);
    $linkedin  = mysqli_real_escape_string($con, $postData['linkedin']);

    // Professional details
    $professionalTitle  = isset($postData['professional_title']) && is_array($postData['professional_title'])
                     ? implode(',', $postData['professional_title']) : '';
   $qualification = mysqli_real_escape_string($con, $postData['highest_qualification']);

if ($qualification === 'Other' && !empty($postData['other_qualification'])) {
    $qualification = mysqli_real_escape_string($con, $postData['other_qualification']);
}
    $institution        = mysqli_real_escape_string($con, $postData['institution']);
    $graduationYear     = mysqli_real_escape_string($con, $postData['graduation_year']);
    $certifications     = mysqli_real_escape_string($con, $postData['certifications']);
    $associations       = mysqli_real_escape_string($con, $postData['associations']);
    $experience         = mysqli_real_escape_string($con, $postData['experience']);
    $sessionFormat      = mysqli_real_escape_string($con, $postData['session_format']);
     $preferred_days = isset($_POST['preferred_days']) ? implode(', ', $_POST['preferred_days']) : '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $consultation_info = "$preferred_days | $start_time - $end_time ";
    $sessionDuration    = mysqli_real_escape_string($con, $postData['session_duration']);
    $rate               = mysqli_real_escape_string($con, $postData['rate']);
    $bio                = mysqli_real_escape_string($con, $postData['bio']);
    $professional_field   = isset($postData['professional_field']) && is_array($postData['professional_field'])
                            ? implode(',', $postData['professional_field']) : '';
    $sub_specialization = isset($postData['sub_specialization']) && is_array($postData['sub_specialization'])
                            ? implode(',', $postData['sub_specialization']) : '';
    // Multi-selects
    $specializations = isset($postData['specializations']) ? mysqli_real_escape_string($con, implode(",", $postData['specializations'])) : '';
   $workWith = '';

if (!empty($postData['work_with'])) {
    if ($postData['work_with'] === 'Other' && !empty($postData['other_work'])) {
        $workWith = mysqli_real_escape_string($con, $postData['other_work']);
    } else {
        $workWith = mysqli_real_escape_string($con, $postData['work_with']);
    }
}
    // Combine first and last name
$fullName = trim($firstName . ' ' . $lastName);

// Replace spaces with hyphens and convert to lowercase
$baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $fullName), '-'));

// Start with the cleaned slug
$alt_title = $baseSlug;
$counter = 1;

// Ensure the alt_title is unique
while (true) {
    $query = "SELECT COUNT(*) AS count FROM " . $siteprefix . "forums WHERE slug = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $alt_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        break; // alt_title is unique
    }

    // Append counter to baseSlug if not unique
    $alt_title = $baseSlug . '-' . $counter;
    $counter++;
}
    // Password
    $password        = $postData['password'] ?? '';
    $retypePassword  = $postData['retypePassword'] ?? '';
    $passwordhash    = hashPassword($password);

    $user_type = "therapist"; // ✅ user type
    $consent = isset($postData['declaration1']) ? 1 : 0;

    // ✅ Check email uniqueness
    $checkEmail = "SELECT id FROM {$siteprefix}users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($con, $checkEmail);
    if ($result && mysqli_num_rows($result) > 0) {
        $messages .= generateMessage("Email already exists. Please use another.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ✅ Validate password
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) {
        $messages .= generateMessage("Password must be at least 8 characters and include upper-case, lower-case, and a number.", "red");
        return ['status' => 'error', 'messages' => $messages];
    } elseif ($password !== $retypePassword) {
        $messages .= generateMessage("Passwords do not match.", "red");
        return ['status' => 'error', 'messages' => $messages];
    }

    // ✅ File uploads
    $targetDir = "../uploads/";

    // Reuse your existing uploadImages() function
    $uploadedFiles = uploadImages($_FILES['photos'], $targetDir);
    $uploadedLogo = uploadImages($_FILES['business_logo'], $targetDir);
    // Since this is a single file upload, just get the first item
    $photopictures = !empty($uploadedFiles) ? $uploadedFiles[0] : '';
    $businessLogo =!empty($uploadedLogo ) ? $uploadedLogo [0] : '';

    $photoFile = uploadFile($filesData['passport'], $targetDir);
    $cvFile = uploadFile($filesData['cv'], $targetDir);
    $licenseFile = uploadFile($filesData['license'], $targetDir);

    $portfolioFiles = [];
        if (!empty($filesData["portfolio"]["name"][0])) {
            foreach ($filesData["portfolio"]["name"] as $key => $fileName) {
                $portfolioFile = uniqid() . '_' . basename($fileName);
                $portfolioPath = $targetDir . $portfolioFile;
                if (move_uploaded_file($filesData["portfolio"]["tmp_name"][$key], $portfolioPath)) {
                    $portfolioFiles[] = $portfolioFile; // ✅ save only filename
                }
            }
        }
        $portfolioCSV = !empty($portfolioFiles) ? implode(",", $portfolioFiles) : '';

    // SQL insert
    $sql = "INSERT INTO {$siteprefix}users 
        (title, first_name, middle_name, last_name,photo, dob, gender, nationality, languages,
         business_name, registered_business_name, owner_name, business_logo, phone, website, email,
         state_residence, lga, address, facebook, twitter, instagram, linkedin,
         professional_title, professional_field, qualification, institution, graduation_year, certifications, associations,
         experience_years, specializations, sub_specialization, work_with, session_format, consultation_days,
         session_duration, rate, bio, cv, license, passport, consent, user_type, status, subscription_status, password,slug)
    VALUES
        ('$title', '$firstName', '$middleName', '$lastName','$photopictures', '$dob', '$gender', '$nationality', '$languages',
         '$businessName', '$registeredBusiness', '$ownerName', '$businessLogo', '$phone', '$website', '$email',
         '$state', '$lga', '$address', '$facebook', '$twitter', '$instagram', '$linkedin',
         '$professionalTitle', '$professional_field', '$qualification', '$institution', '$graduationYear', '$certifications', '$associations',
         '$experience', '$specializations','$sub_specialization', '$workWith', '$sessionFormat', '$consultation_info',
         '$sessionDuration', '$rate', '$bio', '$cvFile', '$licenseFile', '$photoFile', '$consent', '$user_type', 'pending', 'inactive', '$passwordhash','$alt_title')";

    if (mysqli_query($con, $sql)) {
        $userId = mysqli_insert_id($con);
        $verificationToken = bin2hex(random_bytes(16));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 day"));

        mysqli_query($con, "INSERT INTO {$siteprefix}email_verifications (user_id, token, expires_at, verified)
                            VALUES ($userId, '$verificationToken', '$expires_at', 0)");

        $verificationLink = "$siteurl/verify.php?id=$userId&token=$verificationToken&action=verifyemail";
        $emailMessage = "
            Thank you for registering as a therapist on {$siteName}.<br>
            Please verify your email by clicking the link below:<br><br>
            <a href='{$verificationLink}'>{$verificationLink}</a><br><br>
            This link will expire in 24 hours.
        ";
        $emailSubject = "Verify Your Therapist Account on {$siteName}";
        sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);

        $messages .= generateMessage("Therapist registered successfully! Please check your email to verify your account.", "green");
        return ['status' => 'success', 'messages' => 'Therapist registered successfully! Please check your email to verify your account.'];
    } else {
        $messages .= generateMessage("Database Error: " . mysqli_error($con), "red");
        return ['status' => 'error', 'messages' => $messages];
    }
}

/**
 * Helper to handle single file upload safely
 */
function uploadFile($fileData, $targetDir) {
    if (empty($fileData['name'])) return '';
    $fileName = uniqid() . '_' . basename($fileData['name']);
    $targetPath = $targetDir . $fileName;
    move_uploaded_file($fileData['tmp_name'], $targetPath);
    return $fileName;
}



function registerBuyerEndpoint($postData, $filesData) {
    global $con, $siteprefix, $siteurl, $siteName, $siteMail;

    // Sanitize inputs
    $title        = mysqli_real_escape_string($con, $postData['title'] ?? '');
    $firstName    = mysqli_real_escape_string($con, $postData['first_name'] ?? '');
    $middleName   = mysqli_real_escape_string($con, $postData['middle_name'] ?? '');
    $lastName     = mysqli_real_escape_string($con, $postData['last_name'] ?? '');
    $dob          = mysqli_real_escape_string($con, $postData['dob'] ?? '');
    $gender       = mysqli_real_escape_string($con, $postData['gender'] ?? '');
    $nationality  = mysqli_real_escape_string($con, $postData['nationality'] ?? '');
    $languages    = mysqli_real_escape_string($con, $postData['languages'] ?? '');
    $phone        = mysqli_real_escape_string($con, $postData['phone'] ?? '');
    $website      = mysqli_real_escape_string($con, $postData['website'] ?? '');
    $email        = mysqli_real_escape_string($con, $postData['email'] ?? '');
    $state        = mysqli_real_escape_string($con, $postData['state'] ?? '');
    $address      = mysqli_real_escape_string($con, $postData['address'] ?? '');
    $facebook     = mysqli_real_escape_string($con, $postData['facebook'] ?? '');
    $twitter      = mysqli_real_escape_string($con, $postData['twitter'] ?? '');
    $instagram    = mysqli_real_escape_string($con, $postData['instagram'] ?? '');
    $linkedin     = mysqli_real_escape_string($con, $postData['linkedin'] ?? '');
    $bio          = mysqli_real_escape_string($con, $postData['bio'] ?? '');
    $user_type    = "buyer";
    $password = $_POST['password'];
    $retypePassword = $_POST['retypePassword'];
    $passwordhash=hashPassword($password);

    // Check if email already exists
    $check = $con->prepare("SELECT id FROM {$siteprefix}users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    if ($result && $result->num_rows > 0) {
        return ['status' => 'error', 'messages' => 'Email already exists. Please use another one.'];
    }

    // File upload (photo)
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
    $photoFile = '';

    if (!empty($filesData["photo"]["name"])) {
        $photoFile = uniqid() . '_' . basename($filesData["photo"]["name"]);
        move_uploaded_file($filesData["photo"]["tmp_name"], $targetDir . $photoFile);
    }

if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/\d/', $password)) {

         $messages = generateMessage("Password must be at least 8 characters and include upper-case, lower-case, and numbers.", "red");
        return array('status' => 'error', 'messages' => $messages);
  }	
  //check if password match									
  else if ($password !== $retypePassword ){
        $messages = generateMessage("Ooops! Password do not match.", "red");
        return array('status' => 'error', 'messages' => $messages);
  }

    // Insert buyer data
    $sql = "INSERT INTO {$siteprefix}users 
            (title, first_name, middle_name, last_name, photo, dob, gender, nationality, languages,
             phone, website, email, state_residence, address,
             facebook, twitter, instagram, linkedin, bio,
             status, user_type, subscription_status,password)
            VALUES
            ('$title', '$firstName', '$middleName', '$lastName', '$photoFile', '$dob', '$gender', '$nationality', '$languages',
             '$phone', '$website', '$email', '$state', '$address',
             '$facebook', '$twitter', '$instagram', '$linkedin', '$bio',
             'pending', '$user_type', 'inactive','$passwordhash')";

    if (!mysqli_query($con, $sql)) {
        return ['status' => 'error', 'messages' => 'Database Error: ' . mysqli_error($con)];
    }

    // Generate verification token
    $userId = mysqli_insert_id($con);
    $token = bin2hex(random_bytes(16));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 day"));

    mysqli_query($con, "INSERT INTO {$siteprefix}email_verifications (user_id, token, expires_at, verified)
                        VALUES ('$userId', '$token', '$expires_at', 0)");

    $verifyLink = "$siteurl/verify.php?id=$userId&token=$token&action=verifyemail";

    $emailBody = "
        <p>Thank you for registering on <strong>{$siteName}</strong>!</p>
        <p>Please verify your email by clicking the link below:</p>
        <p><a href='{$verifyLink}'>{$verifyLink}</a></p>
        <p>This link will expire in 24 hours.</p>
    ";

    $emailSubject = "Verify Your Email for {$siteName}";
    sendEmail($email, $siteName, $siteMail, $firstName, $emailBody, $emailSubject);

    return ['status' => 'success', 'messages' => 'Registration successful! Please check your email for verification link.'];
}


function verified($con, $userId, $token) {
    global $siteprefix;
    $response = ['status' => 'error', 'messages' => ''];

    if ($userId && $token) {
        // Escape inputs
        $userId = mysqli_real_escape_string($con, $userId);
        $token  = mysqli_real_escape_string($con, $token);

        // Find pending verification
        $sql = "SELECT * FROM {$siteprefix}email_verifications 
                WHERE user_id = '$userId' AND token = '$token' AND verified = 0 
                LIMIT 1";
        $result = mysqli_query($con, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            if (strtotime($row['expires_at']) >= time()) {

                // ✅ Mark verification as used
                mysqli_query($con, "UPDATE {$siteprefix}email_verifications 
                                    SET verified = 1 
                                    WHERE id = '{$row['id']}'");

                // ✅ Fetch user type and slug
                $userQuery = mysqli_query($con, "SELECT user_type, slug FROM {$siteprefix}users WHERE id = '$userId' LIMIT 1");
                $user = mysqli_fetch_assoc($userQuery);
                $userType = strtolower(trim($user['user_type'] ?? ''));
                $userSlug = trim($user['slug'] ?? '');

                if ($userType === 'vendor') {
                    // ✅ Vendor-specific actions
                    mysqli_query($con, "
                        UPDATE {$siteprefix}users 
                        SET 
                            is_verified = 1,
                            is_active = 1,
                            status = 'active',
                            verification_token = '',
                            subscription_plan_id = 1,
                            subscription_status = 'Free'
                        WHERE id = '$userId'
                    ");

                    $response = [
                        'status' => 'success',
                        'messages' => 'Email verified successfully! You have been placed on the Free plan.',
                        'redirect' => 'vendor-pricing/' . $userSlug
                    ];
                } else {
                    // ✅ Non-vendor users
                    mysqli_query($con, "
                        UPDATE {$siteprefix}users 
                        SET 
                            is_verified = 1,
                            is_active = 1,
                            status = 'active',
                            verification_token = NULL
                        WHERE id = '$userId'
                    ");

                    $response = [
                        'status' => 'success',
                        'messages' => 'Email verified successfully! You can now log in.',
                        'redirect' => 'login.php'
                    ];
                }

            } else {
                $response['messages'] = 'Verification link has expired.';
            }
        } else {
            $response['messages'] = 'Invalid or already used verification link.';
        }
    } else {
        $response['messages'] = 'Missing parameters.';
    }

    return $response;
}


function ResetLink($postData, $siteName, $siteMail){   
    global $con, $siteprefix, $siteurl;
    $messages = '';

    // Escape user inputs for security  
    $email = $con->real_escape_string($postData['email']);

    // Validate email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in the database
        $stmt = $con->prepare("SELECT * FROM {$siteprefix}users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $firstName = $row['first_name'];

            // Generate a unique token with expiration (30 minutes)
            $token = bin2hex(random_bytes(16));
            $expires = time() + 1800; // 30 minutes from now

            // Store token and expiration in the database
            $stmtUpdate = $con->prepare("UPDATE {$siteprefix}users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmtUpdate->bind_param("sis", $token, $expires, $email);
            $stmtUpdate->execute();

            // Create reset link
            $resetLink = $siteurl . "reset-password.php?token=" . $token;

            // Send the reset link via email
            $emailMessage = "<p>You requested a password reset. Click the link below to reset your password:</p>
                             <p><a href='$resetLink'>Reset Password</a></p>
                             <p>If you didn’t request a password reset, please ignore this email.</p>";
            $emailSubject = "Password Reset Request";

            sendEmail($email, $siteName, $siteMail, $firstName, $emailMessage, $emailSubject);
            
            $messages .= generateMessage("Password reset link has been sent to your email.", "green");
            return array('status' => 'success', 'messages' => $messages);
        } else {
            $messages .= generateMessage("Email not found.", "red");
        }
    } else {
        $messages .= generateMessage("Invalid email format.", "red");
    }
  
    return array('status' => 'error', 'messages' => $messages);
}


function ResetPassword($postData) {
    global $con, $siteprefix;
    $messages = '';

    $token = $postData['token'] ?? '';
    $newPassword = $postData['password'] ?? '';
    $retypePassword = $postData['retypePassword'] ?? '';

       // Check for missing fields
    if (!$token || !$newPassword || !$retypePassword) {
        return [
            'status' => 'error',
            'messages' => 'Missing token or password fields.'
        ];
    }

    // Check if passwords match
    if ($newPassword !== $retypePassword) {
        return [
            'status' => 'error',
            'messages' => 'Passwords do not match. Please try again.'
        ];
    }


    // Validate password strength
    if (strlen($newPassword) < 8 || 
        !preg_match('/[A-Z]/', $newPassword) || 
        !preg_match('/[a-z]/', $newPassword) || 
        !preg_match('/\d/', $newPassword)) {

        return [
            'status' => 'error',
            'messages' => 'Password must be at least 8 characters and include upper-case, lower-case, and numbers.'
        ];
    }

    $currentTime = time();

    // Check token validity
    $stmt = $con->prepare("SELECT id FROM {$siteprefix}users WHERE reset_token = ? AND reset_expires > ?");
    $stmt->bind_param("si", $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return [
            'status' => 'error',
            'messages' => 'Invalid or expired token.'
        ];
    }

    $row = $result->fetch_assoc();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password and clear token
    $stmtUpdate = $con->prepare("UPDATE {$siteprefix}users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmtUpdate->bind_param("si", $hashedPassword, $row['id']);

    if ($stmtUpdate->execute()) {
        return [
            'status' => 'success',
            'messages' => 'Your password has been successfully reset.'
        ];
    } else {
        return [
            'status' => 'error',
            'messages' => 'Failed to reset password. Try again later.'
        ];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    if ($_GET['action'] == 'categorieslists') {
        $response = getallcategoriesdata($con);
    }  


     if ($_GET['action'] == 'professionlists') {
        $response =  getallprofessiondata($con);
    } 
   

     if ($_GET['action'] == 'specializationlists') {
        $response = getallspecializationsdata($con);
    }  

    if ($_GET['action'] == 'eventslists') {
        $response = getalleventsdata($con);
    }  
    
   if ($_GET['action'] === 'verifyemail') {
        $userId = $_GET['id'] ?? '';
        $token  = $_GET['token'] ?? '';
        $response = verified($con, $userId, $token);
    }
    
    if ($_GET['action'] == 'subcategorieslists' && isset($_GET['parent_ids'])) {
        $response = getsubcategoriesbyparents($con, $_GET['parent_ids']);
    }


 if ($_GET['action'] == 'subspecializationlists' && isset($_GET['parent_ids'])) {
        $response = getspecializationbyparents($con, $_GET['parent_ids']);
    }
    
     if ($_GET['action'] == 'subprofessionlists' && isset($_GET['parent_ids'])) {
        $response = getprofessionbyparents($con, $_GET['parent_ids']);
    }

    
    header('Content-Type: application/json');  
    echo json_encode($response);
}


// ✅ API Endpoint Handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

        if ($_POST['action'] == 'sendresetlink') {
        $response = ResetLink($_POST, $siteName, $siteMail);
    }

    
        if ($_POST['action'] == 'reset-link') {
        $response = ResetPassword($_POST);
    }

    if ($_POST['action'] == 'register_vendor') {
        $response = registerVendorEndpoint($_POST, $_FILES);
    }

    if ($_POST['action'] == 'therapistregister') {
        $response = registerTherapistEndpoint($_POST, $_FILES);
    }

    

      if ($_POST['action'] == 'register_user') {
        $response = registerBuyerEndpoint($_POST, $_FILES);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

}
?>
