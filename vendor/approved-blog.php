

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
                ?>
                <tr>
                    <td><img src="<?php echo $blogimage; ?>" class="small-image" alt="featured"></td>
                    <td><?php echo $title; ?></td>
                    <td><?php echo $author; ?></td>
                    <td><?php echo $content; ?></td>
                     <td><?php echo $category; ?></td>
                      <td><?php echo $subcategory; ?></td>
                    <td><?php echo $date; ?></td>
                    <td><span class="badge bg-success">Published</span></td>
                    <?php
                    echo "
                    <td>
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



<?php include "footer.php"; ?>