<?php include "header.php"; ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Create Event</h3>
    </div>

    <div class="row">
      <div class="col-md-12">
           <form id="addEvent" method="POST" enctype="multipart/form-data">
            <div class="text-center mt-1" id="messages"></div> 
        <!-- Event Details -->
        <div class="card">
          <div class="card-header"><div class="card-title">Event Details</div></div>
          <div class="card-body">
                  <div class="text-center mt-1" id="messages"></div> 

            <!-- Title -->
            <div class="form-group">
              <label for="eventTitle">Title</label>
              <input type="text" id="eventTitle" name="title" class="form-control" placeholder="Enter event title">
            </div>

            
            <div class="form-group">
              <label class="form-label" for="event-id">Event ID</label>
              <input type="text" id="event-id" name="event_id" class="form-control" value="EV<?php echo sprintf('%06d', rand(1, 999999)); ?>" readonly required>
              </div>

            <!-- Cover Image -->
            <div class="form-group">
              <label for="coverImage">Cover Image</label>
              <input type="file" id="coverImage" name="cover_image[]" class="form-control" accept="image/*" multiple>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" class="editor" name="description" placeholder="Enter event description"></textarea>
            </div>

            <!-- Categories -->
            <div class="form-group">
              <label for="categories">Categories</label>
               <select name="category[]" id="category" class="form-select select-multiple" required multiple>
              <option value="">-- Select Category --</option>
              <?php
           $url = $siteurl . "script/register.php?action=categorieslists";
              $data = curl_get_contents($url); // using your helper from header
            if ($data !== false) {
                $categories = json_decode($data);
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                      foreach ($categories as $category) {
                          $categoryId = $category->id;
                          $name = $category->category_name; // adjust if DB column is different
                          echo "<option value='{$categoryId}'>{$name}</option>";
                      }
                  }
              }
            }
   
            else {
                   echo "Error fetching data: " . curl_error($ch);
                      }
              ?>
          </select>
            </div>

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="subcategories">Sub-Categories</label>
               <select name="subcategory[]" id="subcategory" class="form-select select-multiple" required multiple>


                    <option value="">-- Select Sub-Category --</option>
                   
                    </select>
            </div>
            <input type="hidden" name="action" value="addevents">
            <!-- Event Type -->
            <div class="form-group">
              <label for="eventType">Event Type</label>
                         <select id="eventType"  name="eventType"  class="form-control">
                  <?php
           $url = $siteurl . "script/register.php?action=eventslists";
              $data = curl_get_contents($url); // using your helper from header
            if ($data !== false) {
                $events = json_decode($data);
                if (!empty($events)) {
                    foreach ($events as $event) {
                      foreach ($events as $event) {
                          $Id = $event->s;
                          $name = $event->name; // adjust if DB column is different
                          echo "<option value='{$name}'>{$name}</option>";
                      }
                  }
              }
            }

            else {
                   echo "Error fetching data: " . curl_error($ch);
                      }
              ?>
              </select>
              <input type="text" id="otherEventType" name="other_type" class="form-control mt-2" placeholder="Specify event type" style="display:none;">
            </div>

            <!-- Target Audience -->
           <div class="form-group">
  <label>Target Audience</label><br>
  <div class="d-flex flex-wrap">
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Singles"> Singles</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Engaged Couples"> Engaged Couples</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Married Couples"> Married Couples</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Parents"> Parents</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Faith Leaders"> Faith Leaders</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Counselors"> Counselors</label>
    <label class="form-check mr-3"><input type="checkbox" class="form-check-input" name="target_audience[]" value="Teens/Youth"> Teens/Youth</label>
    <label class="form-check"><input type="checkbox" class="form-check-input" name="target_audience[]" value="General Public"> General Public</label>
  </div>
</div>


            <!-- Date & Time -->
            <div class="form-group">
              <label for="eventDate">Date & Time</label>
              <div id="dateTimeRepeater">
                <div class="row mb-2 dateTimeRow">
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
                    <button type="button" class="btn btn-success btn-sm" onclick="addDateTimeRow()"><i class="fa fa-plus me-1"></i></button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- All Delivery Format -->
        <div class="card mt-4">
          <div class="card-header"><div class="card-title">Delivery Format</div></div>
          <div class="card-body">

            <div class="form-group">
              <label for="deliveryFormat">Select Format</label>
              <select id="deliveryFormat" class="form-control" name="delivery_format">
                <option value="">-- Select --</option>
                <option value="physical">Physical (In-person)</option>
                <option value="online">Online (Webinar/Virtual)</option>
                <option value="hybrid">Hybrid (Physical & Online)</option>
                <option value="video">Video</option>
                <option value="text">Text</option>
              </select>
            </div>

            <!-- Physical -->
            <div id="physicalFields" style="display:none;">
              <label>Nigeria or Foreign?</label>
              <select class="form-control mb-2" id="locationType" name="physicalLocationType">
                <option disabled selected>Select</option>
                <option value="nigeria">Nigeria</option>
                <option value="foreign">Foreign</option>
              </select>
              <div id="nigeriaAddress" style="display:none;">
                <input type="text" class="form-control mb-2" placeholder="Address">
                <select id="state" name="state" class="form-control" >
              <option value="">-Select State-</option>
            </select>
                <select class="form-control" id="lga"  name="lga">
            <option value="">-Select LGA-</option>
          </select>
              </div>
              <div id="foreignAddress" style="display:none;">
                <input type="text" class="form-control mb-2" placeholder="Foreign Address" name="foreign_address">
              </div>
            </div>

            <!-- Online -->
            <div id="onlineFields" style="display:none;">
              <input type="text" class="form-control" name="web_address" placeholder="Enter meeting/webinar link (Zoom, YouTube, etc)">
            </div>

            <!-- Hybrid -->
           <!-- Hybrid Address Fields -->
            <div class="mb-3" id="hybridFields" style="display:none;">
              <label class="form-label">Physical Address</label>
              <input type="text" class="form-control mb-2" name="hybrid_physical_address">
              <label class="form-label">Web Address</label>
              <input type="url" class="form-control mb-2" name="hybrid_web_address">
              <label class="form-label">Nigeria or Foreign</label>
              <select class="form-control" id="hybridLocationType" name="hybridLocationType" onchange="toggleHybridLocationFields()">
                <option value="">Select</option>
                <option value="nigeria">Nigeria</option>
                <option value="foreign">Foreign</option>
              </select>
              <div id="nigeriaHybridFields" style="display:none;">
                    <select id="hybrid_state"  name="hybrid_state" class="form-control">
              <option value="">-Select State-</option>
            </select>
                <select class="form-control" id="hybrid_lga" name="hybrid_lga">
            <option value="">-Select LGA-</option>
          </select>
                <input type="text" class="form-control mb-2" name="hybrid_country" value="Nigeria" readonly>
              </div>
              <div id="foreignHybridFields" style="display:none;">
                <input type="text" class="form-control mb-2" name="hybrid_foreign_address" placeholder="Foreign Address">
              </div>
            </div>
            <!-- Video -->
            <div id="videoFields" style="display:none;">
             <label>Total Number of Videos:</label>
  <input type="number" class="form-control" name="total_videos" min="1">

  <div id="videoModules">
    <!-- Template -->
    <div class="video-module mb-3">
      <h6 class="mb-2 mt-3">Module <span class="module-number">1</span></h6>
      <div class="mb-3">
      <label>Lesson / Module Title:</label>
      <input type="text" class="form-control" name="video_module_title[]">
      </div>
      <div class="mb-3">
      <label>Description/Notes:</label>
      <textarea class="editor" name="video_module_desc[]"></textarea>
      </div>
      <div class="mb-3">
      <label>Total Duration:</label>
      <input type="text" class="form-control" name="video_duration[]">
        </div>
        <div class="mb-3">
      <label>Upload/Link Video Files:</label>
      <input type="file" name="video_file[]" class="form-control mb-2" accept="video/*">
      <input type="url" class="form-control" placeholder="Or paste link" name="video_link[]">
                     </div>
                     <div class="mb-2">
      <label>Video Quality</label>
      <label><input type="checkbox" name="video_quality[0][]" value="720p"> 720p</label>
      <label><input type="checkbox" name="video_quality[0][]" value="1080p"> 1080p</label>
      <label><input type="checkbox" name="video_quality[0][]" value="4K"> 4K</label>
                     </div>
     
      <div class="mb-2">
      <label>Include Subtitles?</label><br>
      <label><input type="checkbox" name="video_subtitles[0]" value="Yes"> Yes</label>
      <label><input type="checkbox" name="video_subtitles[0]" value="No"> No</label>
      </div>
    </div>
  </div>

  <button type="button" class="btn btn-secondary mt-3 mb-2" onclick="addVideoModule()">ADD MORE</button>
            </div>

  <input type="hidden" name="user" id="user" value="<?php echo $buyerId; ?>">
            <!-- Text -->
            <div id="textFields" style="display:none;">
             <h6 class="mt-2 mb-2">Text Modules</h6>
  <label>Number of Lessons/Modules:</label>
  <input type="number" class="form-control" name="total_lessons" min="1">

  <div id="textModules">
    <!-- Template -->
    <div class="text-module mb-3">
      <h6 class="mt-2 mb-2">Module <span class="module-number">1</span></h6>

      <div class="mb-3">
      <label>Lesson / Module Title:</label>
      <input type="text" class="form-control" name="text_module_title[]">
      </div>

      <div class="mb-3">
      <label>Description/Notes:</label>
      <textarea class="editor" name="text_module_desc[]"></textarea>
      </div>

      <div class="mb-3">
      <label>Estimated Reading Time:</label>
      <input type="text" class="form-control" name="text_reading_time[]">
      </div>

      <div class="mb-3">
      <label>Upload Text Content (PDF/Text):</label>
      <input type="file" name="text_file[]">
    </div>
     </div>
  </div>

  <button type="button" class="btn btn-secondary mb-3" onclick="addTextModule()">ADD MORE</button>
            </div>

          </div>
        </div>

        

        <!-- Pricing -->
        <div class="card mt-4">
          <div class="card-header"><div class="card-title">Pricing</div></div>
          <div class="card-body">

            <div class="form-group">
              <label>Pricing Type</label><br>
              <label class="form-check mr-3"><input type="radio" name="pricing_type" value="donation"> Donation</label>
              <label class="form-check mr-3"><input type="radio" name="pricing_type" value="free"> Free</label>
              <label class="form-check"><input type="radio"    name="pricing_type" value="paid"> Paid</label>
            </div>

            <!-- Donation -->
            <div id="donationFields" style="display:none;">
              <p>Users will be able to pay any amount they choose.</p>
            </div>

            <!-- Free -->
            <div id="freeFields" style="display:none;">
              <p>This event is free.</p>
            </div>

            <!-- Paid -->
        <div id="paidFields" style="display:none;">
        <div id="ticketsWrapper">
          <div class="ticketBox border p-3 mb-3 position-relative">
            <div class="form-group">
            <input type="text" class="form-control mb-2" name="ticket_name[]" placeholder="Ticket Name">
            </div>
            <div class="form-group">
            <textarea class="editor mb-2" rows="2" name="ticket_benefits[]" placeholder="Benefits"></textarea>
            </div>

            <div class="form-group">
            <input type="number" class="form-control mb-2" placeholder="Price (NGN)" name="ticket_price[]">
            </div>

            <div class="form-group">
            <input type="number" class="form-control mb-2" placeholder="Number of Seats Available" name="ticket_seats[]">
            </div>

            <div class="form-group">
            <button type="button" class="btn btn-danger btn-sm removeTicket" style="display:none;">Remove</button>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addTicket">Add More</button>
      </div>
            
             <div class="form-group">
              <label for="status">Status</label>
             <select name="status" class="form-control" required>
              <option> Select Status</option>
              <option value="active"> Published</option>
               <option value="pending"> pending</option>
            </select>
            </div>
              <button type="submit" id="submitEventBtn" value="Submit"  class="btn btn-primary"> Submit </button> 
              </form>
          </div>
        </div>

      </div>
    </div>
<?php include "footer.php"; ?>
