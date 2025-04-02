<?php

namespace App\Livewire;

use Livewire\Component;

class AgencyStatistique extends Component
{

    public $agency = [];

    public $houses = [];

    function mount($agency) {
        $this->houses = $agency->_Houses;
    }

    public function render()
    {
        return view('livewire.agency-statistique');
    }
}
