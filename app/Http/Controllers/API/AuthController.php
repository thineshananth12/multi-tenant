<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;
use App\Traits\UpdateUserSchema;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    use UpdateUserSchema;
    public function login(Request $request)
    {
        


       
        $credentials = $request->only('email', 'password');



        if (!$token = JWTAuth::attempt($credentials)) {
        //     throw ValidationException::withMessages([
        //         'email' => ['Invalid credentials'],
        //     ]);
        // }
        // if (! $token = Auth::attempt($credentials)) {
           // retry login into new schema
         

        //    $token = $this->retryLogin($credentials);
        //    dd($token);
        // //    dd($schemaName);
           
        //    dd($this->getCurrentSchema());

           
        //    dd($this->getCurrentSchema());
            // dd($request);
            // if (!$token) {

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Credentials'
                ]);
            // }

        }

        // dd($token);

        // dd(334);
        $user = JWTAuth::setToken($token)->authenticate();

        // dd($user);
        

        // $user = Auth::user();
        // $currentSchema = $this->getCurrentSchema();
        // if ($currentSchema != 'public') {
        //     // $user = User::where('email', $user->email)->firstOrFail();
        //     // $token = $user->createToken('API Token')->accessToken;
        //     $tenantPrefix = explode('_', $currentSchema)[1];
        //     $tenant = DB::table('public.tenants')->where('prefix', $tenantPrefix)->first();

        //     Log::info('new token is : '.json_encode($token));

        //     session(['jwt_token' => $token]);
        // }

        $currentSchema= $this->getCurrentSchema();

        if ($currentSchema == 'public') {
            
            $tenant = $user->tenant()->first();
             
        } else {
            $tenantPrefix = explode('_', $currentSchema)[1];
            $tenant = DB::table('public.tenants')->where('prefix', $tenantPrefix)->first();
        }
        // if (!isset($tenant)) {
            
        // }
  

        if (!$tenant) {
            
            // check for admin user
            $role = $user->role; 
            if ($role == 1) {
                // super admin role
                $tenantPrefix = 'admin';
                $schema = 'public';

            } else {

                abort(403, 'Tenant not found.');
            }
        } else {
             
            $tenantPrefix = $tenant->prefix;
            $schema = $tenant->schema ?? 'public';
             
        } 

        return response()->json([
            'status' => true,
            'token' => $token,
            'role'  => $user->role,
            'name'  => $user->name,
            'prefix' => $tenantPrefix,
            'schema'    => $schema
        ]);

    }
}
