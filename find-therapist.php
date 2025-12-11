<?php
include "header.php";
?>

<main class="main">

<!-- Top Banner Advert -->
<?php
$placementSlug = 'therapists-page-top-banner';
include "listing-banner.php";
?>

<div class="container mt-4 mb-5">
  
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="fw-bold">Find Therapists & Counselors</h3>
        <a href="<?php echo $siteurl; ?>register/therapist" class="btn btn-primary">
          <i class="bi bi-person-plus"></i> Become a Therapist
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Filter Column -->
    <div class="col-lg-3">
      <div class="filter-box p-3 shadow-sm rounded-3">
        <h5 class="mb-3">Filter Therapists</h5>

        <?php
        // Read filters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $stateFilter = isset($_GET['state']) ? trim($_GET['state']) : '';
        $perPage = 12;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $perPage;

        // Fetch all users
        $url = $siteurl . "script/admin.php?action=userlists";
        $data = curl_get_contents($url);
        $allTherapists = [];
        $states = [];

        if ($data !== false) {
            $users = json_decode($data);
            if (json_last_error() === JSON_ERROR_NONE && !empty($users)) {
                foreach ($users as $u) {
                    if (
                        isset($u->status, $u->user_type) &&
                        strtolower((string)$u->status) === 'active' &&
                        strtolower((string)$u->user_type) === 'therapist'
                    ) {
                        $allTherapists[] = $u;
                        $st = trim((string)($u->state_residence ?? ''));
                        if ($st !== '') $states[strtolower($st)] = $st;
                    }
                }
            }
        }

        // Sort states alphabetically
        natcasesort($states);
        ?>

        <!-- Filter form -->
        <form id="therapistFilterForm" method="get" class="mb-3">
          <div class="mb-3">
            <label class="form-label">Search</label>
            <input type="search" name="search" class="form-control" placeholder="Name, field or title" value="<?php echo htmlspecialchars($search); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">State</label>
            <select name="state" id="state" class="form-select">
              <option value="">All states</option>
              <?php foreach ($states as $rawState): 
                $val = htmlspecialchars($rawState);
                $sel = (strcasecmp($rawState, $stateFilter) === 0) ? ' selected' : '';
              ?>
                <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $val; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-primary w-100">Apply</button>
        </form>

        <script>
        // optional: auto-submit when state changes
        document.getElementById('state').addEventListener('change', function(){ document.getElementById('therapistFilterForm').submit(); });
        </script>

      </div>
    </div>

    <!-- List Column -->
    <div class="col-lg-9">
      <?php
      // Server-side filtering
      $filtered = [];
      $needle = strtolower($search);

      foreach ($allTherapists as $t) {
          // state filter
          if ($stateFilter !== '') {
              $st = strtolower(trim((string)($t->state_residence ?? '')));
              if ($st !== strtolower($stateFilter)) continue;
          }

          // search filter across name, professional_field_names, professional_title_names
          if ($needle !== '') {
              $hay = strtolower(
                  trim(($t->first_name ?? '') . ' ' . ($t->last_name ?? '') . ' ' .
                  ($t->professional_field_names ?? '') . ' ' . ($t->professional_title_names ?? ''))
              );
              if (strpos($hay, $needle) === false) continue;
          }

          $filtered[] = $t;
      }

      $total = count($filtered);
      $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
      $page = min(max(1, $page), $totalPages);
      $pageSlice = array_slice($filtered, $offset, $perPage);
      ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Therapists (<?php echo $total; ?>)</h4>
        <small class="text-muted">Page <?php echo $page; ?> of <?php echo $totalPages; ?></small>
      </div>

      <div class="row gy-4">
        <?php if (!empty($pageSlice)): ?>
          <?php foreach ($pageSlice as $t): 
            $slug = urlencode($t->slug ?? '');
            $name = htmlspecialchars(trim(($t->first_name ?? '') . ' ' . ($t->last_name ?? 'Unknown')));
            $photo = !empty($t->photo) ? htmlspecialchars($siteurl . $imagePath . $t->photo) : htmlspecialchars($siteurl . "assets/img/user.jpg");
            $field = htmlspecialchars(!empty($t->professional_field_names) ? explode(',', $t->professional_field_names)[0] : 'Uncategorized');
            $rate = is_numeric($t->rate ?? null) ? number_format(floatval($t->rate), 2) : '0.00';
            $profileLink =$siteurl . "therapist/" . $slug;
          ?>
            <div class="col-lg-4 col-md-6">
              <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                  <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" class="rounded-circle me-3" style="width:64px;height:64px;object-fit:cover;">
                    <div>
                      <h5 class="mb-0"><?php echo $name; ?></h5>
                      <small class="text-muted"><?php echo $field; ?></small>
                    </div>
                  </div>

                  <p class="text-truncate mb-3"><?php echo htmlspecialchars(limitWords(strip_tags($t->bio ?? $t->about ?? ''), 20)); ?></p>

                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <a href="<?php echo $profileLink; ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
                    <div class="text-end">
                      <div class="text-muted small">From</div>
                      <div class="fw-bold"><?php echo htmlspecialchars($sitecurrency . $rate); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><p>No therapists match your filters.</p></div>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <nav aria-label="Therapists pagination" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php
              $base = $_GET;
              $base['search'] = $search;
              $base['state'] = $stateFilter;
              if (!isset($base['page'])) $base['page'] = 1;
              $prev = $base; $prev['page'] = max(1, $page - 1);
            ?>
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php echo http_build_query($prev); ?>"><i class="bi bi-chevron-left"></i></a>
            </li>

            <?php
              $display = 5;
              $start = max(1, $page - floor($display/2));
              $end = min($totalPages, $start + $display - 1);
              if ($end - $start + 1 < $display) $start = max(1, $end - $display + 1);
              for ($i = $start; $i <= $end; $i++):
                $p = $base; $p['page'] = $i;
            ?>
              <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query($p); ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>

            <?php $next = $base; $next['page'] = min($totalPages, $page + 1); ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
              <a class="page-link" href="?<?php echo http_build_query($next); ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    </div>
  </div>
</div>
</main>

<?php include "footer.php"; ?>