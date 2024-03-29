<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class FileController extends Controller {

    /**
     * Upload a single file into storage.
     *
     * Expected input: file
     * Expected result: path to stored file
     * Possible status: 200, 500
     */
    public function upload(Request $request): Response {
        try {
            $request->validate([
                'file' => 'file'
            ]);

            $file = $request->file('file');
            $fileName = $file->hashName();
            $success = $file->storePubliclyAs('public', $fileName);

            if ($success) {
                return response($this->getFilePath($fileName), 200);
            } else {
                throw new Exception('Failed to store file.');
            }
        } catch (ValidationException $e) {
            return response($e->getMessage(), 400);
        } catch (Throwable $t) {
            return response($t->getMessage(), 500);
        }
    }

    private function getFilePath(string $fileName): string {
        return asset('storage/' . $fileName);
    }

}
