<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseFollowController extends Controller
{
    //Seguir um Curso
    public function follow(Course $course){
        $user = auth()->user();
        
        if(!$user->followedCourses->contains($course->id)){ //Verifica se o usuário segue o curso
            $user->followedCourses()->attach($course->id);
        }

        return back()->with('success', 'Você está seguindo este curso');
    }

    public function unfollow(Course $course){
        $user = auth()->user();
        
        if($user->followedCourses->contains($course->id)){ //Verifica se o usuário segue o curso
            $user->followedCourses()->detach($course->id); //Desvincula o usuário do curso
        }

        return back()->with('success', ' Vocé deixou de seguir este curso');
    }
}
