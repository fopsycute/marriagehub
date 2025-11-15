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


   $(document).ready(function () {
  $("#multi-filter-select").DataTable({
    pageLength: 5,
    initComplete: function () {
      this.api()
        .columns()
        .every(function () {
          var column = this;

          // Create select filter
          var select = $('<select class="form-select form-select-sm"><option value="">All</option></select>')
            .appendTo($(column.footer()).empty())
            .on("change", function () {
              var val = $.fn.dataTable.util.escapeRegex($(this).val());
              column.search(val ? "^" + val + "$" : "", true, false).draw();
            });

          // Populate dropdown with unique values (strip HTML to get clean text)
          column
            .data()
            .unique()
            .sort()
            .each(function (d) {
              var text = $('<div>').html(d).text().trim(); // convert HTML to plain text
              if (text.length > 0 && select.find("option[value='" + text + "']").length === 0) {
                select.append('<option value="' + text + '">' + text + "</option>");
              }
            });
        });
    },
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


document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.read-toggle').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const container = this.closest('.bio-text');
      const shortBio = container.querySelector('.bio-short');
      const fullBio = container.querySelector('.bio-full');

      if (fullBio.classList.contains('d-none')) {
        shortBio.classList.add('d-none');
        fullBio.classList.remove('d-none');
        this.textContent = 'Read Less';
      } else {
        fullBio.classList.add('d-none');
        shortBio.classList.remove('d-none');
        this.textContent = 'Read More';
      }
    });
  });
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






document.addEventListener('DOMContentLoaded', function () {
      const stateAndLGAs = {
    "Abia": [
        "Aba North",
        "Aba South",
        "Arochukwu",
        "Bende",
        "Ikwuano",
        "Isiala-Ngwa North",
        "Isiala-Ngwa South",
        "Isuikwato",
        "Obi Nwa",
        "Ohafia",
        "Osisioma",
        "Ngwa",
        "Ugwunagbo",
        "Ukwa East",
        "Ukwa West",
        "Umuahia North",
        "Umuahia South",
        "Umu-Neochi"
    ],
		 "Adamawa": [
        "Demsa",
        "Fufore",
        "Ganaye",
        "Gireri",
        "Gombi",
        "Guyuk",
        "Hong",
        "Jada",
        "Lamurde",
        "Madagali",
        "Maiha",
        "Mayo-Belwa",
        "Michika",
        "Mubi North",
        "Mubi South",
        "Numan",
        "Shelleng",
        "Song",
        "Toungo",
        "Yola North",
        "Yola South"
    ],
    "Anambra": [
        "Aguata",
        "Anambra East",
        "Anambra West",
        "Anaocha",
        "Awka North",
        "Awka South",
        "Ayamelum",
        "Dunukofia",
        "Ekwusigo",
        "Idemili North",
        "Idemili south",
        "Ihiala",
        "Njikoka",
        "Nnewi North",
        "Nnewi South",
        "Ogbaru",
        "Onitsha North",
        "Onitsha South",
        "Orumba North",
        "Orumba South",
        "Oyi"
    ],
    "Akwa Ibom": [
        "Abak",
        "Eastern Obolo",
        "Eket",
        "Esit Eket",
        "Essien Udim",
        "Etim Ekpo",
        "Etinan",
        "Ibeno",
        "Ibesikpo Asutan",
        "Ibiono Ibom",
        "Ika",
        "Ikono",
        "Ikot Abasi",
        "Ikot Ekpene",
        "Ini",
        "Itu",
        "Mbo",
        "Mkpat Enin",
        "Nsit Atai",
        "Nsit Ibom",
        "Nsit Ubium",
        "Obot Akara",
        "Okobo",
        "Onna",
        "Oron",
        "Oruk Anam",
        "Udung Uko",
        "Ukanafun",
        "Uruan",
        "Urue-Offong/Oruko ",
        "Uyo"
    ],
    "Bauchi": [
        "Alkaleri",
        "Bauchi",
        "Bogoro",
        "Damban",
        "Darazo",
        "Dass",
        "Ganjuwa",
        "Giade",
        "Itas/Gadau",
        "Jama'are",
        "Katagum",
        "Kirfi",
        "Misau",
        "Ningi",
        "Shira",
        "Tafawa-Balewa",
        "Toro",
        "Warji",
        "Zaki"
    ],
    "Bayelsa": [
        "Brass",
        "Ekeremor",
        "Kolokuma/Opokuma",
        "Nembe",
        "Ogbia",
        "Sagbama",
        "Southern Jaw",
        "Yenegoa"
    ],
    "Benue": [
        "Ado",
        "Agatu",
        "Apa",
        "Buruku",
        "Gboko",
        "Guma",
        "Gwer East",
        "Gwer West",
        "Katsina-Ala",
        "Konshisha",
        "Kwande",
        "Logo",
        "Makurdi",
        "Obi",
        "Ogbadibo",
        "Oju",
        "Okpokwu",
        "Ohimini",
        "Oturkpo",
        "Tarka",
        "Ukum",
        "Ushongo",
        "Vandeikya"
    ],
    "Borno": [
        "Abadam",
        "Askira/Uba",
        "Bama",
        "Bayo",
        "Biu",
        "Chibok",
        "Damboa",
        "Dikwa",
        "Gubio",
        "Guzamala",
        "Gwoza",
        "Hawul",
        "Jere",
        "Kaga",
        "Kala/Balge",
        "Konduga",
        "Kukawa",
        "Kwaya Kusar",
        "Mafa",
        "Magumeri",
        "Maiduguri",
        "Marte",
        "Mobbar",
        "Monguno",
        "Ngala",
        "Nganzai",
        "Shani"
    ],
    "Cross River": [
        "Akpabuyo",
        "Odukpani",
        "Akamkpa",
        "Biase",
        "Abi",
        "Ikom",
        "Yarkur",
        "Odubra",
        "Boki",
        "Ogoja",
        "Yala",
        "Obanliku",
        "Obudu",
        "Calabar South",
        "Etung",
        "Bekwara",
        "Bakassi",
        "Calabar Municipality"
    ],
    "Delta": [
        "Oshimili",
        "Aniocha",
        "Aniocha South",
        "Ika South",
        "Ika North-East",
        "Ndokwa West",
        "Ndokwa East",
        "Isoko south",
        "Isoko North",
        "Bomadi",
        "Burutu",
        "Ughelli South",
        "Ughelli North",
        "Ethiope West",
        "Ethiope East",
        "Sapele",
        "Okpe",
        "Warri North",
        "Warri South",
        "Uvwie",
        "Udu",
        "Warri Central",
        "Ukwani",
        "Oshimili North",
        "Patani"
    ],
    "Ebonyi": [
        "Edda",
        "Afikpo",
        "Onicha",
        "Ohaozara",
        "Abakaliki",
        "Ishielu",
        "lkwo",
        "Ezza",
        "Ezza South",
        "Ohaukwu",
        "Ebonyi",
        "Ivo"
    ],
    "Enugu": [
        "Enugu South,",
        "Igbo-Eze South",
        "Enugu North",
        "Nkanu",
        "Udi Agwu",
        "Oji-River",
        "Ezeagu",
        "IgboEze North",
        "Isi-Uzo",
        "Nsukka",
        "Igbo-Ekiti",
        "Uzo-Uwani",
        "Enugu Eas",
        "Aninri",
        "Nkanu East",
        "Udenu."
    ],
    "Edo": [
        "Esan North-East",
        "Esan Central",
        "Esan West",
        "Egor",
        "Ukpoba",
        "Central",
        "Etsako Central",
        "Igueben",
        "Oredo",
        "Ovia SouthWest",
        "Ovia South-East",
        "Orhionwon",
        "Uhunmwonde",
        "Etsako East",
        "Esan South-East"
    ],
    "Ekiti": [
        "Ado",
        "Ekiti-East",
        "Ekiti-West",
        "Emure/Ise/Orun",
        "Ekiti South-West",
        "Ikere",
        "Irepodun",
        "Ijero,",
        "Ido/Osi",
        "Oye",
        "Ikole",
        "Moba",
        "Gbonyin",
        "Efon",
        "Ise/Orun",
        "Ilejemeje."
    ],
    "FCT": [
        "Abaji",
        "Abuja Municipal",
        "Bwari",
        "Gwagwalada",
        "Kuje",
        "Kwali"
    ],
    "Gombe": [
        "Akko",
        "Balanga",
        "Billiri",
        "Dukku",
        "Kaltungo",
        "Kwami",
        "Shomgom",
        "Funakaye",
        "Gombe",
        "Nafada/Bajoga",
        "Yamaltu/Delta."
    ],
    "Imo": [
        "Aboh-Mbaise",
        "Ahiazu-Mbaise",
        "Ehime-Mbano",
        "Ezinihitte",
        "Ideato North",
        "Ideato South",
        "Ihitte/Uboma",
        "Ikeduru",
        "Isiala Mbano",
        "Isu",
        "Mbaitoli",
        "Mbaitoli",
        "Ngor-Okpala",
        "Njaba",
        "Nwangele",
        "Nkwerre",
        "Obowo",
        "Oguta",
        "Ohaji/Egbema",
        "Okigwe",
        "Orlu",
        "Orsu",
        "Oru East",
        "Oru West",
        "Owerri-Municipal",
        "Owerri North",
        "Owerri West"
    ],
    "Jigawa": [
        "Auyo",
        "Babura",
        "Birni Kudu",
        "Biriniwa",
        "Buji",
        "Dutse",
        "Gagarawa",
        "Garki",
        "Gumel",
        "Guri",
        "Gwaram",
        "Gwiwa",
        "Hadejia",
        "Jahun",
        "Kafin Hausa",
        "Kaugama Kazaure",
        "Kiri Kasamma",
        "Kiyawa",
        "Maigatari",
        "Malam Madori",
        "Miga",
        "Ringim",
        "Roni",
        "Sule-Tankarkar",
        "Taura",
        "Yankwashi"
    ],
    "Kaduna": [
        "Birni-Gwari",
        "Chikun",
        "Giwa",
        "Igabi",
        "Ikara",
        "jaba",
        "Jema'a",
        "Kachia",
        "Kaduna North",
        "Kaduna South",
        "Kagarko",
        "Kajuru",
        "Kaura",
        "Kauru",
        "Kubau",
        "Kudan",
        "Lere",
        "Makarfi",
        "Sabon-Gari",
        "Sanga",
        "Soba",
        "Zango-Kataf",
        "Zaria"
    ],
    "Kano": [
        "Ajingi",
        "Albasu",
        "Bagwai",
        "Bebeji",
        "Bichi",
        "Bunkure",
        "Dala",
        "Dambatta",
        "Dawakin Kudu",
        "Dawakin Tofa",
        "Doguwa",
        "Fagge",
        "Gabasawa",
        "Garko",
        "Garum",
        "Mallam",
        "Gaya",
        "Gezawa",
        "Gwale",
        "Gwarzo",
        "Kabo",
        "Kano Municipal",
        "Karaye",
        "Kibiya",
        "Kiru",
        "kumbotso",
        "Ghari",
        "Kura",
        "Madobi",
        "Makoda",
        "Minjibir",
        "Nasarawa",
        "Rano",
        "Rimin Gado",
        "Rogo",
        "Shanono",
        "Sumaila",
        "Takali",
        "Tarauni",
        "Tofa",
        "Tsanyawa",
        "Tudun Wada",
        "Ungogo",
        "Warawa",
        "Wudil"
    ],
    "Katsina": [
        "Bakori",
        "Batagarawa",
        "Batsari",
        "Baure",
        "Bindawa",
        "Charanchi",
        "Dandume",
        "Danja",
        "Dan Musa",
        "Daura",
        "Dutsi",
        "Dutsin-Ma",
        "Faskari",
        "Funtua",
        "Ingawa",
        "Jibia",
        "Kafur",
        "Kaita",
        "Kankara",
        "Kankia",
        "Katsina",
        "Kurfi",
        "Kusada",
        "Mai'Adua",
        "Malumfashi",
        "Mani",
        "Mashi",
        "Matazuu",
        "Musawa",
        "Rimi",
        "Sabuwa",
        "Safana",
        "Sandamu",
        "Zango"
    ],
    "Kebbi": [
        "Aleiro",
        "Arewa-Dandi",
        "Argungu",
        "Augie",
        "Bagudo",
        "Birnin Kebbi",
        "Bunza",
        "Dandi",
        "Fakai",
        "Gwandu",
        "Jega",
        "Kalgo",
        "Koko/Besse",
        "Maiyama",
        "Ngaski",
        "Sakaba",
        "Shanga",
        "Suru",
        "Wasagu/Danko",
        "Yauri",
        "Zuru"
    ],
    "Kogi": [
        "Adavi",
        "Ajaokuta",
        "Ankpa",
        "Bassa",
        "Dekina",
        "Ibaji",
        "Idah",
        "Igalamela-Odolu",
        "Ijumu",
        "Kabba/Bunu",
        "Kogi",
        "Lokoja",
        "Mopa-Muro",
        "Ofu",
        "Ogori/Mangongo",
        "Okehi",
        "Okene",
        "Olamabolo",
        "Omala",
        "Yagba East",
        "Yagba West"
    ],
    "Kwara": [
        "Asa",
        "Baruten",
        "Edu",
        "Ekiti",
        "Ifelodun",
        "Ilorin East",
        "Ilorin West",
        "Irepodun",
        "Isin",
        "Kaiama",
        "Moro",
        "Offa",
        "Oke-Ero",
        "Oyun",
        "Pategi"
    ],
    "Lagos": [
        "Agege",
        "Ajeromi-Ifelodun",
        "Alimosho",
        "Amuwo-Odofin",
        "Apapa",
        "Badagry",
        "Epe",
        "Eti-Osa",
        "Ibeju/Lekki",
        "Ifako-Ijaye",
        "Ikeja",
        "Ikorodu",
        "Kosofe",
        "Lagos Island",
        "Lagos Mainland",
        "Mushin",
        "Ojo",
        "Oshodi-Isolo",
        "Shomolu",
        "Surulere"
    ],
    "Nasarawa": [
        "Akwanga",
        "Awe",
        "Doma",
        "Karu",
        "Keana",
        "Keffi",
        "Kokona",
        "Lafia",
        "Nasarawa",
        "Nasarawa-Eggon",
        "Obi",
        "Toto",
        "Wamba"
    ],
    "Niger": [
        "Agaie",
        "Agwara",
        "Bida",
        "Borgu",
        "Bosso",
        "Chanchaga",
        "Edati",
        "Gbako",
        "Gurara",
        "Katcha",
        "Kontagora",
        "Lapai",
        "Lavun",
        "Magama",
        "Mariga",
        "Mashegu",
        "Mokwa",
        "Muya",
        "Pailoro",
        "Rafi",
        "Rijau",
        "Shiroro",
        "Suleja",
        "Tafa",
        "Wushishi"
    ],
    "Ogun": [
        "Abeokuta North",
        "Abeokuta South",
        "Ado-Odo/Ota",
        "Yewa North",
        "Yewa South",
        "Ewekoro",
        "Ifo",
        "Ijebu East",
        "Ijebu North",
        "Ijebu North East",
        "Ijebu Ode",
        "Ikenne",
        "Imeko-Afon",
        "Ipokia",
        "Obafemi-Owode",
        "Ogun Waterside",
        "Odeda",
        "Odogbolu",
        "Remo North",
        "Shagamu"
    ],
    "Ondo": [
        "Akoko North East",
        "Akoko North West",
        "Akoko South Akure East",
        "Akoko South West",
        "Akure North",
        "Akure South",
        "Ese-Odo",
        "Idanre",
        "Ifedore",
        "Ilaje",
        "Ile-Oluji",
        "Okeigbo",
        "Irele",
        "Odigbo",
        "Okitipupa",
        "Ondo East",
        "Ondo West",
        "Ose",
        "Owo"
    ],
    "Osun": [
        "Aiyedade",
        "Aiyedire",
        "Atakumosa East",
        "Atakumosa West",
        "Boluwaduro",
        "Boripe",
        "Ede North",
        "Ede South",
        "Egbedore",
        "Ejigbo",
        "Ife Central",
        "Ife East",
        "Ife North",
        "Ife South",
        "Ifedayo",
        "Ifelodun",
        "Ila",
        "Ilesha East",
        "Ilesha West",
        "Irepodun",
        "Irewole",
        "Isokan",
        "Iwo",
        "Obokun",
        "Odo-Otin",
        "Ola-Oluwa",
        "Olorunda",
        "Oriade",
        "Orolu",
        "Osogbo"
    ],
    "Oyo": [
        "Afijio",
        "Akinyele",
        "Atiba",
        "Atisbo",
        "Egbeda",
        "Ibadan Central",
        "Ibadan North",
        "Ibadan North West",
        "Ibadan South East",
        "Ibadan South West",
        "Ibarapa Central",
        "Ibarapa East",
        "Ibarapa North",
        "Ido",
        "Irepo",
        "Iseyin",
        "Itesiwaju",
        "Iwajowa",
        "Kajola",
        "Lagelu Ogbomosho North",
        "Ogbomosho South",
        "Ogo Oluwa",
        "Olorunsogo",
        "Oluyole",
        "Ona-Ara",
        "Orelope",
        "Ori Ire",
        "Oyo East",
        "Oyo West",
        "Saki East",
        "Saki West",
        "Surulere"
    ],
    "Plateau": [
        "Barikin Ladi",
        "Bassa",
        "Bokkos",
        "Jos East",
        "Jos North",
        "Jos South",
        "Kanam",
        "Kanke",
        "Langtang North",
        "Langtang South",
        "Mangu",
        "Mikang",
        "Pankshin",
        "Qua'an Pan",
        "Riyom",
        "Shendam",
        "Wase"
    ],
    "Rivers": [
        "Abua/Odual",
        "Ahoada East",
        "Ahoada West",
        "Akuku Toru",
        "Andoni",
        "Asari-Toru",
        "Bonny",
        "Degema",
        "Emohua",
        "Eleme",
        "Etche",
        "Gokana",
        "Ikwerre",
        "Khana",
        "Obio/Akpor",
        "Ogba/Egbema/Ndoni",
        "Ogu/Bolo",
        "Okrika",
        "Omumma",
        "Opobo/Nkoro",
        "Oyigbo",
        "Port-Harcourt",
        "Tai"
    ],
    "Sokoto": [
        "Binji",
        "Bodinga",
        "Dange-shnsi",
        "Gada",
        "Goronyo",
        "Gudu",
        "Gawabawa",
        "Illela",
        "Isa",
        "Kware",
        "kebbe",
        "Rabah",
        "Sabon birni",
        "Shagari",
        "Silame",
        "Sokoto North",
        "Sokoto South",
        "Tambuwal",
        "Tqngaza",
        "Tureta",
        "Wamako",
        "Wurno",
        "Yabo"
    ],
    "Taraba": [
        "Ardo-kola",
        "Bali",
        "Donga",
        "Gashaka",
        "Cassol",
        "Ibi",
        "Jalingo",
        "Karin-Lamido",
        "Kurmi",
        "Lau",
        "Sardauna",
        "Takum",
        "Ussa",
        "Wukari",
        "Yorro",
        "Zing"
    ],
    "Yobe": [
        "Bade",
        "Bursari",
        "Damaturu",
        "Fika",
        "Fune",
        "Geidam",
        "Gujba",
        "Gulani",
        "Jakusko",
        "Karasuwa",
        "Karawa",
        "Machina",
        "Nangere",
        "Nguru Potiskum",
        "Tarmua",
        "Yunusari",
        "Yusufari"
    ],
    "Zamfara": [
        "Anka",
        "Bakura",
        "Birnin Magaji",
        "Bukkuyum",
        "Bungudu",
        "Gummi",
        "Gusau",
        "Kaura",
        "Namoda",
        "Maradun",
        "Maru",
        "Shinkafi",
        "Talata Mafara",
        "Tsafe",
        "Zurmi"
    ]
      };

      const stateSelect = document.getElementById('state');
      const lgaSelect = document.getElementById('lga');

      // Populate states
      Object.keys(stateAndLGAs).forEach(state => {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        stateSelect.appendChild(option);
      });

      // Populate LGAs when state is selected
      stateSelect.addEventListener('change', function () {
        const selectedState = this.value;
        lgaSelect.innerHTML = '<option value="">-- Select LGA --</option>';

        if (stateAndLGAs[selectedState]) {
          stateAndLGAs[selectedState].forEach(lga => {
            const option = document.createElement('option');
            option.value = lga;
            option.textContent = lga;
            lgaSelect.appendChild(option);
          });
        }
      });
    });

document.getElementById("paystackBtn").addEventListener("click", function () {
    const paystackKey = document.getElementById("paystack-key").value;
    const siteurl = document.getElementById("siteurl").value;
    const email = document.getElementById("client_email").value;
    const amount = document.getElementById("booking_amount").value * 100; // convert to kobo
    const reference = document.getElementById("reference").value;

    const handler = PaystackPop.setup({
        key: paystackKey,
        email: email,
        amount: amount,
        currency: "NGN",
        ref: reference,
        callback: function (response) {
            window.location.href = `${siteurl}verify-payment.php?action=verify-therapist-payment&reference=${response.reference}&booking_id=${reference}`;
        },
        onClose: function () {
            alert("Payment cancelled.");
        }
    });
    handler.openIframe();
});


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



document.addEventListener("DOMContentLoaded", function () {
    const shareButton = document.getElementById("shareProfileBtn");
    const vendorName = document.getElementById("vendorName").value;
    const shareUrl = document.getElementById("shareUrl").value;

    shareButton.addEventListener("click", async function () {
        const shareData = {
            title: vendorName + " - Vendor Profile",
            text: "Check out this vendor profile on HustleTunes!",
            url: shareUrl
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
            } catch (err) {
                console.log("Share cancelled or failed", err);
            }
        } else {
            // Fallback: copy link to clipboard
            navigator.clipboard.writeText(shareData.url)
                .then(() => {
                    alert("Profile link copied to clipboard: " + shareData.url);
                })
                .catch(err => {
                    console.error("Failed to copy link:", err);
                });
        }
    });
});



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



document.addEventListener("DOMContentLoaded", function() {
  const cartItems = document.querySelector('.cart-items');
  if (!cartItems) return; // prevent error if .cart-items doesn't exist

  cartItems.addEventListener('click', function(e) {
    const btn = e.target.closest('.quantity-btn');
    if (!btn) return;

    const container = btn.closest('.quantity-selector');
    const input = container.querySelector('.quantity-input');
    const itemId = input.dataset.itemId;

    let value = parseInt(input.value) || 1;
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 100;

    if (btn.classList.contains('increase')) {
      if (value < max) value++;
    } else if (btn.classList.contains('decrease')) {
      if (value > min) value--;
    }

    input.value = value;

    const updateBtn = document.querySelector('.btn-update');
    if (updateBtn) updateBtn.disabled = false;

    console.log(`Item ${itemId} quantity changed to ${value}`);
  });
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



document.addEventListener('DOMContentLoaded', function() {
  const paystackRadio = document.getElementById('paystack');
  const manualRadio = document.getElementById('manual');
  const paymentButton = document.getElementById('paymentButton');
  const orderTotalEl = document.getElementById('order_total');
  const siteCurrencyEl = document.getElementById('site_currency');
  const btnPriceText = document.getElementById('btn-price-text');

  // ✅ Ensure required elements exist
  if (!paymentButton || !orderTotalEl || !siteCurrencyEl) {
    console.warn('⚠️ Missing required payment elements in DOM.');
    return;
  }

  const orderTotal = orderTotalEl.value;
  const siteCurrency = siteCurrencyEl.value;

  // Display currency and total initially
  if (btnPriceText) {
    btnPriceText.textContent = siteCurrency + orderTotal;
  }

  function updatePaymentButton() {
    if (!paymentButton) return;

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

  if (paystackRadio) paystackRadio.addEventListener('change', updatePaymentButton);
  if (manualRadio) manualRadio.addEventListener('change', updatePaymentButton);
});
