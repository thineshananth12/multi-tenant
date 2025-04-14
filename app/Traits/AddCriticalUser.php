<?php
namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\Tenant;

trait AddCriticalUser
{
    public function migrateData($tenant, $adminUser)
    {
        // creating new schema.
        $schemaName = $this->createSchema($tenant->prefix);   
        Tenant::where('id',  $tenant->id)->update([
            'schema' => $schemaName
        ]);
  
        // set schema seaarch path
        $this->setSchemaSearchPath($schemaName, $tenant->id);

        // migrate to schema
        $this->migrateToSchema($schemaName);
        
        // insert all the user data to new table and delete the existing
        $this->transferData( $schemaName, $tenant->id);
 
    }

    public function createSchema($schemaName)
    {

        $timestamp = now()->toDateTimeString();
        $schema = "tenant_" . $schemaName . "_" . $timestamp;

        $schema = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($schema));

        DB::statement("CREATE SCHEMA IF NOT EXISTS \"$schema\"");

        return $schema;
    }

    public function getCurrentSchema()
    {
        return DB::select('SELECT current_schema();')[0]->current_schema ?? 'public';
    }

    public function transferData($schemaName, $tenantId )
    {
        $insert = DB::statement("INSERT INTO $schemaName.users (name, email, tenant_id, email_verified_at, password, remember_token, created_at, updated_at, role) 
            SELECT name, email, tenant_id, email_verified_at, password, remember_token, created_at, updated_at, role
            FROM public.users 
            WHERE tenant_id = $tenantId
            ON CONFLICT DO NOTHING"); 

        if ($insert) {
            DB::statement("DELETE FROM public.users WHERE tenant_id = $tenantId");
        }
    }

    public function setSchemaSearchPath($schemaName)
    {
        DB::statement("SET search_path TO {$schemaName}");
    }

    public function checkIfSchemaExists($schemaName)
    {
        return  DB::table('information_schema.schemata')->where('schema_name', 'tenant1')->exists();
    }

    public function migrateToSchema($schemaName) {
        $this->createMigrationTable($schemaName);
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant_migration',
            '--database' => env('DB_CONNECTION'),
            '--force' => true,  
        ]);
         
    }

    public function createMigrationTable($schemaName)
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL
            )
        ");
    }
    
}