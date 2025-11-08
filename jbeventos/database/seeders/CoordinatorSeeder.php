<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Coordinator;
use Illuminate\Support\Facades\Storage; // Importa Storage
use Illuminate\Support\Facades\File;    // Importa File
use Symfony\Component\HttpFoundation\File\File as SymfonyFile; // Alias para evitar conflito

class CoordinatorSeeder extends Seeder
{
    /**
     * Executa o seeder para popular a tabela de coordenadores.
     */
    public function run(): void
    {
        // 1. Configurações de Ícones
        $sourceDir = database_path('image-data/user-icons');
        $targetDisk = 'public';
        $targetDir = 'avatars'; // Pasta de destino dentro do disco 'public'
        
        // Garante que o diretório de destino existe (storage/app/public/avatars)
        Storage::disk($targetDisk)->makeDirectory($targetDir);

        // 2. Dados dos coordenadores a serem criados
        $coordinators = [
            // Coordenadores de Curso
            [
                'name' => 'Thiago',
                'email' => 'thiago@coordenador.com',
                'password' => 'thiago@123',
                'type' => 'course',
                'icon_file' => 'thiago-user-icon.png',
            ],
            [
                'name' => 'Lidiane',
                'email' => 'lidiane@coordenadora.com',
                'password' => 'lidiane@123',
                'type' => 'course',
                'icon_file' => 'lidiane-user-icon.png',
            ],
            [
                'name' => 'truzzi',
                'email' => 'truzzi@coordenadora.com',
                'password' => 'truzzi@123',
                'type' => 'course',
                'icon_file' => 'truzzi-user-icon.png',
            ],
            [
                'name' => 'Evandro',
                'email' => 'evandro@coordenador.com',
                'password' => 'evandro@123',
                'type' => 'course',
                'icon_file' => 'evandro-user-icon.png',
            ],
            [
                'name' => 'Roberta',
                'email' => 'roberta@coordenadora.com',
                'password' => 'roberta@123',
                'type' => 'course',
                'icon_file' => 'roberta-user-icon.png',
            ],
            [
                'name' => 'Eduardo',
                'email' => 'eduardo@coordenador.com',
                'password' => 'eduardo@123',
                'type' => 'course',
                'icon_file' => 'eduardo-user-icon.png',
            ],
            // Coordenador Geral
            [
                'name' => 'Paula',
                'email' => 'paula@coordenadora.com',
                'password' => 'paula@geral123',
                'type' => 'general',
                'icon_file' => 'paula-geral-user-icon.png',
            ],
        ];

        // 3. Itera sobre os dados, cria usuários e registra coordenadores
        foreach ($coordinators as $coordinatorData) {
            $iconPath = null;
            $sourceFilePath = $sourceDir . '/' . $coordinatorData['icon_file'];
            
            // Lógica para copiar o ícone
            if (File::exists($sourceFilePath)) {
                // Copia o arquivo usando putFileAs, simulando um upload
                // Nota: Usamos new SymfonyFile para criar um objeto File a partir do caminho
                Storage::disk($targetDisk)->putFileAs(
                    $targetDir, 
                    new SymfonyFile($sourceFilePath), 
                    $coordinatorData['icon_file']
                );
                
                // Salva o caminho que será usado no campo user_icon (ex: 'avatars/thiago-user-icon.png')
                $iconPath = $targetDir . '/' . $coordinatorData['icon_file'];
            }
            
            // Cria o usuário
            $user = User::create([
                'name' => $coordinatorData['name'], 
                'email' => $coordinatorData['email'], 
                'password' => Hash::make($coordinatorData['password']),
                'user_type' => 'coordinator',
                'user_icon' => $iconPath, // Salva o caminho do ícone
            ]);

            // Cria o registro de coordenador vinculado ao usuário
            Coordinator::create([
                'coordinator_type' => $coordinatorData['type'],
                'temporary_password' => false, 
                'user_id' => $user->id,
            ]);
        }
    }
}