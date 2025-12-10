
<?php include "header.php"; ?>

 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Disputes & Reports</h3>
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
                  <a href="#">Disputes & Reports</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Disputes & Reports</a>
                </li>
              </ul>
            </div>
            <div class="row">

             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Disputes & Reports</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                        <thead>
            <tr>
              <th>S/N</th>
              <th>Complainant</th>
              <th>Against</th>
              <th>Complaint Type</th>
              <th>Date</th>
              <th>Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>John Doe (User)</td>
              <td>Jane Smith (Vendor)</td>
              <td>Payment not received</td>
              <td>2025-09-01</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td class="text-center">
                <a href="disputes-chat.php" class="btn btn-sm btn-success"><i class="fa fa-eye"></i>View</a>
                <button class="btn btn-sm btn-danger" title="delete">
                  <i class="fa fa-trash"></i>Delete
                </button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Vendor A</td>
              <td>User B</td>
              <td>Service not delivered</td>
              <td>2025-09-05</td>
              <td><span class="badge bg-success">Resolved</span></td>
              <td class="text-center">
                <a href="disputes-chat.php" class="btn btn-sm btn-success"><i class="fa fa-eye"></i>View</a>
                <button class="btn btn-sm btn-danger" title="delete">
                  <i class="fa fa-trash"></i>Delete
                </button>
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




