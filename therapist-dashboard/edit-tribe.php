
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Edit Group / Tribe</h3>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header">
            <div class="card-title">Group Details</div>
          </div>
          <div class="card-body">

            <!-- Group/Tribe Name -->
            <div class="form-group">
              <label for="groupName">Group/Tribe Name</label>
              <input type="text" class="form-control" id="groupName" placeholder="Enter group name">
            </div>

            <!-- Group Description -->
            <div class="form-group">
              <label for="groupDescription">Group Description</label>
              <textarea id="groupDescription" class="form-control" rows="4" placeholder="Describe the group/tribe"></textarea>
            </div>

            <!-- Group Type -->
            <div class="form-group">
              <label for="groupType">Group Type</label>
              <select id="groupType" class="form-control select2-single">
                <option value="Open">Open (Anyone can join)</option>
                <option value="Closed">Closed (Join only by approval/invitation)</option>
              </select>
            </div>

            <!-- Group Access -->
            <div class="form-group">
              <label>Group Access</label><br>
              <div class="d-flex">
                <div class="form-check mr-3">
                  <input class="form-check-input" type="radio" name="groupAccess" id="freeAccess" value="Free">
                  <label class="form-check-label" for="freeAccess">Free</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="groupAccess" id="paidAccess" value="Paid">
                  <label class="form-check-label" for="paidAccess">Paid Subscription</label>
                </div>
              </div>
            </div>

            <!-- Paid Subscription Plans -->
            <div class="card mt-3">
              <div class="card-header">
                <div class="card-title">Subscription Plans (For Paid Access)</div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="sub1">1 Month - Subscription Fee (₦)</label>
                  <input type="number" class="form-control" id="sub1" placeholder="Enter amount">
                </div>
                <div class="form-group">
                  <label for="sub3">3 Months - Subscription Fee (₦)</label>
                  <input type="number" class="form-control" id="sub3" placeholder="Enter amount">
                </div>
                <div class="form-group">
                  <label for="sub6">6 Months - Subscription Fee (₦)</label>
                  <input type="number" class="form-control" id="sub6" placeholder="Enter amount">
                </div>
                <div class="form-group">
                  <label for="sub12">12 Months - Subscription Fee (₦)</label>
                  <input type="number" class="form-control" id="sub12" placeholder="Enter amount">
                </div>
                <small class="form-text text-muted">
                  Note: <strong>MARRIAGE.NG</strong> takes a twenty percent (20%) commission per payment.
                </small>
              </div>
            </div>

            <!-- Categories -->
            <div class="form-group mt-3">
              <label for="groupCategories">Categories</label>
              <select id="groupCategories" name="categories[]" class="form-control select2-multiple" multiple>
                <option value="Marriage">Marriage</option>
                <option value="Parenting">Parenting</option>
                <option value="Faith">Faith</option>
                <option value="Youth">Youth</option>
                <option value="Health">Health</option>
                <option value="Finance">Finance</option>
              </select>
            </div>

            <!-- Sub-Categories -->
            <div class="form-group">
              <label for="groupSubcategories">Sub-Categories</label>
              <select id="groupSubcategories" name="subcategories[]" class="form-control select2-multiple" multiple>
                <option value="Pre-Marital Counseling">Pre-Marital Counseling</option>
                <option value="Conflict Resolution">Conflict Resolution</option>
                <option value="Parent Coaching">Parent Coaching</option>
                <option value="Teen Mentorship">Teen Mentorship</option>
                <option value="Financial Planning">Financial Planning</option>
                <option value="Health & Wellness">Health & Wellness</option>
              </select>
            </div>

            <!-- Rules & Guidelines -->
            <div class="form-group">
              <label for="groupRules">Group Rules & Guidelines (Optional)</label>
              <textarea id="groupRules" class="form-control" rows="4" placeholder="State basic rules or expectations for members..."></textarea>
            </div>

            <!-- Banner Upload -->
            <div class="form-group">
              <label for="groupBanner">Upload Group Banner / Image</label>
              <input type="file" id="groupBanner" class="form-control" accept="image/png, image/jpeg">
              <small class="form-text text-muted">Recommended size: 1200 x 600px (JPG/PNG)</small>
            </div>

            <!-- Submit Button -->
            <button class="btn btn-primary mt-3">Create Group</button>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>




<?php include "footer.php"; ?>