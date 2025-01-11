<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileUploadRequest;
use App\Models\File;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function store(FileUploadRequest $request)
    {
        $file = $request->file('file');
        $filePath = $file->store('uploads');

        // Создать превью, если это изображение
        $previewPath = null;
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $image = Image::make($file)
                ->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode();

            $previewPath = 'previews/' . $file->hashName();
            Storage::put($previewPath, $image);
        }

        $record = File::create([
            'name' => $request->name,
            'location' => $filePath,
            'file_extension' => $request->fileExtension,
            'file_size' => $request->fileSize,
            'preview' => $previewPath,
        ]);

        return response()->json([
            'message' => 'Файл успешно загружен!',
            'data' => $record,
        ]);
    }

    public function update(FileUploadRequest $request, $id){
        $record = File::findOrFail($id);

        if($request->hasFile("file")) {
            if($record->location) {
                Storage::delete($record->location);
            }

            if($record->preview){
                Storage::delete($record->preview);
            }

            $file = $request->file("file");
            $filePath = $file->store("uploads");

            $previewPath = null;

            if (str_starts_with($file->getMimeType(), 'image/')) {
                $image = Image::make($file)
                    ->resize(100, 100, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode();
    
                $previewPath = 'previews/' . $file->hashName();
                Storage::put($previewPath, $image);
            }

            $record->location = $filePath;
            $record->preview = $previewPath;
            $record->extension = $file->getClientOriginalExtension();
            $record->size = round($file->getSize() / 1024 / 1024, 2);
        }

        if($request->has("name")) {
            $record->name = $request->name;
        }

        $record->save();

        return response()->json([
            'message' => "Успешно обновлено!",
            "data" => $record
        ]);
    }

    public function delete(Request $request, $id) {
        $record = File::findOrFail($id);

        if($record->location) {
            Storage::delete($record->location);
        }

        if($record->preview) {
            Storage::delete($record->preview);
        }

        $record->delete();

        return response()->json([
            "Успешно удалено!"
        ]);
    }

    public function index(Request $request) {
        $query = File::query();

        if($request->has("search")) {
            $query->where("name", "like", "%" . $request->input("search") . "%");
        }

        $records = $query->paginate(50);

        return response()->json([
            'data' => $records->items(),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => 50,
                'total' => $records->total(),
            ],
            'links' => [
                'first' => $records->url(1),
                'last' => $records->url($records->lastPage()),
                'prev' => $records->previousPageUrl(),
                'next' => $records->nextPageUrl(),
            ]
        ]);
    }

    public function suggestions(Request $request) {
        $query = $request->input("q", "");

        if(empty($query)) {
            return response()->json([]);
        }

        $suggestions = File::where("name", "like", "%" . $query . "%")
            ->select("name")
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }

}
