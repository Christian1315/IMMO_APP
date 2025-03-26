<?php

namespace App\Livewire;

use App\Models\House;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class Statistique extends Component
{
    use WithFileUploads;
    public $agency = [];
    public $cautions_link = "";
    public $showCautions = false;
    public $generalSuccess = false;

    public $houses = [];
    public $houses_count = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $locatorsBefore = [];
    public $locatorsAfter = [];

    public $location_locatorsBefore = [];
    public $location_locatorsAfter = [];
    public $total_locators;
    public $afterStopDateTotal_to_paid;
    public $beforeStopDateTotal_to_paid;


    public $current_houseId = [];

    public $generalError = "";

    public $show_locatorsBefore = false;
    public $show_locatorsAfter = false;

    public $currentActivelocation;
    public $currentHouse = [];

    ###___HOUSES
    function refreshThisHousesHouses()
    {
        $this->houses = House::where("visible",1)->get();
        $this->houses_count = count($this->houses);
    }

    function mount()
    {
        set_time_limit(0);

        $this->refreshThisHousesHouses();
    }

    public function render()
    {
        return view('livewire.statistique');
    }
}
