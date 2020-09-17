<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use ZipArchive;
use Log;

class downloadAndImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:downloadAndImport {url} {path} {id}';

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
        $fileId = $this->argument('id');

        $targetFile = fopen( $path, 'w' );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_exec( $ch );

        $fileToRead = array();
        $tableName = array();
        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                try {
                    if(substr($zip->getNameIndex($i), 0, 9) === "__MACOSX/") {
                       continue;
                    }
                    $filename = $zip->getNameIndex($i);
                    if (pathinfo($filename, PATHINFO_EXTENSION)=="csv") {
                        $fileinfo = pathinfo($filename);
                        copy("zip://".$path."#".$filename, base_path(). '\resources\uploads\tempname'.$i.$fileinfo['filename'].'s.csv');
                        array_push($fileToRead, base_path(). '\resources\uploads\tempname'.$i.$fileinfo['filename'].'s.csv');
                        array_push($tableName, 'tempname'.$i.$fileinfo['filename'].'s');
                    }
                } 
                catch(Exception $e) {
                    Log::debug($e);
                }
            }
            Log::debug('ok');
            $zip->close();
        } else {
            Log::debug('failed');
        }

        DB::table('files')->where('id', $fileId)->update(['status' => 'PROCESSING']);

        for ($i = 0; $i < count($fileToRead); $i++) {
            try {
                $count = 0;
                $dataCount = 0;
                $tableColumns = array();

                $file = fopen($fileToRead[$i], "r");
                $data = array();
                while (($line = fgetcsv($file)) !== FALSE) {
                    if( $count == 0 ) {
                        foreach ($line as $key => $value) {
                            array_push($tableColumns, array( 'colName' => $value, 'type' => 'string'));
                        }
                    } else {
                        $row = array();
                        foreach ($line as $key => $value) {
                            if( is_numeric($value) ){
                                $tableColumns[$key]['type'] = 'number';
                            }
                            $row[ $tableColumns[$key]['colName'] ] = $value;
                        }
                        array_push($data, $row);
                        $dataCount++;
                    }
                    $count++;
                    if( $dataCount == 1000 ) {
                         if (!Schema::hasTable($tableName[$i])) {
                            Schema::create($tableName[$i], function (Blueprint $table) use ($tableColumns) {
                                $table->increments('id');
                                if (count($tableColumns) > 0) {
                                    foreach ($tableColumns as $field) {
                                        if( $field['colName'] != 'id' )  {
                                            if( $field['type'] == 'number' ) {
                                                $table->integer($field['colName']);
                                            }
                                            if( $field['type'] == 'string' ) {
                                                $table->string($field['colName']);   
                                            }
                                        }
                                    }
                                }
                                $table->timestamps();
                            });
                        }
                        Log::debug($tableName[$i]);
                        DB::table($tableName[$i])->insert($data);
                        $data = array();
                        $dataCount = 0;
                    }
                }
                if( !empty($data) ) {
                    DB::table($tableName[$i])->insert($data);
                }
                DB::table('files')->where('id', $fileId)->update(['status' => 'SUCCESS']);
            }
            catch (exception $e) {
                Log::debug($e);
                continue;
            }
        }
    }
}
