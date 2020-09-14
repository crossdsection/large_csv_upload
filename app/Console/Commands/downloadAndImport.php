<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class downloadAndImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:downloadAndImport {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download And Import the url passed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = $this->argument('url');
        $parts = explode("/",$url);
        $targetFile = fopen( base_path(). '\resources\uploads\\'.end($parts), 'w' );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_exec( $ch );
        fclose( $ch );

        return 0;
    }
}
