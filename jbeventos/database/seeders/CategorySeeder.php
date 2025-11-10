<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Executa o seeder para popular a tabela de categorias.
     */
    public function run(): void
    {
        // Lista de categorias para popular a tabela 'categories'
        // 'Inovação' foi listada duas vezes, será cadastrada apenas uma vez.
        $categories = [
            'Cultural',
            'Tecnológico',
            'Profissionalizante',
            'Educacional',
            'Esportivo',
            'Meio Ambiente',
            'Inovação',
            'Empreendedorismo',
            'Arte',
            'Musical',
            'Integração de Cursos',
        ];

        // Cria cada categoria no banco de dados, verificando se já existe para evitar erros
        foreach ($categories as $categoryName) {
            Category::firstOrCreate([
                'category_name' => $categoryName
            ]);
        }
    }
}