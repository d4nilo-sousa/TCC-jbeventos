<?php


namespace App\Observers;

use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Course; 

class CoordinatorObserver
{
    public function updated(Coordinator $coordinator): void
    {
        // Se o tipo foi alterado
        if ($coordinator->isDirty('coordinator_type')) {

            // Remove o coordenador dos eventos dele (limpa coordinator_id)
            Event::where('coordinator_id', $coordinator->id)
                ->update(['coordinator_id' => null]);

            // Se o tipo antigo era "course" e tinha curso associado, desvincula
            // Pega o tipo antigo
            $oldType = $coordinator->getOriginal('coordinator_type');
            $newType = $coordinator->coordinator_type;

            // Se o antigo era do tipo curso e mudou para outro tipo
            if ($oldType === 'course' && $newType !== 'course') {
                // Supondo que o curso tem coordinator_id apontando para ele
                Course::where('coordinator_id', $coordinator->id)
                    ->update(['coordinator_id' => null]);
            }
        }
    }
}
