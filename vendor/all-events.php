<?php include "header.php"; ?>

 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">All Events</h3>
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
                  <a href="#">Events</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Events</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">All Events</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                        <thead>
                          <tr>
                  <th>Event Name</th>
              <th>Date</th>
              <th>Location</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>Event Name</th>
              <th>Date</th>
              <th>Location</th>
              <th>Status</th>
           
            </tr>
          </tfoot>
          <tbody>
            <tr>
              <td>Marriage Counseling Session</td>
              <td>2025-09-10</td>
              <td>Lagos</td>
              <td> <span class="badge bg-success"> Confirmed </span> </td>
              <td>
                <div class="form-button-action">
                  <a href="edit-event.php"
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Edit Event"
                    class="btn btn-link btn-primary btn-lg"
                  >
                    <i class="fa fa-edit"></i>
                  </a>
                  <button
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Remove"
                    class="btn btn-link btn-danger"
                  >
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr>
              <td>Family Therapy Workshop</td>
              <td>2025-09-15</td>
              <td>Abuja</td>
              <td><span class="badge bg-warning text-dark"> Pending </span></td>
              <td>
                <div class="form-button-action">
                   <a href="edit-event.php"
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Edit Event"
                    class="btn btn-link btn-primary btn-lg"
                  >
                    <i class="fa fa-edit"></i>
                  </a>
                  <button
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Remove"
                    class="btn btn-link btn-danger"
                  >
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr>
              <td>Premarital Counseling</td>
              <td>2025-09-20</td>
              <td>Port Harcourt</td>
              <td> <span class="badge bg-danger"> Cancelled </span></td>
              <td>
                <div class="form-button-action">
                   <a href="edit-event.php"
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Edit Event"
                    class="btn btn-link btn-primary btn-lg"
                  >
                    <i class="fa fa-edit"></i>
                  </a>
                  <button
                    type="button"
                    data-bs-toggle="tooltip"
                    title="Remove"
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
 <?php include "footer.php"; ?>




