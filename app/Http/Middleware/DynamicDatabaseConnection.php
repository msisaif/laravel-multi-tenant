<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DynamicDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientDomain = $this->getClientDomainFromRequest($request);

        $client = Client::where('domain', $clientDomain)->first();

        // dd($client);

        if ($client) {
            Config::set('database.connections.dynamic', [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => $client->database_name,
                'username' => $client->db_username,
                'password' => $client->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ]);

            DB::reconnect('dynamic');

            Config::set("database.default", "dynamic");

            // dd(Config::all(), 'm');
        }

        return $next($request);
    }

    protected function getClientDomainFromRequest($request)
    {
        $host = $request->getHost();
        
        // dd($host);
        
        return $host;
        
        // return explode('.', $host)[0];
    }
}
