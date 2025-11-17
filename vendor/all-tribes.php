

<?php include "header.php"; ?>

 <div class="container">
 <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">Tribe & Group Moderation</h3>
  <ul class="breadcrumbs mb-3">
  <li class="nav-home">
  <a href="#">
  <i class="icon-home"></i>
  </a>
  </li>
    <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Tribe & Group</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Tribe & Group Moderation</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">All Tribes</h4>

                      <a href="add-tribe.php" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Add Tribes
                      </a>
                    </div>
                  </div>
          <div class="card-body">
    <div class="table-responsive">
      <table id="multi-filter-select"
                        class="display table table-striped table-hover">
        <thead>
          <tr>
            <th>S/N</th>
            <th>Group/Tribe Name</th>
            <th>Created By</th>
            <th>Date Created</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th>S/N</th>
            <th>Group/Tribe Name</th>
            <th>Created By</th>
            <th>Date Created</th>
            <th>Status</th>
        
          </tr>
        </tfoot>
        <tbody>
          <tr>
            <td>1</td>
            <td>Marriage Growth Circle</td>
            <td>John Doe</td>
            <td>2025-09-10</td>
            <td><span class="badge bg-warning">Pending</span></td>
            <td class="text-center">
              <div class="btn-group">
                <a href="edit-tribe.php" class="btn btn-success btn-sm" title="Approve">
                  <i class="fa fa-check-circle"></i>
                </a>
                <button class="btn btn-danger btn-sm" title="Delete / Ban">
                  <i class="fa fa-times-circle"></i>
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#messageLeaderModal" title="Message Leader">
                  <i class="fa fa-comments"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Youth Counseling Forum</td>
            <td>Jane Smith</td>
            <td>2025-09-12</td>
            <td><span class="badge bg-success">Approved</span></td>
            <td class="text-center">
              <div class="btn-group">
                <a href="edit-tribe.php" class="btn btn-success btn-sm" title="Approve">
                  <i class="fa fa-check-circle"></i>
                </a>
                <button class="btn btn-danger btn-sm" title="Delete / Ban">
                  <i class="fa fa-times-circle"></i>
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#messageLeaderModal" title="Message Leader">
                  <i class="fa fa-comments"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal for Admin Messages -->
<div class="modal fade" id="messageLeaderModal" tabindex="-1" aria-labelledby="messageLeaderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageLeaderModalLabel">Message Group Leader</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label for="messageText">Your Message</label>
          <textarea class="editor" id="messageText" rows="4" placeholder="Type your message..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Send Message</button>
      </div>
    </div>
  </div>
</div>
  </div>
    </div>
  </div>
  </div>
 <?php include "footer.php"; ?>




