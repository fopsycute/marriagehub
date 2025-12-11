


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Approved Questions</h3>
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
                  <a href="#">Approved Questions</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Questions</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Approved Questions</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                          <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=questionlists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);

    if (!empty($blogs)) {
        foreach ($blogs as $blog) {

            // ✅ Only display blogs where status is "pending"
            if (isset($blog->status)  && $blog->group_id == ''  && $blog->status == 'active') {
                $questionId = $blog->id;
                $title = $blog->title;
                $author = $blog->first_name . ' ' . $blog->last_name;
                $status = $blog->status;
                $content = limitWords($blog->article, 5);
                $date = date('M d, Y', strtotime($blog->created_at));
                $isPinned = $blog->is_pinned ?? 0;
                $pinIcon = $isPinned ? 'fa-thumb-tack' : 'fa-thumb-tack';
                $pinTitle = $isPinned ? 'Unpin Question' : 'Pin Question';
                $pinClass = $isPinned ? 'btn-warning' : 'btn-secondary';
                
                if ($status === "notactive") {
                    $statuslog = 'danger';
                }

                 if ($status === "notactive") {
                    $statuslog = 'danger';
                } else if ($status === "pending") {
                    $statuslog = 'warning';
                } else if ($status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>
                
                    <td>
                        <?php if ($isPinned): ?>
                            <i class="fa fa-thumb-tack text-warning me-1"></i>
                        <?php endif; ?>
                        <?php echo $title; ?>
                    </td>
                    <td><?php echo $content; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo $status; ?></span></td>
     

                    <?php
                    echo "
                    <td>
                        <a href='#' class='btn btn-link $pinClass pin-question-btn' data-question-id='$questionId' data-is-pinned='$isPinned' data-bs-toggle='tooltip' title='$pinTitle'>
                            <i class='fa $pinIcon'></i>
                        </a>
                        <a href='edit-question.php?question_id=$questionId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fas fa-edit'></i> 
                        </a>
                        <a href='#' id='$questionId' class='btn btn-link btn-danger  deletequestion' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fas fa-trash'></i>
                        </a>
                    </td>";
                    ?>
                  
                </tr>

                <?php
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

<script>
$(document).ready(function() {
    // Pin/Unpin question handler
    $('.pin-question-btn').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const questionId = btn.data('question-id');
        const isPinned = btn.data('is-pinned');
        const newPinStatus = isPinned ? 0 : 1;
        const siteUrl = $('#siteurl').val();
        
        $.ajax({
            url: siteUrl + 'script/admin.php',
            type: 'POST',
            data: {
                action: 'togglePinQuestion',
                question_id: questionId,
                is_pinned: newPinStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to update pin status');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>

<?php include "footer.php"; ?>