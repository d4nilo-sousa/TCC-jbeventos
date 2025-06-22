<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('courseCoordinator')->get();
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coordinators = Coordinator::all();
        return view('admin.courses.create', compact('coordinators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|unique:courses,course_name|max:255',
            'course_description' => 'nullable|string|max:1000',
            'course_icon' => 'nullable|image|max:2048',
            'course_banner' => 'nullable|image|max:2048',
            'coordinator_id' => 'nullable|exists:coordinators,id',
        ]);


        $data = $request->only(['course_name', 'course_description', 'coordinator_id']);

        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

        Course::create($data);
        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::with('courseCoordinator')->findOrFail($id);
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        $coordinators = Coordinator::all();
        return view('admin.courses.edit', compact('course', 'coordinators'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'course_name' => 'required|unique:courses,course_name,' . $id . '|max:255',
            'course_description' => 'nullable|string|max:1000',
            'course_icon' => 'nullable|image|max:2048',
            'course_banner' => 'nullable|image|max:2048',
            'coordinator_id' => 'nullable|exists:coordinators,id',
        ]);

        $data = $request->only(['course_name', 'course_description', 'coordinator_id']);

        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

       $course->update($data);

       return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);

        if ($course->course_icon) {
            Storage::disk('public')->delete($course->course_icon);
        }
        if ($course->course_banner) {
            Storage::disk('public')->delete($course->course_banner);
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso');
    }
}
