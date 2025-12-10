
<?php include "header.php"; ?>



<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Upcoming Sessions</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Upcoming Sessions</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Upcoming Sessions</h4>
            <div>
              <button class="btn btn-sm btn-primary me-2" id="listViewBtn"><i class="fa fa-list"></i> List View</button>
              <button class="btn btn-sm btn-outline-primary" id="calendarViewBtn"><i class="fa fa-calendar"></i> Calendar View</button>
            </div>
          </div>

          <div class="card-body">
            <!-- List View -->
            <div id="listView" class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Booking Id</th>
                    <th>Full Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=get_service_bookings";
                  $data = curl_get_contents($url);
                  $modals_html = '';
                  $today = date('Y-m-d H:i:s');
                  $calendar_events = [];

                  if ($data !== false) {
                    $books = json_decode($data);

                    if (!empty($books)) {
                      foreach ($books as $book) {
                        // only approved + upcoming for this seller
                        if (
                          isset($book->status) &&
                          strtolower($book->status) === 'approved' &&
                          $book->seller_user_id == $buyerId &&
                          strtotime($book->preferred_datetime) >= strtotime($today)
                        ) {
                          $bookingId = htmlspecialchars($book->id ?? '', ENT_QUOTES);
                          if ($bookingId === '') continue;

                          $order_id = htmlspecialchars($book->order_id ?? '', ENT_QUOTES);
                          $full_name = htmlspecialchars($book->full_name ?? 'N/A', ENT_QUOTES);
                          $price_val = (float)($book->price ?? 0);
                          $price = number_format($price_val, 2);
                          $date_raw = $book->preferred_datetime ?? '';
                          $dateDisplay = $date_raw ? date('M d, Y h:i A', strtotime($date_raw)) : 'N/A';
                          $status = htmlspecialchars(ucfirst($book->status ?? 'Approved'), ENT_QUOTES);

                          // list view row
                          echo "<tr>
                                  <td>{$order_id}</td>
                                  <td>{$full_name}</td>
                                  <td>{$dateDisplay}</td>
                                  <td><span class='badge bg-success'>{$status}</span></td>
                                  <td>₦{$price}</td>
                                  <td>
                                    <button type='button' class='btn btn-link btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#bookingModal{$bookingId}'>
                                      <i class='fa fa-eye'></i> View
                                    </button>
                                  </td>
                                </tr>";

                          // modal
                          $notes_html = nl2br(htmlspecialchars($book->notes ?? '', ENT_QUOTES));
                          $contact = htmlspecialchars($book->contact ?? '', ENT_QUOTES);
                          $email = htmlspecialchars($book->email ?? '', ENT_QUOTES);
                          $location = htmlspecialchars($book->location ?? '', ENT_QUOTES);
                          $payment_status = htmlspecialchars(ucfirst($book->payment_status ?? 'Unpaid'), ENT_QUOTES);

                          $modals_html .= "
                          <div class='modal fade' id='bookingModal{$bookingId}' tabindex='-1' aria-labelledby='bookingModalLabel{$bookingId}' aria-hidden='true'>
                            <div class='modal-dialog modal-lg modal-dialog-centered'>
                              <div class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title fw-bold' id='bookingModalLabel{$bookingId}'>Session Details</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <table class='table table-bordered table-striped'>
                                    <tbody>
                                      <tr><th>Order ID</th><td>{$order_id}</td></tr>
                                      <tr><th>Full Name</th><td>{$full_name}</td></tr>
                                      <tr><th>Contact</th><td>{$contact}</td></tr>
                                      <tr><th>Email</th><td>{$email}</td></tr>
                                      <tr><th>Preferred Date/Time</th><td>{$dateDisplay}</td></tr>
                                      <tr><th>Location</th><td>{$location}</td></tr>
                                      <tr><th>Notes</th><td>{$notes_html}</td></tr>
                                      <tr><th>Price</th><td>₦{$price}</td></tr>
                                      <tr><th>Status</th><td>{$status}</td></tr>
                                      <tr><th>Payment Status</th><td>{$payment_status}</td></tr>
                                    </tbody>
                                  </table>
                                </div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                </div>
                              </div>
                            </div>
                          </div>";

                          // prepare calendar event
                          $calendar_events[] = [
                            'title' => $full_name . " - ₦{$price}",
                            'start' => date('Y-m-d\TH:i:s', strtotime($date_raw)),
                            'extendedProps' => [
                              'order_id' => $order_id,
                              'location' => $location,
                              'status' => $status
                            ]
                          ];
                        }
                      }
                    } else {
                      echo "<tr><td colspan='6' class='text-center text-muted'>No upcoming sessions found.</td></tr>";
                    }
                  } else {
                    echo "<tr><td colspan='6' class='text-center text-danger'>Unable to fetch data.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
              <?php echo $modals_html; ?>
            </div>

            <!-- Calendar View -->
            <div id="calendarView" style="display:none;">
              <div id="calendar"></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const listView = document.getElementById("listView");
  const calendarView = document.getElementById("calendarView");
  const listBtn = document.getElementById("listViewBtn");
  const calBtn = document.getElementById("calendarViewBtn");

  // initialize calendar first (but hidden)
  const calendarEl = document.getElementById("calendar");
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    themeSystem: "bootstrap5",
    height: 650,
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay"
    },
    events: <?php echo json_encode($calendar_events); ?>,
    eventDidMount: function(info) {
      // Add tooltip
      new bootstrap.Tooltip(info.el, {
        title: info.event.extendedProps.location || "No location",
        placement: "top",
        trigger: "hover",
        container: "body"
      });
    },
    // Better visibility
    eventColor: "#007bff",       // Blue background
    eventTextColor: "#000"       // White text
  });

  // Initially render the list view only
  listView.style.display = "block";
  calendarView.style.display = "none";

  // List View Button
  listBtn.addEventListener("click", () => {
    listView.style.display = "block";
    calendarView.style.display = "none";
    listBtn.classList.replace("btn-outline-primary", "btn-primary");
    calBtn.classList.replace("btn-primary", "btn-outline-primary");
  });

  // Calendar View Button
  calBtn.addEventListener("click", () => {
    listView.style.display = "none";
    calendarView.style.display = "block";
    listBtn.classList.replace("btn-primary", "btn-outline-primary");
    calBtn.classList.replace("btn-outline-primary", "btn-primary");

    // Wait for the view to be visible, then force re-render
    setTimeout(() => {
      calendar.render();
    }, 150);
  });
});
</script>


<?php include "footer.php"; ?>
