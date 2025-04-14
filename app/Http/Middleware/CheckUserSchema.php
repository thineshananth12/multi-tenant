<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Traits\UpdateUserSchema;
use Illuminate\Support\Facades\DB;



class CheckUserSchema
{
    use UpdateUserSchema;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $schema = $request->header('schema');
         
        if ($schema) { 
            DB::statement("SET search_path TO {$schema}");
        }
    
        return $next($request);
    }
}
