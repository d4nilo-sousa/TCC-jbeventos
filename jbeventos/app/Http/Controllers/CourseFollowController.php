<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseFollowController extends Controller
{
    /**
     * Segue um curso para o usuário autenticado.
     *
     * Verifica se o usuário ainda não segue o curso e, se não, adiciona a relação.
     * Redireciona de volta com uma mensagem de sucesso.
     *
     * @param  \App\Models\Course  $course  O curso a ser seguido
     * @return \Illuminate\Http\RedirectResponse
     */
    public function follow(Course $course)
    {
        $user = auth()->user();
        
        if(!$user->followedCourses->contains($course->id)){ // Verifica se o usuário segue o curso
            $user->followedCourses()->attach($course->id);
        }

        return back()->with('success', 'Você está seguindo este curso');
    }

    /**
     * Deixa de seguir um curso para o usuário autenticado.
     *
     * Verifica se o usuário já segue o curso e, se sim, remove a relação.
     * Redireciona de volta com uma mensagem de sucesso.
     *
     * @param  \App\Models\Course  $course  O curso a ser deixado de seguir
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfollow(Course $course)
    {
        $user = auth()->user();
        
        if($user->followedCourses->contains($course->id)){ // Verifica se o usuário segue o curso
            $user->followedCourses()->detach($course->id); // Desvincula o usuário do curso
        }

        return back()->with('success', 'Você deixou de seguir este curso');
    }
}