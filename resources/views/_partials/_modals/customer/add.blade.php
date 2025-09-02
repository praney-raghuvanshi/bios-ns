<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Customer</h3>
                </div>
                <form id="addCustomerForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.customer.store') }}">
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="code">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Status --</option>
                            <option value="1" @if(old('status')===1) selected @endif>Active</option>
                            <option value="0" @if(old('status')===0) selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="type">Type</label>
                        <select name="type" class="select2 form-select" aria-label="type">
                            <option value="Express" @if(old('type', $customer->type) === "Express") selected
                                @endif>Express</option>
                            <option value="Cargo" @if(old('type', $customer->type) === "Cargo") selected @endif>Cargo
                            </option>
                        </select>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Add Customer Modal -->