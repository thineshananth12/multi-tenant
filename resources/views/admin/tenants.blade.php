<div class="d-flex justify-content-end align-items-center p-2 mt-3 mb-3">
    <button class="btn btn-primary" id="addTenant">Add Tenant</button>
</div>
<table class="table" id="tenantsTable">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Prefix</th>
        <th scope="col">Number of Users</th>
        <th scope="col">...</th>
    </tr>
    </thead>
    <tbody>
        @php $sno = $tenants->firstItem()   @endphp
         
        @foreach($tenants as $tenant)
            <tr>

                <td>{{$sno}}</td>
                <td>{{$tenant->name}}</td>
                <td>{{$tenant->prefix}}</td>
                <td>{{$tenant->users_count}}</td>
                <td><span><i role="button" class="px-1 bi bi-gear manageTenant" data-id="{{$tenant->prefix}}"></i></span></td>
            </tr>
        @php $sno++ @endphp
        @endforeach
    </tbody>
</table>

<div id="pagination" class="d-flex justify-content-center"></div>
<!-- Add Tenant Modal -->
<div class="modal fade" id="addTenantModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
        
        <div id="registerTenantError" class="px-3 mt-3"></div>
        <div class="modal-body p-3 mt-1 mb-3">
            <form id="addTenantForm">
                <div class="row mb-3">
                    
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Name of the Tenant</label>
                        <input type="text" class="form-control input-required" id="name" placeholder="Full Name of the Tenant">
                        <span class="text-danger error-required d-none">The Name is required</span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="prefix" class="form-label">Prefix</label>
                        <input type="text" class="form-control input-required" id="prefix" placeholder="A unique prefix">
                        <span class="text-danger error-required d-none">The Prefix is required</span>

                    </div>
                </div>

                <div class="text-end">
                    <button type="button" id="cancelAddTenant" class="btn btn-primary text-end mx-1">Cancel</button>
                    <button type="button" id="registerTenant" class="btn btn-primary text-end mx-1">Add</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

<!-- Add Tenant Modal ends -->