<?php

namespace App\Livewire;

use Livewire\Component;

class Paiement extends Component
{
    public $current_agency;

    public $Houses = [];

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        // Houses
        $this->refreshHouses();
    }

    function refreshHouses()
    {
        ####_____
        $this->Houses = $this->current_agency->_Houses;

        ######______
        foreach ($this->Houses as $house) {
            GET_HOUSE_DETAIL_FOR_THE_LAST_STATE($house);
        }
    }

    public function render()
    {
        return view('livewire.paiement');
    }
}
