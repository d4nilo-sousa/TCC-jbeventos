<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseFollowController extends Controller
{
    public function follow(Course $course)
    {
        $user = auth()->user();
        
        $user->followedCourses()->syncWithoutDetaching([$course->id]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Você está seguindo este curso.'
        ]);
    }

    public function unfollow(Course $course)
    {
        $user = auth()->user();
        
        $user->followedCourses()->detach($course->id); 

        return response()->json([
            'status' => 'success', 
            'message' => 'Você deixou de seguir este curso.'
        ]);
    }
}