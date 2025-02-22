<!-- Edit Product Modal -->

<form id="editProductForm" class="row g-3" method="POST" action="{{ route('maintenance.product.update', $product) }}">
    @csrf
    <div class="col-12 col-md-6">
        <label class="form-label" for="code">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $product->code) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $product->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $product->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Product Modal -->