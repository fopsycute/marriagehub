<?php
include "header.php";

$eventId = isset($_GET['event_id']) ? trim($_GET['event_id']) : '';
// default values
$event = [
    'event_id' => '',
    'title' => '',
    'description' => '',
    'categories' => '',
    'subcategories' => '',
    'event_type' => '',
    'target_audience' => '',
    'delivery_format' => '',
    'pricing_type' => '',
    'is_foreign' => 0,
    'address' => '',
    'state' => '',
    'lga' => '',
    'country' => '',
    'online_link' => '',
    'hybrid_physical_address' => '',
    'hybrid_web_address' => '',
    'hybrid_state' => '',
    'hybrid_lga' => '',
    'hybrid_country' => '',
    'hybrid_foreign_address' => '',
    'status' => 'pending',
];
$images = $videos = $dates = $tickets = $videoModules = $textModules = [];

if (!empty($eventId)) {
    // fetch using your API - adjust action param if your endpoint differs
    $fetchUrl = $siteurl . "script/admin.php?action=editevent&event_id=" . urlencode($eventId);
    $raw = curl_get_contents($fetchUrl);
    $resp = json_decode($raw, true);
    // support both 'data' and 'event' keys (defensive)
    $data = $resp['data'] ?? $resp['event'] ?? null;
    if ($data) {
        // map DB keys to form-friendly arrays
        $event = array_merge($event, [
            'event_id' => $data['event_id'] ?? $eventId,
            'title' => $data['title'] ?? '',
            'user_id' => $data['user_id'] ?? '',
            'description' => $data['description'] ?? '',
            'categories' => $data['categories'] ?? '',
            'subcategories' => $data['subcategories'] ?? '',
            'event_type' => $data['event_type'] ?? '',
            'target_audience' => $data['target_audience'] ?? '',
            'delivery_format' => $data['delivery_format'] ?? '',
            'pricing_type' => $data['pricing_type'] ?? '',
            'is_foreign' => $data['is_foreign'] ?? 0,
            'address' => $data['address'] ?? '',
            'state' => $data['state'] ?? '',
            'lga' => $data['lga'] ?? '',
            'country' => $data['country'] ?? '',
            'online_link' => $data['online_link'] ?? '',
            'hybrid_physical_address' => $data['hybrid_physical_address'] ?? '',
            'hybrid_web_address' => $data['hybrid_web_address'] ?? '',
            'hybrid_state' => $data['hybrid_state'] ?? '',
            'hybrid_lga' => $data['hybrid_lga'] ?? '',
            'hybrid_country' => $data['hybrid_country'] ?? '',
            'hybrid_foreign_address' => $data['hybrid_foreign_address'] ?? '',
            'status' => $data['status'] ?? 'pending',
        ]);
        // related arrays
        $images = $resp['images'] ?? $data['images'] ?? [];
        $videos = $resp['videos'] ?? $data['videos'] ?? [];
        $dates = $resp['dates'] ?? $data['dates'] ?? [];
        $tickets = $resp['tickets'] ?? $data['tickets'] ?? [];
        $videoModules = $resp['videos'] ?? $data['videos'] ?? [];
        $textModules = $resp['texts'] ?? $data['texts'] ?? [];
        // convert some comma fields to arrays for prefill
        $categories_selected = !empty($event['categories']) ? explode(',', $event['categories']) : [];
        $subcategories_selected = !empty($event['subcategories']) ? explode(',', $event['subcategories']) : [];
        $targetAudience = !empty($event['target_audience']) ? explode(',', $event['target_audience']) : [];
    } else {
        echo "<div class='alert alert-warning'>Event not found.</div>";
    }
} else {
    // empty create form defaults
    $categories_selected = $subcategories_selected = $targetAudience = [];
}
?>
<div class="container">
  <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">Edit Event</h3>
  </div>

    <div class="row">
      <div class="col-md-12">
        <form id="eventForm" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update_event">
          <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['event_id'] ?: $eventId) ?>">
          <input type="hidden" name="user" value="<?= htmlspecialchars($event['user_id'] ?: $eventId) ?>">

          <div class="text-center mt-1" id="messages"></div>

          <!-- Event Details Card -->
          <div class="card">
            <div class="card-header"><div class="card-title">Event Details</div></div>
            <div class="card-body">
              <!-- Title -->
              <div class="form-group mb-3">
                <label for="eventTitle">Title</label>
                <input type="text" id="eventTitle" name="title" class="form-control" placeholder="Enter event title"
                       value="<?= htmlspecialchars($event['title'] ?? '') ?>" required>
              </div>

              <!-- Event ID display (readonly) -->
              <div class="form-group mb-3">
                <label for="event-id">Event ID</label>
                <input type="text" id="event-id" name="event_id_display" class="form-control" value="<?= htmlspecialchars($event['event_id']) ?>" readonly required>
              </div>

              <!-- Existing Cover Images -->
              <?php if (!empty($images)): ?>
                <div class="form-group mb-3">
                  <label>Existing Cover Images</label>
                  <div class="d-flex flex-wrap">
                    <?php foreach ($images as $img): ?>
                      <div class="position-relative m-1" style="width:120px;height:120px;">
                        <img src="<?= $siteurl ?>uploads/<?= htmlspecialchars($img) ?>" class="img-thumbnail" style="width:100%;height:100%;object-fit:cover;">
                        <a href="#" id="<?= htmlspecialchars($img) ?>" class="btn btn-danger btn-sm delete-media" style="position:absolute;top:2px;right:2px;"><i class="fa fa-trash"></i></a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Upload new cover images -->
              <div class="form-group mb-3">
                <label for="coverImage">Upload New Cover Images</label>
                <input type="file" id="coverImage" name="cover_image[]" class="form-control" accept="image/*" multiple>
              </div>

              <!-- Description -->
              <div class="form-group mb-3">
                <label for="description">Description</label>
                <textarea id="description" class="editor" name="description" placeholder="Enter event description"><?= $event['description'] ?? '' ?></textarea>
              </div>

              <!-- Categories -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label>Categories</label>
                  <select name="category[]" id="eventcategory" class="form-select select-multiple" multiple required>
                    <option value="">-- Select Category --</option>
                    <?php
                    // load categories
                    $catUrl = $siteurl . "script/register.php?action=eventcategorieslists";
                    $catRaw = curl_get_contents($catUrl);
                    $catList = $catRaw ? json_decode($catRaw) : [];
                    if (!empty($catList)) {
                        foreach ($catList as $c) {
                            $sel = in_array($c->id, $categories_selected) ? 'selected' : '';
                            echo "<option value=\"".htmlspecialchars($c->id)."\" $sel>" . htmlspecialchars($c->category_name) . "</option>";
                        }
                    }
                    ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <label>Sub-Categories</label>
                  <select name="subcategory[]" id="eventsubcategory" class="form-select select-multiple" multiple required>
                    <option value="">-- Select Sub-Category --</option>
                    <?php
                    if (!empty($categories_selected)) {
                        $subUrl = $siteurl . "script/register.php?action=subcategorieslists&parent_ids=" . urlencode(implode(',', $categories_selected));
                        $subRaw = curl_get_contents($subUrl);
                        $subList = $subRaw ? json_decode($subRaw) : [];
                        if (!empty($subList)) {
                            foreach ($subList as $s) {
                                $sel = in_array($s->id, $subcategories_selected) ? 'selected' : '';
                                echo "<option value=\"".htmlspecialchars($s->id)."\" $sel>" . htmlspecialchars($s->category_name) . "</option>";
                            }
                        }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <!-- Event Type -->
              <div class="form-group mb-3">
                <label for="eventType">Event Type</label>
                <select id="eventType" name="eventType" class="form-control">
                  <option value="">-- Select --</option>
                  <?php
                  $evUrl = $siteurl . "script/register.php?action=eventslists";
                  $evRaw = curl_get_contents($evUrl);
                  $evList = $evRaw ? json_decode($evRaw) : [];
                  if (!empty($evList)) {
                      foreach ($evList as $eo) {
                          $name = $eo->name ?? ($eo->event_type ?? '');
                          $sel = ($event['event_type'] == $name) ? 'selected' : '';
                          echo "<option value=\"".htmlspecialchars($name)."\" $sel>" . htmlspecialchars($name) . "</option>";
                      }
                  }
                  ?>
                </select>
                <input type="text" id="otherEventType" name="other_type" class="form-control mt-2" placeholder="Specify event type" style="display:<?= (empty($event['event_type']) ? 'none' : 'none') ?>;">
              </div>

              <!-- Target Audience -->
              <div class="form-group mb-3">
                <label>Target Audience</label><br>
                <?php
                $audiences = ['Singles','Engaged Couples','Married Couples','Parents','Faith Leaders','Counselors','Teens/Youth','General Public'];
                foreach ($audiences as $aud) {
                    $checked = in_array($aud, $targetAudience ?? []) ? 'checked' : '';
                    echo "<label class='form-check me-3'><input type='checkbox' class='form-check-input' name='target_audience[]' value='".htmlspecialchars($aud)."' $checked> ".htmlspecialchars($aud)."</label>";
                }
                ?>
              </div>

              <!-- Event Dates & Times -->
<div class="form-group mb-3">
  <label>Date & Time</label>
  <div id="dateTimeRepeater">
     <button type="button" class="btn btn-success btn-sm addDate"><i class="fa fa-plus"></i></button>

    <?php
    if (!empty($dates)) {

        foreach ($dates as $d) {
            $date_id = $d['s']; // primary key
            $dateVal = $d['event_date'] ?? $d['date'] ?? '';
            $start   = $d['start_time'] ?? $d['start'] ?? '';
            $end     = $d['end_time'] ?? $d['end'] ?? '';
            ?>

            <div class="row mb-2 dateTimeRow">
              <input type="hidden" name="date_id[]" value="<?= $date_id ?>">

              <div class="col">
                <input type="date" class="form-control" name="event_dates[]" value="<?= htmlspecialchars($dateVal) ?>" required>
              </div>

              <div class="col">
                <input type="time" class="form-control" name="event_start_times[]" value="<?= htmlspecialchars($start) ?>" required>
              </div>

              <div class="col">
                <input type="time" class="form-control" name="event_end_times[]" value="<?= htmlspecialchars($end) ?>" required>
              </div>

              <div class="col-auto">
               
               <button type="button" class="btn btn-danger btn-sm removeRowBtn" onclick="this.closest('.dateTimeRow').remove()">-</button>
              </div>
            </div>

        <?php
        }

    } else { 
        // EMPTY DEFAULT ROW
        ?>

        <div class="row mb-2 dateTimeRow">
          <input type="hidden" name="date_id[]" value="">

          <div class="col">
            <input type="date" class="form-control" name="event_dates[]" required>
          </div>

          <div class="col">
            <input type="time" class="form-control" name="event_start_times[]" required>
          </div>

          <div class="col">
            <input type="time" class="form-control" name="event_end_times[]" required>
          </div>

          <div class="col-auto">
           
          </div>
        </div>

    <?php } ?>

  </div>
</div>

          <!-- Delivery Format Card -->
          <div class="card mt-4">
            <div class="card-header"><div class="card-title">Delivery Format</div></div>
            <div class="card-body">
              <div class="form-group mb-3">
                <label for="deliveryFormat">Select Format</label>
                <select id="deliveryFormat" class="form-control" name="delivery_format">
                  <option value="">-- Select --</option>
                  <option value="physical" <?= ($event['delivery_format']=='physical')?'selected':'' ?>>Physical (In-person)</option>
                  <option value="online" <?= ($event['delivery_format']=='online')?'selected':'' ?>>Online (Webinar/Virtual)</option>
                  <option value="hybrid" <?= ($event['delivery_format']=='hybrid')?'selected':'' ?>>Hybrid (Physical & Online)</option>
                  <option value="video" <?= ($event['delivery_format']=='video')?'selected':'' ?>>Video</option>
                  <option value="text" <?= ($event['delivery_format']=='text')?'selected':'' ?>>Text</option>
                </select>
              </div>

              <!-- Physical -->
              <div id="physicalFields" style="display:<?= ($event['delivery_format']=='physical')?'block':'none' ?>;">
                <label>Nigeria or Foreign?</label>
                <select class="form-control mb-2" id="locationType" name="physicalLocationType">
                  <option value="">Select</option>
                  <option value="nigeria" <?= (($event['is_foreign']==0) && ($event['country']=='' || strtolower($event['country'])=='nigeria')) ? 'selected' : '' ?>>Nigeria</option>
                  <option value="foreign" <?= ($event['is_foreign']==1) ? 'selected' : '' ?>>Foreign</option>
                </select>

                <div id="nigeriaAddress" style="display:<?= ((($event['is_foreign']==0) && ($event['country']=='' || strtolower($event['country'])=='nigeria')) ? 'block' : 'none') ?>;">
                  <input type="text" class="form-control mb-2" name="nigeria_address" placeholder="Address" value="<?= htmlspecialchars($event['address'] ?? '') ?>">
                  <select id="state" name="state" class="form-control mb-2">
                    <option value="">-Select State-</option>
                    <!-- you may populate state list here -->
                    <option value="<?= htmlspecialchars($event['state'] ?? '') ?>" selected><?= htmlspecialchars($event['state'] ?? '') ?></option>
                  </select>
                  <select id="lga" name="lga" class="form-control mb-2">
                    <option value="">-Select LGA-</option>
                    <option value="<?= htmlspecialchars($event['lga'] ?? '') ?>" selected><?= htmlspecialchars($event['lga'] ?? '') ?></option>
                  </select>
                </div>

                <div id="foreignAddress" style="display:<?= ($event['is_foreign']==1) ? 'block' : 'none' ?>;">
                  <input type="text" class="form-control mb-2" name="foreign_address" placeholder="Foreign Address" value="<?= htmlspecialchars($event['address'] ?? '') ?>">
                </div>
              </div>

              <!-- Online -->
              <div id="onlineFields" style="display:<?= ($event['delivery_format']=='online') ? 'block' : 'none' ?>;">
                <input type="text" class="form-control" name="web_address" placeholder="Enter meeting/webinar link (Zoom, YouTube, etc)" value="<?= htmlspecialchars($event['online_link'] ?? '') ?>">
              </div>

              <!-- Hybrid -->
              <div id="hybridFields" style="display:<?= ($event['delivery_format']=='hybrid') ? 'block' : 'none' ?>;">
                <label class="form-label">Physical Address</label>
                <input type="text" class="form-control mb-2" name="hybrid_physical_address" value="<?= htmlspecialchars($event['hybrid_physical_address'] ?? '') ?>">
                <label class="form-label">Web Address</label>
                <input type="url" class="form-control mb-2" name="hybrid_web_address" value="<?= htmlspecialchars($event['hybrid_web_address'] ?? '') ?>">
                <label class="form-label">Nigeria or Foreign</label>
                <select class="form-control mb-2" id="hybridLocationType" name="hybridLocationType">
                  <option value="">Select</option>
                  <option value="nigeria" <?= (strtolower($event['hybrid_country'] ?? '')=='nigeria')?'selected':'' ?>>Nigeria</option>
                  <option value="foreign" <?= (!empty($event['hybrid_foreign_address'])) ? 'selected' : '' ?>>Foreign</option>
                </select>

                <div id="nigeriaHybridFields" style="display:<?= (strtolower($event['hybrid_country'] ?? '')=='nigeria') ? 'block' : 'none' ?>;">
                  <select id="hybrid_state" name="hybrid_state" class="form-control mb-2">
                    <option value="">-Select State-</option>
                    <option value="<?= htmlspecialchars($event['hybrid_state'] ?? '') ?>" selected><?= htmlspecialchars($event['hybrid_state'] ?? '') ?></option>
                  </select>
                  <select id="hybrid_lga" name="hybrid_lga" class="form-control mb-2">
                    <option value="">-Select LGA-</option>
                    <option value="<?= htmlspecialchars($event['hybrid_lga'] ?? '') ?>" selected><?= htmlspecialchars($event['hybrid_lga'] ?? '') ?></option>
                  </select>
                  <input type="text" class="form-control mb-2" name="hybrid_country" value="Nigeria" readonly>
                </div>

                <div id="foreignHybridFields" style="display:<?= (!empty($event['hybrid_foreign_address'])) ? 'block' : 'none' ?>;">
                  <input type="text" class="form-control mb-2" name="hybrid_foreign_address" placeholder="Foreign Address" value="<?= htmlspecialchars($event['hybrid_foreign_address'] ?? '') ?>">
                </div>
              </div>

              <!-- Video -->
<!-- Video Modules -->
<div id="videoFields" style="display:<?= ($event['delivery_format']=='video' || $event['delivery_format']=='hybrid')?'block':'none' ?>;">
  
  <h6 class="mt-2 mb-2">Video Modules</h6>



  <div id="videoModules">

    <?php if (!empty($videoModules)) : ?>
        <?php foreach ($videoModules as $i => $vm): ?>
        <?php 
            $vid      = $vm['id'] ?? '';
            $title    = $vm['title'] ?? '';
            $desc     = $vm['description'] ?? '';
            $duration = $vm['duration'] ?? '';
            $link     = $vm['video_link'] ?? '';
            $module_number = $vm['module_number'] ?? '';
            $qualities = explode(',', $vm['video_quality'] ?? '');
            $subtitle = $vm['subtitles'] ?? '';
        ?>

          <label>Total Number of Videos:</label>
  <input type="number" class="form-control" name="total_videos[]" min="1" value="<?php echo $module_number; ?>">
        
        <div class="video-module mb-3" data-module-index="<?= $i ?>">
          
          <span class="module-number">Module<?= $i+1 ?></span>

          <input type="hidden" name="video_id[]" value="<?= $vid ?>">

          <input type="text" class="form-control mb-2" 
                 name="video_module_title[]" 
                 value="<?= htmlspecialchars($title) ?>" 
                 placeholder="Lesson / Module Title">

          <textarea class="editor mb-2" name="video_module_desc[]" placeholder="Description/Notes"><?= $desc ?></textarea>

          <input type="text" class="form-control mb-2" 
                 name="video_duration[]" 
                 value="<?= htmlspecialchars($duration) ?>" 
                 placeholder="Total Duration">
                   <?php if (!empty($vm['file_path'])): ?>
        <div class="position-relative d-flex gap-2">
          <a href="<?php echo $siteurl . $documentPath . $vm['file_path']; ?>" class="btn btn-primary" > view </a>        
            <a href="#" id="<?= $vm['file_path'] ?>"
               class="btn btn-danger btn-sm deleteeventvideo">
               <i class="fa fa-trash"></i>
            </a>
        </div>
    <?php endif; ?>

          <input type="file" name="video_file[]" class="form-control mb-2" accept="video/*">

          <input type="url" class="form-control mb-2" 
                 name="video_link[]" 
                 value="<?= htmlspecialchars($link) ?>" 
                 placeholder="Or paste link">

          <label>Video Quality</label><br>
          <label><input type="checkbox" name="video_quality[<?= $i ?>][]" value="720p" <?= in_array('720p',$qualities) ? 'checked' : '' ?>> 720p</label>
          <label><input type="checkbox" name="video_quality[<?= $i ?>][]" value="1080p" <?= in_array('1080p',$qualities) ? 'checked' : '' ?>> 1080p</label>
          <label><input type="checkbox" name="video_quality[<?= $i ?>][]" value="4K" <?= in_array('4K',$qualities) ? 'checked' : '' ?>> 4K</label>

          <div class="mt-2">
  <label>Include Subtitles?</label><br>
  <label><input type="radio" name="video_subtitles[<?= $i ?>]" value="Yes" <?= $subtitle=='Yes'?'checked':'' ?>> Yes</label>
  <label><input type="radio" name="video_subtitles[<?= $i ?>]" value="No"  <?= $subtitle=='No'?'checked':'' ?>> No</label>
</div>


          <button type="button" class="btn btn-danger btn-sm removeVideoModule mt-2">Remove</button>

        </div>
        <?php endforeach; ?>
    <?php else: ?>

    <!-- DEFAULT EMPTY MODULE -->
    <div class="video-module mb-3" data-module-index="0">

      <span class="module-number">module 1</span>

      <input type="hidden" name="video_id[]" value="">
        <label>Total Number of Videos:</label>
  <input type="number" class="form-control" name="total_videos[]" min="1">

      <input type="text" class="form-control mb-2" name="video_module_title[]" placeholder="Lesson / Module Title">
      <textarea class="editor mb-2" name="video_module_desc[]" placeholder="Description/Notes"></textarea>
      <input type="text" class="form-control mb-2" name="video_duration[]" placeholder="Total Duration">
      <input type="file" name="video_file[]" class="form-control mb-2" accept="video/*">
      <input type="url" class="form-control mb-2" name="video_link[]" placeholder="Or paste link">

      <label>Video Quality</label><br>
      <label><input type="checkbox" name="video_quality[0][]" value="720p"> 720p</label>
      <label><input type="checkbox" name="video_quality[0][]" value="1080p"> 1080p</label>
      <label><input type="checkbox" name="video_quality[0][]" value="4K"> 4K</label>

      <div class="mt-2">
        <label>Include Subtitles?</label><br>
        <label><input type="radio" name="video_subtitles[0]" value="Yes"> Yes</label>
        <label><input type="radio" name="video_subtitles[0]" value="No"> No</label>
      </div>

      <button type="button" class="btn btn-danger btn-sm removeVideoModule mt-2">Remove</button>

    </div>
    <?php endif; ?>
  </div>

  <button type="button" class="btn btn-secondary mt-2 mb-3" onclick="addVideoModule()">Add More</button>
</div>

<!-- TEXT MODULES -->
<div id="textFields" style="display:<?= ($event['delivery_format']=='text' || $event['delivery_format']=='hybrid')?'block':'none' ?>;">
    <h6 class="mt-2 mb-2">Text Modules</h6>
    <div id="textModules">

        <?php if (!empty($textModules)): ?>
            <?php foreach ($textModules as $i => $tm): ?>
                <?php 
                    $tid      = $tm['id'] ?? ''; 
                    $tTitle   = $tm['title'] ?? ''; 
                    $tDesc    = $tm['description'] ?? ''; 
                    $reading  = $tm['reading_time'] ?? ''; 
                    $filepath = $tm['file_path'] ?? ''; 
                    $total_lessons = $tm['module_number'] ?? ''; 
                ?>

                <div class="text-module mb-3" data-module-index="<?= $i ?>">

                    <span class="module-number">Module <?= $i+1 ?></span>

                       <input type="number" class="form-control mb-2" 
           name="total_lessons[]" 
           value="<?= $total_lessons ?>">

                    <input type="hidden" name="text_id[]" value="<?= $tid ?>">

                    <input type="text" class="form-control mb-2"
                        name="text_module_title[]" 
                        value="<?= htmlspecialchars($tTitle) ?>" 
                        placeholder="Lesson Title">

                    <textarea class="editor mb-2" 
                        name="text_module_desc[]" 
                        placeholder="Description/Notes"><?= $tDesc ?></textarea>

                    <input type="text" class="form-control mb-2" 
                        name="text_reading_time[]" 
                        value="<?= htmlspecialchars($reading) ?>" 
                        placeholder="Estimated Reading Time">

                    <?php if (!empty($filepath)): ?>
                        <a href="../secure/<?= htmlspecialchars($filepath) ?>" 
                           target="_blank" 
                           class="d-block mb-2 text-primary">
                           📄 View Current File
                        </a>

                  <a href="#" id="<?= $filepath ?>" class="btn btn-danger btn-sm ms-auto deleteeventtext" title="Delete file"> <i class="fa fa-trash"></i> </a>
                    <?php endif; ?>

                    <input type="file" name="text_file[]" 
                           class="form-control mb-2"
                           accept=".pdf,.txt,.doc,.docx">


                </div>

            <?php endforeach; ?>
        <?php else: ?>

            <!-- DEFAULT EMPTY TEXT MODULE -->
            <div class="text-module mb-3" data-module-index="0">

                <span class="module-number">Module 1</span>

                <input type="hidden" name="text_id[]" value="">

                <input type="text" class="form-control mb-2"
                    name="text_module_title[]" 
                    placeholder="Lesson Title">

                <textarea class="editor mb-2" 
                    name="text_module_desc[]" 
                    placeholder="Description/Notes"></textarea>

                <input type="text" class="form-control mb-2" 
                    name="text_reading_time[]" 
                    placeholder="Estimated Reading Time">

                <input type="file" name="text_file[]" 
                       class="form-control mb-2"
                       accept=".pdf,.txt,.doc,.docx">

            </div>

        <?php endif; ?>

    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addTextModule()">
        Add More
    </button>

</div>

          <!-- Pricing Card -->
          <div class="card mt-4">
            <div class="card-header"><div class="card-title">Pricing</div></div>
            <div class="card-body">
              <div class="form-group mb-3">
                <label>Pricing Type</label><br>
                <label class="form-check me-3"><input type="radio" name="pricing_type" value="donation" <?= ($event['pricing_type']=='donation')?'checked':'' ?>> Donation</label>
                <label class="form-check me-3"><input type="radio" name="pricing_type" value="free" <?= ($event['pricing_type']=='free')?'checked':'' ?>> Free</label>
                <label class="form-check"><input type="radio" name="pricing_type" value="paid" <?= ($event['pricing_type']=='paid')?'checked':'' ?>> Paid</label>
              </div>

              <div id="donationFields" style="display:none;">
                <p>Users will be able to pay any amount they choose.</p>
              </div>

              <div id="freeFields" style="display:none;">
                <p>This event is free.</p>
              </div>

              <div id="paidFields" style="display:<?= ($event['pricing_type']=='paid') ? 'block' : 'none' ?>;">
                <div id="ticketsWrapper">
                  <?php
                  if (!empty($tickets)) {
                      foreach ($tickets as $i => $t) {
                          $id = $t['id'];
                          $tname = $t['ticket_name'] ?? $t['name'] ?? '';
                          $tbenefits = $t['benefits'] ?? '';
                          $tprice = $t['price'] ?? '';
                          $tseats = $t['seats'] ?? '';
                          ?>
                          <div class="ticketBox border p-3 mb-3 position-relative">
                           <input type="hidden" name="ticket_id[]" value="<?= $id ?>">
                            <input type="text" class="form-control mb-2 ticket_name" name="ticket_name[]" value="<?= htmlspecialchars($tname) ?>" placeholder="Ticket Name">
                            <textarea class="editor mb-2 ticket_benefits" name="ticket_benefits[]" placeholder="Benefits"><?= $tbenefits ?></textarea>
                            <input type="number" class="form-control mb-2 ticket_price" name="ticket_price[]" value="<?= htmlspecialchars($tprice) ?>" placeholder="Price (NGN)">
                            <input type="number" class="form-control mb-2 ticket_seats" name="ticket_seats[]" value="<?= htmlspecialchars($tseats) ?>" placeholder="Number of Seats Available">
                            <button type="button" class="btn btn-danger btn-sm removeTicket">Remove</button>
                          </div>
                          <?php
                      }
                  } else {
                      // empty ticket template
                      ?>
                      <div class="ticketBox border p-3 mb-3 position-relative">
                        <input type="hidden" name="ticket_id[]">
                        <input type="text" class="form-control mb-2 ticket_name" name="ticket_name[]" placeholder="Ticket Name">
                        <textarea class="editor mb-2 ticket_benefits" name="ticket_benefits[]" placeholder="Benefits"></textarea>
                        <input type="number" class="form-control mb-2 ticket_price" name="ticket_price[]" placeholder="Price (NGN)">
                        <input type="number" class="form-control mb-2 ticket_seats" name="ticket_seats[]" placeholder="Number of Seats Available">
                        <button type="button" class="btn btn-danger btn-sm removeTicket">Remove</button>
                      </div>
                      <?php
                  }
                  ?>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" id="addTicket">Add More</button>
              </div>

              <div class="form-group mt-3">
                <label for="status">Status</label>
                <select name="status" class="form-control" required>
                  <option value="active" <?= ($event['status']=='active') ? 'selected' : '' ?>>Published</option>
                  <option value="pending" <?= ($event['status']=='pending') ? 'selected' : '' ?>>Pending</option>
                  <option value="suspended" <?= ($event['status']=='suspended') ? 'selected' : '' ?>>Suspended</option>
                </select>
              </div>

              <button type="submit" id="submitEventBtn" class="btn btn-primary mt-3"> Update Event </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>