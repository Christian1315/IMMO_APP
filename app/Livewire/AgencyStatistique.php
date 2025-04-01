<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class AgencyStatistique extends Component
{
    use WithFileUploads;

    public $agency = [];
    public $cautions_link = "";
    public $showCautions = false;
    public $generalSuccess = false;

    public $houses = [];
    public $houses_count = [];

    public function render()
    {
        return view('livewire.agency-statistique');
    }
}
