<?php

namespace App\Livewire;

use Livewire\Component;

class HouseStopState extends Component
{
    public $agency;
    public $house = [];

    function mount($agency, $house)
    {
        $this->house = GET_HOUSE_DETAIL($house);
    }

    public function render()
    {
        return view('livewire.house-stop-state');
    }
}
