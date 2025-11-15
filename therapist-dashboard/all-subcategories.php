
<?php include "header.php"; ?>

 <div class="container">
 <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">All Sub Categories</h3>
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
                  <a href="#">Sub Categories</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Sub Categories</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">All Sub Categories</h4>

                      <a href="add-sub-category.php" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Add Sub Category
                      </a>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
            <thead>
            <tr>
              <th>S/N</th>
              <th>Category Name</th>
              <th>Subcategory Name</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S/N</th>
              <th>Category Name</th>
              <th>Subcategory Name</th>
            </tr>
          </tfoot>
          <tbody>
            <tr>
              <td>1</td>
              <td>Marriage</td>
              <td>Premarital Counseling</td>
              <td>
                <div class="form-button-action">
                  <a href="edit-subcategory.php" type="button" class="btn btn-link btn-primary btn-lg">
                    <i class="fa fa-edit"></i>
                  </a>
                  <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Parenting</td>
              <td>Teen Parenting</td>
              <td>
                <div class="form-button-action">
                  <a href="edit-subcategory.php" type="button" class="btn btn-link btn-primary btn-lg">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <!-- More subcategories -->
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




