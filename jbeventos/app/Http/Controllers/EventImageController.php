<?php

namespace App\Http\Controllers;

use App\Models\EventImage;
use Illuminate\Http\Request;

class EventImageController extends Controller
{
    public function destroy($id)
    {
        $image = EventImage::findOrFail($id);

        // Apaga o arquivo físico se existir (ajusta o caminho se precisar)
        if ($image->image_path && file_exists(public_path('storage/' . $image->image_path))) {
            unlink(public_path('storage/' . $image->image_path));
        }

        // Apaga o registro do banco
        $image->delete();

        // Retorna JSON para o AJAX saber que deu certo
        return response()->json(['success' => true]);
    }
}
