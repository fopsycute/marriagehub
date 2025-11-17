

<?php include "header.php"; ?>

 <div class="container">
 <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">Disputes & Reports</h3>
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
                  <a href="#">Disputes & Reports</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Disputes & Reports</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">

  <!-- Header -->
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Chat Record</h5>
      <a href="disputes.html" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Back to Disputes
      </a>
    </div>
  </div>

  <!-- Dispute Info -->
  <div class="card mb-3">
    <div class="card-body">
      <h6 class="mb-2">Dispute Details</h6>
      <p class="mb-1"><strong>Complainant:</strong> John Doe (User)</p>
      <p class="mb-1"><strong>Against:</strong> Jane Smith (Vendor)</p>
      <p class="mb-1"><strong>Order:</strong> #12345</p>
      <p class="mb-1"><strong>Issue:</strong> Payment not received</p>
      <p class="mb-1"><strong>Status:</strong> In Progress</p>
      <p class="mb-0"><strong>Date:</strong> 2025-09-01</p>
    </div>
  </div>

  <!-- Update Status Card -->
<div class="card mt-3">
  <div class="card-header">
    <h6 class="mb-0">Update Dispute Status</h6>
  </div>
  <div class="card-body">
    <form>
      <div class="mb-3">
        <label for="disputeStatus" class="form-label">Current Status</label>
        <select class="form-select" id="disputeStatus">
          <option value="pending" selected>Pending</option>
          <option value="in_review">In Review</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="adminNotes" class="form-label">Admin Notes</label>
        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add resolution notes or outcome..."></textarea>
      </div>
      <button type="submit" class="btn btn-success">
        <i class="bi bi-check2-circle me-1"></i> Update Status
      </button>
    </form>
  </div>
</div>


  <!-- Chat Record -->
  <div class="card">
    <div class="chat-container">

      <!-- User Message -->
      <div class="chat-message user">
        <div class="chat-bubble">
          I paid but didn’t receive service.
          <div class="chat-meta">John Doe • 09:00 AM</div>
        </div>
        <img src="https://i.pravatar.cc/40?img=1" alt="User" class="avatar">
      </div>

      <!-- Vendor Message -->
      <div class="chat-message vendor">
        <img src="https://i.pravatar.cc/40?img=2" alt="Vendor" class="avatar">
        <div class="chat-bubble">
          Payment hasn’t reflected yet.
          <div class="chat-meta">Jane Smith • 09:02 AM</div>
        </div>
      </div>

      <!-- User Message -->
      <div class="chat-message user">
        <div class="chat-bubble">
          I have proof of payment.
          <div class="chat-meta">John Doe • 09:05 AM</div>
        </div>
        <img src="https://i.pravatar.cc/40?img=1" alt="User" class="avatar">
      </div>

      <!-- Vendor Message -->
      <div class="chat-message vendor">
        <img src="https://i.pravatar.cc/40?img=2" alt="Vendor" class="avatar">
        <div class="chat-bubble">
          Please share receipt.
          <div class="chat-meta">Jane Smith • 09:07 AM</div>
        </div>
      </div>

    </div>

    <!-- Reply Box -->
    <div class="chat-reply-box d-flex align-items-center gap-2">
      <textarea class="form-control" rows="1" placeholder="Type your reply..."></textarea>
      <button class="btn btn-primary">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>
  </div>

</div>
    </div>
  </div>

</div>
<?php include "footer.php"; ?>