
<?php include "header.php"; ?>
 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Send Message</h3>
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
                  <a href="#">Platform Message</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Send Message</a>
                </li>
              </ul>
            </div>
          <div class="row">
             <div class="col-md-12">
                <div class="card">
  <div class="card-header bg-warning text-white">
    <h4><i class="fa fa-envelope me-2"></i> Send Platform Message</h4>
  </div>
  <div class="card-body">
    <form>
      <!-- Recipient -->
      <div class="mb-3">
        <label for="recipient" class="form-label">Recipient</label>
        <input type="text" class="form-control" id="recipient" value="Vendor/Therapist Name" readonly>
      </div>

      <!-- Subject -->
      <div class="mb-3">
        <label for="subject" class="form-label">Subject</label>
        <input type="text" class="form-control" id="subject" placeholder="Enter subject">
      </div>

      <!-- Message -->
      <div class="mb-3">
        <label for="message" class="form-label">Message</label>
        <textarea class="form-control" id="message" rows="6" placeholder="Type your message here..."></textarea>
      </div>

      <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-warning">
          <i class="fa fa-paper-plane me-1"></i> Send Message
        </button>
      </div>
    </form>
  </div>
</div>
  </div>
</div>
  </div>
</div>
<?php include "footer.php"; ?>