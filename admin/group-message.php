



<?php include "header.php"; ?>


 <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Messages to group leaders</h3>
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
                  <a href="#">Messages to group leaders</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Message</a>
                </li>
              </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h4 class="card-title">Messages to group leaders</h4>
                    <div class="card-tools mt-2">
                      <button id="messageAllBtn" class="btn btn-sm btn-primary">Message all group leaders</button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover">
                     <thead>
          <tr>
          
            <th>Title</th>
            <th>Group Leader</th>
            <th>Email</th>
            <th></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
           <th>Title</th>
            <th>Group Leader</th>
            <th>Email</th>
            <th></th>
          </tr>
        </tfoot>
        <tbody>
          <?php
$url = $siteurl . "script/admin.php?action=grouplists";
$data = curl_get_contents($url);

if ($data !== false) {
    $groups = json_decode($data);

    if (!empty($groups)) {
        foreach ($groups as $group) {

            // ✅ Only display groups where status is "active"
            if (isset($group->status) && strtolower($group->status) === 'active' && $group->user_id != $buyerId) {
                 // Fetch member count
                $groupId = $group->id;
                $title = $group->group_name;
                $author = $group->first_name . ' ' . $group->last_name;
                $email = $group->email;
                ?>
        <tr>
          <td><?php echo htmlspecialchars($title); ?></td>
          <td><?php echo htmlspecialchars($author); ?></td>
          <td><?php echo htmlspecialchars($email); ?></td>
          <td>

            <a href='#' id='<?php echo $groupId; ?>' class='btn btn-link btn-danger deletegroup' data-bs-toggle='tooltip' title='Delete'>
              <i class='fa fa-trash'></i>
            </a>

            <!-- Message icon to open modal for this group's leader -->
            <button class="btn btn-link btn-success" data-bs-toggle="modal" data-bs-target="#sendMessageModal-<?php echo $groupId; ?>" title="Send message">
              <i class="fa fa-envelope"></i>
            </button>
            
            <!-- Per-group modal (no JS required to open) -->
            <div class="modal fade" id="sendMessageModal-<?php echo $groupId; ?>" tabindex="-1" aria-labelledby="sendMessageModalLabel-<?php echo $groupId; ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="sendMessageModalLabel-<?php echo $groupId; ?>">Message: <?php echo htmlspecialchars($author ?: $email); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="<?php echo $siteurl; ?>script/admin.php" method="POST">
                      <input type="hidden" name="action" value="send_group_message">
                      <input type="hidden" name="user_id" value="<?php echo intval($group->user_id ?? 0); ?>">
                      <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Optional title">
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="6" required></textarea>
                      </div>
                      <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
                <?php
            }
        }
    }
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



<?php
// Render a modal for sending to all leaders (uses normal bootstrap data attributes)
?>
<!-- Message All Modal -->
<div class="modal fade" id="messageAllModal" tabindex="-1" aria-labelledby="messageAllModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageAllModalLabel">Message all group leaders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?php echo $siteurl; ?>script/admin.php" method="POST">
          <input type="hidden" name="action" value="send_group_message">
          <input type="hidden" name="send_all" value="1">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" placeholder="Optional title">
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="6" required></textarea>
          </div>
          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Send to all</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>