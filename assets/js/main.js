// ===== Advert Purchase Script =====
document.addEventListener("DOMContentLoaded", function () {

    // --- Elements ---
    const payBtn       = document.getElementById("payNowBtn");
    const priceElem    = document.getElementById("advert_price"); // e.g. "₦1,500"
    const totalPriceEl = document.getElementById("total_price");
    const paystackKey  = document.getElementById("paystack-key").value;
    const advertIdElem = document.getElementById("advert_id");
    const startInput   = document.getElementById("start_date");
    const endInput     = document.getElementById("end_date");
    const emailElem    = document.getElementById("buyer_email");
    const redirectElem = document.getElementById("url_redirection");
    const bannerInput  = document.getElementById("bannerimage");
    const userIdElem   = document.getElementById("user_id");

    const siteUrl      = document.getElementById("siteurl").value;
    const ajaxUrl      = siteUrl + "script/admin.php"; // PHP endpoint

    const pricePerDay  = parseFloat(priceElem.dataset.price || priceElem.innerText.replace(/[₦,]/g, ""));

    // --- Redirect if user not logged in ---
    const user_id = userIdElem.value;
    if (!user_id) {
        window.location.href = siteUrl + 'login';
        return;
    }

    // --- Total price calculation ---
    function calculateTotal() {
        if (!startInput.value || !endInput.value) {
            totalPriceEl.innerText = "₦0.00";
            return 0;
        }

        const startDate = new Date(startInput.value);
        const endDate   = new Date(endInput.value);

        if (endDate < startDate) {
            totalPriceEl.innerText = "₦0.00";
            return 0;
        }

        const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1; // inclusive
        const total = days * pricePerDay;
        totalPriceEl.innerText = "₦" + total.toLocaleString();
        return total;
    }

    startInput.addEventListener('change', calculateTotal);
    endInput.addEventListener('change', calculateTotal);

    // --- Click handler ---
    payBtn.addEventListener("click", async function (e) {
        e.preventDefault();

        const advertId    = advertIdElem.value;
        const startDate   = startInput.value;
        const endDate     = endInput.value;
        const userEmail   = emailElem.value;
        const redirectUrl = redirectElem.value;

        // --- Validation ---
        if (!startDate || !endDate) {
            alert("Please select valid start & end dates.");
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert("Start date cannot be after end date.");
            return;
        }

        if (!bannerInput.files.length) {
            alert("Please upload a banner image.");
            return;
        }

        const totalAmount = calculateTotal();
        if (totalAmount <= 0) {
            alert("Invalid date selection.");
            return;
        }

        const payAmount = totalAmount * 100; // Convert to Kobo
        const reference = "ADV-" + Date.now(); // Unique reference

        // --- Prepare FormData ---
        const fd = new FormData();
        fd.append("action", "create-advert-order");
        fd.append("advert_id", advertId);
        fd.append("start_date", startDate);
        fd.append("end_date", endDate);
        fd.append("url_redirection", redirectUrl);
        fd.append("total_amount", totalAmount);
        fd.append("reference", reference);
        fd.append("bannerimage", bannerInput.files[0]);
        fd.append("user_id", user_id);

        // --- Save order via AJAX ---
        try {
            const response = await fetch(ajaxUrl, { method: "POST", body: fd });
            const result   = await response.json();

            if (result.status !== "success") {
                alert(result.message || "Failed to create order. Try again.");
                return;
            }
        } catch (err) {
            console.error(err);
            alert("An error occurred while saving your order.");
            return;
        }

        // --- Open Paystack payment popup ---
        const handler = PaystackPop.setup({
            key: paystackKey,
            email: userEmail,
            amount: payAmount,
            currency: "NGN",
            ref: reference,
            callback: function () {
                // Redirect to verification page
                window.location.href = `${siteUrl}verify-payment.php?action=verify-advert-payment&reference=${reference}`;
            },
            onClose: function () {
                alert("Payment window closed.");
            }
        });

        handler.openIframe();
    });
});

// Ensure jsPDF is available
document.addEventListener("DOMContentLoaded", function () {

    window.downloadPDF = async function () {
        const ticket = document.getElementById('ticketContent');
        const button = document.getElementById('downloadBtn');

        if (!ticket) {
            console.error("ticketContent not found!");
            return;
        }

        button.style.display = 'none';

        const canvas = await html2canvas(ticket, { scale: 2 });
        const imgData = canvas.toDataURL('image/png');

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const width = pdf.internal.pageSize.getWidth();
        const height = (canvas.height * width) / canvas.width;

        pdf.addImage(imgData, 'PNG', 0, 0, width, height);
        pdf.save("order_receipt.pdf");

        button.style.display = 'inline-block';
    };

});




document.addEventListener("DOMContentLoaded", function () {

    const siteurl = document.getElementById("siteurl").value;
    const orderSelect = document.getElementById("order_ids");
    const recipientSelect = document.getElementById("recipient");

    orderSelect.addEventListener("change", function () {

        const orderId = this.value;
        recipientSelect.innerHTML = "<option>Loading...</option>";

        if (!orderId) {
            recipientSelect.innerHTML = "<option value=''>Select recipient</option>";
            return;
        }

        fetch(siteurl + "script/admin.php?action=getorderitems&order_id=" + orderId)
            .then(res => res.json())
            .then(items => {

                recipientSelect.innerHTML = "<option value=''>Select recipient</option>";

                if (!Array.isArray(items) || items.length === 0) {
                    recipientSelect.innerHTML = "<option>No recipients found</option>";
                    return;
                }

                items.forEach(item => {
                    const opt = document.createElement("option");
                    opt.value = item.seller_id;
                    opt.textContent =
                        `${item.listing_title} (${item.seller_name}) - ${item.variation || item.type}`;
                    recipientSelect.appendChild(opt);
                });
            })
            .catch(() => {
                recipientSelect.innerHTML = "<option>Error loading recipients</option>";
            });
    });
});

// Vendor directory behaviors: loads subcategories, wires filters (moved from inline vendor.php)
document.addEventListener('DOMContentLoaded', function() {
  try {
  // Read server-provided values from hidden inputs
  const siteurlEl = document.getElementById('siteurl');
  const initialCatEl = document.getElementById('initialCategory');
  const initialSubEl = document.getElementById('initialSubcategory');
  const siteurl = siteurlEl ? siteurlEl.value : '';

  // Auto-submit when state changes
  const stateEl = $id('state');
  if (stateEl) stateEl.addEventListener('change', function(){ $id('vendorFilterForm').submit(); });
    // Populate subcategories for a category slug
    async function loadSubcategories(catSlug, preselect){
      const subEl = $id('subcategory');
  if (!subEl) return;
  subEl.innerHTML = '<option value="">All subcategories</option>';
  if (!catSlug) return;
      try {
        const url = siteurl + 'script/admin.php?action=subcategory_list&category_slug=' + encodeURIComponent(catSlug);
        const res = await fetch(url);
  if (!res.ok) return;
  const data = await res.json();
  if (Array.isArray(data)){
          data.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.slug || s.id || '';
            if (preselect && (opt.value === preselect)) opt.selected = true;
            subEl.appendChild(opt);
          });
        }
      } catch (e) { console.error('loadSubcategories error', e); }
    }
  // Wire category change to refresh subcategories
  const catEl = $id('category');
  if (catEl) catEl.addEventListener('change', function(){ loadSubcategories(this.value); });
  // initial load
  const initialCat = initialCatEl ? initialCatEl.value : '';
  const initialSub = initialSubEl ? initialSubEl.value : '';
    if (initialCat) loadSubcategories(initialCat, initialSub);
  } catch (err) {
    console.error('Vendor directory init error', err);
  }
});

/**
* Template Name: ZenBlog
* Template URL: https://bootstrapmade.com/zenblog-bootstrap-blog-template/
* Updated: Aug 08 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);


 

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

})();


$(document).ready(function() {
    // Check if table exists
    var $table = $('#multi-filter-select');
    if (!$table.length || !$.fn.DataTable) return; // Table or DataTables not loaded

    // Ensure <tfoot> exists
    if ($table.find('tfoot').length === 0) {
        var colCount = $table.find('thead tr').first().children('th,td').length;
        var $tfoot = $('<tfoot><tr></tr></tfoot>');
        for (var i = 0; i < colCount; i++) {
            $tfoot.find('tr').append('<th></th>');
        }
        $table.append($tfoot);
    }

    // Initialize DataTable
    var table = $table.DataTable({
        paging: true,
        searching: true,
        ordering: true,
        lengthChange: true,
        initComplete: function() {
            var api = this.api();

            // Add a multi-select filter to each footer cell
            api.columns().every(function() {
                var column = this;
                var $footer = $(column.footer());
                $footer.empty();

                // Create select element
                var select = $('<select class="form-select form-select-sm" multiple="multiple" style="width:100%"><option value="">All</option></select>');
                $footer.append(select);

                // Populate options with unique column values (strip HTML)
                column.data().unique().sort().each(function(d) {
                    var text = $('<div>').html(d).text().trim();
                    if (text.length && select.find("option[value='" + text + "']").length === 0) {
                        select.append('<option value="' + text + '">' + text + '</option>');
                    }
                });

                // Handle long column titles for placeholder
                var colTitle = $(column.header()).text().trim();
                var shortTitle = colTitle.length > 20 ? colTitle.substring(0, 17) + '...' : colTitle;

                // Initialize Select2
                select.select2({
                    placeholder: "Filter " + shortTitle,
                    allowClear: true,
                    width: 'resolve' // make dropdown match column width
                });

                // Filter table on change
                select.on('change', function() {
                    var vals = $(this).val(); // array of selected values
                    if (vals && vals.length > 0) {
                        column.search(vals.join('|'), true, false).draw();
                    } else {
                        column.search('', true, false).draw();
                    }
                });
            });
        }
    });
});



function togglePasswordVisibility(fieldId) {
  const passwordField = document.getElementById(fieldId);
  const parent = passwordField.parentElement; // input-group
  const icon = parent.querySelector('i'); // icon inside the same input-group
  if (passwordField.type === 'password') {
    passwordField.type = 'text';
    icon.classList.remove('bi-eye');
    icon.classList.add('bi-eye-slash');
  } else {
    passwordField.type = 'password';
    icon.classList.remove('bi-eye-slash');
    icon.classList.add('bi-eye');
  }
}

function updateCartCount(count) {
  const cartCountElement = document.querySelector('.cart-count');
  if (cartCountElement) {
    cartCountElement.textContent = count;
  }
}

function updateWishlistCount(count) {
  const wishlistCountElement = document.querySelector('.wishlist-count');
  if (wishlistCountElement) {
    wishlistCountElement.textContent = count;
  }
}



function showToast(message) {
  const toastContainer = document.createElement('div');
  toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
  toastContainer.style.zIndex = 11;

  const toast = document.createElement('div');
  toast.id = 'liveToast';
  toast.className = 'toast align-items-center text-white bg-primary border-0';
  toast.role = 'alert';
  toast.ariaLive = 'assertive';
  toast.ariaAtomic = 'true';

  const toastBody = document.createElement('div');
  toastBody.className = 'toast-body';
  toastBody.textContent = message;

  const toastButton = document.createElement('button');
  toastButton.type = 'button';
  toastButton.className = 'btn-close btn-close-white me-2 m-auto';
  toastButton.setAttribute('data-bs-dismiss', 'toast');
  toastButton.ariaLabel = 'Close';

  const toastFlex = document.createElement('div');
  toastFlex.className = 'd-flex';
  toastFlex.appendChild(toastBody);
  toastFlex.appendChild(toastButton);

  toast.appendChild(toastFlex);
  toastContainer.appendChild(toast);
  document.body.appendChild(toastContainer);

  const bootstrapToast = new bootstrap.Toast(toast, { delay: 5000 });
  bootstrapToast.show();
}

function payWithPaystack() {
  const key = document.getElementById('paystack-key').value;
  const email = document.getElementById('email')?.value || 'user@example.com';
  const amount = parseFloat(document.getElementById('amount').value) * 100;
  const ref = document.getElementById('ref').value + '_' + Date.now();
  const callbackUrl = document.getElementById('refer').value;

  if (!key || !email || !amount) {
    alert('Missing payment details. Please refresh the page.');
    return;
  }

  const handler = PaystackPop.setup({
    key: key,
    email: email,
    amount: amount,
    currency: 'NGN', // ✅ Hardcoded to NGN directly here
    ref: ref,
    callback: function(response) {
      window.location.href = callbackUrl + "&transaction=" + response.reference;
    },
    onClose: function() {
      alert('Payment window closed.');
    }
  });

  handler.openIframe();
}


function toggleTicketInfo(checkbox) {
  const ticketId = checkbox.value;

  // Get hidden data
  const seatRemain = document.getElementById('seat-' + ticketId)?.value || '';
  const benefits   = document.getElementById('benefits-' + ticketId)?.value || '';
  const price      = document.getElementById('price-' + ticketId)?.value || '';

  // Find the info container
  let infoDiv = document.getElementById('info-' + ticketId);
  if (!infoDiv) {
    infoDiv = document.createElement('div');
    infoDiv.id = 'info-' + ticketId;
    infoDiv.className = 'ticket-info mt-1 small text-muted';
    checkbox.closest('.ticket-item').appendChild(infoDiv);
  }

  if (checkbox.checked) {
    infoDiv.innerHTML = `
      <strong>Remaining Seats:</strong> ${seatRemain}<br>
      <strong>Benefits:</strong> ${benefits}<br>
      <strong>Price:</strong> ${price}
    `;
    infoDiv.style.display = 'block';
  } else {
    infoDiv.style.display = 'none';
  }
}


function handleGroupAccessToggle() {
  const paidFields = $('#paid-subscription-fields');
  const feeInputs = paidFields.find('input[type="number"]');

  // hide initially
  paidFields.hide();

  // when user selects Free or Paid
  $('input[name="group_access"]').on('change', function() {
    const selected = $(this).val();

    if (selected === 'paid') {
      paidFields.slideDown(300);
      feeInputs.attr('required', true);
    } else {
      paidFields.slideUp(300);
      feeInputs.val('').removeAttr('required');
    }
  });

  // handle pre-selected option (for edit pages)
  $('input[name="group_access"]:checked').trigger('change');
}

// ✅ Call function immediately after HTML elements are loaded
$(document).ready(function() {
  handleGroupAccessToggle();
});

document.addEventListener('click', function (e) {
  // share action
  const target = e.target.closest('.share-action');
  if (target) {
    e.preventDefault();
    const provider = target.dataset.provider;
    const url = target.dataset.url;
    const title = target.dataset.title;

    if (provider === 'native' && navigator.share) {
      navigator.share({ title: title, url: url }).catch(()=>{});
      return;
    }

    let shareUrl = '';
    if (provider === 'facebook') shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
    else if (provider === 'twitter') shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
    else if (provider === 'whatsapp') shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' ' + url);
    else if (provider === 'linkedin') shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);

    if (shareUrl) window.open(shareUrl, '_blank', 'noopener');
  }

  // copy link action
  if (e.target.closest('.copy-link')) {
    e.preventDefault();
    const url = e.target.closest('.copy-link').dataset.url;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(function(){
        alert('Link copied to clipboard');
      }).catch(function(){
        // fallback
        const ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); alert('Link copied to clipboard');
      });
    } else {
      const ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); alert('Link copied to clipboard');
    }
  }
});

//web share
document.addEventListener('DOMContentLoaded', function () {
  const shareBtn = document.getElementById('webShareBtn');
  if (shareBtn) {
    shareBtn.addEventListener('click', function () {
      const title = shareBtn.getAttribute('data-title');
      const url = shareBtn.getAttribute('data-url');

      if (navigator.share) {
        navigator.share({
          title: title,
          text: title,
          url: url
        }).catch(err => {
          console.error('Share failed:', err);
        });
      } else {
        alert('Sharing is not supported in this browser. Please use the social icons.');
      }
    });
  }
});


document.addEventListener('DOMContentLoaded', function() {
  const preferredTime = document.querySelector('input[name="preferred_time"]');
  if (preferredTime) {
    preferredTime.addEventListener('change', function() {
      const min = this.min;
      const max = this.max;
      const val = this.value;
      if (val < min || val > max) {
        alert(`Please select a time between ${min} and ${max}`);
        this.value = '';
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const likeBtn = document.getElementById('likeBtn');
  if (!likeBtn) return;

  likeBtn.addEventListener('click', function () {
    const blogId = likeBtn.dataset.blogId;
    const apiUrl = likeBtn.dataset.likeUrl;
    const likeCountEl = document.getElementById('likeCount');
    likeBtn.disabled = true;

    fetch(apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'like_blog',
        blog_id: blogId
      })
    })
    .then(res => res.json())
    .then(data => {
      likeBtn.disabled = false;
      if (data.status === 'success') {
        likeCountEl.textContent = data.likes;
      } else {
        alert(data.messages.replace(/<\/?[^>]+(>|$)/g, ''));
      }
    })
    .catch(err => {
      likeBtn.disabled = false;
      console.error('Error:', err);
    });
  });
});


document.addEventListener('DOMContentLoaded', function () {
  const likeBtn = document.getElementById('likesBtn');
  if (!likeBtn) return;

  likeBtn.addEventListener('click', function () {
    const groupId = likeBtn.dataset.groupId;
    const apiUrl = likeBtn.dataset.likesUrl;
    const likeCountEl = document.getElementById('likeCounts');
    likeBtn.disabled = true;

    fetch(apiUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'like_group',
        group_id: groupId
      })
    })
    .then(res => res.json())
    .then(data => {
      likeBtn.disabled = false;
      if (data.status === 'success') {
        likeCountEl.textContent = data.likes;
      } else {
        alert(data.messages.replace(/<\/?[^>]+(>|$)/g, ''));
      }
    })
    .catch(err => {
      likeBtn.disabled = false;
      console.error('Error:', err);
    });
  });
});

<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelects');
    const reasonBox = document.getElementById('rejectReasonBox');
    function toggleReason() {
      reasonBox.style.display = (statusSelect.value === 'rejected') ? 'block' : 'none';
    }
    statusSelect.addEventListener('change', toggleReason);
    toggleReason(); // initial check
  });
=======
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762


document.addEventListener('DOMContentLoaded', function() {
  const qualificationSelect = document.getElementById('highest_qualification');
  const otherField = document.getElementById('otherQualificationField');

  if (!qualificationSelect || !otherField) return; // stop if missing

  qualificationSelect.addEventListener('change', function() {
    const input = otherField.querySelector('input');
    if (this.value === 'Other') {
      otherField.style.display = 'block';
      input.required = true;
    } else {
      otherField.style.display = 'none';
      input.required = false;
      input.value = '';
    }
  });
});




document.addEventListener("DOMContentLoaded", function() {
  const select = document.getElementById("workWith");
  const otherInput = document.getElementById("otherWorkInput");

  // Stop here if elements are missing
  if (!select || !otherInput) return;

  select.addEventListener("change", function() {
    const values = Array.from(select.selectedOptions).map(opt => opt.value);

    if (values.includes("Other")) {
      otherInput.style.display = "block";
      otherInput.required = true;
    } else {
      otherInput.style.display = "none";
      otherInput.required = false;
      otherInput.value = ""; // clear input if hidden
    }
  });
});




document.addEventListener("DOMContentLoaded", function () {

    const payBtn = document.getElementById("paystackBtn");
    if (!payBtn) return; // Stop if the button does not exist on this page

    payBtn.addEventListener("click", function () {
        const paystackKey = document.getElementById("paystack-key").value;
        const siteurl = document.getElementById("siteurl").value;
        const email = document.getElementById("client_email").value;
        const amount = document.getElementById("booking_amount").value * 100;
        const reference = document.getElementById("reference").value;

        const handler = PaystackPop.setup({
            key: paystackKey,
            email: email,
            amount: amount,
            currency: "NGN",
            ref: reference,
            callback: function (response) {
                window.location.href =
                    `${siteurl}verify-payment.php?action=verify-therapist-payment&reference=${response.reference}&booking_id=${reference}`;
            },
            onClose: function () {
                alert("Payment cancelled.");
            }
        });

        handler.openIframe();
    });

});


function updatePaymentButton() {
  const paystackRadio = document.getElementById('paystack');
  const manualRadio = document.getElementById('manual');
  const paymentButton = document.getElementById('paymentButton');
  const orderTotalEl = document.getElementById('order_total');
  const siteCurrencyEl = document.getElementById('site_currency');

  if (!paymentButton || !orderTotalEl || !siteCurrencyEl) {
    return; // Safe exit if elements are not on this page
  }

  const orderTotal = orderTotalEl.value;
  const siteCurrency = siteCurrencyEl.value;

  if (manualRadio && manualRadio.checked) {
    paymentButton.removeAttribute('onClick');
    paymentButton.setAttribute('data-bs-toggle', 'modal');
    paymentButton.setAttribute('data-bs-target', '#manualPaymentModal');
    paymentButton.classList.remove('paystack-button');
    paymentButton.innerHTML = `
      <span class="btn-text">Proceed to Manual Payment</span>
      <span class="btn-price">${siteCurrency}${orderTotal}</span>
    `;
  } else {
    paymentButton.removeAttribute('data-bs-toggle');
    paymentButton.removeAttribute('data-bs-target');
    paymentButton.setAttribute('onClick', 'payWithPaystack()');
    paymentButton.classList.add('paystack-button');
    paymentButton.innerHTML = `
      <span class="btn-text">Pay with Paystack</span>
      <span class="btn-price">${siteCurrency}${orderTotal}</span>
    `;
  }
}





 document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.payButton').forEach(button => {
        button.addEventListener('click', function () {
            const groupId = this.getAttribute('data-group-id');
            const amount = this.getAttribute('data-amount');
            const groupName = this.getAttribute('data-group-name');
            const userId = this.getAttribute('data-user-id');
            const email = this.getAttribute('data-email');
            const duration = this.getAttribute('data-duration');
            const key = document.getElementById('paystack-key').value;
            const siteUrl = document.getElementById('siteurl').value;

            let handler = PaystackPop.setup({
                key: key,
                email: email,
                amount: amount * 100, // convert to kobo
                currency: "NGN",
                ref: 'GROUP' + Math.floor(Math.random() * 1000000000 + 1),
                metadata: {
                    custom_fields: [
                        { display_name: "Group Name", variable_name: "group_name", value: groupName },
                        { display_name: "Duration", variable_name: "duration", value: duration },
                        { display_name: "User ID", variable_name: "user_id", value: userId },
                        { display_name: "Group ID", variable_name: "group_id", value: groupId }
                    ]
                },
                onClose: function() {
                    alert('Payment window closed.');
                },
                callback: function(response) {
                    // ✅ Redirect to verification page with ALL data
                    const queryString = new URLSearchParams({
                        action: 'verify-group-payment',
                        reference: response.reference,
                        group_id: groupId,
                        group_name: groupName,
                        duration: duration,
                        user_id: userId,
                        amount: amount
                    }).toString();

                    window.location.href = `${siteUrl}verify-payment.php?${queryString}`;
                }
            });
            handler.openIframe();
        });
    });
});




document.addEventListener("DOMContentLoaded", function () {
    const payButtons = document.querySelectorAll(".subscribeButton");

    payButtons.forEach(button => {
        button.addEventListener("click", function () {
            const planId = button.dataset.planId;
            const amount = parseFloat(button.dataset.amount) * 100; // Convert to kobo
            const planName = button.dataset.planName;
            const userId = button.dataset.userId;
            const email = button.dataset.email;
            const key = document.getElementById("paystack-key").value;
            const siteurl = document.getElementById("siteurl").value;

            // ✅ Basic validation
            if (!planId || !userId || !email || isNaN(amount)) {
                alert("Invalid payment details. Please refresh and try again.");
                return;
            }

            const handler = PaystackPop.setup({
                key: key,
                email: email,
                amount: amount,
                currency: "NGN",
                ref: "VS-" + Date.now() + "-" + Math.floor(Math.random() * 1000),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Plan Name",
                            variable_name: "plan_name",
                            value: planName
                        }
                    ]
                },
                callback: function (response) {
                    // ✅ Redirect with proper encoding
                    const redirectUrl =
                        `${siteurl}verify-payment.php?action=verify_payment` +
                        `&reference=${encodeURIComponent(response.reference)}` +
                        `&plan_id=${encodeURIComponent(planId)}` +
                        `&user_id=${encodeURIComponent(userId)}` +
                        `&plan_name=${encodeURIComponent(planName)}` +
                        `&amount=${amount / 100}`;

                    window.location.href = redirectUrl;
                },
                onClose: function () {
                    alert("Payment was canceled.");
                }
            });

            handler.openIframe();
        });
    });
});



 function shareProfile(){
                                    var vendorNameEl = document.getElementById('vendorName');
                                    var shareUrlEl = document.getElementById('shareUrl');
                                    var vendorName = vendorNameEl ? vendorNameEl.value : '';
                                    var shareUrl = shareUrlEl ? shareUrlEl.value : window.location.href;
                                    var shareText = vendorName ? "Check out " + vendorName + "'s profile" : 'Check out this profile';

                                    if (navigator.share){
                                        navigator.share({ title: vendorName, text: shareText, url: shareUrl }).catch(function(){});
                                        return;
                                    }

                                    var shareTextEl = document.getElementById('shareText');
                                    if (shareTextEl) shareTextEl.textContent = shareText + ' — ' + shareUrl;

                                    var modalEl = document.getElementById('shareModal');
                                    if (typeof bootstrap !== 'undefined' && modalEl){
                                        var modal = new bootstrap.Modal(modalEl);
                                        modal.show();
                                        return;
                                    }

                                    // Fallback prompt if no bootstrap modal
                                    try { prompt(shareText + '\nCopy this link:', shareUrl); } catch (e) { /* ignore */ }
                                }

                                function copyShareLink(){
                                    var shareUrlEl = document.getElementById('shareUrl');
                                    var shareUrl = shareUrlEl ? shareUrlEl.value : window.location.href;
                                    var feedback = document.getElementById('shareFeedback');
                                    function showFeedback(msg, type){
                                        if (!feedback) return;
                                        feedback.style.display = 'block';
                                        feedback.innerHTML = '<div class="alert alert-' + (type||'success') + ' py-1 my-0">' + msg + '</div>';
                                        setTimeout(function(){ feedback.style.display = 'none'; }, 2500);
                                    }

                                    if (navigator.clipboard && navigator.clipboard.writeText){
                                        navigator.clipboard.writeText(shareUrl).then(function(){ showFeedback('Link copied to clipboard'); }).catch(function(){ showFeedback('Unable to copy link', 'danger'); });
                                    } else {
                                        var tmp = document.createElement('input'); tmp.value = shareUrl; document.body.appendChild(tmp); tmp.select();
                                        try { document.execCommand('copy'); showFeedback('Link copied to clipboard'); } catch (e) { showFeedback('Unable to copy link', 'danger'); }
                                        tmp.remove();
                                    }
                                }



document.addEventListener("DOMContentLoaded", function() {
  // ✅ Selectors
  const thumbnails = document.querySelectorAll('.thumbnail-item');
  const mainImage = document.getElementById('main-product-image');
  const prevBtn = document.querySelector('.prev-image');
  const nextBtn = document.querySelector('.next-image');

  if (!thumbnails.length || !mainImage) return;

  let currentIndex = 0; // Track active image index

  // ✅ Function: Update Main Image + Active Thumbnail
  function updateMainImage(index) {
    const selected = thumbnails[index];
    if (!selected) return;

    const newImage = selected.getAttribute('data-image');
    mainImage.src = newImage;
    mainImage.setAttribute('data-zoom', newImage);

    thumbnails.forEach(t => t.classList.remove('active'));
    selected.classList.add('active');

    currentIndex = index;
  }

  // ✅ Thumbnail Click Event
  thumbnails.forEach((thumbnail, index) => {
    thumbnail.addEventListener('click', function(e) {
      e.preventDefault();
      updateMainImage(index);
    });
  });

  // ✅ Prev Button
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      let newIndex = currentIndex - 1;
      if (newIndex < 0) newIndex = thumbnails.length - 1; // Loop back to last
      updateMainImage(newIndex);
    });
  }

  // ✅ Next Button
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      let newIndex = (currentIndex + 1) % thumbnails.length; // Loop forward
      updateMainImage(newIndex);
    });
  }

  // ✅ Initialize First Image
  updateMainImage(0);
});


document.addEventListener("DOMContentLoaded", function () {
    const variationSelect = document.getElementById("variationSelect");
    const priceDisplay = document.querySelector(".price-display .sale-price");
    const siteCurrency = document.getElementById("siteCurrency")?.value || "";
    const defaultPrice = priceDisplay ? priceDisplay.textContent : "";

    if (variationSelect && priceDisplay) {
        variationSelect.addEventListener("change", function () {
            const selectedOption = this.options[this.selectedIndex];
            const newPrice = selectedOption.getAttribute("data-price");

            if (newPrice && !isNaN(newPrice)) {
                // ✅ Format with commas and currency from hidden input
                const formatted = parseFloat(newPrice)
                    .toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                priceDisplay.textContent = siteCurrency + formatted;
            } else {
                priceDisplay.textContent = defaultPrice;
            }
        });
    }
});






document.addEventListener("DOMContentLoaded", function () {
    const quantityInput = document.querySelector(".quantity-input");
    const decreaseBtn = document.querySelector(".quantity-btn.decrease");
    const increaseBtn = document.querySelector(".quantity-btn.increase");
    const priceDisplay = document.querySelector(".sale-price");
    const variationSelect = document.querySelector("#variationSelect");
    const limitedSlot = parseInt(document.querySelector("#limited-slot")?.value || "999");
    const basePrice = parseFloat(document.querySelector("#base-price")?.value.replace(/[^\d.]/g, "")) || 0;
    const siteCurrency = document.querySelector("#siteCurrency")?.value || "$";

    let currentPrice = basePrice;

    // 🧩 Function to update price display
    function updatePrice() {
        const quantity = parseInt(quantityInput.value) || 1;
        const total = currentPrice * quantity;
        priceDisplay.textContent = siteCurrency + total.toFixed(2);
    }

    // 🧩 Listen for variation change
    if (variationSelect) {
        variationSelect.addEventListener("change", function () {
            const selected = variationSelect.options[variationSelect.selectedIndex];
            const selectedPrice = parseFloat(selected.getAttribute("data-price")) || 0;
            currentPrice = selectedPrice;
            updatePrice();
        });
    }

    // 🧩 Increase button
    if (increaseBtn) {
        increaseBtn.addEventListener("click", function () {
            let current = parseInt(quantityInput.value) || 1;

            // ✅ Require variation first (if exists)
            if (variationSelect && !variationSelect.value) {
                alert("Please select a variation first.");
                return;
            }

            if (current >= limitedSlot) {
                alert("Only " + limitedSlot + " slots left!");
                return;
            }

            quantityInput.value = current + 1;
            updatePrice();
        });
    }

    // 🧩 Decrease button
    if (decreaseBtn) {
        decreaseBtn.addEventListener("click", function () {
            let current = parseInt(quantityInput.value) || 1;

            // ✅ Require variation first (if exists)
            if (variationSelect && !variationSelect.value) {
                alert("Please select a variation first.");
                return;
            }

            if (current > 1) {
                quantityInput.value = current - 1;
                updatePrice();
            }
        });
    }

    // 🧩 Input change manually
    if (quantityInput) {
        quantityInput.addEventListener("input", function () {
            let current = parseInt(quantityInput.value) || 1;
            if (current > limitedSlot) {
                alert("Only " + limitedSlot + " slots left!");
                quantityInput.value = limitedSlot;
            }
            updatePrice();
        });
    }

    // ✅ Initialize price display
    updatePrice();
});

function toggleCustomReason(value) {
    document.getElementById('customReasonContainer').style.display =
        (value === 'Other') ? 'block' : 'none';
}




function increaseQuantity(id) {
    const input = document.getElementById('quantity-' + id);
    if (!input) return;

    let value = parseInt(input.value) || 1;
    const max = parseInt(input.max) || 100;

    if (value < max) value++;
    input.value = value;

    const updateBtn = document.querySelector('.btn-update');
    if (updateBtn) updateBtn.disabled = false;

    console.log(`Item ${id} quantity increased to ${value}`);
}

function decreaseQuantity(id) {
    const input = document.getElementById('quantity-' + id);
    if (!input) return;

    let value = parseInt(input.value) || 1;
    const min = parseInt(input.min) || 1;

    if (value > min) value--;
    input.value = value;

    const updateBtn = document.querySelector('.btn-update');
    if (updateBtn) updateBtn.disabled = false;

    console.log(`Item ${id} quantity decreased to ${value}`);
}



function toggleBio(el) {
    let parent = el.closest(".bioBox");
    let shortText = parent.querySelector(".bioShort");
    let fullText = parent.querySelector(".bioFull");

    if (fullText.classList.contains("d-none")) {
        // Expand
        fullText.classList.remove("d-none");
        shortText.classList.add("d-none");
        el.textContent = "Read Less";
    } else {
        // Collapse
        fullText.classList.add("d-none");
        shortText.classList.remove("d-none");
        el.textContent = "Read More";
    }
}



document.addEventListener("click", function (e) {
  if (!e.target.classList.contains("read-toggle")) return;

  e.preventDefault();

  const link = e.target;
  const container = link.closest(".bio-text");
  if (!container) return;

  const shortBio = container.querySelector(".bio-short");
  const fullBio = container.querySelector(".bio-full");

  if (fullBio.classList.contains("d-none")) {
    shortBio.classList.add("d-none");
    fullBio.classList.remove("d-none");
    link.textContent = "Read Less";
  } else {
    fullBio.classList.add("d-none");
    shortBio.classList.remove("d-none");
    link.textContent = "Read More";
  }
});


<<<<<<< HEAD
document.addEventListener("DOMContentLoaded", function() {
    const wrapper = document.getElementById("groupblogFilterWrappers");
    if (!wrapper) return; // safety

    // Get siteurl from hidden input
    const siteurl = document.getElementById("siteurl").value;

    // Initialize Select2
    const $category = $('#category', wrapper);
    const $subcategory = $('#subcategory', wrapper);

    $category.select2({ placeholder: "Select category", allowClear: true });
    $subcategory.select2({ placeholder: "Select subcategory", allowClear: true });

// RUN FILTER AJAX
function runBlogFilter() {
    const form = wrapper.querySelector("#blogFilterForm");
    const params = new URLSearchParams(new FormData(form)).toString();

    // Get full current URL (path + slug if clean URL + slug param if query string)
    let baseUrl = window.location.pathname; 
    let slugQuery = window.location.search; // keeps ?slug=relationship-growth if present

    // Build final URL
    let finalUrl = baseUrl + slugQuery;
    if (finalUrl.includes("?")) {
        finalUrl += "&" + params;
    } else {
        finalUrl += "?" + params;
    }

    fetch(finalUrl)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newContent = doc.querySelector("#blogResults").innerHTML;
            wrapper.querySelector("#blogResults").innerHTML = newContent;
        });
}


    // LIVE SEARCH
    wrapper.querySelector("#searchInput").addEventListener("keyup", runBlogFilter);

    // CATEGORY CHANGE => LOAD SUBCATEGORIES + FILTER
    $category.on("change", function() {
        let selected = $(this).val();
        if (!selected || selected.length === 0) {
            $subcategory.html('').trigger('change');
            runBlogFilter();
            return;
        }

        fetch(siteurl + "script/register.php?action=subcategorieslists&parent_ids=" + selected.join(","))
            .then(res => res.json())
            .then(data => {
                let options = '';
                data.forEach(sc => {
                    options += `<option value="${sc.id}">${sc.category_name}</option>`;
                });
                $subcategory.html(options).trigger('change');
                runBlogFilter();
            });
    });

    // SUBCATEGORY CHANGE
    $subcategory.on("change", runBlogFilter);

    // CLEAR FILTERS
    wrapper.querySelector("#clearFilters").addEventListener("click", function() {
        wrapper.querySelector("#searchInput").value = '';
        $category.val(null).trigger('change');
        $subcategory.val(null).trigger('change');
        runBlogFilter();
    });

});
=======
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('eventmarketplace');
    if (!container) return;

    const searchInput = container.querySelector("#searchInput");
    const categorySelect = $('#eventcategory'); // Select2
    const subcategorySelect = $('#eventsubcategory'); // Select2
    const typeSelect = container.querySelector("#eventType");
    const deliveryFormatSelect = container.querySelector("#deliveryFormat");
    const pricingTypeSelect = container.querySelector("#pricingType");
    const stateSelect = container.querySelector("#state");
    const clearBtn = container.querySelector("#clearFilters");

    const cardsPerPage = 16;
    let currentPage = 1;
    let filteredCards = [];

    function refreshSubcategories(preserve = []) {
        const selectedCats = categorySelect.val() || [];
        subcategorySelect.empty().append('<option value="">-- Select Sub-Category --</option>');
        if (!selectedCats.length) return;

        const url = "<?php echo $siteurl; ?>script/register.php?action=eventsubcategorieslists&parent_ids=" + encodeURIComponent(selectedCats.join(','));
        fetch(url)
            .then(res => res.json())
            .then(list => {
                if (!Array.isArray(list)) return;
                list.forEach(item => {
                    const opt = new Option(item.category_name || item.name || item.id, item.id, preserve.includes(String(item.id)), preserve.includes(String(item.id)));
                    subcategorySelect.append(opt);
                });
                subcategorySelect.trigger('change'); // refresh Select2
            });
    }

    function filterEvents(page = 1) {
        const keyword = (searchInput.value || '').toLowerCase().trim();
        const selectedCategories = categorySelect.val() || [];
        const selectedSubcategories = subcategorySelect.val() || [];
        const selectedType = (typeSelect.value || '').toLowerCase().trim();
        const selectedFormat = (deliveryFormatSelect.value || '').toLowerCase().trim();
        const selectedPricing = (pricingTypeSelect.value || '').toLowerCase().trim();
        const selectedState = (stateSelect.value || '').toLowerCase().trim();

        filteredCards = $(container).find('.event-card').filter(function() {
            const card = $(this);
            const title = (card.data('title') || '').toLowerCase();
            const cardCategories = (card.data('category') || '').toString().split(/\s*,\s*/).filter(Boolean);
            const cardSubcategories = (card.data('subcategory') || '').toString().split(/\s*,\s*/).filter(Boolean);
            const cardType = (card.data('type') || '').toLowerCase();
            const cardFormat = (card.data('delivery_format') || '').toLowerCase();
            const cardPricing = (card.data('pricing_type') || '').toLowerCase();
            const cardState = (card.data('state') || '').toLowerCase();
            const cardCatNames = (card.data('catname') || '').toLowerCase();
            const cardSubcatNames = (card.data('subcatname') || '').toLowerCase();

            // Show all if no filters applied
            if (!keyword && !selectedCategories.length && !selectedSubcategories.length && !selectedType && !selectedFormat && !selectedPricing && !selectedState) {
                return true;
            }

            const matchKeyword = !keyword || title.includes(keyword) || cardCatNames.includes(keyword) || cardSubcatNames.includes(keyword) || cardType.includes(keyword);
            const matchCategory = !selectedCategories.length || selectedCategories.some(c => cardCategories.includes(c));
            const matchSubcategory = !selectedSubcategories.length || selectedSubcategories.some(sc => cardSubcategories.includes(sc));
            const matchType = !selectedType || cardType === selectedType;
            const matchFormat = !selectedFormat || cardFormat === selectedFormat;
            const matchPricing = !selectedPricing || cardPricing === selectedPricing;
            const matchState = !selectedState || cardState === selectedState;

            return matchKeyword && matchCategory && matchSubcategory && matchType && matchFormat && matchPricing && matchState;
        });

        renderPage(page);
        renderPagination();
    }

    function renderPage(page) {
        currentPage = page;
        $(container).find('.event-card').hide();
        const start = (page - 1) * cardsPerPage;
        const end = start + cardsPerPage;
        filteredCards.slice(start, end).show();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredCards.length / cardsPerPage);
        const $pagination = $(container).find('#marketplace-pagination-list');
        $pagination.empty();

        for (let i = 1; i <= totalPages; i++) {
            $pagination.append(`
                <li class="page-item ${i===currentPage ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
                </li>
            `);
        }

        $pagination.find('a').click(function() {
            const page = parseInt($(this).data('page'));
            renderPage(page);
        });
    }

    searchInput.addEventListener('keyup', debounce(() => filterEvents(1), 200));
    typeSelect.addEventListener('change', () => filterEvents(1));
    deliveryFormatSelect.addEventListener('change', () => filterEvents(1));
    pricingTypeSelect.addEventListener('change', () => filterEvents(1));
    stateSelect.addEventListener('change', () => filterEvents(1));

    categorySelect.on('change', function() {
        const preserve = subcategorySelect.val() || [];
        refreshSubcategories(preserve);
        setTimeout(() => filterEvents(1), 50);
    });
    subcategorySelect.on('change', () => filterEvents(1));

    // Clear button now reloads page
    clearBtn.addEventListener('click', function() {
        location.reload();
    });

    function debounce(fn, wait) {
        let t;
        return function() { clearTimeout(t); t = setTimeout(() => fn.apply(this, arguments), wait); };
    }

    filterEvents(1); // initial load
});


// FILTER TOGGLE FOR MOBILE & TABLET
document.addEventListener("DOMContentLoaded", function() {
    console.log("Filter toggle script loaded");
    
    const toggleBtn = document.getElementById("toggleFilterBtn");
    const filterSidebar = document.getElementById("filterSidebar");
    const filterBackdrop = document.getElementById("filterBackdrop");
    const closeFilterBtn = document.querySelector(".close-filter-btn");

    console.log("Toggle Btn:", toggleBtn);
    console.log("Filter Sidebar:", filterSidebar);
    console.log("Backdrop:", filterBackdrop);
    console.log("Close Btn:", closeFilterBtn);

    if (!toggleBtn) {
        console.error("Toggle button not found!");
        return;
    }

    // Show filter
    toggleBtn.addEventListener("click", function(e) {
        e.preventDefault();
        console.log("Toggle button clicked");
        filterSidebar.classList.add("show-filter");
        filterBackdrop.classList.add("show");
        document.body.style.overflow = "hidden";
        toggleBtn.innerHTML = '<i class="bi bi-funnel"></i> Hide Filters';
    });

    // Hide filter - backdrop click
    filterBackdrop.addEventListener("click", function() {
        console.log("Backdrop clicked");
        filterSidebar.classList.remove("show-filter");
        filterBackdrop.classList.remove("show");
        document.body.style.overflow = "auto";
        toggleBtn.innerHTML = '<i class="bi bi-funnel"></i> Show Filters';
    });

    // Hide filter - close button click
    if (closeFilterBtn) {
        closeFilterBtn.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("Close button clicked");
            filterSidebar.classList.remove("show-filter");
            filterBackdrop.classList.remove("show");
            document.body.style.overflow = "auto";
            toggleBtn.innerHTML = '<i class="bi bi-funnel"></i> Show Filters';
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const wrapper = document.getElementById("blogFilterWrapper");
    if (!wrapper) return;

    const siteurlEl = document.getElementById("siteurl");
    if (!siteurlEl) return console.error("siteurl element not found!");
    const siteurl = siteurlEl.value;

    const $category = $('#category', wrapper);
    const $subcategory = $('#subcategory', wrapper);

    if ($category.length) $category.select2({ placeholder: "Select category", allowClear: true });
    if ($subcategory.length) $subcategory.select2({ placeholder: "Select subcategory", allowClear: true });

    const resultsContainer = wrapper.querySelector("#blogResults");
    const paginationList = document.getElementById("marketplace-pagination-list");
    const perPage = 16; // Blogs per page
    let blogsData = []; // Holds filtered blogs
    let currentPage = 1;

    // RUN AJAX FILTER
    function runBlogFilter(page = 1) {
        const form = wrapper.querySelector("#blogFilterForm");
        if (!form) return;

        const params = new URLSearchParams(new FormData(form)).toString();

        fetch(siteurl + "blog.php?" + params)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newContent = doc.querySelectorAll("#blogResults > div.col-lg-4");

                blogsData = Array.from(newContent); // Store all filtered blogs
                currentPage = page;
                renderPagination();
                renderPage(currentPage);
            })
            .catch(err => {
                console.error("AJAX filter error:", err);
                resultsContainer.innerHTML =
                    "<p class='text-center text-danger'>Error loading blogs.</p>";
            });
    }

    function renderPage(page) {
        resultsContainer.innerHTML = '';
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const pageItems = blogsData.slice(start, end);

        if (pageItems.length === 0) {
            resultsContainer.innerHTML = "<p class='text-center'>No blogs found for this filter.</p>";
            paginationList.innerHTML = '';
            return;
        }

        pageItems.forEach(item => resultsContainer.appendChild(item));
    }

    function renderPagination() {
        const totalPages = Math.ceil(blogsData.length / perPage);
        paginationList.innerHTML = '';

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement("li");
            li.className = `page-item ${i === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener("click", e => {
                e.preventDefault();
                currentPage = i;
                renderPage(currentPage);
                renderPagination();
            });
            paginationList.appendChild(li);
        }
    }

    // Live search
    const searchInput = wrapper.querySelector("#searchInput");
    if (searchInput) searchInput.addEventListener("keyup", () => runBlogFilter(1));

    // Category change → load subcategories + filter
    if ($category.length) {
        $category.on("change", function() {
            let selected = $(this).val() || [];
            if (!selected.length) {
                $subcategory.html('').trigger('change');
                runBlogFilter(1);
                return;
            }

            fetch(siteurl + "script/register.php?action=subcategorieslists&parent_ids=" + selected.join(","))
                .then(res => res.json())
                .then(data => {
                    let options = '';
                    data.forEach(sc => options += `<option value="${sc.id}">${sc.category_name}</option>`);
                    $subcategory.html(options).trigger('change');
                    runBlogFilter(1);
                })
                .catch(err => console.error("Error loading subcategories:", err));
        });
    }

    // Subcategory change
    if ($subcategory.length) $subcategory.on("change", () => runBlogFilter(1));

    // Clear filters
    const clearBtn = wrapper.querySelector("#clearFilters");
    if (clearBtn) {
        clearBtn.addEventListener("click", function() {
            if (searchInput) searchInput.value = '';
            $category.val(null).trigger('change');
            $subcategory.val(null).trigger('change');
            runBlogFilter(1);
        });
    }

    // Initial load
    runBlogFilter(1);
});


document.addEventListener("DOMContentLoaded", function () {
   const container = document.getElementById("questionscontainer");
    if (!container) return;
    const siteurl = document.getElementById("siteurl").value; // your site URL
    const searchInput = document.getElementById("searchInput");
    const categorySelect = $("#category"); // Select2 instance (or native)
    const subcategorySelect = $("#subcategory"); // Select2 instance (or native)
    const sortDropdown = document.getElementById("sortDropdown");
    const clearBtn = document.getElementById("clearFiltersBtn");
    const filterTagsContainer = document.getElementById("filterTagsContainer");
    const listContainer = document.querySelector(".courses-grid .row");

    // Initialize Select2 if available (guarded) and ensure selects are enabled
    if (typeof $.fn.select2 === 'function') {
      try {
        categorySelect.select2({ width: '100%' });
        subcategorySelect.select2({ width: '100%' });
      } catch (e) {
        console.warn('Select2 init failed:', e);
      }
    }
    // Ensure native selects are enabled in case something disabled them
    try { categorySelect.prop('disabled', false); subcategorySelect.prop('disabled', false); } catch(e){}

    // Small/medium filter toggle for Questions page
    const qaToggleBtn = document.getElementById('toggleQAFiltersBtn');
    const qaFilterBlock = document.getElementById('qaFilterBlock');
    if (qaToggleBtn && qaFilterBlock) {
      const slideUp = (el, duration = 220) => {
        el.style.transitionProperty = 'height, margin, padding';
        el.style.transitionDuration = duration + 'ms';
        el.style.boxSizing = 'border-box';
        el.style.height = el.offsetHeight + 'px';
        el.offsetHeight;
        el.style.overflow = 'hidden';
        el.style.height = 0;
        window.setTimeout(() => {
          el.style.display = 'none';
          el.style.removeProperty('height');
          el.style.removeProperty('overflow');
          el.style.removeProperty('transition-duration');
          el.style.removeProperty('transition-property');
        }, duration);
      };

      const slideDown = (el, duration = 220) => {
        el.style.removeProperty('display');
        let display = window.getComputedStyle(el).display;
        if (display === 'none') display = 'block';
        el.style.display = display;
        const height = el.scrollHeight + 'px';
        el.style.height = '0';
        el.style.overflow = 'hidden';
        el.style.transitionProperty = 'height, margin, padding';
        el.style.transitionDuration = duration + 'ms';
        el.offsetHeight;
        el.style.height = height;
        window.setTimeout(() => {
          el.style.removeProperty('height');
          el.style.removeProperty('overflow');
          el.style.removeProperty('transition-duration');
          el.style.removeProperty('transition-property');
        }, duration);
      };

        // Prevent the clearSearch absolute element from accidentally overlaying selects
        const clearSearchEl = document.getElementById('clearSearch');
        if (clearSearchEl) {
          clearSearchEl.style.position = 'relative';
          clearSearchEl.style.right = '';
          clearSearchEl.style.top = '';
        }

      function qaApplyInitial() {
        if (window.innerWidth < 992) {
          if (!qaFilterBlock.classList.contains('d-none')) qaFilterBlock.classList.add('d-none');
          qaToggleBtn.textContent = 'Show filters';
          qaToggleBtn.setAttribute('aria-expanded', 'false');
          qaToggleBtn.style.display = '';
        } else {
          qaFilterBlock.classList.remove('d-none');
          qaToggleBtn.style.display = 'none';
          qaToggleBtn.setAttribute('aria-expanded', 'true');
        }
      }

      qaToggleBtn.addEventListener('click', function () {
        const hidden = qaFilterBlock.classList.contains('d-none') || window.getComputedStyle(qaFilterBlock).display === 'none';
        if (hidden) {
          qaFilterBlock.classList.remove('d-none');
          slideDown(qaFilterBlock, 220);
          qaToggleBtn.textContent = 'Hide filters';
          qaToggleBtn.setAttribute('aria-expanded', 'true');
          setTimeout(() => qaFilterBlock.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
        } else {
          slideUp(qaFilterBlock, 220);
          qaToggleBtn.textContent = 'Show filters';
          qaToggleBtn.setAttribute('aria-expanded', 'false');
          setTimeout(() => qaFilterBlock.classList.add('d-none'), 230);
        }
      });

      window.addEventListener('resize', qaApplyInitial);
      qaApplyInitial();
    }

    let ALL_QUESTIONS = [];
    let CATEGORY_NAMES = {};
    let SUBCATEGORY_NAMES = {};
    let ALL_SUBCATEGORIES = [];

    // Fetch all questions
    fetch(siteurl + "script/admin.php?action=questionlists")
        .then(res => res.json())
        .then(data => {
            ALL_QUESTIONS = data;
            applyFilters();
        });

    // Fetch categories
    fetch(siteurl + "script/register.php?action=categorieslists")
        .then(res => res.json())
        .then(data => {
            data.forEach(cat => CATEGORY_NAMES[String(cat.id)] = cat.category_name);
        });

    // Fetch subcategories
    fetch(siteurl + "script/register.php?action=subcategorieslists")
        .then(res => res.json())
        .then(data => {
            ALL_SUBCATEGORIES = data; // full list for filtering
            data.forEach(sub => SUBCATEGORY_NAMES[String(sub.id)] = sub.category_name);
        });

    //--------------------------------------
    // Refresh Subcategory Options Based on Selected Categories
    //--------------------------------------
    function refreshSubcategories() {
        const selectedCats = categorySelect.val() || [];
        subcategorySelect.empty().append('<option value="">-- Select Sub-Category --</option>');

        if (selectedCats.length === 0) return;

        const filteredSubs = ALL_SUBCATEGORIES.filter(sub =>
            selectedCats.includes(String(sub.parent_id))
        );

        filteredSubs.forEach(sub => {
            const option = new Option(sub.category_name, sub.id, false, false);
            subcategorySelect.append(option);
        });

        subcategorySelect.trigger("change");
    }

    //--------------------------------------
    // Update Active Filter Tags
    //--------------------------------------
    function updateFilterTags() {
        filterTagsContainer.innerHTML = "";

        const search = searchInput.value.trim();
        const categories = (categorySelect.val() || []).filter(Boolean).map(String);
        const subcategories = (subcategorySelect.val() || []).filter(Boolean).map(String);

        if (search) {
            const tag = document.createElement("span");
            tag.className = "badge bg-primary me-2";
            tag.textContent = "Search: " + search;
            filterTagsContainer.appendChild(tag);
        }

        categories.forEach(catId => {
            const tag = document.createElement("span");
            tag.className = "badge bg-info me-2";
            tag.textContent = "Category: " + (CATEGORY_NAMES[String(catId)] || catId);
            filterTagsContainer.appendChild(tag);
        });


    }

    //--------------------------------------
    // Apply Filters
    //--------------------------------------
    function applyFilters() {
        const search = searchInput.value.toLowerCase().trim();
        const categories = (categorySelect.val() || []).filter(Boolean).map(String);
        const subcategories = (subcategorySelect.val() || []).filter(Boolean).map(String);
        const sort = sortDropdown.value;

        let filtered = ALL_QUESTIONS.filter(q => {
            if (!q.status || q.status.toLowerCase() !== "active") return false;

            // Search filter
            if (search) {
                const matchSearch =
                    q.title?.toLowerCase().includes(search) ||
                    q.article?.toLowerCase().includes(search) ||
                    ((q.first_name || "") + " " + (q.last_name || "")).toLowerCase().includes(search) ||
                    q.category_names?.toLowerCase().includes(search) ||
                    q.subcategory_names?.toLowerCase().includes(search);
                if (!matchSearch) return false;
            }

            // Category filter
            if (categories.length) {
                const qCats = (q.categories || "").split(",").map(c => c.trim());
                if (!qCats.some(cat => categories.includes(cat))) return false;
            }

            // Subcategory filter
            if (subcategories.length) {
                const qSubs = (q.subcategories || "").split(",").map(s => s.trim());
                if (!qSubs.some(sub => subcategories.includes(sub))) return false;
            }

            return true;
        });

        // Sorting and special filters
        if (sort === "recent" || sort === "newest") {
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else if (sort === "upvoted") {
          filtered.sort((a, b) => {
            const av = Number(a.upvotes ?? a.votes ?? a.likes ?? 0);
            const bv = Number(b.upvotes ?? b.votes ?? b.likes ?? 0);
            return bv - av;
          });
        } else if (sort === "answered") {
          filtered.sort((a, b) => (b.total_answers ?? 0) - (a.total_answers ?? 0));
        } else if (sort === "unanswered") {
          filtered = filtered.filter(q => Number(q.total_answers ?? 0) === 0);
          // show newest unanswered first
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else { // popular or fallback
          filtered.sort((a, b) => (b.views ?? 0) - (a.views ?? 0));
        }

        renderQuestions(filtered);
        updateFilterTags();
    }

    //--------------------------------------
    // Render Questions
    //--------------------------------------
    function renderQuestions(list) {
        listContainer.innerHTML = "";
        if (!list.length) {
            listContainer.innerHTML = "<p>No questions found.</p>";
            return;
        }

        list.forEach(q => {
            const shortText = q.article.split(" ").slice(0, 12).join(" ") + "…";
            const category = q.category_names?.split(",")[0] || "General";
            const subcategory = q.subcategory_names?.split(",")[0] || "General";
            const authorDisplay = q.anonymous == 1
                ? "Anonymous"
                : ((q.first_name || "") + " " + (q.last_name || "")).trim() || "Unknown User";
            const date = new Date(q.created_at).toDateString();

            const card = document.createElement("div");
            card.className = "col-lg-4 col-md-6";
            card.innerHTML = `
                <div class="course-card">
                    <div class="course-content">
                        <div class="course-meta">
                            <span class="category">${category}</span>
                            <span class="level">${subcategory}</span>
                        </div>
                        <h3 class="mb-2">${q.title}</h3>
                        <p>${shortText}</p>
                        <div class="mt-2 text-muted small">
                            Asked by ${authorDisplay} on ${date}
                        </div>
                        <div class="mt-1 text-muted small">
                            ${q.views ?? 0} Views | ${q.total_answers ?? 0} Answers
                        </div>
                        <a href="single-questions/${q.slug}" class="btn-course mt-2">
                            View Question
                        </a>
                    </div>
                </div>`;
            listContainer.appendChild(card);
        });
    }

    //--------------------------------------
    // Event Listeners
    //--------------------------------------
    let typingTimer;
    searchInput.addEventListener("keyup", () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(applyFilters, 300);
    });

    categorySelect.on("change", () => {
        refreshSubcategories();
        applyFilters();
    });
    subcategorySelect.on("change", applyFilters);
    sortDropdown.addEventListener("change", applyFilters);

    clearBtn.addEventListener("click", () => {
        searchInput.value = "";
        categorySelect.val(null).trigger("change");
        subcategorySelect.val(null).trigger("change");
        sortDropdown.value = "popular";
        applyFilters();
    });
});


document.addEventListener('click', function (e) {
  // share action
  const target = e.target.closest('.share-action');
  if (target) {
    e.preventDefault();
    const provider = target.dataset.provider;
    const url = target.dataset.url;
    const title = target.dataset.title;

    if (provider === 'native' && navigator.share) {
      navigator.share({ title: title, url: url }).catch(()=>{});
      return;
    }

    let shareUrl = '';
    if (provider === 'facebook') shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
    else if (provider === 'twitter') shareUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url);
    else if (provider === 'whatsapp') shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' ' + url);
    else if (provider === 'linkedin') shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);

    if (shareUrl) window.open(shareUrl, '_blank', 'noopener');
  }

  // copy link action
  if (e.target.closest('.copy-link')) {
    e.preventDefault();
    const url = e.target.closest('.copy-link').dataset.url;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(function(){
        alert('Link copied to clipboard');
      }).catch(function(){
        // fallback
        const ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); alert('Link copied to clipboard');
      });
    } else {
      const ta = document.createElement('textarea'); ta.value = url; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); alert('Link copied to clipboard');
    }
  }
});


<<<<<<< HEAD




=======
>>>>>>> 90f3a825660d92875ae26d6ae25097bb295f3762
  (function () {
        const btn = document.getElementById('toggleBlogFiltersBtn');
        const block = document.getElementById('blogFilterBlock');
        if (!btn || !block) return;

        function applyInitial() {
            if (window.innerWidth < 992) { // below lg
                block.classList.add('d-none');
                btn.textContent = 'Show filters';
                btn.setAttribute('aria-expanded', 'false');
                btn.style.display = '';
            } else {
                block.classList.remove('d-none');
                btn.style.display = 'none';
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        btn.addEventListener('click', function () {
            const hidden = block.classList.toggle('d-none');
            btn.textContent = hidden ? 'Show filters' : 'Hide filters';
            btn.setAttribute('aria-expanded', String(!hidden));
            if (!hidden) block.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        window.addEventListener('resize', applyInitial);
        applyInitial();
    })();