<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Hash;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\AddCriticalUser;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    use AddCriticalUser;
    
    public function getTenants(Request $request)
    {
       
        $user= Auth::user();
        if (!$user && $user->role != 1) {
            return response()->json(
                [
                    'error' => 'UnAuthorized User'
                ], 401
            );
        } 

        
        // $tenants = Tenant::withCount('users')->where()->paginate(10);;

        // $tenants = Tenant::paginate(10);


        // $tenants = Cache::remember('tenants', 60, function () {
        //     return  Tenant::a);
        // });
        $page = $request->input('page', 1);
        $tenants = Cache::remember('users_page_' . $page, now()->addMinutes(10), function () {
            return Tenant::paginate(10);
        });
 

        foreach ($tenants as $tenant) {
            if ($tenant->schema) {
                $tenant->users_count = DB::table(DB::raw("{$tenant->schema}.users"))
                    ->count();
            } else {
                $tenant->users_count = DB::table('users')
                    ->where('tenant_id', $tenant->id)
                    ->count();
            }
        }

  
        
        $links = view('admin.tenants_links', compact('tenants'))->render();
        $html = view('admin.tenants', compact('tenants'))->render();

        if ($request->ajax()) {
          
            return response()->json([
                'html' => $html,
                'links' => $links
            ]);
        } else {
            return $tenants;
        }

        
         
    } 

    public function registerTenant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'prefix' => 'required|unique:tenants',
            ], [


                'prefix.unique' => 'This prefix is already taken.',
            ]);

            if ($validator->fails()) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors(), // contains all error messages
                ], 422);
            }
            
            // continue saving data...
            $tenant = Tenant::create([
                'name' => $request->name,
                'prefix' => $request->prefix
            ]);
    
            if ($tenant) {
                return response()->json([
                    'status'    => true,
                    'message' => 'Tenant registered',
                    'id'    => $tenant->id
                ]);
    
            } else {
                return response()->json([
                    'status'    => false,
                    'message' => 'Registration Failed. Try Again'
                ]);
            }
            return response()->json(['success' => true]);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function registerUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
            ], [


                'email.unique' => 'This email is already taken.',
                'email.email' => 'Enter a valid Email.',
            ]);

            if ($validator->fails()) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors(), // contains all error messages
                ], 422);
            }

            $tenant = Tenant::find($request->tenantId);
            if (!$tenant) {
                return response()->json([
                    'status'    => false,
                    'message' => 'Tenant Not Found'
                ]);
            }
            $tenantSchema = $tenant->schema;

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->email),
                'tenant_id' => $request->tenantId,
                'email_verified_at' => now(),
                'role' => $request->role,
            ];
            if ($tenantSchema) {
                // Tenant has separate Database.
                $user = new User();
                $user->setTable("$tenantSchema.users");
                $user = $user->create($userData);
            } else {

                // Check if Critical user
                $maxUserCount = env('TENANT_MAX_USER_COUNT', 50);
                $userCount = User::where('tenant_id', $request->tenantId)->count();

                if ($userCount >= $maxUserCount) {
                    $migrateData = $this->migrateData($tenant, $tenant);

                }


                $user = User::create($userData);

            }
     
            // continue saving data...
    
            if ($user) {
                return response()->json([
                    'status'    => true,
                    'message' => 'User registered'
                ]);
    
            } else {
                return response()->json([
                    'status'    => false,
                    'message' => 'Registration Failed. Try Again'
                ]);
            }
            return response()->json(['success' => true]);
    
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function manageUsers(Request $request)
    {
        
        
        $tenant = Tenant::where('prefix', $request->tenant)->first();
        $schema = $tenant->schema;

        // dd($schema);
        if ($schema) {
            $users = User::fromSchema($schema)->paginate();
        } else {
            
            $users = User::join('tenants', 'users.tenant_id', '=', 'tenants.id')
                ->select('users.*', 'tenants.prefix as prefix')
                ->where('tenants.prefix', $request->tenant)
                ->orderBy('users.role', 'ASC')
                ->paginate(10);
        }


      
         
        // $users = User::
        
        
        $tenant = Tenant::where('prefix', $request->tenant)->first();
        
        if (!$tenant) {
            return response()->json([
                'status'    => false,
                'message' => 'Tenant not found.'
            ]);
            
        } 
        
        $tenantId = $tenant->prefix;
        $links = view('admin.users_links', compact('users','tenantId'))->render();
        
        $html = view('admin.users', compact('users','tenant','links'))->render();
      
        if ($request->ajax()) {
            return response()->json([
                'html' => $html,
                'links' => $links
            ]);
        } else {
            return $users;
        }

    }
}
