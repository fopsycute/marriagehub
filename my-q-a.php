

<?php 
$requireLogin = true;

include "header.php"; 

?>

<!-- Top Banner Advert -->
<?php
$placementSlug = 'my-qa-page-top-banner';
include "listing-banner.php";
?>

<div class="container">
          <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                <div class="card mt-5 mb-5">
                 <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">All Questions</h4>
                    <a href="<?php echo $siteurl; ?>create-questions.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Add Question
                    </a>
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
            if (isset($blog->status) && $blog->user_id == $buyerId && $blog->group_id == '') {
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
                } else if ($status === "draft") {
                    $statuslog = 'info';
                } else {
                    $statuslog = 'secondary';
                }
                ?>
                <tr>
                
                    <td><?php echo $title; ?></td>
                    <td><?php echo $content; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
     

                    <?php
                    echo "
                    <td>
                        <a href='edit-question.php?question_id=$questionId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='bi bi-pencil'></i> 
                        </a>
                        <a href='#' id='$questionId' class='btn btn-link btn-danger  deletequestion' data-bs-toggle='tooltip' title='Delete'>
                            <i class='bi bi-trash'></i>
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
