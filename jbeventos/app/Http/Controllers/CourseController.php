<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage;

/**
 * Class CourseController
 *
 * Controlador responsável por gerenciar cursos, incluindo listagem, criação, atualização, exclusão
 * e atualizações rápidas de banner, ícone e descrição.
 *
 * @package App\Http\Controllers
 */
class CourseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTAGEM E DETALHES
    |--------------------------------------------------------------------------
    */

    /**
     * Exibe a listagem paginada de cursos, com opção de busca por nome do curso ou nome do coordenador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $courses = Course::with(['courseCoordinator', 'events'])
            ->when($search, function ($query, $search) {
                $query->where('course_name', 'like', "%{$search}%")
                      ->orWhereHas('courseCoordinator.userAccount', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->paginate(6)
            ->appends(['search' => $search]);

        return view('courses.index', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    /**
     * Exibe o formulário para criação de um novo curso, carregando os coordenadores para seleção.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $coordinators = Coordinator::all();
        return view('admin.courses.create', compact('coordinators'));
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD ADMINISTRATIVO
    |--------------------------------------------------------------------------
    */

    /**
     * Armazena um novo curso após validação dos dados e upload dos arquivos de ícone e banner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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

        $data['user_id'] = auth()->id();

        Course::create($data);

        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso');
    }

    /**
     * Exibe os detalhes de um curso específico, incluindo coordenador e eventos relacionados.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $course = Course::with('courseCoordinator', 'events')->findOrFail($id);
        return view('courses.show', compact('course'));
    }

    /**
     * Exibe o formulário de edição de um curso, carregando o curso e os coordenadores disponíveis.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        $coordinators = Coordinator::all();
        return view('admin.courses.edit', compact('course', 'coordinators'));
    }

    /**
     * Atualiza um curso existente com novos dados validados e possíveis arquivos enviados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
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
     * Remove um curso, apagando também os arquivos de ícone e banner relacionados.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
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

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAÇÕES RÁPIDAS (BANNER, ÍCONE, DESCRIÇÃO)
    | Usadas na view estilo "perfil"
    |--------------------------------------------------------------------------
    */

    /**
     * Atualiza o banner do curso, removendo o antigo e salvando o novo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBanner(Request $request, Course $course)
    {
        $request->validate([
            'course_banner' => 'nullable|image|max:2048',
        ]);

        if ($course->course_banner) {
            Storage::disk('public')->delete($course->course_banner);
        }

        $path = $request->file('course_banner')->store('course_banners', 'public');
        $course->update(['course_banner' => $path]);

        return back()->with('success', 'Banner atualizado com sucesso');
    }

    /**
     * Atualiza o ícone do curso, removendo o antigo e salvando o novo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateIcon(Request $request, Course $course)
    {
        $request->validate([
            'course_icon' => 'nullable|image|max:2048',
        ]);

        if ($course->course_icon) {
            Storage::disk('public')->delete($course->course_icon);
        }

        $path = $request->file('course_icon')->store('course_icons', 'public');
        $course->update(['course_icon' => $path]);

        return back()->with('success', 'Ícone atualizado com sucesso');
    }

    /**
     * Atualiza a descrição do curso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDescription(Request $request, Course $course)
    {
        $request->validate([
            'course_description' => 'nullable|string|max:1000',
        ]);

        $course->update(['course_description' => $request->course_description]);

        return back()->with('success', 'Descrição atualizada com sucesso');
    }
}