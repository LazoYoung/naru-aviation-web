<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Throwable;

class GalleryController extends Controller {

    public function showGallery() {
        return Inertia::render('Gallery/Browse');
    }

    /**
     * Upload an image.
     *
     * Expected input: title, description, file
     * Expected result: none
     * Possible status: 200, 500
     */
    public function submitImage(Request $request): Response {
        try {
            $validated = $request->validate([
                'title' => ['string'],
                'description' => ['nullable', 'string'],
                'file' => ['file'],
            ]);
            $user = $request->user();
            $file = $request->file('file');
            $fileName = $file->hashName();
            $success = $file->storePubliclyAs('public', $fileName);

            if ($success) {
                $image = new Image();
                $image->user()->associate($user);
                $image->title = $validated['title'];
                $image->description = $validated['description'];
                $image->file = $this->getFilePath($fileName);
                $image->author = $user->name;
                $image->saveOrFail();
                return response(null, 200);
            } else {
                throw new Exception('Failed to store file.');
            }
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    public function getImages() {
        return response()->json(Image::latest()->get()->toJson());
    }

    private function getFilePath(string $fileName): string {
        return asset('storage/' . $fileName);
    }

}
