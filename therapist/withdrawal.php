
<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Withdrawal Requests & Payout Logs</h3>
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
                  <a href="#">Withdrawal Requests & Payout Logs</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Withdrawal Requests & Payout Logs</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Withdrawal Requests & Payout Logs</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
    <thead>
          <tr>
            <th>Request ID</th>
            <th>User</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>Request ID</th>
            <th>User</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Requested Date</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>
          <tr>
            <td>#W301</td>
            <td>John Doe</td>
            <td>$150</td>
            <td><span class="badge bg-warning text-dark">Pending</span></td>
            <td>2025-09-12</td>
            <td>
              <div class="form-button-action">
                <button class="btn btn-link btn-success btn-lg" data-bs-toggle="tooltip" title="Approve">
                  <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Reject">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr>
            <td>#W302</td>
            <td>Jane Smith</td>
            <td>$200</td>
            <td><span class="badge bg-success">Approved</span></td>
            <td>2025-09-10</td>
            <td>
              <div class="form-button-action">
                <button class="btn btn-link btn-info btn-lg" data-bs-toggle="tooltip" title="View">
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
  </div>


<?php include "footer.php"; ?>