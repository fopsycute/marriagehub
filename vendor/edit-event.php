
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Event</h3>
    </div>

    <div class="row">
      <div class="col-md-12">
        
        <!-- Event Details -->
        <div class="card">
          <div class="card-header"><div class="card-title">Event Details</div></div>
          <div class="card-body">

            <!-- Title -->
            <div class="form-group">
              <label for="eventTitle">Title</label>
              <input type="text" id="eventTitle" class="form-control" placeholder="Enter event title">
            </div>

            <!-- Cover Image -->
            <div class="form-group">
              <label for="coverImage">Cover Image</label>
              <input type="file" id="coverImage" class="form-control" accept="image/*" multiple>
           
            </div>

            <!-- Description -->
            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" class="editor"  placeholder="Enter event description"></textarea>
            </div>

            <!-- Categories -->
            <div class="form-group">
              <label for="categories">Categories</label>
              <select id="categories" class="form-control select2-multiple" multiple>
                <option>Marriage</option>
                <option>Parenting</option>
                <option>Faith</option>
                <option>Youth</option>
                <option>Health</option>
                <option>Finance</option>
              </select>
            </div>

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="subcategories">Sub-Categories</label>
              <select id="subcategories" class="form-control select2-multiple" multiple>
                <option>Pre-Marital Counseling</option>
                <option>Conflict Resolution</option>
                <option>Parent Coaching</option>
                <option>Teen Mentorship</option>
                <option>Financial Planning</option>
                <option>Health & Wellness</option>
              </select>
            </div>

            <!-- Event Type -->
            <div class="form-group">
              <label for="eventType">Event Type</label>
              <select id="eventType" class="form-control">
                <option>Webinar</option>
                <option>Workshop</option>
                <option>Seminar</option>
                <option>Retreat</option>
                <option>Marriage Conference</option>
                <option>Parenting Class</option>
                <option>Faith-Based Program</option>
                <option>Online Course</option>
                <option>Virtual Support Group</option>
                <option>Other (please specify)</option>
              </select>
            </div>

            <!-- Target Audience -->
            <div class="form-group">
              <label>Target Audience</label><br>
              <div class="d-flex flex-wrap">
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Singles</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Engaged Couples</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Married Couples</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Parents</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Faith Leaders</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Counselors</label>
                <label class="form-check mr-3"><input type="checkbox" class="form-check-input"> Teens/Youth</label>
                <label class="form-check"><input type="checkbox" class="form-check-input"> General Public</label>
              </div>
            </div>

            <!-- Date & Time -->
            <div class="form-group">
              <label for="eventDate">Date & Time</label>
              <input type="datetime-local" id="eventDate" class="form-control">
            </div>

          </div>
        </div>

        <!-- Delivery Format -->
        <div class="card mt-4">
          <div class="card-header"><div class="card-title">Delivery Format</div></div>
          <div class="card-body">

            <div class="form-group">
              <label>Select Format</label>
              <select id="deliveryFormat" class="form-control">
                <option value="physical">Physical (In-person)</option>
                <option value="online">Online (Webinar/Virtual)</option>
                <option value="hybrid">Hybrid (Physical & Online)</option>
                <option value="video">Video</option>
                <option value="text">Text</option>
              </select>
            </div>

            <!-- Physical -->
            <h6>Physical (In-person)</h6>
            <label>Nigeria or Foreign?</label>
            <select class="form-control mb-2" id="locationType">
              <option value="nigeria">Nigeria</option>
              <option value="foreign">Foreign</option>
            </select>
            <div>
              <h6>Nigerian Address</h6>
              <input type="text" class="form-control mb-2" placeholder="Address">
              <input type="text" class="form-control mb-2" placeholder="State">
              <input type="text" class="form-control mb-2" placeholder="LGA">
              <input type="text" class="form-control mb-2" placeholder="Country">
            </div>
            <div>
              <h6>Foreign Address</h6>
              <input type="text" class="form-control mb-2" placeholder="Foreign Address">
            </div>

            <!-- Online -->
            <h6 class="mt-3">Online (Webinar/Virtual)</h6>
            <input type="text" class="form-control" placeholder="Enter meeting/webinar link (Zoom, YouTube, etc)">

            <!-- Hybrid -->
            <h6 class="mt-3">Hybrid (Physical & Online)</h6>
            <h6>Physical Address</h6>
            <input type="text" class="form-control mb-2" placeholder="Address">
            <input type="text" class="form-control mb-2" placeholder="State">
            <input type="text" class="form-control mb-2" placeholder="LGA">
            <input type="text" class="form-control mb-2" placeholder="Country">
            <h6 class="mt-3">Online Link</h6>
            <input type="text" class="form-control" placeholder="Zoom, YouTube, etc">

            <!-- Video -->
            <h6 class="mt-3">Video</h6>
            <textarea class="form-control" rows="3" placeholder="Enter course contents and details"></textarea>

            <!-- Text -->
            <h6 class="mt-3">Text</h6>
            <textarea class="form-control" rows="3" placeholder="Enter course contents and details"></textarea>

          </div>
        </div>

        <!-- Pricing -->
        <div class="card mt-4">
          <div class="card-header"><div class="card-title">Pricing</div></div>
          <div class="card-body">

            <div class="form-group">
              <label>Pricing Type</label><br>
              <label class="form-check mr-3"><input type="radio" name="pricing" value="donation"> Donation</label>
              <label class="form-check mr-3"><input type="radio" name="pricing" value="free"> Free</label>
              <label class="form-check"><input type="radio" name="pricing" value="paid"> Paid</label>
            </div>

            <!-- Donation -->
            <h6>Donation</h6>
            <p>Users will be able to pay any amount they choose.</p>

            <!-- Free -->
            <h6>Free</h6>
            <p>This event is free.</p>

            <!-- Paid -->
            <h6>Paid</h6>
            <div id="ticketsWrapper">
              <div class="ticketBox border p-3 mb-3">
                <input type="text" class="form-control mb-2" placeholder="Ticket Name">
                <textarea class="form-control mb-2" rows="2" placeholder="Benefits"></textarea>
                <input type="number" class="form-control mb-2" placeholder="Price (NGN)">
                <input type="number" class="form-control" placeholder="Number of Seats Available">
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mt-2">Add More</button>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

















<?php include "footer.php"; ?>