<?php

namespace App\Http\Controllers;

use App\Models\EventImage;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function destroyEventImage($id)
    {
        $image = EventImage::findOrFail($id);

        // Apaga o arquivo físico se existir
        if ($image->image_path && file_exists(public_path('storage/' . $image->image_path))) {
            unlink(public_path('storage/' . $image->image_path));
        }

        // Remove o registro do banco
        $image->delete();

        return response()->json(['success' => true]);
    }

    public function destroyCourseImage($id, $type)
    {
        $course = Course::findOrFail($id);

        // Verifica se o tipo é válido
        if (!in_array($type, ['icon', 'banner'])) {
            return response()->json(['error' => 'Tipo de imagem inválido.'], 400);
        }

        $column = $type === 'icon' ? 'course_icon' : 'course_banner';
        $path = $course->$column;

        // Apaga o arquivo físico se existir
        if ($path && file_exists(public_path('storage/' . $path))) {
            unlink(public_path('storage/' . $path));
        }

        // Atualiza o banco
        $course->update([$column => null]);

        return response()->json(['success' => true]);
    }
}
