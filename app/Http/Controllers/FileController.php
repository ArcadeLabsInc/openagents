<?php

namespace App\Http\Controllers;

use App\Services\Parser;
use App\Services\Vectara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info("Here we are.");

            // If we're not testing, validate the request
            if (!app()->runningUnitTests()) {
                $request->validate([
                  'file' => 'required|mimetypes:application/pdf' // application/json,text/markdown,text/plain|max:1000240
                ]);

                Log::info('FileController:store: $request->file(): ' . print_r($request->file(), true));
            }

            // Store the file
            $file = $request->file('file');
            $path = Storage::putFile('uploads', $file);
            Log::info('FileController:store: $path: ' . print_r($path, true));
// Parse the file
$parser = new Parser();
$res = $parser->parsePdf($path);
Log::info('FileController:store: $res: ' . print_r($res, true));

// Save the parsed file to the database
$file = File::create([
    'name' => $file->getClientOriginalName(),
    'path' => $path,
]);

return Redirect::route('start')
  ->with('message', 'File uploaded.')
  ->with('filename', $file->id);
} catch (\Exception $e) {
            // Log just the error message
            Log::error('FileController:store: $e->getMessage(): ' . print_r($e->getMessage(), true));

            return Redirect::route('start')->with('error', 'Error uploading file.');
        }
    }
}
