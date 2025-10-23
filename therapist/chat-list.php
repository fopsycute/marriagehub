<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3"Chats</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Chat</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Chat List</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-8">

<div class="card">
    <div class="card-body h-100">


        <!-- Chat List -->
        <div class="col-md-8 border-end" style=" overflow-y: auto;">
          <div class="d-flex flex-column h-100">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
    <h6 class="mb-0 fw-bold">Chats</h6>
    <button class="btn btn-sm btn-outline-secondary d-md-none" data-bs-toggle="collapse" data-bs-target="#chatList">
      <i class="bi bi-list"></i>
    </button>
  </div>

  <!-- Chat List -->
  <div id="chatList" class="collapse d-md-block">
    <ul class="list-unstyled mb-0 overflow-auto">
      
      <!-- Therapist -->
      <li class="p-3 border-bottom chat-item" data-user="Dr. Adewale">
        <a href="chat.php" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
          <div class="d-flex align-items-center">
            <div class="position-relative">
              <img src="https://via.placeholder.com/50" alt="avatar" class="rounded-circle me-2" width="50" height="50">
              <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle"></span>
            </div>
            <div class="ms-2">
              <p class="fw-bold mb-0">Dr. Adewale</p>
              <p class="small text-muted mb-0">Therapist</p>
              <p class="small text-muted mb-0 text-truncate">Hello, how are you?</p>
            </div>
          </div>
          <div class="text-end">
            <p class="small text-muted mb-1">Just now</p>
            <span class="badge bg-danger rounded-pill">3</span>
          </div>
        </a>
      </li>

      <!-- Vendor -->
      <li class="p-3 border-bottom chat-item" data-user="Mrs. Johnson">
        <a href="chat.php" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
          <div class="d-flex align-items-center">
            <div class="position-relative">
              <img src="https://via.placeholder.com/50" alt="avatar" class="rounded-circle me-2" width="50" height="50">
              <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-warning border border-light rounded-circle"></span>
            </div>
            <div class="ms-2">
              <p class="fw-bold mb-0">Mrs. Johnson</p>
              <p class="small text-muted mb-0">Vendor</p>
              <p class="small text-muted mb-0 text-truncate">New product available...</p>
            </div>
          </div>
          <div class="text-end">
            <p class="small text-muted mb-1">2 mins ago</p>
            <span class="badge bg-danger rounded-pill">1</span>
          </div>
        </a>
      </li>

    </ul>
  </div>
</div>

  </div>
</div>
  </div>
</div>
</div>

  </div>
</div>

<?php include "footer.php"; ?>