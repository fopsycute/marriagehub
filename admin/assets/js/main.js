function toggleSlotField(selectElement) {
    const slotField = document.getElementById('slotField');
    const slotInput = document.getElementById('available_slots');

    if (selectElement.value === 'Limited Slot') {
        slotField.style.display = 'block';
    } else {
        slotField.style.display = 'none';
        slotInput.value = ''; // clear input when hidden
    }
}


(function() {
    const pricingType = document.getElementById("pricingType");
    if (!pricingType) return;

    const singlePrice = document.getElementById("singlePriceGroup");
    const priceRange = document.getElementById("priceRangeGroup");
    const customQuote = document.getElementById("customQuoteNote");

    const priceInput = document.getElementById("price");
    const minPrice = document.getElementById("minPrice");
    const maxPrice = document.getElementById("maxPrice");

    pricingType.addEventListener("change", function () {
        const value = this.value;

        // Hide all sections
        singlePrice.classList.add("d-none");
        priceRange.classList.add("d-none");
        customQuote.classList.add("d-none");

        // Clear values
        priceInput.value = "";
        minPrice.value = "";
        maxPrice.value = "";

        // Show appropriate section
        if (value === "Starting Price") {
            singlePrice.classList.remove("d-none");
        } else if (value === "Price Range") {
            priceRange.classList.remove("d-none");
        } else if (value === "Custom Quote") {
            customQuote.classList.remove("d-none");
        }
    });
})();


// Show/hide suspend reason field
(function () {
    const statusSelect = document.getElementById('statusSelect');
    if (!statusSelect) return; // <-- prevents "null addEventListener" error

    const reasonBox = document.getElementById('suspendReasonBox');

    function toggleReason() {
        reasonBox.style.display = (statusSelect.value === 'suspended') ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleReason);
    toggleReason(); // initial check
})();


    document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelects');
    const reasonBox = document.getElementById('rejectReasonBox');
    function toggleReason() {
      reasonBox.style.display = (statusSelect.value === 'rejected') ? 'block' : 'none';
    }
    statusSelect.addEventListener('change', toggleReason);
    toggleReason(); // initial check
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





    (function () {

    function initDateTimeRow(row) {
        const today = new Date().toISOString().split("T")[0];

        const dateInput = row.querySelector('input[name="event_dates[]"]');
        const startTimeInput = row.querySelector('input[name="event_start_times[]"]');
        const endTimeInput = row.querySelector('input[name="event_end_times[]"]');

        // Block past dates
        dateInput.setAttribute("min", today);

        dateInput.addEventListener("change", function () {
            const selectedDate = dateInput.value;

            if (selectedDate === today) {
                const now = new Date();
                const minTime = now.toTimeString().slice(0, 5);

                startTimeInput.min = minTime;
                endTimeInput.min = minTime;

                validateStart();
                validateEnd();
            } else {
                startTimeInput.removeAttribute("min");
                endTimeInput.removeAttribute("min");

                startTimeInput.setCustomValidity("");
                endTimeInput.setCustomValidity("");
            }
        });

        function validateStart() {
            startTimeInput.setCustomValidity("");

            if (!dateInput.value || !startTimeInput.value) return;

            const selectedDate = dateInput.value;
            const now = new Date();
            const [sh, sm] = startTimeInput.value.split(":");

            const selectedStart = new Date();
            selectedStart.setHours(sh, sm, 0, 0);

            // Prevent past time if date is today
            if (selectedDate === today && selectedStart < now) {
                startTimeInput.setCustomValidity("Start time must be in the future.");
            }

            validateEnd();
        }

        function validateEnd() {
            endTimeInput.setCustomValidity("");

            if (!startTimeInput.value || !endTimeInput.value) return;

            const [sh, sm] = startTimeInput.value.split(":");
            const [eh, em] = endTimeInput.value.split(":");

            const start = new Date();
            start.setHours(sh, sm, 0, 0);

            const end = new Date();
            end.setHours(eh, em, 0, 0);

            if (end <= start) {
                endTimeInput.setCustomValidity("End time must be later than start time.");
            }
        }

        startTimeInput.addEventListener("input", validateStart);
        endTimeInput.addEventListener("input", validateEnd);
    }

    function addRow() {
        const container = document.getElementById("dateTimeRepeater");
        const row = document.createElement("div");
        row.className = "row mb-2 dateTimeRow";

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
                <button type="button" class="btn btn-danger btn-sm removeRowBtn">-</button>
            </div>
        `;

        container.appendChild(row);

        // Activate validation logic for this new row
        initDateTimeRow(row);

        // Remove row button
        row.querySelector(".removeRowBtn").addEventListener("click", function () {
            row.remove();
        });
    }

    // Initialize page
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize existing rows
        document.querySelectorAll(".dateTimeRow").forEach(initDateTimeRow);

        // Attach (+) button event
        const plusBtn = document.querySelector(".btn-success.btn-sm");
        if (plusBtn) {
            plusBtn.addEventListener("click", function () {
                addRow();
            });
        }
    });

})();

// Define the add ticket handler
(function () {

    const ticketsWrapper = document.getElementById('ticketsWrapper');
    const addTicketBtn = document.getElementById('addTicket');

    // Stop if elements don't exist
    if (!ticketsWrapper || !addTicketBtn) return;

    // -------------------------------
    // Add More Ticket
    // -------------------------------
    addTicketBtn.addEventListener('click', function () {

        const firstTicket = ticketsWrapper.querySelector('.ticketBox');
        if (!firstTicket) return;

        const newTicket = firstTicket.cloneNode(true);

        // Clear inputs in cloned ticket
        newTicket.querySelectorAll('input, textarea').forEach(field => {
            field.value = '';
        });

        // Show remove button on cloned ticket
        const removeBtn = newTicket.querySelector('.removeTicket');
        if (removeBtn) removeBtn.style.display = 'block';

        ticketsWrapper.appendChild(newTicket);
    });

    // -------------------------------
    // Remove Ticket
    // -------------------------------
    ticketsWrapper.addEventListener('click', function (e) {
        if (!e.target.classList.contains('removeTicket')) return;

        const ticketBoxes = ticketsWrapper.querySelectorAll('.ticketBox');

        // Keep at least 1 ticket
        if (ticketBoxes.length > 1) {
            e.target.closest('.ticketBox').remove();
        } else {
            alert('At least one ticket must remain.');
        }
    });

})();





  // Prevent multiple bindings (in case script runs again).
  // Use an IIFE and local variables so re-loading this script won't trigger
  // a duplicate top-level const/let declaration error.

  // Handle remove ticket dynamically
  document.getElementById('ticketsWrapper').addEventListener('click', function (e) {
    if (e.target.classList.contains('removeTicket')) {
      const wrapper = document.getElementById('ticketsWrapper');
      const allTickets = wrapper.querySelectorAll('.ticketBox');

      // Prevent removing if only one ticket left
      if (allTickets.length > 1) {
        e.target.closest('.ticketBox').remove();
      } else {
        alert('At least one ticket must remain.');
      }
    }
  });

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


function initTinyMCE(selector) {
  tinymce.init({
    selector: selector,
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' }
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
}


  function toggleHybridLocationFields() {
    const type = document.getElementById('hybridLocationType').value;
    const nigeriaFields = document.getElementById('nigeriaHybridFields');
    const foreignFields = document.getElementById('foreignHybridFields');

    // Hide both
    nigeriaFields.style.display = 'none';
    foreignFields.style.display = 'none';

    // Clear inputs in both sections first
    document.querySelectorAll('#nigeriaHybridFields input').forEach(input => input.value = '');
    document.querySelectorAll('#foreignHybridFields input').forEach(input => input.value = '');

    // Then show and use only the selected section
    if (type === 'nigeria') {
      nigeriaFields.style.display = 'block';
    } else if (type === 'foreign') {
      foreignFields.style.display = 'block';
    }
  }

// Re-number modules after removal
function updateModuleNumbers(container, selector) {
  container.querySelectorAll(selector).forEach((module, index) => {
    module.querySelector('.module-number').textContent = index + 1;
  });
}



