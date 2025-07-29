<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Course;

class CourseFollowController extends Controller
{
    //Seguir um Curso
    public function follow(Course $courses){
        $user = Auth()->user();
        if(!$user->followedCourses->contains($course->id)){
            $user->followedCourses()->attach($course->id);
        }
    }
}
