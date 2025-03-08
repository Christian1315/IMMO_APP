<?php

namespace App\Livewire;

use App\Models\House;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class HouseStopState extends Component
{
    public $agency;
    public $house = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $recovery_rapport="";

    public $generalError = "";
    public $generalSuccess = "";

    
    function refresh($message) {
        $this->generalSuccess = $message;
    }

    function mount($agency, $house)
    {
        $this->house = GET_HOUSE_DETAIL($house);
    }

    public function render()
    {
        return view('livewire.house-stop-state');
    }
}
