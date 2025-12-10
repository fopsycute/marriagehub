
<?php
$requireLogin = true;
include "header.php";

$user_id = $buyerId; // current user ID
$bookmarks = [];

// Fetch bookmarks via API or function
$apiUrl = $siteurl . "script/admin.php?action=getuserbookmarks&user_id=" . urlencode($user_id);
$bookmarkData = curl_get_contents($apiUrl);
if ($bookmarkData !== false) {
    $bookmarks = json_decode($bookmarkData, true);
}
?>

<div class="container mt-5 mb-5">
    <div class="col-lg-12">
        <h2 class="mb-4 text-center">My Bookmarks</h2>

        <?php if (!empty($bookmarks)) { ?>
            <div class="table-responsive">
                <table  id="multi-filter-select" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                             <th>Image</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Added On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($bookmarks as $bm): ?>
                        <?php
                            $title = '';
                            $url = '';

                            // Fetch title & slug via function/API
<<<<<<< HEAD
                    if ($bm['item_type'] === 'blog') {

                    $blogUrl = $siteurl . "script/admin.php?action=blogmultiple&blog_id=" . urlencode($bm['item_id']);
                    $blogData = curl_get_contents($blogUrl);
                    $blog = $blogData ? json_decode($blogData, true) : null;

                    if ($blog) {
                        $title = $blog['title'] ?? '';
                        $slug  = $blog['slug'] ?? '';
                        $group_id = $blog['group_id'] ?? 0;
                        $featuredImg = $blog['featured_image'] ?? '';

                        // START: Group blog logic
                        if (!empty($group_id) && $group_id != 0) {
                         $sitelink = $siteurl . "script/";
                            // Fetch group slug
                            $groupApi = $sitelink . "admin.php?action=fetchgroupid&group_id=" . $group_id;
                            $groupData = curl_get_contents($groupApi);

                            if ($groupData !== false) {
                                $groupInfo = json_decode($groupData);

                                if (!empty($groupInfo[0])) {
                                    $group_slug = $groupInfo[0]->slug ?? '';

                                    // Group blog URL
                                    $url = $siteurl . "single-blog/" . $slug . "/" . $group_slug;
                                }
                            }

                        } else {
                            // Normal blog URL
                            $url = $siteurl . "blog-details/" . $slug;
                        }
                        // END: Group blog logic

                        // Image
                        if (!empty($featuredImg)) {
                            $imageUrl = $siteurl . $imagePath . $featuredImg;
                        }
                    }
                }
            elseif ($bm['item_type'] === 'question') {
=======
                            if ($bm['item_type'] === 'blog') {
                                $blogUrl = $siteurl . "script/admin.php?action=blogmultiple&blog_id=" . urlencode($bm['item_id']);
                                $blogData = curl_get_contents($blogUrl);
                                $blog = $blogData ? json_decode($blogData, true) : null;
                                if ($blog) {
                                    $title = $blog['title'] ?? '';
                                    $url = $siteurl . "blog-details/" . ($blog['slug'] ?? '');
                                    $featuredImg = $blog['featured_image'] ?? '';
                                    $imageUrl = !empty($featuredImg) ? $siteurl . $imagePath . $featuredImg : '';
                                }
                            } elseif ($bm['item_type'] === 'question') {
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
                                $qUrl = $siteurl . "script/user.php?action=getmultipleQuestionID&question_id=" . urlencode($bm['item_id']);
                                $qData = curl_get_contents($qUrl);
                                $q = $qData ? json_decode($qData, true) : null;
                                if ($q) {
                                    $title = $q['title'] ?? '';
                                    $url = $siteurl . "single-questions?slug=" . ($q['slug'] ?? '');
                                      $featuredImg = $q['featured_image'] ?? '';
                                     $imageUrl = !empty($featuredImg) ? $siteurl . $imagePath . $featuredImg : '';
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                               <td>
                                <?php if (!empty($imageUrl)) : ?>
                                    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($title) ?>" class="blog-thumb">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(ucfirst($bm['item_type'])) ?></td>
                            <td><?= htmlspecialchars($title) ?></td>
                            <td><a href="<?= htmlspecialchars($url) ?>" target="_blank">View</a></td>
                            <td><?= formatDateTime($bm['created_at']) ?></td>
                         
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center">You have no bookmarks yet.</div>
        <?php } ?>
    </div>
</div>

<?php include "footer.php"; ?>
