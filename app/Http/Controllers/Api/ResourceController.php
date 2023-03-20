<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Models\Resource;

class ResourceController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $filename = time() . "." . $file->getClientOriginalExtension();
        $filepath = Storage::cloud()->putFileAs("temp-resources", new File($file), $filename);

        $resource = Resource::create([
            'filepath' => $filepath
        ]);

        return $resource->id;
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        $resource->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
