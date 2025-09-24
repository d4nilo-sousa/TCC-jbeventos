<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | LISTAGEM E DETALHES
     |--------------------------------------------------------------------------
     */
    public function index(Request $request)
    {
        $courses = Course::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $courses->where('course_name', 'like', "%{$search}%");
        }

        $courses = $courses->get();

        return view('courses.index', compact('courses'));
    }



    public function create()
    {
        // Busca todos os coordenadores para preencher o select no formulário de criação do curso
        $coordinators = Coordinator::all();
        return view('admin.courses.create', compact('coordinators'));
    }

    /*
     |--------------------------------------------------------------------------
     | CRUD ADMINISTRATIVO
     |--------------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        // Validação dos dados enviados no formulário de criação
        $request->validate([
            'course_name' => 'required|unique:courses,course_name|max:255',
            'course_description' => 'nullable|string|max:1000',
            'course_icon' => 'nullable|image|max:2048', // Imagem opcional, até 2MB
            'course_banner' => 'nullable|image|max:2048', // Imagem opcional, até 2MB
            'coordinator_id' => 'nullable|exists:coordinators,id', // Verifica se coordenador existe
        ]);

        // Pega apenas os campos necessários para salvar no banco
        $data = $request->only(['course_name', 'course_description', 'coordinator_id']);

        // Se um ícone for enviado, armazena na pasta 'course_icons' no disco público
        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        // Se um banner for enviado, armazena na pasta 'course_banners' no disco público
        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

        // Adiciona o ID do admin logado ao curso criado
        $data['user_id'] = auth()->id();

        // Cria o curso com os dados validados e arquivos armazenados
        Course::create($data);

        // Redireciona para a lista de cursos com mensagem de sucesso
        return redirect()->route('courses.index')->with('success', 'Curso criado com sucesso');
    }

    public function show(string $id)
    {
        // Busca o curso pelo ID junto com o coordenador; se não existir, retorna erro 404
        $course = Course::with('courseCoordinator', 'events')->findOrFail($id);
        return view('courses.show', compact('course'));
    }

    public function edit(string $id)
    {
        // Busca o curso para edição e todos os coordenadores para o select do formulário
        $course = Course::findOrFail($id);
        $coordinators = Coordinator::all();
        return view('admin.courses.edit', compact('course', 'coordinators'));
    }

    public function update(Request $request, string $id)
    {
        // Busca o curso a ser atualizado
        $course = Course::findOrFail($id);

        // Validação semelhante à criação, mas ignora o próprio registro na regra unique
        $request->validate([
            'course_name' => 'required|unique:courses,course_name,' . $id . '|max:255',
            'course_description' => 'nullable|string|max:1000',
            'course_icon' => 'nullable|image|max:2048',
            'course_banner' => 'nullable|image|max:2048',
            'coordinator_id' => 'nullable|exists:coordinators,id',
        ]);

        // Pega os campos para atualizar
        $data = $request->only(['course_name', 'course_description', 'coordinator_id']);

        // Se novo ícone for enviado, armazena e atualiza a referência
        if ($request->hasFile('course_icon')) {
            $data['course_icon'] = $request->file('course_icon')->store('course_icons', 'public');
        }

        // Se novo banner for enviado, armazena e atualiza a referência
        if ($request->hasFile('course_banner')) {
            $data['course_banner'] = $request->file('course_banner')->store('course_banners', 'public');
        }

        // Atualiza o curso com os novos dados
        $course->update($data);

        // Redireciona para a lista com mensagem de sucesso
        return redirect()->route('courses.index')->with('success', 'Curso atualizado com sucesso');
    }

    public function destroy(string $id)
    {
        // Busca o curso para exclusão
        $course = Course::findOrFail($id);

        // Deleta os arquivos de imagem do storage se existirem para evitar lixo no servidor
        if ($course->course_icon) {
            Storage::disk('public')->delete($course->course_icon);
        }
        if ($course->course_banner) {
            Storage::disk('public')->delete($course->course_banner);
        }

        // Deleta o registro do curso no banco
        $course->delete();

        // Redireciona para a lista com mensagem de sucesso
        return redirect()->route('courses.index')->with('success', 'Curso excluído com sucesso');
    }


    /*
     |--------------------------------------------------------------------------
     | ATUALIZAÇÕES RÁPIDAS (BANNER, ÍCONE, DESCRIÇÃO)
     | Usadas na view estilo "perfil"
     |--------------------------------------------------------------------------
     */
    public function updateBanner(Request $request, Course $course)
    {
        $request->validate([
            'course_banner' => 'nullable|image|max:2048',
        ]);

        //apaga o banner antigo
        if ($course->course_banner) {
            Storage::disk('public')->delete($course->course_banner); // Armazena o arquivo no disco publico e deleta
        }

        $path = $request->file('course_banner')->store('course_banners', 'public'); // Armazena o arquivo no disco publico
        $course->update(['course_banner' => $path]); // Atualiza o caminho do arquivo no banco

        return back()->with('success', 'Banner atualizado com sucesso'); // Redireciona para a mesma rota com uma mensagem de sucesso
    }

    public function updateIcon(Request $request, Course $course)
    {

        $request->validate([
            'course_icon' => 'nullable|image|max:2048',
        ]);

        if ($course->course_icon) {
            Storage::disk('public')->delete($course->course_icon);
        }

        $path = $request->file('course_icon')->store('course_icons', 'public'); // Armazena o arquivo no disco publico
        $course->update(['course_icon' => $path]); // Atualiza o caminho do arquivo no banco

        return back()->with('success', 'Ícone atualizado com sucesso'); // Redireciona para a mesma rota com uma mensagem de sucesso
    }

    public function updateDescription(Request $request, Course $course)
    {
        $request->validate([
            'course_description' => 'nullable|string|max:1000',
        ]);

        $course->update(['course_description' => $request->course_description]);

        return back()->with('success', 'Descrição atualizada com sucesso'); // Redireciona para a mesma rota com uma mensagem de sucesso
    }
}
