

<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Approved Listings</h3>
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
                  <a href="#">Approved Listings</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Approved Listings</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Approved Listings</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Listing Name</th>
            <th>Listing Type</th>
            <th>Pricing Type</th>
            <th>Price (NGN)</th>
            <th>User</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Listing Name</th>
            <th>Listing Type</th>
            <th>Pricing Type</th>
            <th>Price (NGN)</th>
            <th>User</th>
            <th>Status</th>
          
          </tr>
        </tfoot>
        <tbody>
          <tr>
             <td>Keys to eternity</td>
            <td>Product</td>
            <td>Paid</td>
            <td>₦50,000</td>
            <td>John Doe</td>
            <td><span class="badge bg-success">Approved</span></td>
            <td>
              <div class="form-button-action">
                   <a href="edit-listing.php" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Edit">
                  <i class="fa fa-edit"></i>
                </a>
                <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </td>
          </tr>
           <tr>
             <td>Keys to eternity</td>
            <td>Product</td>
            <td>Free</td>
            <td>₦0</td>
            <td>John David</td>
            <td><span class="badge bg-success">Approved</span></td>
            <td>
              <div class="form-button-action">
                <a href="edit-listing.php" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Edit">
                  <i class="fa fa-edit"></i>
                </a>
                <button type="button" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </td>
          </tr>
            </tbody>
        </table>


          </div>
        </div>
      </div>
    </div>
  </div>
  </div>



<?php include "footer.php"; ?>