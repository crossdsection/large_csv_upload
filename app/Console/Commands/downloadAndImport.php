<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Log;
use SplFileObject;

class downloadAndImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:downloadAndImport {url} {path}';

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

    function headerFunction() {

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('auto_detect_line_endings',true);

        $url = $this->argument('url');
        $path = $this->argument('path');

        $targetFile = fopen( $path, 'w' );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_exec( $ch );

        $fileToRead = array();
        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (pathinfo($filename, PATHINFO_EXTENSION)=="csv"){
                    $fileinfo = pathinfo($filename);
                    copy("zip://".$path."#".$filename, base_path(). '\resources\uploads\newname'.$i.'.csv');
                    array_push($fileToRead, base_path(). '\resources\uploads\newname'.$i.'.csv');
                }
            }   
            $zip->close();
            echo 'ok';
        } else {
            echo 'failed';
        }

        for ($i = 0; $i < count($fileToRead); $i++) {
            try {
                $count = 0;
                $file = fopen($fileToRead[$i], "r");
                while (($line = fgetcsv($file)) !== FALSE) {
                    // if( $count == 0 ) {
                    //     $tableColumns = array();
                    //     foreach ($line as $key => $value) {
                    //         array_push($tableColumns, VARCHAR(50));
                    //     }
                    //     $createTable = "CREATE TABLE newname".$i." (".implode(",", $tableColumns).")";
                    //     Log::debug($createTable);
                    // }
                    $count++;
                }
            }
            catch (exception $e) {
                continue;
            }
        }
    }
}
