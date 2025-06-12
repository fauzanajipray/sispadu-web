<?php

namespace App\Http\Controllers;

use App\Models\ReportImage;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    public function show($image_id)
    {
        $image = ReportImage::find($image_id);

        if ($image && file_exists(public_path('storage/'.$image->getRawOriginal('image_path')))) {
            return response()->file(public_path('storage/'.$image->getRawOriginal('image_path')));
        }

        abort(Response::HTTP_NOT_FOUND, 'Image not found or does not exist.');
    }
}
