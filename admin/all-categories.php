<?php include "header.php"; ?>

 <div class="container">
 <div class="page-inner">
  <div class="page-header">
  <h3 class="fw-bold mb-3">All Categories</h3>
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
                  <a href="#">Categories</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Categories</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                     <div class="d-flex align-items-center">
                    <h4 class="card-title">All Categories</h4>
                    <a href="add-category.php" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Add Category
                      </a>
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
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S/N</th>
              <th>Category Name</th>
           
            </tr>
          </tfoot>
          <tbody>
            <tr>
              <td>1</td>
              <td>Marriage</td>
              <td>
                <div class="form-button-action">
                  <button type="button" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Parenting</td>
              <td>
                <div class="form-button-action">
                  <a href="edit-category.php" type="button" class="btn btn-link btn-primary btn-lg">
                    <i class="fa fa-edit"></i>
                  </a>
                  <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <!-- More categories -->
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




