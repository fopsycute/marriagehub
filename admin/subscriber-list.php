
<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Subscriber List</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="#">
            <i class="icon-home"></i>
          </a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Subscribers</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Subscriber List</a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Subscriber List</h4>
            <button id="downloadSubscribers" class="btn btn-primary mb-3">
                <i class="fa fa-download"></i> Download CSV
            </button>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Subscriber ID</th>
                    <th>Date</th>
                  
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>Subscriber ID</th>
                     <th>Date</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=subscriberlists";
                  $data = curl_get_contents($url);
                  if ($data !== false) {
                      $subscribers = json_decode($data);

                      if (!empty($subscribers)) {
                         

                          foreach ($subscribers as $subscriber) {
                              // Only show bookings for this seller that have passed
                              if (isset($subscriber->email)) {
                                  $subscriber_email = htmlspecialchars($subscriber->email);
                                  $date_raw = $subscriber->subscribed_at ?? '';
                                 $dateDisplay = $date_raw ? date('M d, Y h:i A', strtotime($date_raw)) : 'N/A';

                                  echo "<tr>";
                                  echo "<td>" . $subscriber_email . "</td>";
                                  echo "<td>" . $dateDisplay . "</td>";
                                  echo "</tr>";
                              }
                            }
                        }
                    }

                    ?>
                    </tbody>
        </table>


          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  </div>

<?php include "footer.php"; ?>