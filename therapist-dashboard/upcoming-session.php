
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Upcoming Booking Sessions</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">My Bookings</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Upcoming Sessions</h4>

            <div>
              <button class="btn btn-sm btn-primary me-2" id="listViewBtn">
                <i class="fa fa-list"></i> List View
              </button>

              <button class="btn btn-sm btn-outline-primary" id="calendarViewBtn">
                <i class="fa fa-calendar"></i> Calendar View
              </button>
            </div>
          </div>

          <div class="card-body">

            <!-- LIST VIEW -->
            <div id="listView" class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Date</th>
                    <th>Booking Status</th>
                    <th>Payment Status</th>
                    <th>Amount Paid</th>
                    <th></th>
                  </tr>
                </thead>

                <tbody>
                <?php
                  $url = $siteurl . "script/admin.php?action=bookinglist";
                  $data = curl_get_contents($url);

                  $calendar_events = [];
                  $today = date('Y-m-d H:i:s');

                  if ($data !== false) {
                      $bookings = json_decode($data);

                      if (!empty($bookings)) {
                          foreach ($bookings as $booking) {

                              // show only: therapist's bookings + upcoming dates
                              if ($booking->therapist_id == $buyerId &&
                                  strtotime($booking->consultation_date) 
                              ) {

                                  $bookingId = $booking->id;
                                  $reference = htmlspecialchars($booking->reference ?? 'N/A');
                                  $client_name  = htmlspecialchars($booking->client_name);
                                  $client_email = htmlspecialchars($booking->client_email);
                                  $dateDisplay  = date('M d, Y h:i A', strtotime($booking->consultation_date));
                                  $amount = $sitecurrency . number_format((float)$booking->amount, 2);

                                  $bookingStatus = strtolower($booking->booking_status);
                                  $paymentStatus = strtolower($booking->payment_status);

                                  $bookingBadge = match ($bookingStatus) {
                                      'pending' => 'warning',
                                      'confirmed' => 'success',
                                      'cancelled' => 'danger',
                                      default => 'secondary'
                                  };

                                  $paymentBadge = match ($paymentStatus) {
                                      'paid' => 'success',
                                      'unpaid' => 'danger',
                                      'pending' => 'warning',
                                      default => 'secondary'
                                  };

                                  echo "
                                  <tr>
                                    <td>{$reference}</td>
                                    <td>{$client_name}</td>
                                    <td>{$client_email}</td>
                                    <td>{$dateDisplay}</td>
                                    <td><span class='badge bg-{$bookingBadge}'>" . ucfirst($bookingStatus) . "</span></td>
                                    <td><span class='badge bg-{$paymentBadge}'>" . ucfirst($paymentStatus) . "</span></td>
                                    <td>{$amount}</td>
                                    <td>
                                      <a href='edit-booking.php?booking_id={$bookingId}' class='btn btn-link btn-primary btn-lg'>
                                        <i class='fa fa-edit'></i>
                                      </a>
                                      <a href='#' id='{$bookingId}' class='btn btn-link btn-danger deletebooking'>
                                        <i class='fa fa-trash'></i>
                                      </a>
                                    </td>
                                  </tr>
                                  ";

                                  // add event to calendar
                                  $calendar_events[] = [
                                    'title' => $client_name . " - " . $amount,
                                    'start' => date('Y-m-d\TH:i:s', strtotime($booking->consultation_date)),
                                    'extendedProps' => [
                                      'email' => $client_email,
                                      'status' => ucfirst($bookingStatus),
                                      'payment' => ucfirst($paymentStatus),
                                    ]
                                  ];
                              }
                          }
                      } else {
                          echo "<tr><td colspan='7' class='text-center text-muted'>No bookings found.</td></tr>";
                      }
                  } else {
                      echo "<tr><td colspan='7' class='text-center text-danger'>Failed to fetch data.</td></tr>";
                  }
                ?>
                </tbody>
              </table>
            </div>

            <!-- CALENDAR VIEW -->
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
document.addEventListener("DOMContentLoaded", function() {

  const listView = document.getElementById("listView");
  const calendarView = document.getElementById("calendarView");
  const listBtn = document.getElementById("listViewBtn");
  const calBtn = document.getElementById("calendarViewBtn");

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
    eventColor: "#007bff",
    eventTextColor: "#fff",
    eventDidMount: function(info) {
      new bootstrap.Tooltip(info.el, {
        title: "Status: " + info.event.extendedProps.status +
               "\nPayment: " + info.event.extendedProps.payment,
        placement: "top",
        trigger: "hover",
        container: "body"
      });
    }
  });

  // default = list view
  listView.style.display = "block";
  calendarView.style.display = "none";

  listBtn.addEventListener("click", () => {
    listView.style.display = "block";
    calendarView.style.display = "none";
    listBtn.classList.replace("btn-outline-primary", "btn-primary");
    calBtn.classList.replace("btn-primary", "btn-outline-primary");
  });

  calBtn.addEventListener("click", () => {
    listView.style.display = "none";
    calendarView.style.display = "block";
    listBtn.classList.replace("btn-primary", "btn-outline-primary");
    calBtn.classList.replace("btn-outline-primary", "btn-primary");

    setTimeout(() => {
      calendar.render();
    }, 150);
  });

});
</script>

<?php include "footer.php"; ?>
