<?php
include "header.php";
?>

<main class="main">
<div class="container mt-4 mb-5">

	<div class="row">
		<!-- Filter Column -->
		<div class="col-lg-3">
			<div class="filter-box p-3 shadow-sm rounded-3">
				<h5 class="mb-3">Vendor Directory</h5>

				<?php
				// Read filters
				$search = isset($_GET['search']) ? trim($_GET['search']) : '';
				$stateFilter = isset($_GET['state']) ? trim($_GET['state']) : '';
				$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
				$subcategorySlug = isset($_GET['subcategory']) ? trim($_GET['subcategory']) : '';
				$perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 12;
				$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
				$offset = ($page - 1) * $perPage;

				// Fetch all users
				$url = $siteurl . "script/admin.php?action=userlists";
				$data = curl_get_contents($url);
				$allVendors = [];
				$states = [];

				if ($data !== false) {
						$users = json_decode($data);
						if (json_last_error() === JSON_ERROR_NONE && !empty($users)) {
				foreach ($users as $u) {
					// prefer simple, explicit PHP checks (trim + strtolower)
					if (isset($u->status, $u->user_type)) {
					    $status = strtolower(trim((string)($u->status ?? '')));
					    $utype = strtolower(trim((string)($u->user_type ?? '')));
					    if ($status === 'active' && $utype === 'vendor') {
						// directory appearance flag on user must be truthy; we'll enforce canAccess later
						$allVendors[] = $u;
						$st = trim((string)($u->state_residence ?? ''));
						if ($st !== '') $states[strtolower($st)] = $st;
					    }
					}
				}
						}
				}

				// Fetch categories for filter (slug => name mapping + slug=>id map)
				$categories = []; // slug => name
				$categorySlugToId = []; // slug => id
				$catUrl = $siteurl . "script/admin.php?action=category_list";
				$catData = curl_get_contents($catUrl);
				if ($catData !== false) {
						$catJson = json_decode($catData);
						if (json_last_error() === JSON_ERROR_NONE && !empty($catJson)) {
								foreach ($catJson as $c) {
										$slugKey = $c->slug ?? ($c->id ?? '');
										$categories[$slugKey] = $c->category_name ?? '';
										if (!empty($c->slug)) $categorySlugToId[$c->slug] = intval($c->id ?? 0);
								}
						}
				}

				// Resolve requested category slug -> id (users store category_id)
				$filterCategoryId = 0;
				if ($categorySlug !== '') {
					if (isset($categorySlugToId[$categorySlug])) {
						$filterCategoryId = $categorySlugToId[$categorySlug];
					} elseif (is_numeric($categorySlug)) {
						$filterCategoryId = intval($categorySlug);
					}
				}

				// If a subcategory slug is provided, fetch subcategories for the selected category and map slug->id
				$filterSubcategoryId = 0;
				if ($subcategorySlug !== '') {
					// prefer fetching subcategories by category id if available
					$subUrl = $siteurl . 'script/admin.php?action=subcategory_list' . ($filterCategoryId ? '&category_id=' . $filterCategoryId : '&category_slug=' . urlencode($categorySlug));
					$subData = curl_get_contents($subUrl);
					if ($subData !== false) {
						$subJson = json_decode($subData);
						if (json_last_error() === JSON_ERROR_NONE && !empty($subJson)) {
							foreach ($subJson as $s) {
								if (!empty($s->slug) && ($s->slug === $subcategorySlug)) {
									$filterSubcategoryId = intval($s->id ?? 0);
									break;
								}
								// also accept numeric slug passed as id
								if (is_numeric($subcategorySlug) && intval($s->id ?? 0) === intval($subcategorySlug)) {
									$filterSubcategoryId = intval($s->id ?? 0);
									break;
								}
							}
						}
					}
					// fallback: if the provided subcategory slug is numeric, use it
					if ($filterSubcategoryId === 0 && is_numeric($subcategorySlug)) $filterSubcategoryId = intval($subcategorySlug);
				}

				// Sort states alphabetically
				natcasesort($states);
				?>

				<!-- Filter form -->
				<form id="vendorFilterForm" method="get" class="mb-3">
					<div class="mb-3">
						<label class="form-label">Search</label>
						<input type="search" name="search" class="form-control" placeholder="Name, category or service" value="<?php echo htmlspecialchars($search); ?>">
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

					<div class="mb-3">
						<label class="form-label">Category</label>
						<select name="category" id="category" class="form-select">
							<option value="">All categories</option>
							<?php foreach ($categories as $slug => $name): 
								$val = htmlspecialchars($slug);
								$sel = ($slug === $categorySlug) ? ' selected' : '';
							?>
								<option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo htmlspecialchars($name); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<!--
					<div class="mb-3">
						<label class="form-label">Subcategory</label>
						<select name="subcategory" id="subcategory" class="form-select">
							<option value="">All subcategories</option>
							 populated dynamically when a category is selected
						</select>
					</div>
					 -->
					<div class="mb-3">
						<label class="form-label">Per page</label>
						<select name="per_page" class="form-select">
							<?php foreach ([6,12,24,48] as $opt): $sel = ($perPage == $opt) ? ' selected' : ''; ?>
								<option value="<?php echo $opt; ?>"<?php echo $sel; ?>><?php echo $opt; ?> per page</option>
							<?php endforeach; ?>
						</select>
					</div>

					<button type="submit" class="btn btn-primary w-100">Apply</button>
				</form>

						<!-- Hidden config values for external JS (no PHP inside JS). Copy these IDs into your central JS file. -->
						<input type="hidden" id="siteurl" value="<?php echo htmlspecialchars($siteurl); ?>">
						<input type="hidden" id="initialCategory" value="<?php echo htmlspecialchars($categorySlug); ?>">
						<input type="hidden" id="initialSubcategory" value="<?php echo htmlspecialchars($subcategorySlug); ?>">

						<!-- Note: JS moved to bottom of page (pure JS). -->

			</div>
		</div>

		<!-- List Column -->
		<div class="col-lg-9">
			<?php
			// Server-side filtering
			$filtered = [];
			$needle = strtolower($search);

			foreach ($allVendors as $v) {
		    // Appearance is controlled by the vendor's subscription/plan.
		    // Do NOT check a user-level `directory_appearance` column here (it's not on the users table).
		    // We'll enforce appearance via the plan using canAccess().

		    // verify subscription-based access (plan must allow directory appearance)
		    $can = false;
		    if (function_exists('canAccess')) {
			    $can = canAccess($con, intval($v->id ?? 0), 'directory_appearance', $siteprefix);
		    }
		    if (!$can) continue;

		    // state filter
		    if ($stateFilter !== '') {
			    $st = strtolower(trim((string)($v->state_residence ?? '')));
			    if ($st !== strtolower($stateFilter)) continue;
		    }

		    // category/subcategory filter: compare numeric IDs stored on users table (category_id, subcategory_id)
		    if ($filterCategoryId > 0) {
			$vCatId = intval($v->category_id ?? $v->category ?? $v->professional_field ?? 0);
			if ($vCatId !== $filterCategoryId) continue;
		    }

		    if ($filterSubcategoryId > 0) {
			$vSubId = intval($v->subcategory_id ?? $v->subcategory ?? $v->professional_title ?? 0);
			if ($vSubId !== $filterSubcategoryId) continue;
		    }

					// search filter across name, professional fields and title
					if ($needle !== '') {
							$hay = strtolower(
									trim(($v->first_name ?? '') . ' ' . ($v->last_name ?? '') . ' ' .
									($v->professional_field_names ?? '') . ' ' . ($v->professional_title_names ?? '') . ' ' . ($v->bio ?? $v->about ?? ''))
							);
							if (strpos($hay, $needle) === false) continue;
					}

					$filtered[] = $v;
			}

			$total = count($filtered);
			$totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
			$page = min(max(1, $page), $totalPages);
			$pageSlice = array_slice($filtered, $offset, $perPage);
			?>

			<div class="d-flex justify-content-between align-items-center mb-3">
				<h4 class="mb-0">Vendors (<?php echo $total; ?>)</h4>
				<small class="text-muted">Page <?php echo $page; ?> of <?php echo $totalPages; ?></small>
			</div>

			<div class="row gy-4">
				<?php if (!empty($pageSlice)): ?>
					<?php foreach ($pageSlice as $v): 
						$slug = urlencode($v->slug ?? '');
						$name = htmlspecialchars(trim(($v->first_name ?? '') . ' ' . ($v->last_name ?? 'Unknown')));
						$photo = !empty($v->photo) ? htmlspecialchars($siteurl . $imagePath . $v->photo) : htmlspecialchars($siteurl . "assets/img/user.jpg");
						$rate = is_numeric($v->rate ?? null) ? number_format(floatval($v->rate), 2) : '0.00';
						$profileLink = $siteurl . "vendor-profile/" . $slug;

						// Compute a single category name to display (first non-empty trimmed value)
						$rawField = isset($v->professional_field_names) ? (string)$v->professional_field_names : '';
						$fieldName = 'Uncategorized';
						if ($rawField !== '') {
							$parts = array_filter(array_map('trim', explode(',', $rawField)), function($x){ return $x !== ''; });
							if (!empty($parts)) {
								$fieldName = reset($parts);
							}
						}
						$field = htmlspecialchars($fieldName);
					?>
						<div class="col-lg-4 col-md-6">
							<div class="card h-100 shadow-sm">
								<div class="card-body d-flex flex-column">
									<div class="d-flex align-items-center mb-3">
										<img src="<?php echo $photo; ?>" alt="<?php echo $name; ?>" class="rounded-circle me-3" style="width:64px;height:64px;object-fit:cover;">
										<div>
											<h5 class="mb-0"><?php echo $name; ?></h5>
											
										</div>
									</div>

									<p class="text-truncate mb-3"><?php echo htmlspecialchars(limitWords(strip_tags($v->bio ?? $v->about ?? ''), 20)); ?></p>

									<div class="mt-auto d-flex justify-content-between align-items-center">
										<a href="<?php echo $profileLink; ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
									
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="col-12"><p>No vendors match your filters.</p></div>
				<?php endif; ?>
			</div>

			<!-- Pagination -->
			<?php if ($totalPages > 1): ?>
				<nav aria-label="Vendors pagination" class="mt-4">
					<ul class="pagination justify-content-center">
						<?php
							$base = $_GET;
							$base['search'] = $search;
							$base['state'] = $stateFilter;
							$base['category'] = $categorySlug;
							$base['subcategory'] = $subcategorySlug;
							$base['per_page'] = $perPage;
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



