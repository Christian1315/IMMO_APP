<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Supervisor extends Component
{
    use WithFileUploads;
    public $compteAgents = [];
    public function mount()
    {
        $this->compteAgents = User::get()->filter(function ($user) {
            return $user->hasRole("Gestionnaire de compte");
        });
    }

    public function render()
    {
        return view('livewire.supervisor');
    }
}
