<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Tymon\JWTAuth\Facades\JWTAuth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\AddCriticalUser;


class TenantController extends Controller
{


    use AddCriticalUser;

    public function getUsers(Request $request)
    {
      
        
      
        $tenant= Auth::user();

                
        // if ($tenant->role != 2) {
        //     return response()->json(
        //         [
        //             'error' => 'UnAuthorized User'
        //         ], 401
        //     );
        // } 

        // dd($tenant);
   
        $currentSchema= $this->getCurrentSchema();
   
        // dd($currentSchema);
        if ($currentSchema && $currentSchema != 'public') {
            $users = User::paginate(10);
        } else {
            $this->setSchemaSearchPath('public');
            $users = User::where('tenant_id', $tenant->tenant_id)->paginate(10);

        }
        
        // dd($users);
        
     
        // dd(34);

        // dd(56);
        
        $links = view('tenants.users_links', compact('users'))->render();
        $html = view('tenants.users', compact('users'))->render();

        if ($request->ajax()) {
          
            return response()->json([
                'html' => $html,
                'links' => $links
            ]);
        } else {
            return $users;
        }
    
       
         

        
         
    } 

    public function addUser(Request $request)
    {
        // Log::info('reached to  add user');
        // dd(2);
        $tenantAdmin= Auth::user();
        // dd($tenantAdmin);
        // $token = JWTAuth::fromUser($tenantAdmin);
        if ($tenantAdmin && $tenantAdmin->role != 2) {
            return response()->json(
                [
                    'error' => 'UnAuthorized User'
                ], 401
            );
        } 

        // dd($tenantAdmin);
        Log::info('tenantAdmin user is : '.json_encode($tenantAdmin));

        $currentSchema = $this->getCurrentSchema();

        $redirected = false;
        // dd($currentSchema);

        Log::info('curent schema is : '.json_encode($currentSchema));
         
        if ($currentSchema != 'public') {
            // check if table transferred

            $user = User::where('email', $tenantAdmin->email)->firstOrFail();
            
            // $token = $user->createToken('API Token')->accessToken;
            $tenantPrefix = explode('_', $currentSchema)[1];

            // dd($tenantPrefix);
            $tenant = DB::table('public.tenants')->where('prefix', $tenantPrefix)->first();

            // Log::info('new token is : '.json_encode($token));

            // session(['jwt_token' => $token]);
        } else {
            // dd($currentSchema);
            $tenant = Tenant::where('id',$tenantAdmin->tenant_id)->withCount('users')->first();

            // checking if critical user
            $maxUserCount = env('TENANT_MAX_USER_COUNT', 50);

            $usersCount = $tenant->users_count;

            if ($usersCount >= $maxUserCount) {
                // if ($tenant->schema != '') {

                // }
                // dd(33);

                 
                $migrateData = $this->migrateData($tenant, $tenantAdmin);

                $redirected = true;
                 

        
                // Log::info('user created '.json_encode($user));
            
                // create a user token to set



            }

        }

        // dd($tenant);
        Log::info('new token set');



        // if (!isset($tenant)) {

        //     $tenant = Tenant::find($tenantAdmin->tenant_id);
        // }


        Log::info('finding tenant is : '.json_encode($tenant));
        

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'role' => 'required|in:2,3',
            ], [


                'email.unique' => 'This email is already taken.',
                'email.email' => 'Enter a valid Email.',
                'role.in' => 'Role: 2=>Admin, 3=>User'
            ]);

            if ($validator->fails()) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors(), // contains all error messages
                ], 422);
            }
            
            Log::info('user creation begins');
      
            Log::info('new usr: '. json_encode($request));
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password'  => bcrypt($request->email),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
                'role'  => $request->role
            ]);
    
            Log::info('user created '.json_encode($user));
        
            if ($user) {
                return response()->json([
                    'status'    => true,
                    'message' => 'User registered',
                    'redirected' => $redirected
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
     
}
