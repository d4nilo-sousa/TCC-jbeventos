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
        // Lista de categorias iniciais para popular a tabela 'categories'
        $categories = [
            'Teste1',
            'Teste2',
        ];

        // Cria cada categoria no banco de dados
        foreach ($categories as $categoryName) {
            Category::create([
                'category_name' => $categoryName
            ]);
        }
    }
}
