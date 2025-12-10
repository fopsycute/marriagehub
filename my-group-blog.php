
<?php 
include "header.php"; 

if (isset($_GET['slug'])) {
    $groupslug = $_GET['slug'];
    $sitelink = $siteurl . "script/";
    $url = $sitelink . "admin.php?action=fetchgroupslug&slug=" . $groupslug;

    $data = curl_get_contents($url);
    if ($data !== false) {
        $groupdetails = json_decode($data);
        if (!empty($groupdetails)) {
            $groupdetail = $groupdetails[0]; 
            $group_id  = $groupdetail->id ?? '';
            $group_name  = $groupdetail->group_name ?? '';
        } else {
            echo "<div class='alert alert-warning'>No group found with the given ID.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error fetching group data. Please try again later.</div>";
    }
} else {
    header("Location: $siteurl");
    exit;
}

// ✅ Get Group Creator Info
$groupSlug = '';
$groupCreatorId = 0;
if (!empty($group_id)) {
    $groupUrl = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $group_id;
    $groupData = curl_get_contents($groupUrl);
    if ($groupData !== false) {
        $groupInfo = json_decode($groupData);
        if (!empty($groupInfo[0])) {
            $groupSlug = $groupInfo[0]->slug ?? '';
            $groupCreatorId = $groupInfo[0]->user_id ?? 0;
        }
    }
}

// ✅ Check login cookies
$adminAuth  = $_COOKIE['admin_auth']  ?? '';
$vendorAuth = $_COOKIE['vendor_auth'] ?? '';
$therapistAuth = $_COOKIE['therapist_auth'] ?? '';
$userAuth   = $_COOKIE['user_auth']   ?? '';

$canAccess = false;
$buyerId = 0;
$userRole = 'member'; // default

// ✅ If current user is group creator
if (
    ($adminAuth) || 
    ($vendorAuth == $groupCreatorId) || 
    ($therapistAuth == $groupCreatorId) || 
    ($userAuth == $groupCreatorId)
) {
    if ($adminAuth) {
        $buyerId = $adminAuth;
    } elseif ($vendorAuth == $groupCreatorId) {
        $buyerId = $vendorAuth;
    } elseif ($therapistAuth == $groupCreatorId) {
        $buyerId = $therapistAuth;
    } elseif ($userAuth == $groupCreatorId) {
        $buyerId = $userAuth;
    }
    $canAccess = true;
    $userRole = 'creator'; // mark as creator
} else {
    // ✅ Otherwise, check if user is a group member
    $activeUserId = $adminAuth ?: ($vendorAuth ?: ($userAuth ?: $therapistAuth));
    if (!empty($activeUserId) && !empty($group_id)) {
        $checkMemberUrl = $sitelink . "admin.php?action=checkuserMember&group_id={$group_id}&user_id={$activeUserId}";
        $memberData = curl_get_contents($checkMemberUrl);

        if ($memberData !== false) {
            $memberResult = json_decode($memberData, true);
            if (!empty($memberResult[0]) && strtolower($memberResult[0]['status']) === 'active') {
                $canAccess = true;
                $buyerId = $activeUserId;
                $userRole = strtolower($memberResult[0]['role']); // admin, subadmin, member
            }
        }
    }
}

// 🚫 Restrict access if not authorized
if (!$canAccess) {
    echo "
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Join Group to View',
            html: 'You are not a member of this group. <br><b>Join our group to view this blog post.</b>',
            confirmButtonText: 'Join Group',
            showCancelButton: true,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{$siteurl}group/{$groupSlug}';
            } else {
                window.location.href = '{$siteurl}';
            }
        });
    </script>";
    exit;
}
?>

<div class="container py-5 mb-5">
  <div class="page-inner">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Group Blogs</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="multi-filter-select" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Featured Image</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Content</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
<?php
$url = $siteurl . "script/admin.php?action=bloglists";
$data = curl_get_contents($url);

if ($data !== false) {
    $blogs = json_decode($data);
    if (!empty($blogs)) {
        foreach ($blogs as $blog) {
            if (isset($blog->status) && $blog->group_id == $group_id && $blog->user_id == $buyerId ) {

                // ✅ Member only sees their own post
                

                $blogId = $blog->id;
                $title = limitWords($blog->title, 5);
                $author = $blog->first_name . ' ' . $blog->last_name;
                $content = limitWords($blog->article, 5);
                $date = date('M d, Y', strtotime($blog->created_at));
                $blogimage = $siteurl . $imagePath . $blog->featured_image;

                // ✅ Determine badge color
                $status = strtolower($blog->status);
                if ($status === "notactive") {
                    $statuslog = 'danger';
                } elseif ($status === "pending") {
                    $statuslog = 'warning';
                } elseif ($status === "active") {
                    $statuslog = 'success';
                } else {
                    $statuslog = 'secondary';
                }
?>
<tr>
  <td><img src="<?php echo $blogimage; ?>" class="small-image" alt="featured"></td>
  <td><?php echo $title; ?></td>
  <td><?php echo $author; ?></td>
  <td><?php echo $content; ?></td>
  <td><?php echo $date; ?></td>
  <td><span class="badge bg-<?php echo $statuslog; ?>"><?php echo ucfirst($status); ?></span></td>
  <td>
    <a href='<?php echo $siteurl; ?>edit-group-blog.php?blog_id=<?php echo $blogId; ?>' class='btn btn-link btn-primary btn-lg' title='Edit'>
      <i class='bi bi-pencil'></i>
    </a>
    <a href='#' id='<?php echo $blogId; ?>' class='btn btn-link btn-danger deleteblog' title='Delete'>
      <i class='bi bi-trash'></i>
    </a>
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
</div>

<?php include "footer.php"; ?>



