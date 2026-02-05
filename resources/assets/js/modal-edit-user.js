/**
 * Edit User
 */
console.log('modal-edit-user.js loaded');

'use strict';

$(function () {
  /* =========================
   * SELECT2 INIT
   * ========================= */
  $('.select2').each(function () {
    const $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Select value',
      dropdownParent: $this.parent()
    });
  });

  /* =========================
   * EDIT MODAL CLICK
   * ========================= */
  $(document).on('click', '.edit-product', function () {
    const id = $(this).data('id');

    $.get(`/products/${id}/edit`, function (res) {

      $('#edit_product_id').val(id);
      $('#edit_name').val(res.product_name);
      $('#edit_category').val(res.detail.category).trigger('change');
      $('#edit_price').val(res.detail.base_price);
      $('#edit_discount_price').val(res.detail.discounted_price);
      $('#edit_stock').val(res.detail.stock);
      $('#edit_status').val(res.detail.status).trigger('change');

      // IMAGE PREVIEW
      if (res.image) {
        $('#edit_image_preview').attr('src', res.image).show();
      } else {
        $('#edit_image_preview').hide();
      }

      bootstrap.Modal
        .getOrCreateInstance(document.getElementById('editProductModal'))
        .show();
    });
  });

  /* =========================
   * IMAGE PREVIEW ON CHANGE
   * ========================= */
  $('#edit_image').on('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
      $('#edit_image_preview').attr('src', e.target.result).show();
    };
    reader.readAsDataURL(file);
  });
});

/* =========================
 * DOM READY (Cleave + Validation + Submit)
 * ========================= */
document.addEventListener('DOMContentLoaded', function () {

  // Cleave inputs
  const modalEditUserTaxID = document.querySelector('.modal-edit-tax-id');
  const modalEditUserPhone = document.querySelector('.phone-number-mask');

  if (modalEditUserTaxID) {
    new Cleave(modalEditUserTaxID, {
      prefix: 'TIN',
      blocks: [3, 3, 3, 4],
      uppercase: true
    });
  }

  if (modalEditUserPhone) {
    new Cleave(modalEditUserPhone, {
      phone: true,
      phoneRegionCode: 'US'
    });
  }

  /* =========================
   * FORM VALIDATION
   * ========================= */
  const form = document.getElementById('editProductForm');

  const fv = FormValidation.formValidation(form, {
    fields: {
      modalEditUserFirstName: {
        validators: {
          notEmpty: { message: 'Please enter your first name' }
        }
      },
      modalEditUserLastName: {
        validators: {
          notEmpty: { message: 'Please enter your last name' }
        }
      },
      modalEditUserName: {
        validators: {
          notEmpty: { message: 'Please enter your username' },
          stringLength: {
            min: 6,
            max: 30,
            message: 'Username must be 6–30 characters'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: '.col-12'
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  /* =========================
   * SUBMIT → UPDATE API
   * ========================= */
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    fv.validate().then(function (status) {
      if (status !== 'Valid') return;

      const id = $('#edit_product_id').val();
      formData.append('_method', 'PUT');

$.ajax({
  url: `/products/${id}`,
  type: 'POST',

        url: `/products/${id}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function () {
          bootstrap.Modal
            .getOrCreateInstance(document.getElementById('editProductModal'))
            .hide();

          $('.datatables-ajax').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
          console.error(xhr.responseText);
        }
      });
    });
  });
});
