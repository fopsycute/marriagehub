// select
$(document).ready(function() {
    $('.select-multiple').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%'
    });
});


//select multicategory
  // when category changes
  $(document).ready(function () {
    $('#category').on('change', function () {
        let selectedCategories = $(this).val(); // array of selected IDs
        var siteUrl = $('#siteurl').val(); // hidden input holding your base URL
        var ajaxUrl = siteUrl + "script/register.php"; // build full path

        if (selectedCategories && selectedCategories.length > 0) {
            $.ajax({
                url: ajaxUrl,
                method: "GET",
                data: {
                    action: "subcategorieslists",
                    parent_ids: selectedCategories.join(",") // send comma-separated IDs
                },
                dataType: "json",
                success: function (response) {
                    let $subcategory = $('#subcategory');
                    $subcategory.empty(); // clear old options

                    if (response.length > 0) {
                        $.each(response, function (index, subcat) {
                            $subcategory.append(
                                $('<option>', {
                                    value: subcat.id,
                                    text: subcat.category_name
                                })
                            );
                        });
                    } else {
                        $subcategory.append('<option value="">No subcategories found</option>');
                    }

                    // refresh Select2 to update new options
                    $subcategory.trigger('change');
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching subcategories:", error);
                }
            });
        } else {
            $('#subcategory').empty()
                .append('<option value="">-- Select Sub-Category --</option>')
                .trigger('change');
        }
    });
});

//specialization list
$(document).ready(function () {
    $('#specializations').on('change', function () {
        let selectedSpecs = $(this).val();
        let siteUrl = $('#siteurl').val();
        let ajaxUrl = siteUrl + "script/register.php";

        if (selectedSpecs && selectedSpecs.length > 0) {
            $.ajax({
                url: ajaxUrl,
                method: "GET",
                data: {
                    action: "subspecializationlists",
                    parent_ids: selectedSpecs.join(",")
                },
                dataType: "json",
                success: function (response) {
                    let $subSelect = $('#sub_specialization');
                    $subSelect.empty();

                    if (response && response.length > 0) {
                        $.each(response, function (index, sub) {
                            $subSelect.append(
                                $('<option>', {
                                    value: sub.id,
                                    text: sub.name // adjust key to match your DB column
                                })
                            );
                        });
                    } else {
                        $subSelect.append('<option value="">No sub-specializations found</option>');
                    }

                    $subSelect.trigger('change');
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching sub-specializations:", error);
                }
            });
        } else {
            $('#sub_specialization').empty()
                .append('<option value="">-- Select Sub-Specialization --</option>')
                .trigger('change');
        }
    });
});

//filter
$('#blogFilterForm #category').on('change', function () {
    var selected = $(this).val();
    var siteUrl = $('#siteurl').val(); // hidden input holding your base URL
    var $subcat = $('#blogFilterForm #subcategory');
    $subcat.html('<option value="">Loading...</option>');
    if (selected && selected.length > 0) {
        $.get(siteUrl + 'script/register.php', {action: 'subcategorieslists', parent_ids: selected.join(',')}, function (data) {
            var opts = '<option value="">-- Select Sub-Category --</option>';
            var arr = [];
            try { arr = JSON.parse(data); } catch(e) {}
            if (arr.length) {
                arr.forEach(function (s) {
                    opts += '<option value="'+s.id+'">'+s.category_name+'</option>';
                });
            }
            $subcat.html(opts);
        });
    } else {
        $subcat.html('<option value="">-- Select Sub-Category --</option>');
    }
});




$(function() {
  // Auto-submit when sort changes
  $('#questionFilterForm select[name="sort"]').on('change', function() {
    $('#questionFilterForm').submit();
  });

  // Load subcategories dynamically
  $('#questionFilterForm #category').on('change', function() {
    var selected = $(this).val();
    var siteUrl = '<?php echo $siteurl; ?>';
    var $subcat = $('#questionFilterForm #subcategory');
    $subcat.html('<option value="">Loading...</option>');
    if (selected && selected.length > 0) {
      $.get(siteUrl + 'script/register.php', {action: 'subcategorieslists', parent_ids: selected.join(',')}, function(data) {
        var opts = '<option value="">-- Select Sub-Category --</option>';
        try {
          var arr = JSON.parse(data);
          if (arr.length) {
            arr.forEach(function(s) {
              opts += '<option value="'+s.id+'">'+s.category_name+'</option>';
            });
          }
        } catch(e) {}
        $subcat.html(opts);
      });
    } else {
      $subcat.html('<option value="">-- Select Sub-Category --</option>');
    }
  });
});


$(document).ready(function() {
  $('#groupFilterForm #category').on('change', function() {
    var selected = $(this).val();
    var siteUrl = $('#siteurl').val();
    var $subcat = $('#groupFilterForm #subcategory');
    
    $subcat.html('<option value="">Loading...</option>');
    if (selected && selected.length > 0) {
      $.get(siteUrl + 'script/register.php', {action: 'subcategorieslists', parent_ids: selected.join(',')}, function(data) {
        var opts = '<option value="">-- Select Subcategory --</option>';
        var arr = [];
        try { arr = JSON.parse(data); } catch(e) {}
        if (arr.length) {
          arr.forEach(function(s) {
            opts += '<option value="' + s.id + '">' + s.category_name + '</option>';
          });
        }
        $subcat.html(opts);
      });
    } else {
      $subcat.html('<option value="">-- Select Subcategory --</option>');
    }
  });
});

//profession
 $(document).ready(function () {
    $('#professional_field').on('change', function () {
        let selectedFields = $(this).val();
        let siteUrl = $('#siteurl').val();
        let ajaxUrl = siteUrl + "script/register.php";

        if (selectedFields && selectedFields.length > 0) {
            $.ajax({
                url: ajaxUrl,
                method: "GET",
                data: {
                    action: "subprofessionlists",
                    parent_ids: selectedFields.join(",")
                },
                dataType: "json",
                success: function (response) {
                    let $titleDropdown = $('#professional_title');
                    $titleDropdown.empty();

                    if (response && response.length > 0) {
                        $.each(response, function (index, title) {
                            $titleDropdown.append(
                                $('<option>', {
                                    value: title.id,
                                    text: title.name // adjust if backend uses `name` or `title`
                                })
                            );
                        });
                    } else {
                        $titleDropdown.append('<option value="">No titles found</option>');
                    }

                    $titleDropdown.trigger('change'); // refresh if using select2
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching titles:", error);
                }
            });
        } else {
            $('#professional_title').empty()
                .append('<option value="">-- Select Title --</option>')
                .trigger('change');
        }
    });
});




$(document).ready(function () {
    $('#vendoreditForum').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


//create tiicket

$(document).ready(function () {
    $('#createTicketForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Ticket');
            }
        });
    });
});


$(document).ready(function () {
    $('#sendawaiting').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "successfully sent!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Send Message');
            }
        });
    });
});



$(document).ready(function () {
    $('#updateStatusForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#updateStatusBtn').prop('disabled', true).text('Submitting...');
        $('#display').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#display')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "successfully updated!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#display')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#display').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#display')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#display').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#updateStatusBtn').prop('disabled', false).text('Update Status');
            }
        });
    });
});


$(document).ready(function () {

    // Open the confirmation modal when the "Update Wallet" button is clicked
    $('#openConfirmModal').on('click', function () {
        $('#confirmModal').modal('show');
    });

    // Perform AJAX when user clicks "Yes, Update"
    $('#confirmSubmit').on('click', function () {
        const form = $('#walletForm')[0];
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val(); // make sure you have <input type="hidden" id="siteurl" value="<?= $siteurl ?>">
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#messages').removeClass('alert alert-success alert-danger').hide();
        $('#confirmSubmit').prop('disabled', true).text('Processing...');

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $('#confirmModal').modal('hide'); // hide modal
                if (response.status === 'success') {
                    $('#messages').addClass('alert alert-success').html(response.message).fadeIn();
                    form.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    $('#messages').addClass('alert alert-danger').html(response.message).fadeIn();
                    $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
                }
            },
            error: function (xhr) {
                $('#confirmModal').modal('hide');
                $('#messages').addClass('alert alert-danger')
                    .text('An error occurred. Please try again.')
                    .fadeIn();
            },
            complete: function () {
                $('#confirmSubmit').prop('disabled', false).text('Yes, Update');
            }
        });
    });

});


$(document).ready(function () {
    $('#editForum').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


// update booking


$(document).ready(function () {
    $('#editBookingForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


// edit admin group
$(document).ready(function () {
    $('#admineditGroupForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});

//edit group
$(document).ready(function () {
    $('#admineditnewGroupForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});



$(document).ready(function () {
    $('#updategroupmember').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});

//book appointment
$(document).ready(function () {
    $('#book-appointment-form').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Your booking request has been sent and is pending approval.");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});




$(document).ready(function () {
    $('#update-user').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});

//delete comment
$(document).off('click', '.deletecomment').on('click', '.deletecomment', function() {
    var comment_id = $(this).attr("id");
    var action = "deletecomment";
    var clickedBtn = $(this);
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/user.php";

    if(confirm("Are you sure you want to delete this comment?")) {
        $.ajax({
            url: ajaxUrl,
            method: 'POST',
            data: { image_id: comment_id, action: action },
            dataType: 'json',
            beforeSend: function() {
                clickedBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
            },
            success: function(data) {
                if (data.status === 'success') {
                    const $commentBlock = clickedBtn.closest('.comment');
                    const $nestedReplies = $commentBlock.next('.nested-replies');

                    $commentBlock.fadeOut(300, function() {
                        $(this).remove();
                        if ($nestedReplies.length) {
                            $nestedReplies.slideUp(200, function() {
                                $(this).remove();
                            });
                        }
                    });

                    alert(data.messages);
                } else {
                    alert(data.messages || 'Failed to delete comment.');
                    clickedBtn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
                }
            },
            error: function() {
                alert('Error: Unable to delete comment. Please try again.');
                clickedBtn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
            }
        });
    } else {
        return false;
    }
});


//deleteanswer
$(document).off('click', '.deleteanswer').on('click', '.deleteanswer', function() {
    var comment_id = $(this).attr("id");
    var action = "deleteanswer";
    var clickedBtn = $(this);
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/user.php";

    if(confirm("Are you sure you want to delete this answer?")) {
        $.ajax({
            url: ajaxUrl,
            method: 'POST',
            data: { image_id: comment_id, action: action },
            dataType: 'json',
            beforeSend: function() {
                clickedBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
            },
            success: function(data) {
                if (data.status === 'success') {
                    const $commentBlock = clickedBtn.closest('.comment');
                    const $nestedReplies = $commentBlock.next('.nested-replies');

                    $commentBlock.fadeOut(300, function() {
                        $(this).remove();
                        if ($nestedReplies.length) {
                            $nestedReplies.slideUp(200, function() {
                                $(this).remove();
                            });
                        }
                    });

                    alert(data.messages);
                } else {
                    alert(data.messages || 'Failed to delete comment.');
                    clickedBtn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
                }
            },
            error: function() {
                alert('Error: Unable to delete comment. Please try again.');
                clickedBtn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
            }
        });
    } else {
        return false;
    }
});

//reply comment
$(document).ready(function() {

  // Smooth toggle for nested replies
  $(document).on('click', '.view-replies-link', function(e) {
    e.preventDefault();

    const commentId = $(this).data('comment-id');
    const $replies = $('#replies-' + commentId);

    if ($replies.length) {
      // Close other visible reply groups if desired (optional)
      // $('.nested-replies').not($replies).stop(true, true).slideUp(250).fadeOut(150);

      if ($replies.is(':visible')) {
        $replies.stop(true, true).slideUp(250).fadeOut(150);
      } else {
        $replies
          .stop(true, true)
          .slideDown(250)
          .css('display', 'block')
          .hide()
          .fadeIn(200);
      }
    } else {
      console.warn('No replies container found for comment:', commentId);
    }
  });

  // Toggle reply form for both comments and replies

  $(document).on('click', '.reply', function(e) {
    e.preventDefault();

    const commentId = $(this).data('comment-id');
    const $targetForm = $('#reply-form-' + commentId);

    if ($targetForm.length) {
      // Close all other reply forms smoothly
      $('.reply-form').not($targetForm).stop(true, true).slideUp(200).fadeOut(150);

      // Toggle the selected reply form smoothly
      if ($targetForm.is(':visible')) {
        $targetForm.stop(true, true).slideUp(250).fadeOut(150);
      } else {
        $targetForm
          .stop(true, true)
          .slideDown(250)
          .css('display', 'flex')
          .hide()
          .fadeIn(200, function() {
            $(this).find('textarea').focus();
          });
      }
    } else {
      console.warn('No reply form found for ID:', commentId);
    }
  });

});




//view question answer
$(document).ready(function() {

  // Smooth toggle for nested replies
  $(document).on('click', '.view-answer-link', function(e) {
    e.preventDefault();

    const commentId = $(this).data('comment-id');
    const $replies = $('#replies-' + commentId);

    if ($replies.length) {
      // Close other visible reply groups if desired (optional)
      // $('.nested-replies').not($replies).stop(true, true).slideUp(250).fadeOut(150);

      if ($replies.is(':visible')) {
        $replies.stop(true, true).slideUp(250).fadeOut(150);
      } else {
        $replies
          .stop(true, true)
          .slideDown(250)
          .css('display', 'block')
          .hide()
          .fadeIn(200);
      }
    } else {
      console.warn('No replies container found for answer:', commentId);
    }
  });

  // Toggle reply form for both comments and replies

  $(document).on('click', '.replyanswer', function(e) {
    e.preventDefault();

    const commentId = $(this).data('question-id');
    const $targetForm = $('#answer-form-' + commentId);

    if ($targetForm.length) {
      // Close all other reply forms smoothly
      $('.answer-form').not($targetForm).stop(true, true).slideUp(200).fadeOut(150);

      // Toggle the selected reply form smoothly
      if ($targetForm.is(':visible')) {
        $targetForm.stop(true, true).slideUp(250).fadeOut(150);
      } else {
        $targetForm
          .stop(true, true)
          .slideDown(250)
          .css('display', 'flex')
          .hide()
          .fadeIn(200, function() {
            $(this).find('textarea').focus();
          });
      }
    } else {
      console.warn('No reply form found for ID:', commentId);
    }
  });

});

//post comment on blog 

//create events
$(document).ready(function () {
    // Ensure handler attaches once
    $('#addEvent').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitEventBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Event created successfully!");
                        location.reload();
                    }, 500);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitEventBtn').prop('disabled', false).text('Create Event');
            }
        });
    });
});

//create tribe

$(document).ready(function () {
  // ensure we only bind once
  $('#createGroupForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'create_group');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#createGroupBtn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('SUBMIT AND CREATE');
      }
    });
  });
});


//post answer
$(document).ready(function () {
  // ensure we only bind once
  $('#postanswer').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'post_answers');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});

//post review

$(document).ready(function () {
  // ensure we only bind once
  $('#postreview').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'post_review');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});

$(document).ready(function () {
  // ensure we only bind once
  $('#postproductreview').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'post_productreview');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});

//update review
$(document).ready(function () {
  // ensure we only bind once
  $('#editReviewForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'updateproduct_review');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});




$(document).on('click', '.remove-item', function () {
    let button = $(this);
    let itemId = button.data('item-id');
    let siteurl = $('#siteurl').val();

    if (!itemId) {
        showToast('Invalid item ID.');
        return;
    }

    if (confirm("Remove this item from your cart?")) {
        $.post(siteurl + 'script/user.php', { action: 'remove_cart_item', item_id: itemId }, function (response) {
            if (response.status === 'success') {
                showToast(response.message);

                // Smooth fade-out, then reload
                button.closest('.cart-item').fadeOut(300, function () {
                    $(this).remove();

                    if (response.cartCount !== undefined) {
                        $('.cart-count').text(response.cartCount);
                    }

                    if (response.total !== undefined) {
                        $('.cart-total-amount').text(response.total);
                    }

                    if ($('.cart-item').length === 0) {
                        $('.cart-items').html(`
                            <p class="text-center mt-4">
                                <i class="bi bi-cart"></i> Your cart is empty.
                            </p>
                        `);
                    }

                    // ✅ Reload page after fade-out completes
                    setTimeout(() => {
                        location.reload();
                    }, 400);
                });
            } else {
                showToast(response.message || 'Error removing item.');
            }
        }, 'json').fail(function () {
            showToast('Network error. Please try again.');
        });
    }
});



$(document).on('click', '.btn-update', function () {
    let siteurl = $('#siteurl').val();
    let updates = [];

    // Collect all cart items
    $('.cart-item').each(function () {
        let itemId = $(this).find('.remove-item').data('item-id');
        let quantity = parseInt($(this).find('.quantity-input').val());

        if (itemId && quantity > 0) {
            updates.push({ item_id: itemId, quantity: quantity });
        }
    });

    if (updates.length === 0) {
        showToast('No items to update.');
        return;
    }

    // Send all updates at once
    $.ajax({
        url: siteurl + 'script/user.php',
        method: 'POST',
        data: {
            action: 'bulk_update_cart',
            items: JSON.stringify(updates)
        },
        dataType: 'json',
        beforeSend: function () {
            $('.btn-update').prop('disabled', true).text('Updating...');
        },
        success: function (response) {
            if (response.status === 'success') {
                showToast('Cart updated successfully.');
                location.reload(); // Refresh cart
            } else {
                showToast(response.message || 'Failed to update cart.');
            }
        },
        error: function () {
            showToast('Network error. Please try again.');
        },
        complete: function () {
            $('.btn-update').prop('disabled', false).text('Update Cart');
        }
    });
});


// manual payment button
$(document).ready(function () {
  // Handle manual payment form submission
  $('#manual-payment').off('submit').on('submit', function (e) {
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);

    // Ensure correct action for backend
    formData.append('action', 'paymanual');

    // Ensure your site base URL is available in a hidden input
    let siteUrl = $('#siteurl').val();
    if (!siteUrl) {
      alert("Site URL not found. Please ensure #siteurl input is set.");
      return;
    }

    let ajaxUrl = siteUrl + "script/user.php";
    let $btn = $(form).find('button[type="submit"]');
    let $messages = $('#messages');

    // Disable submit button while processing
    $btn.prop('disabled', true).text('Submitting...');
    $messages.hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        let message = '';

        if (response && response.messages) {
          // Extract plain text if HTML is wrapped
          message = $('<div>').html(response.messages).text();
        } else {
          message = response.message || 'No response message received.';
        }

        if (response.status === 'success') {
          // Success alert
          alert(message);
          $messages.addClass('alert alert-success').html(response.messages).show();

          // Optional: reset form after success
          form.reset();

          // Reload to reflect updates (like new order status)
          setTimeout(() => location.reload(), 1500);
        } else {
          // Show error message inline
          $messages.addClass('alert alert-danger').html(response.messages || message).show();
          $('html, body').animate({ scrollTop: $messages.offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        let errMsg = 'An error occurred while submitting. Please try again.';
        $messages.addClass('alert alert-danger').text(errMsg).show();
        $('html, body').animate({ scrollTop: $messages.offset().top - 100 }, 500);
      },
      complete: function () {
        // Re-enable the button and restore label
        $btn.prop('disabled', false).text('Submit Payment');
      }
    });
  });
});




$(document).ready(function () {
  // ensure we only bind once
  $('#posttherapistreview').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'post_reviewtherapist');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});


//post comment


$(document).ready(function () {
  // ensure we only bind once
  $('#postcomment').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'post_comment');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/user.php";

    var $btn = $('#submit-btn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('Post Comment');
      }
    });
  });
});


//comments
// Prevent double-binding and double submission
$(document).off('submit', '.reply-form').on('submit', '.reply-form', function (e) {
  e.preventDefault();

  const $form = $(this);

  // Stop if form already submitting
  if ($form.data('submitting') === true) {
    console.warn('Form already submitting, skipping duplicate.');
    return false;
  }
  $form.data('submitting', true);

  const formData = new FormData(this);
  const siteUrl = $('#siteurl').val();
  const ajaxUrl = siteUrl + 'script/user.php';

  const $btn = $form.find('button[type="submit"]');
  const $msgBox = $form.find('.message-box');

  // UI feedback
  $btn.prop('disabled', true).text('Submitting...');
  $msgBox.hide().removeClass('alert-success alert-danger').html('');

  $.ajax({
    url: ajaxUrl,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',

    success: function (response) {
      let plainMsg = $('<div>').html(response.messages || response.message || '').text();

      if (response.status === 'success') {
        $msgBox.addClass('alert alert-success').html(plainMsg).fadeIn();

        setTimeout(() => {
          $form.slideUp(250);
        }, 1000);

        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        $msgBox.addClass('alert alert-danger').html(plainMsg).fadeIn();
      }
    },

    error: function (xhr, status, err) {
      console.error(xhr.responseText || err);
      $msgBox
        .addClass('alert alert-danger')
        .text('An error occurred while submitting your reply. Please try again.')
        .fadeIn();
    },

    complete: function () {
      $btn.prop('disabled', false).text('Reply');
      $form.data('submitting', false); // allow new submit after finish
    },
  });

  return false;
});


//submit reply form
$(document).off('submit', '.answer-form').on('submit', '.answer-form', function (e) {
  e.preventDefault();

  const $form = $(this);

  // Stop if form already submitting
  if ($form.data('submitting') === true) {
    console.warn('Form already submitting, skipping duplicate.');
    return false;
  }
  $form.data('submitting', true);

  const formData = new FormData(this);
  const siteUrl = $('#siteurl').val();
  const ajaxUrl = siteUrl + 'script/user.php';

  const $btn = $form.find('button[type="submit"]');
  const $msgBox = $form.find('.message-box');

  // UI feedback
  $btn.prop('disabled', true).text('Submitting...');
  $msgBox.hide().removeClass('alert-success alert-danger').html('');

  $.ajax({
    url: ajaxUrl,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',

    success: function (response) {
      let plainMsg = $('<div>').html(response.messages || response.message || '').text();

      if (response.status === 'success') {
        $msgBox.addClass('alert alert-success').html(plainMsg).fadeIn();

        setTimeout(() => {
          $form.slideUp(250);
        }, 1000);

        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        $msgBox.addClass('alert alert-danger').html(plainMsg).fadeIn();
      }
    },

    error: function (xhr, status, err) {
      console.error(xhr.responseText || err);
      $msgBox
        .addClass('alert alert-danger')
        .text('An error occurred while submitting your reply. Please try again.')
        .fadeIn();
    },

    complete: function () {
      $btn.prop('disabled', false).text('Reply');
      $form.data('submitting', false); // allow new submit after finish
    },
  });

  return false;
});



//create admin group
$(document).ready(function () {
  // ensure we only bind once
  $('#admincreateGroupForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    var form = this;
    var formData = new FormData(form);

    // append the action expected by the API
    formData.append('action', 'create_admingroup');

    var siteUrl = $('#siteurl').val(); // make sure this hidden exists
    var ajaxUrl = siteUrl + "script/admin.php";

    var $btn = $('#submitBtn'); // change to your submit button id if different
    if ($btn.length === 0) $btn = $(form).find('button[type="submit"]');

    $btn.prop('disabled', true).text('Submitting...');
    $('#messages').hide().removeClass('alert-success alert-danger').html('');

    $.ajax({
      url: ajaxUrl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        var plainMsg = '';
        if (response && response.messages) {
          // strip HTML for alert box
          plainMsg = $('<div>').html(response.messages).text();
        } else {
          plainMsg = response.message || 'No response message.';
        }

        if (response.status === 'success') {
          // show alert and reload on OK
          alert(plainMsg);
          // optionally also set #messages area
          $('#messages').addClass('alert alert-success').html(response.messages).show();
          // reload
          location.reload();
        } else {
          // show inline message and scroll to it
          $('#messages').addClass('alert alert-danger').html(response.messages || plainMsg).show();
          $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function (xhr, status, err) {
        console.error(xhr.responseText || err);
        var errMsg = 'An error occurred while submitting. Please try again.';
        $('#messages').addClass('alert alert-danger').text(errMsg).show();
        $('html,body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
      },
      complete: function () {
        $btn.prop('disabled', false).text('SUBMIT AND CREATE');
      }
    });
  });
});


$(document).ready(function() {
    const currentPage = window.location.pathname.split("/").pop(); // get current file name
    if (currentPage !== "reset-password.php") return; // only run on reset-password page

    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const siteUrl = $('#siteurl').val(); // hidden input holding your base URL

    if (!token) {
        alert("Invalid or missing reset token.");
        return;
    }

    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();

        const action = $('#action').val(); // hidden input for action
        const password = $('#password').val().trim();

        if (!password) {
            alert("Please enter a password.");
            return;
        }

        $.ajax({
            url: siteUrl + "script/register.php",
            type: "POST",
            dataType: "json",
            data: {
                token: token,
                password: password,
                action: action
            },
            success: function(res) {
                alert(res.messages);
                if (res.status === 'success') {
                    window.location.href = siteUrl + "login.php";
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert("An error occurred. Please try again.");
            }
        });
    });
});



//login authentication
$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/login.php";

        $('#submitBtn').prop('disabled', true).text('Logging in...');

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.status === 'inactive') {
                        $('#login-result').html('<div class="alert alert-warning">' + response.message + '</div>');
                        return;
                    }

                    // ✅ Unified redirect from PHP
                    $('#login-result').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(() => {
                        window.location.href = siteUrl + response.redirect;
                    }, 1000);
                } else {
                    $('#login-result').html('<div class="alert alert-danger">' + (response.error || 'Invalid credentials') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                $('#login-result').html('<div class="alert alert-danger">Error: ' + error + '</div>');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).text('Login');
            }
        });
    });
});

$(document).on('click', '.deleteblog', function(){
    var image_id = $(this).attr("id");
    var action = "deleteblog";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this blog permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   


//
$(document).on('click', '.deleteimage, .deletevideo', function(e){
    e.preventDefault();

    var fileName = $(this).attr("id");
    var action = $(this).hasClass('deleteimage') ? "deleteimage" : "deletevideo";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if(confirm("Are you sure you want to delete this file permanently?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { file_name: fileName, action: action },
            success: function(data) {
                clickedBtn.parent().remove(); // remove the preview container
                alert(data);
            }
        });
    }
});

// delete listing

//reject listing
$(document).on('click', '.reject-booking', function (e) {
    e.preventDefault();
    var modal = $(this).closest('.modal');
    var reasonSection = modal.find('.rejection-section');
    var actionButtons = modal.find('.action-buttons');

    if (confirm("Are you sure you want to reject this booking?")) {
        actionButtons.hide();
        reasonSection.slideDown();
    }
});

// Cancel rejection
$(document).on('click', '.cancel-reject', function () {
    var modal = $(this).closest('.modal');
    modal.find('.rejection-section').slideUp();
    modal.find('.action-buttons').show();
});

// Submit rejection
$(document).on('click', '.confirm-reject', function () {
    var bookingId = $(this).data('id');
    var modal = $(this).closest('.modal');
    var reason = modal.find('textarea').val().trim();

    if (reason === '') {
        alert('Please enter a reason for rejection.');
        return;
    }

    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";

    $.ajax({
        url: ajaxUrl,
        method: "POST",
        dataType: "json", // ✅ Expect JSON response
        data: {
            image_id: bookingId,
            action: "reject-booking",
            reason: reason
        },
        success: function (response) {
            if (response.status === "success") {
                alert(response.message);
                modal.modal('hide');
                $("tr:has(a[id='" + bookingId + "'])").fadeOut();
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            alert("AJAX Error: " + error);
        }
    });
});



//approve booking
$(document).on('click', '.approve-booking', function() {
    var image_id = $(this).attr("id");
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if (confirm("Are you sure you want to approve this booking? A payment link will be sent to the client.")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            dataType: "json", // ✅ Expect JSON
            data: { image_id: image_id, action: "approve-booking" },
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message);
                    clickedBtn.closest('tr').remove(); // ✅ safer way to remove row
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert("AJAX Error: " + error);
            }
        });
    } else {
        return false;
    }
});
 

//delete question
$(document).on('click', '.deletequestion', function(){
    var question_id = $(this).attr("id");
    var action = "deletequestion";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this question permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { question_id: question_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});

$(document).on('click', '.deletegroup', function(){
    var image_id = $(this).attr("id");
    var action = "deletegroup";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this group permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   


$(document).on('click', '.deletegroupmembers', function(){
    var image_id = $(this).attr("id");
    var action = "deletegroupmembers";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this member permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

$(document).on('click', '.deleteusers', function(){
    var image_id = $(this).attr("id");
    var action = "deleteusers";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this user permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

// delete plan

$(document).on('click', '.deleteplan', function(){
    var image_id = $(this).attr("id");
    var action = "deleteplans";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this plan permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

// delete listing

$(document).on('click', '.deletelisting', function(){
    var image_id = $(this).attr("id");
    var action = "deletelistings";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this listing permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

// delete review
$(document).on('click', '.deleteReview', function(){
    var image_id = $(this).attr("id");
    var action = "deletereviews";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this review permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

//delete subcategory

$(document).on('click', '.deletesubcategory', function(){
    var image_id = $(this).attr("id");
    var action = "deletesubcategory";
    var siteUrl = $('#siteurl').val();
     var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this); // save reference to clicked button
    if(confirm("Are you sure you want to delete this subcategory permanently?")) {
        $.ajax({
            url: ajaxUrl ,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(data) {
                clickedBtn.parent().parent().remove(); // use saved reference to delete button
                alert(data);
            }  
        });   
    } else {  
        return false;
    }
});   

//delete category
$(document).on('click', '.deletecategory', function(){
    var image_id = $(this).attr("id");
    var action = "deletecategory";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if (confirm("Are you sure you want to delete this category permanently? All subcategories will also be deleted.")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            dataType: "json", // expect JSON from PHP
            data: { image_id: image_id, action: action },
            success: function(response) {
                if (response.status === "success") {
                    clickedBtn.closest('tr').remove(); // remove the row cleanly
                    alert(response.messages);
                } else {
                    alert("Error: " + response.messages);
                }
            },
            error: function(xhr, status, error) {
                alert("AJAX Error: " + error);
            }
        });
    } else {
        return false;
    }
});





//read notification

$(document).on('click', '.read-notifications', function() {
    var action = "markuserNotificationsRead";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var user_id = $(this).data('userid'); // ✅ Get buyerId here

    if (confirm("Are you sure you want to mark all notifications as read?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { action: action, user_id: user_id }, // ✅ Send user_id
            success: function(data) {
                alert(data); // Show response from PHP
                location.reload();
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            }
        });
    } else {
        return false;
    }
});


$(document).on('click', '.read-message', function(){
    var action = "markAllNotificationsRead";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if(confirm("Are you sure you want to mark all notifications as read?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { action: action },
            success: function(data) {
                alert(data); // show message from endpoint
                location.reload(); // ✅ Reload page after success
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            }
        });
    } else {
        return false;
    }
});

// approve withdrawal
$(document).on('click', '.approvewallet', function() {
    var image_id = $(this).attr("id");
    var action = "approvewallet";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";

    if (confirm("Are you sure you want to approve this withdrawal request?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(response) {
                try {
                    // ✅ Handle JSON if returned
                    var data = JSON.parse(response);
                    if (data.status === "success") {
                        alert(data.message);
                        location.reload(); // ✅ Reload page to refresh table
                    } else {
                        alert(data.message || "An error occurred.");
                    }
                } catch (e) {
                    // ✅ Handle plain text response
                    alert(response);
                    location.reload(); // ✅ Reload anyway after success message
                }
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            }
        });
    } else {
        return false;
    }
});


// approve manual payment
$(document).on('click', '.approveManualPayment', function() {
    var image_id = $(this).attr("id");
    var action = "approvemanual";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";

    if (confirm("Are you sure you want to approve this manual payment request?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { image_id: image_id, action: action },
            success: function(response) {
                try {
                    // ✅ Handle JSON if returned
                    var data = JSON.parse(response);
                    if (data.status === "success") {
                        alert(data.message);
                        location.reload(); // ✅ Reload page to refresh table
                    } else {
                        alert(data.message || "An error occurred.");
                    }
                } catch (e) {
                    // ✅ Handle plain text response
                    alert(response);
                    location.reload(); // ✅ Reload anyway after success message
                }
            },
            error: function(xhr, status, error) {
                alert("Error: " + error);
            }
        });
    } else {
        return false;
    }
});

$(document).off('click', '.confirmReject').on('click', '.confirmReject', function() {
    var image_id = $(this).data("id");
    var reason = $('#rejectReason' + image_id).val().trim();
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var action = "rejectmanual";

    if (reason === "") {
        alert("Please provide a reason for rejection.");
        return;
    }

    if (!confirm("Are you sure you want to reject this manual payment?")) return;

    $.ajax({
        url: ajaxUrl,
        method: "POST",
        data: { image_id: image_id, reason: reason, action: action },
        success: function(response) {
            let data;
            try {
                data = JSON.parse(response);
            } catch (e) {
                alert("Unexpected response from server.");
                return;
            }

            alert(data.message || "An error occurred.");
            if (data.status === "success") location.reload();
        },
        error: function(xhr, status, error) {
            alert("Error: " + error);
        }
    });
});



// reject manual payment



$(document).off('click', '.join-group').on('click', '.join-group', function(){
    var group_id = $(this).attr("id");
    var user_id = $(this).data("user");
    var action = "join-group";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if (confirm("Are you sure you want to join this group?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { group_id: group_id, user_id: user_id, action: action },
            success: function(data) {
                alert(data);
                clickedBtn.html('<i class="bi bi-hourglass-split"></i> Pending')
                          .removeClass('join-group')
                          .addClass('pending-group')
                          .prop('disabled', true);
            }
        });
    } else {
        return false;
    }
});


//request join group_access

$(document).off('click', '.request-join-group').on('click', '.request-join-group', function(){
    var group_id = $(this).attr("id");
    var user_id = $(this).data("user");
    var action = "request-join-group";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if (confirm("Are you sure you want to join this group?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { group_id: group_id, user_id: user_id, action: action },
            success: function(data) {
                alert(data);
                clickedBtn.html('<i class="bi bi-hourglass-split"></i> Pending')
                          .removeClass('request-join-group')
                          .addClass('pending-group')
                          .prop('disabled', true);
            }
        });
    } else {
        return false;
    }
});



//exit group_access
$(document).off('click', '.exit-group').on('click', '.exit-group', function(e){
    e.preventDefault();

    var group_id = $(this).attr("id");
    var user_id = $(this).data("user");
    var action = "exit-group";
    var siteUrl = $('#siteurl').val();
    var ajaxUrl = siteUrl + "script/admin.php";
    var clickedBtn = $(this);

    if (confirm("Are you sure you want to exit this group?")) {
        $.ajax({
            url: ajaxUrl,
            method: "POST",
            data: { group_id: group_id, user_id: user_id, action: action },
            success: function(data) {
                alert(data);
                clickedBtn.html('<i class="bi bi-person-plus"></i> Join Group')
                          .removeClass('exit-group')
                          .addClass('join-group');
            }
        });
    }
});


  

$(document).ready(function () {
    $('#adminForum').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post created successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


//group blog

$(document).ready(function () {
    $('#addgroupblog').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post created successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


//register buyer
$(document).ready(function(){
    $('.enrollment-buyer').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/register.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('.btn-enroll').html('Submitting...').prop('disabled', true);
                $('#messages').hide().removeClass('alert-success alert-danger');
            },
            success: function(response){
                if (response.status === 'success') {
                    // Redirect to login with success message
                    var msg = encodeURIComponent(response.messages || 'Registration successful!');
                    window.location.href = siteUrl + "login.php?success=" + msg;
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages || 'Submission failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error(xhr.responseText);
            },
            complete: function(){
                $('.btn-enroll').html('<i class="bi bi-check-circle me-2"></i> Submit').prop('disabled', false);
            }
        });
    });
});


//register therapist
$(document).ready(function(){
  $(document).off('submit', '.enrollment-therapist').on('submit', '.enrollment-therapist', function(event){
    event.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/register.php";

    const $submitBtn = $('.btn-enroll', form);

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      beforeSend: function(){
        $submitBtn.html('Submitting...').prop('disabled', true);
        $('#messages').hide().removeClass('alert-success alert-danger');
      },
      success: function(response){
        if (response.status === 'success') {
          const msg = encodeURIComponent(response.messages || 'Registration successful!');
          window.location.href = siteUrl + "login.php?success=" + msg;
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages || 'Submission failed. Please check your inputs.')
            .show();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        }
      },
      error: function(xhr){
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting the form. Please try again.')
          .show();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 500);
        console.error(xhr.responseText);
      },
      complete: function(){
        $submitBtn.html('<i class="bi bi-check-circle me-2"></i> Submit').prop('disabled', false);
      }
    });
  });
});


//register vendor
$(document).ready(function(){
    $('.enrollment-form').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/register.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn').text('Submitting...').prop('disabled', true);
                $('#messages').hide().removeClass('alert-success alert-danger');
            },
            success: function(response){
                if (response.status === 'success') {
                    // Redirect with success message in URL
                    var msg = encodeURIComponent(response.messages || 'Vendor application submitted successfully!');
                    window.location.href = siteUrl + "login.php?success=" + msg;
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages || 'Submission failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error(xhr.responseText);
            },
            complete: function(){
                $('#submitBtn').text('Submit').prop('disabled', false);
            }
        });
    });
});


//update plans

$(document).ready(function(){
    $('.updatePlanForm').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // ✅ Add the action value required by PHP
        formData.append('action', 'updateplan');

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/admin.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn')
                    .text('Updating...')
                    .prop('disabled', true);
                $('#messages')
                    .hide()
                    .removeClass('alert alert-success alert-danger');
            },
            success: function(response){
                if (response && response.status === 'success') {
                    $('#messages')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(response.messages || 'Vendor profile updated successfully!')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);

                    // ✅ Reload page after 2 seconds
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    $('#messages')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html((response && response.messages) || 'Update failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error('Raw response:', xhr.responseText);
            },
            complete: function(){
                $('#submitBtn')
                    .text('Submit')
                    .prop('disabled', false);
            }
        });
    });
});



//add plans
$(document).ready(function(){
    $('.addsubscription').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // ✅ Add the action value required by PHP
        formData.append('action', 'subscription_plans');

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/admin.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn')
                    .text('Updating...')
                    .prop('disabled', true);
                $('#messages')
                    .hide()
                    .removeClass('alert alert-success alert-danger');
            },
            success: function(response){
                if (response && response.status === 'success') {
                    $('#messages')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(response.messages || 'Vendor profile updated successfully!')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);

                    // ✅ Reload page after 2 seconds
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    $('#messages')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html((response && response.messages) || 'Update failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error('Raw response:', xhr.responseText);
            },
            complete: function(){
                $('#submitBtn')
                    .text('Submit')
                    .prop('disabled', false);
            }
        });
    });
});


//update vendor
$(document).ready(function(){
    $('.vendorenrollment-form').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // ✅ Add the action value required by PHP
        formData.append('action', 'edit_vendorsettings');

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/admin.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn')
                    .text('Updating...')
                    .prop('disabled', true);
                $('#messages')
                    .hide()
                    .removeClass('alert alert-success alert-danger');
            },
            success: function(response){
                if (response && response.status === 'success') {
                    $('#messages')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(response.messages || 'Vendor profile updated successfully!')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);

                    // ✅ Reload page after 2 seconds
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    $('#messages')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html((response && response.messages) || 'Update failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error('Raw response:', xhr.responseText);
            },
            complete: function(){
                $('#submitBtn')
                    .text('Submit')
                    .prop('disabled', false);
            }
        });
    });
});


//user wallet
$(document).ready(function(){
    $('.vendorwallet').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // ✅ Add the action value required by PHP
        formData.append('action', 'withdraw');

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/admin.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn')
                    .text('Updating...')
                    .prop('disabled', true);
                $('#messages')
                    .hide()
                    .removeClass('alert alert-success alert-danger');
            },
            success: function(response){
                if (response && response.status === 'success') {
                    $('#messages')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(response.messages || 'Vendor profile updated successfully!')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);

                    // ✅ Reload page after 2 seconds
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    $('#messages')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html((response && response.messages) || 'Update failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error('Raw response:', xhr.responseText);
            },
            complete: function(){
                $('#submitBtn')
                    .text('Submit')
                    .prop('disabled', false);
            }
        });
    });
});

//update vendor
$(document).ready(function(){
    $('.updateenrollment-form').submit(function(event){
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        // ✅ Add the action value required by PHP
        formData.append('action', 'edit_adminvendor');

        // Get site URL from hidden input
        var siteUrl = $('#siteurl').val();
        var ajaxUrl = siteUrl + "script/admin.php"; // endpoint

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(){
                $('#submitBtn')
                    .text('Updating...')
                    .prop('disabled', true);
                $('#messages')
                    .hide()
                    .removeClass('alert alert-success alert-danger');
            },
            success: function(response){
                if (response && response.status === 'success') {
                    $('#messages')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html(response.messages || 'Vendor profile updated successfully!')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);

                    // ✅ Reload page after 2 seconds
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    $('#messages')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html((response && response.messages) || 'Update failed. Please check your inputs.')
                        .show();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr){
                $('#messages')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting the form. Please try again.')
                    .show();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 500);

                console.error('Raw response:', xhr.responseText);
            },
            complete: function(){
                $('#submitBtn')
                    .text('Submit')
                    .prop('disabled', false);
            }
        });
    });
});

//forgotten-password

// Password reset link
$('#passwordforgotten').submit(function(event) {
    event.preventDefault();
    $('#submitBtn').prop('disabled', true);

    // Clear previous error messages
    $('#errorMessages').empty();

    var formData = new FormData(this);
    var siteUrl = $('#siteurl').val(); // hidden input holding your base URL
    var ajaxUrl = siteUrl + "script/register.php"; // build full path (fixed typo)

    $.ajax({
        type: 'POST',
        url: ajaxUrl, // ✅ fixed typo (was 'ajaxurl' before)
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            // Optional: show loading indicator or message
            $('#errorMessages').html('<span style="color: blue;">Processing...</span>').show();
        },
        success: function(response) {
            if (response.status === 'success') {
                alert('Password reset link has been sent to your email.');
                $('#passwordforgotten')[0].reset(); // clear form
            } else {
                $('#errorMessages').html('<span style="color: red;">' + response.messages + '</span>').show();
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#errorMessages').html('<span style="color: red;">An error occurred while resetting. Please try again.</span>').show();
        },
        complete: function() {
            $('#submitBtn').prop('disabled', false);
        }
    });
});


//add blog
$(document).ready(function () {
    // Ensure this handler only attaches once
    $('#addForum').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/user.php";

        // Prevent double click
        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post created successfully!");
                        location.reload();
                    }, 500);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Create');
            }
        });
    });
});



$(document).ready(function () {
    // Ensure this handler only attaches once
    $('#addvendorblog').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        // Prevent double click
        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post created successfully!");
                        location.reload();
                    }, 500);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Create');
            }
        });
    });
});

//update admin 
$(document).ready(function () {
    $('#updateadminsettings').off('submit').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const siteUrl = $('#siteurl').val();
        const ajaxUrl = siteUrl + "script/admin.php";

        $('#submitBtn').prop('disabled', true).text('Submitting...');
        $('#messages').removeClass('alert alert-success alert-danger').hide();

        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                console.log("Response:", response);
                if (response.status === 'success') {
                    $('#messages')
                        .addClass('alert alert-success')
                        .html(response.messages)
                        .fadeIn();

                    form.reset();

                    setTimeout(() => {
                        alert(response.messages || "Forum post updated successfully!");
                        location.reload();
                    }, 1000);
                } else {
                    $('#messages')
                        .addClass('alert alert-danger')
                        .html(response.messages)
                        .fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#messages').offset().top - 100
                    }, 600);
                }
            },
            error: function (xhr) {
                console.log("Raw Response:", xhr.responseText);
                $('#messages')
                    .addClass('alert alert-danger')
                    .text('An error occurred while submitting. Please try again.')
                    .fadeIn();

                $('html, body').animate({
                    scrollTop: $('#messages').offset().top - 100
                }, 600);
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Submit Blog');
            }
        });
    });
});


//add questions
$(document).off('submit', '#addQuestions').on('submit', '#addQuestions', function(e) {
  e.preventDefault();

  // Prevent multiple simultaneous submissions
  if ($(this).data('submitted') === true) {
    return false;
  }
  $(this).data('submitted', true);

  var formData = new FormData(this);
  var siteUrl = $('#siteurl').val(); // hidden input for base URL
  formData.append('action', 'createQuestion');

  var $submitBtn = $(this).find('button[type="submit"]');
  $submitBtn.prop('disabled', true).text('Submitting...');

  $.ajax({
    url: siteUrl + 'script/user.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('#messages').html('<div class="alert alert-info">Submitting your question...</div>');
    },
    success: function(response) {
      try {
        response = typeof response === 'string' ? JSON.parse(response) : response;
      } catch (e) {
        $('#messages').html('<div class="alert alert-danger">Invalid server response.</div>');
        return;
      }

      if (response.status === 'success') {
        $('#messages').html('<div class="alert alert-success">' + response.messages + '</div>');
        $('#addQuestions')[0].reset();

        // ✅ Reload after 2 seconds
        setTimeout(function() {
          location.reload();
        }, 2000);
      } else {
        $('#messages').html('<div class="alert alert-danger">' + response.messages + '</div>');
      }
    },
    error: function() {
      $('#messages').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
    },
    complete: function() {
      $submitBtn.prop('disabled', false).text('Submit Question');
      $('#addQuestions').data('submitted', false);
    }
  });
});



//booking form
$(document).off('submit', '#serviceBookingForm').on('submit', '#serviceBookingForm', function(e) {
  e.preventDefault();

  // Prevent multiple simultaneous submissions
  if ($(this).data('submitted') === true) {
    return false;
  }
  $(this).data('submitted', true);

  var formData = new FormData(this);
  var siteUrl = $('#siteurl').val(); // hidden input for base URL
  formData.append('action', 'book-service');

  var $submitBtn = $(this).find('button[type="submit"]');
  $submitBtn.prop('disabled', true).text('Submitting...');

  $.ajax({
    url: siteUrl + 'script/user.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('html, body').animate({ scrollTop: 0 }, 'slow');
      $('#messages').html('<div class="alert alert-info">Submitting your booking...</div>');
    },
    success: function(response) {
      try {
        response = typeof response === 'string' ? JSON.parse(response) : response;
      } catch (e) {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        $('#messages').html('<div class="alert alert-danger">Invalid server response.</div>');
        return;
      }

      $('html, body').animate({ scrollTop: 0 }, 'slow');

      if (response.status === 'success') {
        $('#messages').html('<div class="alert alert-success">' + response.messages + '</div>');
        $('#serviceBookingForm')[0].reset();

        // ✅ Reload after 2 seconds
        setTimeout(function() {
          location.reload();
        }, 2000);
      } else {
        $('#messages').html('<div class="alert alert-danger">' + response.messages + '</div>');
      }
    },
    error: function() {
      $('html, body').animate({ scrollTop: 0 }, 'slow');
      $('#messages').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
    },
    complete: function() {
      $submitBtn.prop('disabled', false).text('Submit Bookings');
      $('#serviceBookingForm').data('submitted', false);
    }
  });
});


//update questions
$(document).off('submit', '#editQuestions').on('submit', '#editQuestions', function(e) {
  e.preventDefault();

  // Prevent multiple simultaneous submissions
  if ($(this).data('submitted') === true) {
    return false;
  }
  $(this).data('submitted', true);

  var formData = new FormData(this);
  var siteUrl = $('#siteurl').val(); // hidden input for base URL
  formData.append('action', 'updateQuestion');

  var $submitBtn = $(this).find('button[type="submit"]');
  $submitBtn.prop('disabled', true).text('Submitting...');

  $.ajax({
    url: siteUrl + 'script/user.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: function() {
      $('html, body').animate({ scrollTop: 0 }, 'slow');
      $('#messages').html('<div class="alert alert-info">Submitting your question...</div>');
    },
    success: function(response) {
      try {
        response = typeof response === 'string' ? JSON.parse(response) : response;
      } catch (e) {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        $('#messages').html('<div class="alert alert-danger">Invalid server response.</div>');
        return;
      }

      $('html, body').animate({ scrollTop: 0 }, 'slow');

      if (response.status === 'success') {
        $('#messages').html('<div class="alert alert-success">' + response.messages + '</div>');
        $('#editQuestions')[0].reset();

        // ✅ Reload after 2 seconds
        setTimeout(function() {
          location.reload();
        }, 2000);
      } else {
        $('#messages').html('<div class="alert alert-danger">' + response.messages + '</div>');
      }
    },
    error: function() {
      $('html, body').animate({ scrollTop: 0 }, 'slow');
      $('#messages').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
    },
    complete: function() {
      $submitBtn.prop('disabled', false).text('Submit Question');
      $('#editQuestions').data('submitted', false);
    }
  });
});

// respond reviews 
$(document).ready(function() {
    // Declare URLs
    const siteUrl = $('#siteurl').val(); // Hidden input holding your site URL
    const ajaxUrl = siteUrl + "script/admin.php"; // Base admin handler

    $(".save-response").on("click", function() {
        const reviewId = $(this).data("id");
        const responseText = $("#response" + reviewId).val().trim();

        if (responseText === "") {
            alert("Please write a response before submitting.");
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: {
                action: "respond_review", // action declared separately
                review_id: reviewId,
                response: responseText
            },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    alert("Your response has been posted successfully!");
                    $("#respondModal" + reviewId).modal("hide");
                    location.reload(); // Refresh the table or page
                } else {
                    alert(res.message || "Something went wrong. Please try again.");
                }
            },
            error: function() {
                alert("Unable to save response. Please try again later.");
            }
        });
    });
});

//change password
$(document).ready(function () {
  $('#changePasswordForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    $('#changePasswordButton').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();

          // Logout after 1 second
          setTimeout(() => {
            window.location.href = siteUrl + 'logout.php';
          }, 1000);

        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#changePasswordButton').prop('disabled', false).text('Change Password');
      }
    });
  });
});


//admin listing edit  


$(document).ready(function () {
  // Attach submit handler once
  $('#admineditlistingForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitListing').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Listing submitted successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitListing').prop('disabled', false).text('Submit Listing');
      }
    });
  });
});

//add subcategory

$(document).ready(function () {
  // Attach submit handler once
  $('#addsubcategory').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitcategory').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Category submitted successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitcategory').prop('disabled', false).text('Submit Category');
      }
    });
  });
});


//update category
$(document).ready(function () {
  // Attach submit handler once
  $('#admineditcategoryForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitcategory').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Category updated successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitcategory').prop('disabled', false).text('Submit Category');
      }
    });
  });
});


//update subcategory
$(document).ready(function () {
  // Attach submit handler once
  $('#adminEditSubCategoryForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitcategory').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Subcategory updated successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitcategory').prop('disabled', false).text('Submit Category');
      }
    });
  });
});

//add category

$(document).ready(function () {
  // Attach submit handler once
  $('#addcategory').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitcategory').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Category submitted successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitcategory').prop('disabled', false).text('Submit Category');
      }
    });
  });
});

// edit listing

$(document).ready(function () {
  // Attach submit handler once
  $('#editlistingForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitListing').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Listing submitted successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitListing').prop('disabled', false).text('Submit Listing');
      }
    });
  });
});


// admin listings
$(document).ready(function () {
  // Attach submit handler once
  $('#adminlistingForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitListing').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Listing updated successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitListing').prop('disabled', false).text('Submit Listing');
      }
    });
  });
});






//add listing
$(document).ready(function () {
  // Attach submit handler once
  $('#listingForm').off('submit').on('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const siteUrl = $('#siteurl').val();
    const ajaxUrl = siteUrl + "script/admin.php";

    // Prevent double click
    $('#submitListing').prop('disabled', true).text('Submitting...');
    $('#messages').removeClass('alert alert-success alert-danger').hide();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          $('#messages')
            .addClass('alert alert-success')
            .html(response.messages)
            .fadeIn();
          form.reset();
          setTimeout(() => {
            alert(response.messages || "Listing submitted successfully!");
            location.reload();
          }, 800);
        } else {
          $('#messages')
            .addClass('alert alert-danger')
            .html(response.messages)
            .fadeIn();
          $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        $('#messages')
          .addClass('alert alert-danger')
          .text('An error occurred while submitting. Please try again.')
          .fadeIn();
        $('html, body').animate({ scrollTop: $('#messages').offset().top - 100 }, 600);
      },
      complete: function () {
        $('#submitListing').prop('disabled', false).text('Submit Listing');
      }
    });
  });
});






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


// place before </body>
(function($){
  $(document).ready(function(){
    var $stars = $('.star-rating .star');
    var $ratingInput = $('#ratingInput');

    function highlight(value){
      $stars.each(function(){
        var v = parseInt($(this).data('value'), 10);
        $(this).toggleClass('filled', v <= value);
        $(this).text(v <= value ? '★' : '☆');
      });
    }

    // hover preview
    $stars.on('mouseenter', function(){
      highlight(parseInt($(this).data('value'), 10));
    }).on('mouseleave', function(){
      highlight(parseInt($ratingInput.val(), 10) || 0);
    });

    // click to set rating
    $stars.on('click', function(){
      var val = parseInt($(this).data('value'), 10);
      $ratingInput.val(val);
      highlight(val);
    });

    // keyboard support
    $stars.on('keydown', function(e){
      var current = parseInt($ratingInput.val(), 10) || 0;
      if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
        var next = Math.min(5, current + 1);
        $ratingInput.val(next); highlight(next);
      } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
        var prev = Math.max(0, current - 1);
        $ratingInput.val(prev); highlight(prev);
      } else if (e.key === 'Enter' || e.key === ' ') {
        var val = parseInt($(this).data('value'), 10);
        $ratingInput.val(val); highlight(val);
        e.preventDefault();
      }
    });

    // initialize from any pre-set value
    highlight(parseInt($ratingInput.val(), 10) || 0);
  });
})(jQuery);


$(document).ready(function () {
  $('#bookService').click(function () {
    var siteurl = $('#siteurl').val();
    var listing_id = $('#listing_id').val();
    var user_id = $('#user_id').val();
    var order_id = $('#order_id').val();
    var selectedVariation = $('#variationSelect').val();
    var price = parseFloat($('#variationSelect option:selected').data('price')) || parseFloat($('#single-price').val()) || 0;
    var quantity = parseInt($('#quantity').val()) || 1;

    // 🔒 Require login
    if (!user_id) {
      window.location.href = siteurl + 'login';
      return;
    }

    // 🔹 Ensure variation (if applicable)
    if ($('#variationSelect').length && !selectedVariation) {
      showToast('Please select a variation.');
      return;
    }

    // 🧭 Redirect with parameters
    const params = new URLSearchParams({
      listing_id: listing_id,
      user_id: user_id,
      variation: selectedVariation,
      order_id: order_id,
      price: price
    }).toString();

    window.location.href = siteurl + 'book-service.php?' + params;
  });
});




$(document).ready(function () {

  // ✅ Quantity buttons
  $('.increase').click(function () {
    let q = parseInt($('#quantity').val()) || 1;
    let limitedSlot = parseInt($('#limited-slot').val()) || 0;

    // Prevent exceeding available quantity
    if (limitedSlot > 0 && q >= limitedSlot) {
      showToast(`Only ${limitedSlot} item${limitedSlot > 1 ? 's' : ''} available.`);
      return;
    }

    $('#quantity').val(q + 1);
  });

  $('.decrease').click(function () {
    let q = parseInt($('#quantity').val()) || 1;
    if (q > 1) $('#quantity').val(q - 1);
  });

  // ✅ Add to Cart
  $('#addCart').click(function () {
    var listing_id = $('#listing_id').val();
    var user_id = $('#user_id').val();
    var order_id = $('#order_id').val();
    var siteurl = $('#siteurl').val();
    var quantity = parseInt($('#quantity').val()) || 1;
    var selectedVariation = $('#variationSelect').val();
    var limitedSlot = parseInt($('#limited-slot').val()) || 0;

    // ✅ Get price (variation first, then base)
    var price = parseFloat($('#variationSelect option:selected').data('price')) || 0;
    if (!price || isNaN(price)) {
      price = parseFloat($('#single-price').val()) || 0;
    }

    // 🔒 Redirect if user not logged in
    if (!user_id) {
      window.location.href = siteurl + 'login';
      return;
    }

    // 🔹 Ensure variation is selected (if exists)
    if ($('#variationSelect').length && !selectedVariation) {
      showToast('Please select a variation.');
      return;
    }

    // 🧩 Check limited slot availability
    if (limitedSlot > 0 && quantity > limitedSlot) {
      showToast(`Only ${limitedSlot} item${limitedSlot > 1 ? 's' : ''} available.`);
      return;
    }

    // 🧾 Send data to server
    $.ajax({
      url: siteurl + 'script/user.php',
      type: 'POST',
      data: {
        action: 'addtocart',
        listing_id: listing_id,
        user_id: user_id,
        order_id: order_id,
        quantity: quantity,
        variation: selectedVariation,
        price: price
      },
      success: function (response) {
        let data;
        try {
          data = typeof response === 'string' ? JSON.parse(response) : response;
        } catch (e) {
          showToast('Unexpected server response.');
          return;
        }

        // 🧩 Handle response
        if (data.status === 'error') {
          showToast(data.message || 'Error adding to cart');
        } else {
          showToast(data.message || 'Item added to cart successfully');
        }

        // 🛒 Update cart counter dynamically
        if (data.cartCount) {
          updateCartCount(data.cartCount);
        }
      },
      error: function () {
        showToast('Network error. Please try again.');
      }
    });
  });

});

$(document).on('click', '.wishlist-btn', function (e) {
    e.preventDefault();

    var button = $(this);
    var icon = button.find('i');
    var listing_id = button.data('product-id');
    var user_id = $('#user_id').val();
    var siteurl = $('#siteurl').val();

    if (!user_id || user_id === "0") {
        window.location.href = siteurl + 'login';
        return;
    }

    $.ajax({
        url: siteurl + 'script/user.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'addtowishlist',
            listing_id: listing_id,
            user_id: user_id
        },
        success: function(response) {
            var data;
            try {
                data = typeof response === 'string' ? JSON.parse(response) : response;
            } catch (e) {
                showToast('Unexpected server response.');
                return;
            }

            // Toggle heart icon
            if (data.status === 'success') {
                icon.removeClass('bi-heart').addClass('bi-heart-fill text-red-500');
                button.addClass('added').attr('title', 'Remove from Wishlist');
            } else if (data.status === 'removed') {
                icon.removeClass('bi-heart-fill text-red-500').addClass('bi-heart');
                button.removeClass('added').attr('title', 'Add to Wishlist');
            }

            showToast(data.message);

            // 🔹 Update wishlist count instantly
            if (typeof data.wishlist_count !== "undefined") {
                updateWishlistCount(data.wishlist_count);
            }
        },
        error: function () {
            showToast('Network error. Please try again.');
        }
    });
});


$(document).on("click", ".bio-btn-toggle", function(e) {
    e.preventDefault();

    let wrapper = $(this).closest(".bio-wrapper");
    let shortText = wrapper.find(".bio-text-short");
    let fullText = wrapper.find(".bio-text-full");

    if (fullText.hasClass("d-none")) {
        fullText.removeClass("d-none");
        shortText.addClass("d-none");
        $(this).text("Read Less");
    } else {
        fullText.addClass("d-none");
        shortText.removeClass("d-none");
        $(this).text("Read More");
    }
});


$(document).on('click', '.view-details', function() {
    $('#modal-source').text($(this).data('source'));
    $('#modal-source-name').text($(this).data('source-name'));
    $('#modal-source-amount').text($(this).data('source-amount'));
    $('#modal-earned').text($(this).data('earned'));
    $('#modal-type').text($(this).data('type'));
    $('#modal-date').text($(this).data('date'));
    $('#viewDetailsModal').modal('show');
});
