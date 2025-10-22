<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseFollowController extends Controller
{
    public function follow(Course $course)
    {
        $user = auth()->user();
        
        // 1. Executa a ação de seguir
        $user->followedCourses()->syncWithoutDetaching([$course->id]);

        // 2. Calcula a nova contagem
        $newFollowersCount = $course->followers()->count(); // <-- OBRIGATÓRIO

        // 3. Retorna a contagem no JSON com a chave correta
        return response()->json([
            'status' => 'success', 
            'message' => 'Você está seguindo este curso.',
            'followers_count' => $newFollowersCount // <-- CHAVE NECESSÁRIA PARA O JS
        ]);
    }

    public function unfollow(Course $course)
    {
        $user = auth()->user();
        
        // 1. Executa a ação de deixar de seguir
        $user->followedCourses()->detach($course->id); 

        // 2. Calcula a nova contagem
        $newFollowersCount = $course->followers()->count(); // <-- OBRIGATÓRIO
        
        // 3. Retorna a contagem no JSON com a chave correta
        return response()->json([
            'status' => 'success', 
            'message' => 'Você deixou de seguir este curso.',
            'followers_count' => $newFollowersCount // <-- CHAVE NECESSÁRIA PARA O JS
        ]);
    }

    // O método followersCount pode ser mantido, mas não é usado na lógica de clique do botão
    public function followersCount(Course $course)
    {
        $count = $course->followers()->count();

        return response()->json([
            'count' => $count
        ]);
    }
}