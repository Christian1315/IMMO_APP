<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\City;
use App\Models\Country;
use App\Models\Departement;
use App\Models\House as ModelsHouse;
use App\Models\HouseType;
use App\Models\Quarter;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;


class House extends Component
{
    use WithFileUploads;

    public $agency;
    public $current_agency;
    public $houses = [];
    public $currentHouseId = null;
    public $houses_count = [];

    // 
    public $countries = [];
    public $proprietors = [];
    public $cities = [];
    public $house_types = [];
    public $departements = [];
    public $quartiers = [];
    public $zones = [];
    
    ###___PROPRIETORS
    function refreshThisAgencyProprietors()
    {
        $this->proprietors = $this->current_agency->_Proprietors;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $title = 'Suppression d\'une maison!';
        $text = "Voulez-vous vraiment supprimer cette maison?";
        confirmDelete($title, $text);

        $this->houses = $this->current_agency->_Houses;
        $this->houses_count = count($this->houses);
    }

    // COUNTRIES
    function refreshCountries()
    {
        $countries = Country::all();
        $this->countries = $countries;
    }

    // COUNTRIES
    function refreshCities()
    {
        $cities = City::all();
        $this->cities = $cities;
    }

    // TYPES DE MAISON
    function refreshTypes()
    {
        $house_types = HouseType::all();
        $this->house_types = $house_types;
    }

    // REFRESH DEPARTEMENT
    function refreshDepartements()
    {
        $departements = Departement::all();
        $this->departements = $departements;
    }

    // QUARTIERS
    function refreshQuartiers()
    {
        $quartiers = Quarter::all();
        $this->quartiers = $quartiers;
    }

    // REFRESH ZONE
    function refreshZones()
    {
        $zones = Zone::all();
        $this->zones = $zones;
    }

    function mount($agency)
    {
        $this->current_agency = $agency;
        ###___PROPRIETORS
        $this->refreshThisAgencyProprietors();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // PAYS
        $this->refreshCountries();

        // CITIES
        $this->refreshCities();

        // HOUSES TYPES
        $this->refreshTypes();

        // DEPARTEMENTS
        $this->refreshDepartements();

        // QUARTIER
        $this->refreshQuartiers();

        // ZONE
        $this->refreshZones();
    }

    public function render()
    {
        return view('livewire.house');
    }
}
