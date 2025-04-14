<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class IdentifyTenantFromRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $user = DB::table('public.users')->where('email', $request->email)->first();
        if ($user) {
            DB::statement("SET search_path TO public");
            return $next($request);
        }

        $schemas = DB::select("
            SELECT schema_name 
            FROM information_schema.schemata 
            WHERE schema_name NOT LIKE 'pg_%' 
            AND schema_name NOT IN ('information_schema', 'public')
        ");

        foreach ($schemas as $schema) {
            try {
              
                $user = DB::table("{$schema->schema_name}.users")->where('email', $request->email)->first();
                if ($user) {
                    // Merge it into the request
                    DB::statement("SET search_path TO {$schema->schema_name}");


                }
            } catch (\Exception $e) {
                // Skip schema if table doesn't exist or throws error
                continue;
            }
        }  

        return $next($request);
    }
}
