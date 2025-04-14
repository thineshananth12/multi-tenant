<div id="details-container" class="mt-3 p-3 border rounded">
  <div class="row p-2">
    <div class="col-5">Tenant Name</div>
    <div class="col-1">:</div>
    <div class="col-6">{{$tenant->name}}</div>
  </div>

  <div class="row p-2">
    <div class="col-5">Tenant Prefix</div>
    <div class="col-1">:</div>
    <div class="col-6">{{$tenant->prefix}}</div>
  </div>
</div>
<div class="d-flex justify-content-end align-items-center p-2 mt-3 mb-3">
    <button class="btn btn-primary" id="addUser" data-id="{{ ($tenant) ? $tenant->id : ''}}" data-prefix="{{ ($tenant) ? $tenant->prefix : ''}}">Add User</button>
</div>
<table class="table" id="tenantsTable">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">Role</th>
        <!-- <th scope="col">...</th> -->
    </tr>
    </thead>
    <tbody>
        @php $sno = $users->firstItem() @endphp
        @foreach($users as $user)
            <tr>
                <td>{{$sno}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{ ($user->role == 2) ? 'Admin' : 'User' }}</td>
                
            </tr>
        @php $sno++ @endphp
        @endforeach
    </tbody>
</table>

<div id="pagination" class="d-flex justify-content-center"></div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
        
        <div id="registerUserError" class="px-3 mt-3"></div>
        <div class="modal-body p-3 mt-1 mb-3">
            <form id="addTenantForm">
                <div class="row mb-3">
                    <input type="hidden" id="tenantId">
                    <input type="hidden" id="tenantPrefix">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control input-required" id="name" placeholder="Full Name of the User">
                        <span class="text-danger error-required d-none">The Name is required</span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Email</label>
                        <input type="email" class="form-control input-required" id="email" placeholder="Email of the User">
                        <span class="text-danger error-required d-none">The Email is required</span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Role</label>
                        <select class="form-control input-required" id="role">
                            <option value="" >Select Role</option>
                            <option value="2" >Admin</option>
                            <option value="3" >User</option>
                        </select>
                        <span class="text-danger error-required d-none">The Role is required</span>
                    </div>

                     
                </div>

                <div class="text-end">
                    <button type="button" id="cancelAddUser" class="btn btn-primary text-end mx-1">Cancel</button>
                    <button type="button" id="registerUser" class="btn btn-primary text-end mx-1">Add</button>
                </div>
            </form>

        </div>
        </div>
    </div>
</div>

<!-- Add User Modal ends -->