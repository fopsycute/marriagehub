

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
            <th>Subcategory Name</th>
            <th>Parent Category</th>
            <th>Actions</th>
        
          </tr>
        </thead>
        <tfoot>
          <tr>
                <th>Subcategory Name</th>
            <th>Parent Category</th>
         
        
          </tr>
        </tfoot>
        <tbody>
    <?php
                        $url = $siteurl . "script/admin.php?action=allsub";
                        $data = curl_get_contents($url);

                        if ($data !== false) {
                          $cats = json_decode($data);
                          if (!empty($cats)) {
                            foreach ($cats as $cat) {
             $subcategory = $cat->subcategory;
              $subcategory_id = $cat->sub_id;
              $parent_category = $cat->parent_category;

                              ?>
                <tr>

                    <td><?php echo $subcategory; ?></td>
                  <td><?php echo $parent_category; ?></td>
                    <?php
                    echo "
                    <td>
                        <a href='edit-subcategory.php?subcategory_id=$subcategory_id' class='btn btn-link btn-primary btn-lg' data-bs-toggle='tooltip' title='Edit'>
                            <i class='fa fa-edit'></i> 
                        </a>
                        <a href='#' id='$subcategory_id' class='btn btn-link btn-danger  deletesubcategory' data-bs-toggle='tooltip' title='Delete'>
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