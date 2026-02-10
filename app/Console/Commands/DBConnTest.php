<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DBConnTest extends Command
{
    protected $signature = 'ven:testdbconn 
                            {--c|clear : Clear config cache before testing}
                            {--p|show-password : Show password in output (use with caution)}';
    
    protected $description = 'Test database connection';

    public function handle()
    {
        $this->info("Ven's DB Connection Tester started!");
        
        // clear cache if option is provided
        // I swear this one section took me half an hour
        // Only reason why it breaks is that I was running the wrong command entirely
        // I am never doing commands again
        // - Ven
        if ($this->option('clear')) {
            if ($this->confirm('Config cache will be cleared. Are you sure?')) {
                $this->clearConfigCache();
            }
        }

        $this->line("Attempting to connect to the database...");
        
        try {
            // connection test
            DB::connection()->getPdo();
            $this->info("Connected successfully to database: " . DB::connection()->getDatabaseName());
            // line separator
            $this->line("");
            // throws config cutely
            $this->info("throws db config at you");
            $this->displayConfig();
            // line separator 2: electric boogaloo
            $this->line("");
            
            // query test
            $results = DB::select('SELECT 1 as test_value');
            $this->info("Simple query test passed! yeah I think it worked");
            // if cache not cleared yet, offer to clear and retry
            if (!$this->option('clear')) {
                $this->line("");
                if ($this->confirm('Would you like to clear caches and try again?')) {
                    $this->clearConfigCache();
                    $this->info("One more time! Restarting tester...");
                    $this->line("");
                    sleep(5);
                    $this->handle(); 
                } else {
                    $this->info("All done! Exiting.");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Uh oh! Connection failed: " . $e->getMessage());
            // throws config 
            $this->line("Current DB config:");
            $this->displayConfig();
            
            // if conn failed and cache not cleared yet, offer to clear and retry
            if (!$this->option('clear')) {
                $this->line("");
                if ($this->confirm('Would you like to clear caches and try again?')) {
                    $this->clearConfigCache();
                    $this->line("One more time! Restarting tester...");
                    sleep(1);
                    $this->handle(); 
                } else {
                    $this->error("Connection failed! Exiting.");
                }
            }
        }
        
        return 0;
    }

    private function displayConfig()
    {
        $this->line("Host: " . config('database.connections.mysql.host'));
        $this->line("Port: " . config('database.connections.mysql.port'));
        $this->line("Database: " . config('database.connections.mysql.database'));
        $this->line("Username: " . config('database.connections.mysql.username'));
        
        if ($this->option('show-password')) {
            $this->line("Password: " . config('database.connections.mysql.password') . " (here you go!)");
        } else {
            $this->line("Password: [hidden, sorry! run with -p to show password]");
        }
    }

    private function clearConfigCache()
    {
        $this->info("Clearing cache...");
        
        Artisan::call('config:clear');
        $this->info("Config cache cleared!");
        
        Artisan::call('cache:clear');
        $this->info("Application cache cleared!");
        
        $this->info("All caches cleared!");
        $this->line("");
    }
}