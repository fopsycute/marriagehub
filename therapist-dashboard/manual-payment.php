<?php include "header.php"; ?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manual Refund Requests</h3>
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
          <a href="#">Payments</a>
        </li>
        <li class="separator">
          <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
          <a href="#">Refunds</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Manual Refund Requests</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table
                id="multi-filter-select"
                class="display table table-striped table-hover"
              >
                <thead>
                  <tr>
                    <th>Refund ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Booking ID</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th>Refund ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Booking ID</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </tfoot>
                <tbody>
                  <tr>
                    <td>#R101</td>
                    <td>John Doe</td>
                    <td>$100</td>
                    <td>#B501</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td>
                      <div class="form-button-action">
                        <button
                          type="button"
                          data-bs-toggle="tooltip"
                          title="Approve Refund"
                          class="btn btn-link btn-success btn-lg"
                        >
                          <i class="fa fa-check"></i>
                        </button>
                        <button
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#rejectModal"
                          title="Reject Refund"
                          class="btn btn-link btn-danger"
                        >
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>#R102</td>
                    <td>Jane Smith</td>
                    <td>$250</td>
                    <td>#B502</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td>
                      <div class="form-button-action">
                        <button
                          type="button"
                          data-bs-toggle="tooltip"
                          title="Approve Refund"
                          class="btn btn-link btn-success btn-lg"
                        >
                          <i class="fa fa-check"></i>
                        </button>
                        <button
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#rejectModal"
                          title="Reject Refund"
                          class="btn btn-link btn-danger"
                        >
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>#R103</td>
                    <td>Samuel Johnson</td>
                    <td>$75</td>
                    <td>#B503</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td>
                      <div class="form-button-action">
                        <button
                          type="button"
                          data-bs-toggle="tooltip"
                          title="Approve Refund"
                          class="btn btn-link btn-success btn-lg"
                        >
                          <i class="fa fa-check"></i>
                        </button>
                        <button
                          type="button"
                          data-bs-toggle="modal"
                          data-bs-target="#rejectModal"
                          title="Reject Refund"
                          class="btn btn-link btn-danger"
                        >
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
</div>

<!-- Reject Modal -->
<div
  class="modal fade"
  id="rejectModal"
  tabindex="-1"
  aria-labelledby="rejectModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel">Reject Refund</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="rejectReason" class="form-label"
              >Reason for Rejection</label
            >
            <textarea
              class="form-control"
              id="rejectReason"
              rows="4"
              placeholder="Enter reason..."
            ></textarea>
          </div>
          <button type="submit" class="btn btn-danger">Submit Rejection</button>
        </form>
      </div>
    </div>
  </div>
</div>




<?php include "footer.php"; ?>