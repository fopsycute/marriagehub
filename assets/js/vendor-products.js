(function(){
  if (typeof window === 'undefined') return;

  const API_BASE = window.API_BASE || '';
  const SITEURL = window.SITEURL || '';
  const IMAGE_PATH = window.IMAGE_PATH || 'uploads/';
  const SITE_CURRENCY = window.SITE_CURRENCY || '';
  let currentPage = typeof window.currentPage !== 'undefined' ? parseInt(window.currentPage, 10) : 1;
  let itemsPerPage = typeof window.itemsPerPage !== 'undefined' ? parseInt(window.itemsPerPage, 10) : 12;
  let totalItems = typeof window.totalItems !== 'undefined' ? parseInt(window.totalItems, 10) : 0;

  function buildQuery(params) {
    return Object.keys(params).filter(k => params[k] !== undefined && params[k] !== '' && params[k] !== null).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k])).join('&');
  }

  function renderListings(listings) {
    const container = document.querySelector('.best-sellers .container .row.g-5');
    if (!container) return;
    if (!Array.isArray(listings) || listings.length === 0) {
      container.innerHTML = '<div class="col-12"><p>No products found.</p></div>';
      return;
    }
    let html = '';
    listings.forEach(listing => {
      if (!listing.status || listing.status.toLowerCase() !== 'active') return;
      const type = (listing.type || '').toString().toLowerCase();
      if (type !== 'product') return;
      const listingId = listing.id;
      const title = listing.title ? listing.title.replace(/</g,'&lt;') : '';
      const slug = listing.slug || '';
      const pricingType = listing.pricing_type || '';
      const price = listing.price || '';
      const priceMin = listing.price_min || '';
      const priceMax = listing.price_max || '';
      const categoryNames = listing.category_names ? listing.category_names.split(',') : ['General'];
      const category = categoryNames[0] ? categoryNames[0].trim() : 'General';
      const featuredImg = listing.featured_image ? (SITEURL + IMAGE_PATH + listing.featured_image) : (SITEURL + 'assets/img/default-product.jpg');
      const listingUrl = SITEURL + 'products/' + encodeURIComponent(slug);
      const sellerName = ((listing.first_name || '') + ' ' + (listing.last_name || '')).trim();
      const sellerPhoto = listing.photo ? (SITEURL + IMAGE_PATH + listing.photo) : (SITEURL + 'assets/img/user.jpg');
      let displayPrice = 'Contact for price';
      if (pricingType === 'Starting Price' && price) displayPrice = SITE_CURRENCY + parseFloat(price).toFixed(2);
      else if (pricingType === 'Price Range' && priceMin && priceMax) displayPrice = SITE_CURRENCY + parseFloat(priceMin).toFixed(2) + '-' + SITE_CURRENCY + parseFloat(priceMax).toFixed(2);

      html += `
        <div class="col-lg-3 col-md-6 col-6">
          <div class="product-item">
            <div class="product-image">
              <div class="product-badge trending-badge">${category}</div>
              <img src="${featuredImg}" alt="${title}" class="img-fluid" loading="lazy">
              <div class="product-actions">
                <button class="action-btn wishlist-btn" data-product-id="${listingId}" title="Add to Wishlist">
                  <i class="bi bi-heart"></i>
                </button>
              </div>
            </div>
            <div class="product-info">
              <div class="product-category">${category}</div>
              <h4 class="product-name"><a href="${listingUrl}">${title}</a></h4>
              <div class="product-price">${displayPrice}</div>
              <div class="mt-3 d-flex align-items-center">
                <img src="${sellerPhoto}" alt="${sellerName}" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                <span class="small text-muted">${sellerName}</span>
              </div>
            </div>
          </div>
        </div>
      `;
    });
    container.innerHTML = html;
  }

  function liveFetch() {
    const form = document.getElementById('marketFilter');
    if (!form) return;
    const formData = new FormData(form);
    const params = {};
    for (const [k, v] of formData.entries()) params[k] = v;
    const query = buildQuery(params);
    const url = API_BASE + (query ? '&' + query : '');
    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (data && data.data && Array.isArray(data.data)) {
          renderListings(data.data);
          renderPagination(data.total || 0, currentPage, itemsPerPage);
        } else if (Array.isArray(data)) {
          renderListings(data);
          renderPagination(0, 1, itemsPerPage);
        } else {
          console.error('Unexpected response format', data);
        }
        updateActiveFilters();
        try {
          const urlParams = new URLSearchParams(buildQuery(params));
          const newUrl = window.location.pathname + '?' + urlParams.toString();
          history.replaceState(null, '', newUrl);
        } catch (e) {}
      })
      .catch(err => console.error('Live fetch error', err));
  }

  let debounceTimer = null;
  function scheduleLiveFetch() { clearTimeout(debounceTimer); currentPage = 1; debounceTimer = setTimeout(liveFetch, 300); }

  document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.querySelector('#marketFilter [name=search]');
    const selects = document.querySelectorAll('#marketFilter select');
    if (searchInput) searchInput.addEventListener('input', scheduleLiveFetch);
    selects.forEach(s => s.addEventListener('change', scheduleLiveFetch));
    document.addEventListener('click', function(e){
      const t = e.target.closest && e.target.closest('[data-market-page]');
      if (t) { e.preventDefault(); const p = parseInt(t.getAttribute('data-market-page')) || 1; if (p === currentPage) return; currentPage = p; liveFetch(); }
    });
    updateActiveFilters();
    try { if (typeof totalItems !== 'undefined' && totalItems > 0) renderPagination(totalItems, currentPage, itemsPerPage); } catch (e) {}
  });

  function updateActiveFilters() {
    const container = document.querySelector('.filter-tags'); if (!container) return; const form = document.getElementById('marketFilter'); const fd = new FormData(form); let html = ''; const search = (fd.get('search') || '').trim(); const price = (fd.get('price_range') || '').trim(); if (search) html += `<span class="filter-tag">${escapeHtml(search)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=search]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`; if (price) html += `<span class="filter-tag">${escapeHtml(price)} <button class="filter-remove" type="button" onclick="document.getElementById('marketFilter').querySelector('[name=price_range]').value=''; scheduleLiveFetch();"><i class="bi bi-x"></i></button></span>`; html += `<button class="clear-all-btn btn-primary" type="button" onclick="document.getElementById('marketFilter').reset(); scheduleLiveFetch();">Clear All</button>`; container.innerHTML = html; }

  function escapeHtml(unsafe) { return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'\/','`':'&#96;','=':'&#61;'})[s]; }); }

  function renderPagination(total, page, perPage) {
    const container = document.getElementById('marketplace-pagination-list'); if (!container) return; perPage = parseInt(perPage, 10) || 1; total = parseInt(total, 10) || 0; const totalPages = Math.max(1, Math.ceil(total / perPage)); if (total <= perPage || totalPages <= 1) { container.innerHTML = ''; return; }
    let html = '';
    const prevDisabled = page <= 1;
    html += `<li class="page-item ${prevDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.max(1, page-1)}" aria-label="Previous"><span aria-hidden="true"><i class="bi bi-arrow-left"></i></span><span class="d-none d-sm-inline"> Previous</span></a></li>`;
    const showRange = 2;
    for (let p = 1; p <= totalPages; p++) {
      if (p === 1 || p === totalPages || (p >= page - showRange && p <= page + showRange)) {
        const active = p === page ? ' active' : '';
        const aria = p === page ? ' aria-current="page"' : '';
        html += `<li class="page-item${active}"><a class="page-link" href="#" data-market-page="${p}"${aria}>${p}</a></li>`;
      } else if (p === 2 && page - showRange > 2) {
        html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
      } else if (p === totalPages - 1 && page + showRange < totalPages - 1) {
        html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
      }
    }
    const nextDisabled = page >= totalPages;
    html += `<li class="page-item ${nextDisabled ? 'disabled' : ''}"><a class="page-link" href="#" data-market-page="${Math.min(totalPages, page+1)}" aria-label="Next"><span class="d-none d-sm-inline">Next </span><span aria-hidden="true"><i class="bi bi-arrow-right"></i></span></a></li>`;
    container.innerHTML = html;
  }

})();
