<!-- Manage Customer Product(s) Modal -->
<div class="modal fade" id="manageProductsForCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Manage Products for Customer : <strong>{{ $customer->name }}</strong></h3>
                </div>
                <form id="manageCustomerProductsForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.customer.manage-products', $customer) }}">
                    @csrf
                    <div class="col-12">
                        <select name="products[]" class="select2 form-select" multiple aria-label="products">
                            @foreach ($products as $product)
                            <option value="{{$product->id}}" @if(in_array($product->id, $customerProductIds)) selected
                                @endif>{{ $product->name }}</option>
                            @endforeach
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