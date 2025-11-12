<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class, // Seeder para as Categorias de Evento
            AdminSeeder::class, // Seeder para os Admins
            CoordinatorSeeder::class, // Seeder para os Coordenadores
            CourseSeeder::class, // Seeder para os Cursos
            CourseFollowerSeeder::class, // Seeder para os Seguidores dos Cursos
            EventSeeder::class, // Seeder para os Eventos
            PostSeeder::class, // Seeder para os Posts
            EventReactionSeeder::class, // Seeder para as Reações dos Eventos
            ReplySeeder::class, // Seeder para as Respostas aos Posts
            EventCommentSeeder::class, // Seeder para os Comentários dos Eventos
        ]);
    }
}
