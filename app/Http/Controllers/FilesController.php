<?php

namespace App\Http\Controllers;

use App\Models\Files;
use Illuminate\Http\Request;
use Artisan;
use App\Jobs\DownloadImportZip;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Files::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $url = $request->input('url');
        $headers = get_headers($url, 1);
        $parts = explode("/",$url);

        $file = new Files;
        $file->size = $headers['Content-Length'];
        $file->name = end($parts);
        $file->path = base_path(). '\resources\uploads\\'.end($parts);
        $file->status = 'DOWNLOADING';

        $file->save();

        DownloadImportZip::dispatch($url, $file->path);
        // Artisan::queue('command:downloadAndImport', ['url' => $url, 'path' => $file->path]);
        
        return response()->json(['error' => 0, 'message' => 'Successfully Queued']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Files::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file = Files::find($id);
        $file->update($request->all());
        return $file;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Files::destroy($id);
    }
}
