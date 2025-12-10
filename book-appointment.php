
<?php 
$requireLogin = true;
include "header.php";

?>


<?php
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

     // API URL
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchtherapistslug&slug=" . $slug;

    // Fetch therapist details
    $data = curl_get_contents($url);

    if ($data !== false) {
        $userdetails = json_decode($data);

        if (!empty($userdetails) && isset($userdetails[0])) {
            $userdetail = $userdetails[0];

            // 🧠 Basic details
            $therapist_id = $userdetail->id ?? '';
            $fullName = htmlspecialchars(trim(($userdetail->first_name ?? '') . ' ' . ($userdetail->last_name ?? '') . ' ' . ($userdetail->middle_name ?? '')));

            // 📋 Profile info
            $nationality = $userdetail->nationality ?? '';
            $languages = $userdetail->languages ?? '';
            $website = $userdetail->website ?? '';
            $email = $userdetail->email ?? '';
            $facebook = $userdetail->facebook ?? '';
            $twitter = $userdetail->twitter ?? '';
            $instagram = $userdetail->instagram ?? '';
            $linkedin = $userdetail->linkedin ?? '';
            $phone = $userdetail->phone ?? '';
            $avgRating = floatval($userdetail->avg_rating ?? 0);
            $reviewCount = intval($userdetail->review_count ?? 0);
            $address = $userdetail->address ?? '';
            $bio = $userdetail->bio ?? '';
            $experience_years = $userdetail->experience_years ?? '';
            $specializations_names = $userdetail->specializations_names ?? '';
            $sub_specialization_names = $userdetail->subspecializations_names ?? '';
            $professional_field_names = $userdetail->professional_field_names ?? '';
            $professional_title_names = $userdetail->professional_title_names ?? '';
            $work_with = $userdetail->work_with ?? '';
            $session_format = $userdetail->session_format ?? '';
            $consultation_info = $userdetail->consultation_days ?? '';
            $session_duration = $userdetail->session_duration ?? '';
            $rate = $userdetail->rate ?? '0';
            $qualification = $userdetail->qualification ?? '';
            $associations = $userdetail->associations ?? '';
            $certifications = $userdetail->certifications ?? '';

            // 📷 Photo fallback
            $photo = !empty($userdetail->photo)
                ? $siteurl . $imagePath . $userdetail->photo
                : $siteurl . "assets/img/user.jpg";

        } else {
            echo "<div class='alert alert-warning'>No therapist found for the provided slug.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching therapist data. Please try again later.</div>";
    }
} else {
    // No slug provided — redirect to homepage
    header("Location: {$siteurl}");
    exit;
}

if (strpos($consultation_info, '|') !== false) {
    list($daysPart, $timePart) = explode('|', $consultation_info);
    $available_days = array_map('trim', explode(',', trim($daysPart)));
    list($start_time, $end_time) = array_map('trim', explode('-', trim($timePart)));
} else {
    $available_days = [];
    $start_time = '09:00';
    $end_time = '17:00';
}
?>
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card p-4 shadow-sm">

            <h3 class="mb-4 text-center">Book an Appointment with <?php echo $fullName; ?></h3>

            <form id="book-appointment-form" method="POST">
                <div id="messages" style="display:none;"></div>
              <input type="hidden" name="user_id" value="<?php echo $buyerId; ?>">
              <input type="hidden" name="therapist_id" value="<?php echo $therapist_id; ?>">

                <!-- Client Name -->
                <div class="mb-3">
                    <label class="form-label">Your Full Name</label>
                    <input type="text" name="client_name" class="form-control" placeholder="Enter your name" value="<?php echo htmlspecialchars($buyerName); ?>" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Your Email Address</label>
                    <input type="hidden" name="client_email" class="form-control" placeholder="Enter your email" value="<?php echo $buyerEmail; ?>" required>
                </div>

                <!-- Preferred Day -->
                <div class="mb-3">
                    <label class="form-label">Preferred Consultation Day</label>
                    <select name="preferred_day" class="form-select" required>
                        <option value="">Select a day</option>
                        <?php foreach ($available_days as $day): ?>
                            <option value="<?php echo htmlspecialchars($day); ?>"><?php echo htmlspecialchars($day); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="book_therapy_session">
                <!-- Preferred Time -->
                <div class="mb-3">
                    <label class="form-label">Preferred Time</label>
                    <input 
                        type="time" 
                        name="preferred_time" 
                        class="form-control" 
                        required 
                        min="<?php echo $start_time; ?>" 
                        max="<?php echo $end_time; ?>">
                    <small class="text-muted">
                        Available between <?php echo $start_time; ?> and <?php echo $end_time; ?>
                    </small>
                </div>

                <!-- Message -->
                <div class="mb-3">
                    <label class="form-label">Brief Message (optional)</label>
                    <textarea name="message" class="editor"  placeholder="Describe what you’d like to discuss..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5 py-2" id="submitBtn">Request Booking</button>
                </div>
            </form>
			
			</div>
			</div>
			</div>
			</div>
			</section>
			
			<?php include "footer.php"; ?>