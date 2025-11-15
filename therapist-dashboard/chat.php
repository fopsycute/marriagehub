
<?php include "header.php"; ?>
 <div class="container">
 <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">Chat Support</h3>
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
                  <a href="#">Chat</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Chat</a>
                </li>
              </ul>
            </div>
            <div class="row">

    <div class="col-md-12">
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