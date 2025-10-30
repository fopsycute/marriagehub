


<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Pending Questions</h3>
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
                  <a href="#">Pending Questions</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Pending Questions</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Pending Questions</h4>
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
            if (isset($blog->status)  && $blog->group_id == ''  && $blog->status == 'pending') {
                $questionId = $blog->id;
                $title = $blog->title;
                $author = $blog->first_name . ' ' . $blog->last_name;
                $status = $blog->status;
                $content = limitWords($blog->article, 5);
                $date = date('M d, Y', strtotime($blog->created_at));
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
                
                    <td><?php echo $title; ?></td>
                    <td><?php echo $content; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo $status; ?></span></td>
     

                    <?php
                    echo "
                    <td>
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



<?php include "footer.php"; ?>