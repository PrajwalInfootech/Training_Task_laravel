<!-- Edit User Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2">Edit Product Information</h4>
        </div>
        <form id="editProductForm" enctype="multipart/form-data"class="needs-validation" novalidate>
<input type="hidden" id="edit_product_id" name="id">

  <div class="row g-4">
    <div class="col-md-6">
      <label class="form-label">Product Name<span class="text-danger">*</span></label>
<input type="text" id="edit_name" name="name" class="form-control" required>
         <div class="invalid-feedback">
  Product name is required
</div>
    </div>

   <div class="col-md-6">
  <label class="form-label">Category<span class="text-danger">*</span></label>
  <select id="edit_category" name="category" class="form-select" required>
    <option value="">Select Category</option>
    <option value="men">Men</option>
    <option value="women">Women</option>
    <option value="kids">Kids</option>
  </select>
</div>


    <div class="col-md-4">
      <label class="form-label">Base Price<span class="text-danger">*</span></label>
<input type="number" id="edit_price" name="price" class="form-control" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Discounted Price<span class="text-danger">*</span></label>
<input type="number" id="edit_discount_price" name="discount_price" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label">Stock<span class="text-danger">*</span></label>
<input type="number" id="edit_stock" name="stock" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Status<span class="text-danger">*</span></label>
<select id="edit_status" name="status" class="form-select">
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
    </div>

  

<div class="col-md-6">
  <label class="form-label">Change Image<span class="text-danger">*</span></label>
  <input type="file" id="edit_image"          name ="image"
 class="form-control">
</div>
 <div class="col-md-6">
  <label class="form-label">Current Image</label>
  <div class="mb-2">
    <img id="edit_image_preview"
         src=""
         alt="Product Image"
         class="img-thumbnail"
         style="max-height: 120px; display: none;">
  </div>
</div>

    <div class="col-12 text-center">
      <button type="submit" class="btn btn-primary">Update Product</button>
    </div>
  </div>
</form>

      </div>
    </div>
  </div>
</div>
<!--/ Edit User Modal -->
