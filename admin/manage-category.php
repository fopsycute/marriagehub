
<?php include "header.php"; ?>
 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Manage Category</h3>
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
                  <a href="#">Manage Category</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Manage Category</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Manage Category</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Category Name</th>
            <th>Actions</th>
        
          </tr>
        </thead>
        <tfoot>
          <tr>
             <th>Category Name</th>
         
        
          </tr>
        </tfoot>
        <tbody>
    <?php
                        $url = $siteurl . "script/admin.php?action=categorylists";
                        $data = curl_get_contents($url);

                        if ($data !== false) {
                          $cats = json_decode($data);
                          if (!empty($cats)) {
                            foreach ($cats as $cat) {
                              $categoryName = htmlspecialchars($cat->category_name);
                              $categoryId = $cat->id;
                              ?>
                <tr>

                    <td><?php echo $categoryName; ?></td>
                
                    <?php
                    echo "
                    <td>
                        <a href='edit-category.php?category_id=$categoryId' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$categoryId' class='btn btn-link btn-danger  deletecategory' data-bs-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>
                    </td>";
                    ?>
                        <!-- Action buttons here -->
                   
                </tr>

                <?php
           }
                          } else {
                            echo '<option value="">No category found</option>';
                          }
                        } else {
                          echo '<option value="">Error fetching categories</option>';
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