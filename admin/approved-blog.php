

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Approved Blogs</h3>
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
                  <a href="#">Approved Blogs</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Blogs</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Approved Blogs</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>featured image</th>
            <th>Title</th>
            <th>Author</th>
            <th>Content</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
           <th>featured image</th>
            <th>Title</th>
            <th>Author</th>
            <th>Content</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Date</th>
            <th>Status</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
            <?php
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);

    if (!empty($blogs)) {
        foreach ($blogs as $blog) {

            // ✅ Only display blogs where status is "pending"
            if (isset($blog->status) && strtolower($blog->status) === 'active' && $blog->group_id == '') {

                $blogId = $blog->id;
                $title = $blog->title;
                $category = $blog->category_names;
                $subcategory = $blog->subcategory_names;
                $author = $blog->first_name . ' ' . $blog->last_name;
                $content = limitWords($blog->article, 5);
                $date = date('M d, Y', strtotime($blog->created_at));
                $blogimage = $siteurl . $imagePath . $blog->featured_image;
                $isPinned = $blog->is_pinned ?? 0;
                $pinIcon = $isPinned ? 'fa-thumb-tack' : 'fa-thumb-tack';
                $pinTitle = $isPinned ? 'Unpin Post' : 'Pin Post';
                $pinClass = $isPinned ? 'btn-warning' : 'btn-secondary';
                ?>
                <tr>
                    <td><img src="<?php echo $blogimage; ?>" class="small-image" alt="featured"></td>
                    <td>
                        <?php if ($isPinned): ?>
                            <i class="fa fa-thumb-tack text-warning me-1"></i>
                        <?php endif; ?>
                        <?php echo $title; ?>
                    </td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $content; ?></td>
                     <td><?php echo $category; ?></td>
                      <td><?php echo $subcategory; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-success">Published</span></td>
                    <?php
                    echo "
                    <td>
                        <a href='#' class='btn btn-link $pinClass pin-blog-btn' data-blog-id='$blogId' data-is-pinned='$isPinned' data-bs-toggle='tooltip' title='$pinTitle'>
                            <i class='fa $pinIcon'></i>
                        </a>
                        <a href='edit-blog.php?blog_id=$blogId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$blogId' class='btn btn-link btn-danger  deleteblog' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>
                    </td>";
                    ?>
                        <!-- Action buttons here -->
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

<script>
$(document).ready(function() {
    // Pin/Unpin blog handler
    $('.pin-blog-btn').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const blogId = btn.data('blog-id');
        const isPinned = btn.data('is-pinned');
        const newPinStatus = isPinned ? 0 : 1;
        const siteUrl = $('#siteurl').val();
        
        $.ajax({
            url: siteUrl + 'script/admin.php',
            type: 'POST',
            data: {
                action: 'togglePinBlog',
                blog_id: blogId,
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