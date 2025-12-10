  
 /*
 document.addEventListener("DOMContentLoaded", function() {
  const pricingType = document.getElementById("pricingType");
  const singlePrice = document.getElementById("singlePriceGroup");
  const priceRange = document.getElementById("priceRangeGroup");
  const customQuote = document.getElementById("customQuoteNote");

  const priceInput = document.getElementById("price");
  const minPrice = document.getElementById("minPrice");
  const maxPrice = document.getElementById("maxPrice");

  pricingType.addEventListener("change", function() {
    const value = this.value;

    // Hide all sections first
    singlePrice.classList.add("d-none");
    priceRange.classList.add("d-none");
    customQuote.classList.add("d-none");

    // Clear all hidden field values
    priceInput.value = "";
    minPrice.value = "";
    maxPrice.value = "";

    // Show the correct field
    if (value === "Starting Price") {
      singlePrice.classList.remove("d-none");
    } else if (value === "Price Range") {
      priceRange.classList.remove("d-none");
    } else if (value === "Custom Quote") {
      customQuote.classList.remove("d-none");
    }
  });
});
*/


document.addEventListener("DOMContentLoaded", function() {
  const statusSelect = document.getElementById("booking_status");
  if (!statusSelect) return; // stop if the element doesn't exist on this page

  const statusDescription = document.getElementById("statusDescription");
  const reasonContainer = document.getElementById("reasonContainer");
  const reasonLabel = document.getElementById("reasonLabel");

  const descriptions = {
    "pending": "Your booking is awaiting review or confirmation by the provider.",
    "confirmed": "Approving of booking. A confirmation and payment details will be sent to you shortly.",
    "in progress": "The service or booking is currently being handled or is in progress.",
    "cancelled": "Reason for cancelling the booking must be provided below.",
    "completed": "This booking has been successfully completed."
  };

  function updateStatusDetails() {
    const value = statusSelect.value;
    statusDescription.textContent = descriptions[value] || "";
    reasonContainer.style.display = (value === "cancelled") ? "block" : "none";
    reasonLabel.textContent = (value === "cancelled")
      ? "Reason for Cancelling"
      : "Reason for Status Update";
  }

  updateStatusDetails();
  statusSelect.addEventListener("change", updateStatusDetails);
});



// delete portfolio image or video
$(document).on('click', '.remove-file', function() {

    var parent = $(this).closest(".portfolio-item");
    var file = parent.data("file");

    var user_id = $("#user_id").val();
    var siteUrl = $("#siteurl").val();
    var ajaxUrl = siteUrl + "script/admin.php";

    if (!confirm("Remove this file permanently?")) return;

    $.ajax({
        url: ajaxUrl,
        method: "POST",
        dataType: "json",
        data: {
            action: "deletePortfolio",
            file: file,
            user_id: user_id
        },
        success: function(response) {
            parent.remove(); // remove only the file badge
            alert(response);
        }
    });

});


// Show/hide suspend reason field


  document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const reasonBox = document.getElementById('suspendReasonBox');
    function toggleReason() {
      reasonBox.style.display = (statusSelect.value === 'suspended') ? 'block' : 'none';
    }
    statusSelect.addEventListener('change', toggleReason);
    toggleReason(); // initial check
  });

  // Event Type "Other" toggle
  document.getElementById('eventType').addEventListener('change', function() {
    document.getElementById('otherEventType').style.display = this.value.includes('Other') ? 'block' : 'none';
  });

  // Delivery format toggle
  document.getElementById('deliveryFormat').addEventListener('change', function() {
    const formats = ['physicalFields','onlineFields','hybridFields','videoFields','textFields'];
    formats.forEach(f => document.getElementById(f).style.display = 'none');
    if(this.value) document.getElementById(this.value+'Fields').style.display = 'block';
  });

  // Location type toggle
  document.getElementById('locationType').addEventListener('change', function() {
    document.getElementById('nigeriaAddress').style.display = this.value==='nigeria' ? 'block':'none';
    document.getElementById('foreignAddress').style.display = this.value==='foreign' ? 'block':'none';
  });

  // Pricing toggle
  document.querySelectorAll('input[name="pricing_type"]').forEach(r => {
    r.addEventListener('change', function(){
      document.getElementById('donationFields').style.display = 'none';
      document.getElementById('freeFields').style.display = 'none';
      document.getElementById('paidFields').style.display = 'none';
      if(this.value==='donation') document.getElementById('donationFields').style.display='block';
      if(this.value==='free') document.getElementById('freeFields').style.display='block';
      if(this.value==='paid') document.getElementById('paidFields').style.display='block';
    });
  });

  // Add more tickets
  document.getElementById('addTicket').addEventListener('click', function(){
    const wrapper = document.getElementById('ticketsWrapper');
    const newTicket = wrapper.firstElementChild.cloneNode(true);
    newTicket.querySelectorAll('input, textarea').forEach(i=>i.value='');
    wrapper.appendChild(newTicket);
  });

  
    function addDateTimeRow() {
  const container = document.getElementById('dateTimeRepeater');
  const row = document.createElement('div');
  row.className = 'row mb-2 dateTimeRow';
  row.innerHTML = `
    <div class="col">
      <input type="date" class="form-control" name="event_dates[]" required>
    </div>
    <div class="col">
      <input type="time" class="form-control" name="event_start_times[]" required>
    </div>
    <div class="col">
      <input type="time" class="form-control" name="event_end_times[]" required>
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.dateTimeRow').remove()">-</button>
    </div>
  `;
  container.appendChild(row);

    const today = new Date().toISOString().split('T')[0];
  const dateInput = row.querySelector('input[name="event_dates[]"]');
  const startTimeInput = row.querySelector('input[name="event_start_times[]"]');
  const endTimeInput = row.querySelector('input[name="event_end_times[]"]');
  dateInput.setAttribute('min', today);

  // Helper to check and reset invalid time
  function validateTime(input) {
    if (dateInput.value === today && input.value) {
      const now = new Date();
      const [h, m] = input.value.split(':');
      const selected = new Date();
      selected.setHours(h, m, 0, 0);
      if (selected < now) {
        input.value = '';
        input.setCustomValidity('Please select a future time.');
        input.reportValidity();
      } else {
        input.setCustomValidity('');
      }
    } else {
      input.setCustomValidity('');
    }
  }

  dateInput.addEventListener('change', function() {
    if (dateInput.value === today) {
      const now = new Date();
      const minTime = now.toTimeString().slice(0,5);
      startTimeInput.min = minTime;
      endTimeInput.min = minTime;
      validateTime(startTimeInput);
      validateTime(endTimeInput);
    } else {
      startTimeInput.removeAttribute('min');
      endTimeInput.removeAttribute('min');
      startTimeInput.setCustomValidity('');
      endTimeInput.setCustomValidity('');
    }
  });

  startTimeInput.addEventListener('input', function() {
    validateTime(startTimeInput);
  });
  endTimeInput.addEventListener('input', function() {
    validateTime(endTimeInput);
  });
}


function addVideoModule() {
  const container = document.getElementById('videoModules');
  const firstModule = container.querySelector('.video-module');
  const newModule = firstModule.cloneNode(true);

  const moduleCount = container.querySelectorAll('.video-module').length + 1;
  newModule.querySelector('.module-number').textContent = moduleCount;

  // Clean cloned TinyMCE UI
  newModule.querySelectorAll('.tox').forEach(el => el.remove());

  // Reset fields
  newModule.querySelectorAll('input, textarea').forEach(el => {
    if (el.type === 'checkbox' || el.type === 'radio') {
      el.checked = false;
    } else {
      el.value = '';
    }

    if (el.classList.contains('editor')) {
      el.removeAttribute('aria-hidden');
      el.style.display = '';
      el.id = `video_editor_${moduleCount}`;
    }
  });

  // Add Remove button (only for cloned modules)
  const removeBtn = document.createElement('button');
  removeBtn.type = 'button';
  removeBtn.className = 'remove-module';
  removeBtn.innerHTML = '❌ Remove';
  removeBtn.onclick = function () {
    newModule.remove();
    updateModuleNumbers(container, '.video-module');
  };
  newModule.appendChild(removeBtn);

  container.appendChild(newModule);

  initTinyMCE(`#video_editor_${moduleCount}`);
}

function addTextModule() {
  const container = document.getElementById('textModules');
  const firstModule = container.querySelector('.text-module');
  const newModule = firstModule.cloneNode(true);

  const moduleCount = container.querySelectorAll('.text-module').length + 1;
  newModule.querySelector('.module-number').textContent = moduleCount;

  // Clean cloned TinyMCE UI
  newModule.querySelectorAll('.tox').forEach(el => el.remove());

  // Reset fields
  newModule.querySelectorAll('input, textarea').forEach(el => {
    if (el.type === 'checkbox' || el.type === 'radio') {
      el.checked = false;
    } else {
      el.value = '';
    }

    if (el.classList.contains('editor')) {
      el.removeAttribute('aria-hidden');
      el.style.display = '';
      el.id = `text_editor_${moduleCount}`;
    }
  });

  // Add Remove button (only for cloned modules)
  const removeBtn = document.createElement('button');
  removeBtn.type = 'button';
  removeBtn.className = 'remove-module';
  removeBtn.innerHTML = '❌ Remove';
  removeBtn.onclick = function () {
    newModule.remove();
    updateModuleNumbers(container, '.text-module');
  };
  newModule.appendChild(removeBtn);

  container.appendChild(newModule);

  initTinyMCE(`#text_editor_${moduleCount}`);
}

// Re-number modules after removal
function updateModuleNumbers(container, selector) {
  container.querySelectorAll(selector).forEach((module, index) => {
    module.querySelector('.module-number').textContent = index + 1;
  });
}


