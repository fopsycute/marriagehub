<?php include "header.php"; ?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Reported Items</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home">
          <a href="index.php">
            <i class="icon-home"></i>
          </a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">Reports</a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">Reported Items</a>
        </li>
      </ul>
    </div>
    
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">All Reported Items</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Item Type</th>
                    <th>Item Title</th>
                    <th>Reporter</th>
                    <th>Reason</th>
                    <th>Date Reported</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>ID</th>
                    <th>Item Type</th>
                    <th>Item Title</th>
                    <th>Reporter</th>
                    <th>Reason</th>
                    <th>Date Reported</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  $url = $siteurl . "script/admin.php?action=reportslists";
                  $data = curl_get_contents($url);

                  if ($data !== false) {
                      $reports = json_decode($data);

                      if (!empty($reports)) {
                          foreach ($reports as $report) {
                              $report_id = $report->id ?? '';
                              $item_type = ucfirst($report->item_type ?? 'Unknown');
                              $item_title = $report->item_title ?? 'N/A';
                              $item_slug = $report->item_slug ?? '';
                              $first_name = $report->first_name ?? '';
                              $last_name = $report->last_name ?? '';
                              $reporter_name = trim($first_name . ' ' . $last_name);
                              $reporter_email = $report->email ?? '';
                              $reason = $report->reason ?? '';
                              $custom_reason = $report->custom_reason ?? '';
                              $created_at = $report->created_at ?? '';
                              $status = $report->status ?? 'pending';
                              $item_id = $report->item_id ?? '';
                              $user_id = $report->user_id ?? '';

                              // Create link based on item type using slug
                              $item_link = '#';
                              if ($item_type == 'Blog' && $item_slug) {
                                  $item_link = $siteurl . "blog-details/" . $item_slug;
                              } elseif ($item_type == 'Question' && $item_slug) {
                                  $item_link = $siteurl . "single-questions/" . $item_slug;
                              } elseif ($item_type == 'Group' && $item_slug) {
                                  $item_link = $siteurl . "group/" . $item_slug;
                              }

                              // Display reason with custom reason if available
                              $full_reason = $reason;
                              if ($custom_reason) {
                                  $full_reason .= ': ' . substr($custom_reason, 0, 50);
                                  if (strlen($custom_reason) > 50) {
                                      $full_reason .= '...';
                                  }
                              }
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($report_id); ?></td>
                    <td><span class="badge bg-info"><?php echo htmlspecialchars($item_type); ?></span></td>
                    <td>
                      <a href="<?php echo htmlspecialchars($item_link); ?>" target="_blank" title="<?php echo htmlspecialchars($item_title); ?>">
                        <?php 
                        $display_title = strlen($item_title) > 40 ? substr($item_title, 0, 40) . '...' : $item_title;
                        echo htmlspecialchars($display_title); 
                        ?>
                      </a>
                    </td>
                    <td>
                      <?php echo htmlspecialchars($reporter_name); ?>
                      <br>
                      <small class="text-muted"><?php echo htmlspecialchars($reporter_email); ?></small>
                    </td>
                    <td title="<?php echo htmlspecialchars($custom_reason); ?>">
                      <?php echo htmlspecialchars($full_reason); ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($created_at)); ?></td>
                    <td>
                      <span class="badge bg-<?php echo getBadgeColor($status); ?>">
                        <?php echo htmlspecialchars($status); ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="<?php echo htmlspecialchars($item_link); ?>" 
                           class="btn btn-sm btn-primary" 
                           target="_blank" 
                           title="View Item">
                          <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-success resolve-report" 
                                data-report-id="<?php echo $report_id; ?>"
                                title="Mark as Resolved">
                          <i class="fas fa-check"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-sm btn-danger delete-report" 
                                data-report-id="<?php echo $report_id; ?>"
                                title="Delete Report">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php
                          }
                      } else {
                          echo '<tr><td colspan="8" class="text-center">No reports found</td></tr>';
                      }
                  } else {
                      echo '<tr><td colspan="8" class="text-center text-danger">Error loading reports</td></tr>';
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

<!-- Report Details Modal -->
<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-labelledby="reportDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reportDetailsModalLabel">Report Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="reportDetailsContent">
        <!-- Content will be loaded here via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Initialize DataTable
  $("#multi-filter-select").DataTable({
    pageLength: 25,
    order: [[0, 'desc']], // Sort by ID descending (newest first)
    initComplete: function() {
      this.api().columns([1, 6]).every(function() { // Item Type and Status columns
        var column = this;
        var select = $('<select class="form-select form-select-sm"><option value="">All</option></select>')
          .appendTo($(column.footer()).empty())
          .on("change", function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            column.search(val ? "^" + val + "$" : "", true, false).draw();
          });

        column.data().unique().sort().each(function(d, j) {
          // Extract text from badge if present
          var text = $(d).text() || d;
          select.append('<option value="' + text + '">' + text + '</option>');
        });
      });
    }
  });

  // Resolve report
  $(document).on('click', '.resolve-report', function() {
    var reportId = $(this).data('report-id');
    
    if (confirm('Are you sure you want to mark this report as resolved?')) {
      $.ajax({
        url: '<?php echo $siteurl; ?>script/admin.php',
        method: 'POST',
        data: {
          action: 'resolve_report',
          report_id: reportId
        },
        success: function(response) {
          var result = JSON.parse(response);
          if (result.status === 'success') {
            alert('Report marked as resolved!');
            location.reload();
          } else {
            alert('Error: ' + (result.message || 'Unknown error'));
          }
        },
        error: function() {
          alert('Failed to resolve report. Please try again.');
        }
      });
    }
  });

  // Delete report
  $(document).on('click', '.delete-report', function() {
    var reportId = $(this).data('report-id');
    
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
      $.ajax({
        url: '<?php echo $siteurl; ?>script/admin.php',
        method: 'POST',
        data: {
          action: 'delete_report',
          report_id: reportId
        },
        success: function(response) {
          var result = JSON.parse(response);
          if (result.status === 'success') {
            alert('Report deleted successfully!');
            location.reload();
          } else {
            alert('Error: ' + (result.message || 'Unknown error'));
          }
        },
        error: function() {
          alert('Failed to delete report. Please try again.');
        }
      });
    }
  });
});
</script>

<?php include "footer.php"; ?>
