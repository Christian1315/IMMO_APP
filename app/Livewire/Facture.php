<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Facture extends Component
{
    public function render()
    {
        return view('livewire.facture');
    }
}
