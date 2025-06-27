<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        // Lista todos os cursos com seus coordenadores
        $courses = Course::with('courseCoordinator')->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        // Carrega coordenadores para exibir no formulário de criação
        $coordinators = Coordinator::all();
        return view('admin.courses.create', compact('coordinators'));
    }

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

        // Armazena imagens se forem enviadas
        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso');
    }

    public function show(string $id)
    {
        $course = Course::with('courseCoordinator')->findOrFail($id);
        return view('courses.show', compact('course'));
    }

    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        $coordinators = Coordinator::all();
        return view('admin.courses.edit', compact('course', 'coordinators'));
    }

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

        // Substitui as imagens se novas forem enviadas
        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso');
    }

    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);

        // Remove os arquivos de imagem do storage, se existirem
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
