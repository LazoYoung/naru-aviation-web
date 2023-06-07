<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class FileController extends Controller {

    /**
     * Upload a single file into storage.
     *
     * Expected input: file
     * Expected result: 200, 500
     */
    public function upload(Request $request): Response {
        $request->validate([
            'file' => 'file'
        ]);

        try {
            $file = $request->file('file');
            $fileName = $file->hashName();
            $success = $file->storePubliclyAs('public', $fileName);

            if ($success) {
                return response($this->getFilePath($fileName), 200);
            } else {
                throw new Exception('Failed to store file.');
            }
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    private function getFilePath(string $fileName): string {
        return asset('storage/' . $fileName);
    }

}
