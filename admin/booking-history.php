
<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Booking History</h3>
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
                  <a href="#">Booking History</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Booking History</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Booking History</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
            <th>Booking ID</th>
            <th>User</th>
            <th>Service</th>
            <th>Date</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Booking ID</th>
            <th>User</th>
            <th>Service</th>
            <th>Date</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>
          <tr>
            <td>#B501</td>
            <td>John Doe</td>
            <td>Marriage Counseling</td>
            <td>2025-09-10</td>
            <td><span class="badge bg-success">Confirmed</span></td>
            <td>$100</td>
            <td>
              <div class="form-button-action">
                <button class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="View">
                  <i class="fa fa-eye"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>#B502</td>
            <td>Jane Smith</td>
            <td>Family Therapy</td>
            <td>2025-09-15</td>
            <td><span class="badge bg-warning text-dark">Pending</span></td>
            <td>$250</td>
            <td>
              <div class="form-button-action">
                <button class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="View">
                  <i class="fa fa-eye"></i>
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