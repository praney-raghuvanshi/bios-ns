<!-- Manage Group Role(s) Modal -->
<div class="modal fade" id="manageRolesForGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Manage Roles for Group : <strong>{{ $group->name }}</strong></h3>
                </div>
                <form id="manageGroupRolesForm" class="row g-3" method="POST"
                    action="{{ route('administration.group.manage-roles', $group) }}">
                    @csrf

                    <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Dashboard</h6>

                        <!-- Dashboard Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalDashboardRole" class="form-label mb-0">Dashboard</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="{{$allRoles['dashboard-view']}}" @if(in_array('dashboard-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Search</h6>

                        <!-- Search Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalSearchRole" class="form-label mb-0">Search</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['search-view']}}" @if(in_array('search-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Administration</h6>

                        <!-- Administration Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminRole" class="form-label mb-0">Administration</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-view']}}"
                                        @if(in_array('administration-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Groups Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminGroupsRole" class="form-label mb-0">Groups</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-groups-view']}}"
                                        @if(in_array('administration-groups-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-groups-user']}}"
                                        @if(in_array('administration-groups-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-groups-manager']}}"
                                        @if(in_array('administration-groups-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-groups-admin']}}"
                                        @if(in_array('administration-groups-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Roles Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminRolesRole" class="form-label mb-0">Roles</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-roles-view']}}"
                                        @if(in_array('administration-roles-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-roles-user']}}"
                                        @if(in_array('administration-roles-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-roles-manager']}}"
                                        @if(in_array('administration-roles-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-roles-admin']}}"
                                        @if(in_array('administration-roles-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Users Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminUsersRole" class="form-label mb-0">Users</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-users-view']}}"
                                        @if(in_array('administration-users-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-users-user']}}"
                                        @if(in_array('administration-users-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-users-manager']}}"
                                        @if(in_array('administration-users-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-users-admin']}}"
                                        @if(in_array('administration-users-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- BIOS Audit Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminBiosAuditRole" class="form-label mb-0">BIOS Audit</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-bios-audit-view']}}"
                                        @if(in_array('administration-bios-audit-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Products Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminProductsRole" class="form-label mb-0">Products</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-products-view']}}"
                                        @if(in_array('administration-products-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-products-user']}}"
                                        @if(in_array('administration-products-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-products-manager']}}"
                                        @if(in_array('administration-products-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-products-admin']}}"
                                        @if(in_array('administration-products-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Airports Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminAirportsRole" class="form-label mb-0">Airports</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-airports-view']}}"
                                        @if(in_array('administration-airports-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-airports-user']}}"
                                        @if(in_array('administration-airports-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-airports-manager']}}"
                                        @if(in_array('administration-airports-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-airports-admin']}}"
                                        @if(in_array('administration-airports-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Airlines Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminAirlinesRole" class="form-label mb-0">Airlines</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-airlines-view']}}"
                                        @if(in_array('administration-airlines-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-airlines-user']}}"
                                        @if(in_array('administration-airlines-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-airlines-manager']}}"
                                        @if(in_array('administration-airlines-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-airlines-admin']}}"
                                        @if(in_array('administration-airlines-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Customers Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminCustomersRole" class="form-label mb-0">Customers</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-customers-view']}}"
                                        @if(in_array('administration-customers-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-customers-user']}}"
                                        @if(in_array('administration-customers-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-customers-manager']}}"
                                        @if(in_array('administration-customers-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-customers-admin']}}"
                                        @if(in_array('administration-customers-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Sheds Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminShedsRole" class="form-label mb-0">Sheds</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-sheds-view']}}"
                                        @if(in_array('administration-sheds-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-sheds-user']}}"
                                        @if(in_array('administration-sheds-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-sheds-manager']}}"
                                        @if(in_array('administration-sheds-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-sheds-admin']}}"
                                        @if(in_array('administration-sheds-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Consol Consignees Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminShedsRole" class="form-label mb-0">Consol Consignees</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-consol-consignees-view']}}"
                                        @if(in_array('administration-consol-consignees-view', $groupRoles)) selected
                                        @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-consol-consignees-user']}}"
                                        @if(in_array('administration-consol-consignees-user', $groupRoles)) selected
                                        @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-consol-consignees-manager']}}"
                                        @if(in_array('administration-consol-consignees-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-consol-consignees-admin']}}"
                                        @if(in_array('administration-consol-consignees-admin', $groupRoles)) selected
                                        @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                        <!-- Special Packing Codes Section -->
                        {{-- <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAdminShedsRole" class="form-label mb-0">Special Packing Codes</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['administration-special-packing-codes-view']}}"
                                        @if(in_array('administration-special-packing-codes-view', $groupRoles)) selected
                                        @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['administration-special-packing-codes-user']}}"
                                        @if(in_array('administration-special-packing-codes-user', $groupRoles)) selected
                                        @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['administration-special-packing-codes-manager']}}"
                                        @if(in_array('administration-special-packing-codes-manager', $groupRoles))
                                        selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['administration-special-packing-codes-admin']}}"
                                        @if(in_array('administration-special-packing-codes-admin', $groupRoles))
                                        selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div> --}}

                    </div>

                    {{-- <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Operations</h6>

                        <!-- Operations Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsRole" class="form-label mb-0">Operations</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-view']}}" @if(in_array('operations-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- AWB Generator Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsAwbGeneratorRole" class="form-label mb-0">AWB
                                    Generator</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-awb-generator-view']}}"
                                        @if(in_array('operations-awb-generator-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-awb-generator-user']}}"
                                        @if(in_array('operations-awb-generator-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-awb-generator-manager']}}"
                                        @if(in_array('operations-awb-generator-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-awb-generator-admin']}}"
                                        @if(in_array('operations-awb-generator-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- AWB Allocations Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsAwbAllocationsRole" class="form-label mb-0">AWB
                                    Allocations</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-awb-allocations-view']}}"
                                        @if(in_array('operations-awb-allocations-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-awb-allocations-user']}}"
                                        @if(in_array('operations-awb-allocations-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-awb-allocations-manager']}}"
                                        @if(in_array('operations-awb-allocations-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-awb-allocations-admin']}}"
                                        @if(in_array('operations-awb-allocations-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- AWB Stocks Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsAwbStocksRole" class="form-label mb-0">AWB
                                    Stocks</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-awb-stocks-view']}}"
                                        @if(in_array('operations-awb-stocks-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-awb-stocks-user']}}"
                                        @if(in_array('operations-awb-stocks-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-awb-stocks-manager']}}"
                                        @if(in_array('operations-awb-stocks-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-awb-stocks-admin']}}"
                                        @if(in_array('operations-awb-stocks-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Handling Log Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsHandlingLogRole" class="form-label mb-0">Handling Log</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-handling-log-view']}}"
                                        @if(in_array('operations-handling-log-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-handling-log-user']}}"
                                        @if(in_array('operations-handling-log-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-handling-log-manager']}}"
                                        @if(in_array('operations-handling-log-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-handling-log-admin']}}"
                                        @if(in_array('operations-handling-log-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipments Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsShipmentsRole" class="form-label mb-0">Shipments</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-shipments-view']}}"
                                        @if(in_array('operations-shipments-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-shipments-user']}}"
                                        @if(in_array('operations-shipments-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-shipments-manager']}}"
                                        @if(in_array('operations-shipments-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-shipments-admin']}}"
                                        @if(in_array('operations-shipments-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Pre Bookings Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsPreBookingsRole" class="form-label mb-0">Pre Bookings</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-pre-bookings-view']}}"
                                        @if(in_array('operations-pre-bookings-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-pre-bookings-user']}}"
                                        @if(in_array('operations-pre-bookings-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-pre-bookings-manager']}}"
                                        @if(in_array('operations-pre-bookings-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-pre-bookings-admin']}}"
                                        @if(in_array('operations-pre-bookings-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Pre Alert Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsPreAlertRole" class="form-label mb-0">Pre Alert</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-pre-alert-view']}}"
                                        @if(in_array('operations-pre-alert-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-pre-alert-user']}}"
                                        @if(in_array('operations-pre-alert-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-pre-alert-manager']}}"
                                        @if(in_array('operations-pre-alert-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-pre-alert-admin']}}"
                                        @if(in_array('operations-pre-alert-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Post Flight Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsPostFlightRole" class="form-label mb-0">Post Flight</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-post-flight-view']}}"
                                        @if(in_array('operations-post-flight-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-post-flight-user']}}"
                                        @if(in_array('operations-post-flight-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-post-flight-manager']}}"
                                        @if(in_array('operations-post-flight-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-post-flight-admin']}}"
                                        @if(in_array('operations-post-flight-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipment Consols Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalOperationsShipmentConsolsRole" class="form-label mb-0">Shipment
                                    Consols</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['operations-shipment-consols-view']}}"
                                        @if(in_array('operations-shipment-consols-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['operations-shipment-consols-user']}}"
                                        @if(in_array('operations-shipment-consols-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['operations-shipment-consols-manager']}}"
                                        @if(in_array('operations-shipment-consols-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['operations-shipment-consols-admin']}}"
                                        @if(in_array('operations-shipment-consols-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div> --}}

                    {{-- <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Accounts</h6>

                        <!-- Accounts Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsRole" class="form-label mb-0">Accounts</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-view']}}" @if(in_array('accounts-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- CASS Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsCassRole" class="form-label mb-0">CASS</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-cass-view']}}"
                                        @if(in_array('accounts-cass-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['accounts-cass-user']}}"
                                        @if(in_array('accounts-cass-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['accounts-cass-manager']}}"
                                        @if(in_array('accounts-cass-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['accounts-cass-admin']}}"
                                        @if(in_array('accounts-cass-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Currency Exchange Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsCurrencyExchangeRole" class="form-label mb-0">Currency
                                    Exchange</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-currency-exchange-view']}}"
                                        @if(in_array('accounts-currency-exchange-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['accounts-currency-exchange-user']}}"
                                        @if(in_array('accounts-currency-exchange-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['accounts-currency-exchange-manager']}}"
                                        @if(in_array('accounts-currency-exchange-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['accounts-currency-exchange-admin']}}"
                                        @if(in_array('accounts-currency-exchange-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipment Validation Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsShipmentValidationRole" class="form-label mb-0">Shipment
                                    Validation</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-shipment-validation-view']}}"
                                        @if(in_array('accounts-shipment-validation-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-validation-user']}}"
                                        @if(in_array('accounts-shipment-validation-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-validation-manager']}}"
                                        @if(in_array('accounts-shipment-validation-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-validation-admin']}}"
                                        @if(in_array('accounts-shipment-validation-admin', $groupRoles)) selected
                                        @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipment Billing Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsShipmentBillingRole" class="form-label mb-0">Shipment
                                    Billing</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-shipment-billing-view']}}"
                                        @if(in_array('accounts-shipment-billing-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-billing-user']}}"
                                        @if(in_array('accounts-shipment-billing-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-billing-manager']}}"
                                        @if(in_array('accounts-shipment-billing-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['accounts-shipment-billing-admin']}}"
                                        @if(in_array('accounts-shipment-billing-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- PLC Manual Invoices Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalAccountsPlcManualInvoicesRole" class="form-label mb-0">PLC Manual
                                    Invoices</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['accounts-plc-manual-invoices-view']}}"
                                        @if(in_array('accounts-plc-manual-invoices-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['accounts-plc-manual-invoices-user']}}"
                                        @if(in_array('accounts-plc-manual-invoices-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['accounts-plc-manual-invoices-manager']}}"
                                        @if(in_array('accounts-plc-manual-invoices-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['accounts-plc-manual-invoices-admin']}}"
                                        @if(in_array('accounts-plc-manual-invoices-admin', $groupRoles)) selected
                                        @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div> --}}

                    {{-- <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Customs</h6>

                        <!-- Customs Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalCustomsRole" class="form-label mb-0">Customs</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['customs-view']}}" @if(in_array('customs-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Problem Queue Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalCustomsProblemQueueRole" class="form-label mb-0">Problem Queue</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['customs-problem-queue-view']}}"
                                        @if(in_array('customs-problem-queue-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['customs-problem-queue-user']}}"
                                        @if(in_array('customs-problem-queue-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['customs-problem-queue-manager']}}"
                                        @if(in_array('customs-problem-queue-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['customs-problem-queue-admin']}}"
                                        @if(in_array('customs-problem-queue-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- MOU Log Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalCustomsMouLogRole" class="form-label mb-0">MOU Log</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['customs-mou-log-view']}}"
                                        @if(in_array('customs-mou-log-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['customs-mou-log-user']}}"
                                        @if(in_array('customs-mou-log-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['customs-mou-log-manager']}}"
                                        @if(in_array('customs-mou-log-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['customs-mou-log-admin']}}"
                                        @if(in_array('customs-mou-log-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div> --}}

                    {{-- <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Reports</h6>

                        <!-- Reports Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsRole" class="form-label mb-0">Reports</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-view']}}" @if(in_array('reports-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Margin Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsMarginReportRole" class="form-label mb-0">Margin Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-margin-report-view']}}"
                                        @if(in_array('reports-margin-report-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Accrual Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsAccrualReportRole" class="form-label mb-0">Accrual
                                    Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-accrual-report-view']}}"
                                        @if(in_array('reports-accrual-report-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipment Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsShipmentReportRole" class="form-label mb-0">Shipment
                                    Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-shipment-report-view']}}"
                                        @if(in_array('reports-shipment-report-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Airline Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsAirlineReportRole" class="form-label mb-0">Airline
                                    Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-airline-report-view']}}"
                                        @if(in_array('reports-airline-report-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div> --}}

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