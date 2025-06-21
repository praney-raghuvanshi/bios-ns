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

                    </div>

                    <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Maintenance</h6>

                        <!-- Maintenance Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceRole" class="form-label mb-0">Maintenance</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-view']}}" @if(in_array('maintenance-view',
                                        $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Flights Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceFlightsRole" class="form-label mb-0">Flights</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-flights-view']}}"
                                        @if(in_array('maintenance-flights-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-flights-user']}}"
                                        @if(in_array('maintenance-flights-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-flights-manager']}}"
                                        @if(in_array('maintenance-flights-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-flights-admin']}}"
                                        @if(in_array('maintenance-flights-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Zones Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceZonesRole" class="form-label mb-0">Zones</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-zones-view']}}"
                                        @if(in_array('maintenance-zones-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-zones-user']}}"
                                        @if(in_array('maintenance-zones-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-zones-manager']}}"
                                        @if(in_array('maintenance-zones-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-zones-admin']}}"
                                        @if(in_array('maintenance-zones-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Locations Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceLocationsRole" class="form-label mb-0">Locations</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-locations-view']}}"
                                        @if(in_array('maintenance-locations-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-locations-user']}}"
                                        @if(in_array('maintenance-locations-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-locations-manager']}}"
                                        @if(in_array('maintenance-locations-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-locations-admin']}}"
                                        @if(in_array('maintenance-locations-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Airports Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceAirportsRole" class="form-label mb-0">Airports</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-airports-view']}}"
                                        @if(in_array('maintenance-airports-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-airports-user']}}"
                                        @if(in_array('maintenance-airports-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-airports-manager']}}"
                                        @if(in_array('maintenance-airports-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-airports-admin']}}"
                                        @if(in_array('maintenance-airports-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceProductsRole" class="form-label mb-0">Products</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-products-view']}}"
                                        @if(in_array('maintenance-products-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-products-user']}}"
                                        @if(in_array('maintenance-products-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-products-manager']}}"
                                        @if(in_array('maintenance-products-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-products-admin']}}"
                                        @if(in_array('maintenance-products-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Customers Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceCustomersRole" class="form-label mb-0">Customers</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-customers-view']}}"
                                        @if(in_array('maintenance-customers-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-customers-user']}}"
                                        @if(in_array('maintenance-customers-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-customers-manager']}}"
                                        @if(in_array('maintenance-customers-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-customers-admin']}}"
                                        @if(in_array('maintenance-customers-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Operational Calendars Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceOperationalCalendarsRole"
                                    class="form-label mb-0">Operational Calendars</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-operational-calendars-view']}}"
                                        @if(in_array('maintenance-operational-calendars-view', $groupRoles)) selected
                                        @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-operational-calendars-user']}}"
                                        @if(in_array('maintenance-operational-calendars-user', $groupRoles)) selected
                                        @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-operational-calendars-manager']}}"
                                        @if(in_array('maintenance-operational-calendars-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-operational-calendars-admin']}}"
                                        @if(in_array('maintenance-operational-calendars-admin', $groupRoles)) selected
                                        @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Aircrafts Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalMaintenanceAircraftsRole" class="form-label mb-0">Aircrafts</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['maintenance-aircrafts-view']}}"
                                        @if(in_array('maintenance-aircrafts-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['maintenance-aircrafts-user']}}"
                                        @if(in_array('maintenance-aircrafts-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['maintenance-aircrafts-manager']}}"
                                        @if(in_array('maintenance-aircrafts-manager', $groupRoles)) selected @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['maintenance-aircrafts-admin']}}"
                                        @if(in_array('maintenance-aircrafts-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="p-3 border rounded bg-white shadow-sm">
                        <h6 class="fw-bold mb-3">Flight Operations</h6>

                        <!-- Flight Operations Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalFlightOperationsRole" class="form-label mb-0">Flight Operations</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['flight-operations-view']}}"
                                        @if(in_array('flight-operations-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Schedule Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalFlightOperationsScheduleRole" class="form-label mb-0">
                                    Schedule</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['flight-operations-schedules-view']}}"
                                        @if(in_array('flight-operations-schedules-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                    <option value="{{$allRoles['flight-operations-schedules-user']}}"
                                        @if(in_array('flight-operations-schedules-user', $groupRoles)) selected @endif>
                                        User
                                    </option>
                                    <option value="{{$allRoles['flight-operations-schedules-manager']}}"
                                        @if(in_array('flight-operations-schedules-manager', $groupRoles)) selected
                                        @endif>
                                        Manager
                                    </option>
                                    <option value="{{$allRoles['flight-operations-schedules-admin']}}"
                                        @if(in_array('flight-operations-schedules-admin', $groupRoles)) selected @endif>
                                        Administrator
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="p-3 border rounded bg-white shadow-sm">
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

                        <!-- Flight Performance Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsFlightPerformanceReportRole" class="form-label mb-0">
                                    Flight Performance Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-flight-performance-report-view']}}"
                                        @if(in_array('reports-flight-performance-report-view', $groupRoles)) selected
                                        @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Billing Extract Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsBillingExtractRole" class="form-label mb-0">
                                    Billing Extract</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-billing-extract-view']}}"
                                        @if(in_array('reports-billing-extract-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Daily Flight Report Section -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-3">
                                <label for="modalReportsDailyFlightReportRole" class="form-label mb-0">
                                    Daily Flight Report</label>
                            </div>
                            <div class="col-md-9">
                                <select name="roles[]" class="form-select select2" data-allow-clear="true">
                                    <option value="0">No Access</option>
                                    <option value="{{$allRoles['reports-daily-flight-report-view']}}"
                                        @if(in_array('reports-daily-flight-report-view', $groupRoles)) selected @endif>
                                        View Only
                                    </option>
                                </select>
                            </div>
                        </div>

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