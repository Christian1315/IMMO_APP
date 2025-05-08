<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\CardType;
use App\Models\City;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithFileUploads;

class Proprietor extends Component
{
    use WithFileUploads;
    public $current_agency;

    public $proprietors = [];
    public $proprietors_count = [];

    // 
    public $countries = [];
    public $cities = [];
    public $card_types = [];

    
    public $show_form = false;
    public $click_count = 2;

    function refreshThisAgencyProprietors()
    {
        ###___PROPRIETORS
        $agency = Agency::findOrFail($this->current_agency['id']);

        $this->proprietors = $agency->_Proprietors;
        $this->proprietors_count = count($agency->_Proprietors);
    }

    public function mount($agency)
    {
        $this->current_agency = $agency;

        ###___PROPRIETORS
        $this->refreshThisAgencyProprietors();

        // PAYS
        $countries = Country::all();
        $this->countries = $countries;

        // CITIES
        $cities = City::all();
        $this->cities = $cities;

        // CARD TYPES
        $card_types = CardType::all();
        $this->card_types = $card_types;
    }

    public function render()
    {
        return view('livewire.proprietor');
    }
}
