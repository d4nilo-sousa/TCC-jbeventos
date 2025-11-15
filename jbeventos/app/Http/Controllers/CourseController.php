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
            // Busca case-insensitive e em português
            $courses->where('course_name', 'like', "%{$search}%");
        }

        // carregando o coordenador para o course-card funcionar
        $courses = $courses->with('courseCoordinator.userAccount')->get();

        // Lógica para responder à requisição AJAX do views/js/search-highlight.js
        if ($request->ajax()) {
            // Retorna apenas a parte da view que contém os cards de curso
            return view('courses.index', compact('courses'))->render();
        }

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
        return redirect()->route('courses.show', $course->id)->with('success_course', 'Curso atualizado com sucesso!');
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
            'course_banner' => 'nullable|image|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:2048', // 2MB
        ]);

        if ($request->hasFile('course_banner')) {
            // Apaga o banner antigo
            if ($course->course_banner) {
                Storage::disk('public')->delete($course->course_banner);
            }

            // Armazena o novo banner
            $path = $request->file('course_banner')->store('course_banners', 'public');

            // Atualiza o curso
            $course->update(['course_banner' => $path]);

            // Retorna JSON para AJAX
            return response()->json([
                'success' => true,
                'banner_url' => asset('storage/' . $path),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhum arquivo enviado.',
        ], 400);
    }

    public function updateBannerColor(Request $request, Course $course)
    {
        // Validação: a cor deve ser um código hexadecimal válido
        $request->validate([
            'banner_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ], [
            'banner_color.required' => 'O campo de cor é obrigatório.',
            'banner_color.regex' => 'A cor deve ser um código hexadecimal válido (ex: #RRGGBB).',
        ]);

        $newColor = $request->input('banner_color');

        // Antes de salvar a nova cor, verificamos se existe um banner de imagem antigo.
        // Se houver, ele deve ser excluído do storage para evitar lixo no servidor,
        if ($course->course_banner && !preg_match('/^#[a-f0-9]{6}$/i', $course->course_banner)) {
            // Se o valor não for um código hexadecimal (ou seja, é um caminho de arquivo)
            Storage::disk('public')->delete($course->course_banner);
        }

        // Atualiza o curso com o novo código de cor
        $course->update(['course_banner' => $newColor]);

        // Retorna JSON para o AJAX
        return response()->json([
            'success' => true,
            'color' => $newColor,
            'message' => 'Cor do banner atualizada com sucesso!',
        ]);
    }

    public function updateIcon(Request $request, Course $course)
    {
        $request->validate([
            'course_icon' => 'nullable|image|mimetypes:image/jpeg,image/png,image/gif,image/webp|max:2048',
        ]);

        if ($request->hasFile('course_icon')) {
            // Deleta o ícone antigo, se existir
            if ($course->course_icon && Storage::disk('public')->exists($course->course_icon)) {
                Storage::disk('public')->delete($course->course_icon);
            }

            // Armazena o novo ícone
            $path = $request->file('course_icon')->store('course_icons', 'public');

            // Atualiza o registro do curso
            $course->update(['course_icon' => $path]);

            // Retorna JSON para o AJAX
            return response()->json([
                'success' => true,
                'icon_url' => asset('storage/' . $path),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhum arquivo enviado.',
        ], 400);
    }

    public function updateDescription(Request $request, Course $course)
{
    // Validação
    $request->validate([
        'course_description' => 'nullable|string|max:1000',
    ]);

    if ($request->has('course_description')) {
        // Atualiza a descrição do curso
        $course->update(['course_description' => $request->course_description]);

        // Retorna JSON para AJAX
        return response()->json([
            'success' => true,
            'course_description' => $course->course_description,
        ]);
    }

    // Caso não tenha enviado descrição
    return response()->json([
        'success' => false,
        'message' => 'Nenhuma descrição enviada.',
    ], 400);
}

}
