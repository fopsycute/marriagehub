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
                    action: "subspecializationlists",
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


//edit group
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
