<?php

namespace App\Livewire;

use App\Models\Agency as ModelsAgency;
use App\Models\City;
use App\Models\Country;
use App\Models\House;
use App\Models\Image;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Agency extends Component
{
    use WithFileUploads;

    public $agencies = [];
    public $agencies_count = [];
    public $agenciesLinks = [];
    public $showPrestations = false;

    // 
    public $countries = [];
    public $cities = [];


    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    // ADD AGENCY DATAS
    public $name = "";
    public $ifu = "";
    public $rccm = "";
    public $country = "";
    public $city = "";
    public $email = "";
    public $phone = "";
    public $rccm_file;
    public $ifu_file;

    // TRAITEMENT DES ERREURS
    public $name_error = "";
    public $ifu_error = "";
    public $rccm_error = "";
    public $country_error = "";
    public $city_error = "";
    public $email_error = "";
    public $phone_error = "";
    public $rccm_file_error = "";
    public $ifu_file_error = "";

    public $search = '';

    public $generalError = "";
    public $generalSuccess = "";


    public $showCautions = false;
    public $cautions_link = "";

    public $display_caution_options = false;

    public $generate_caution_by_periode = false;
    public $generate_caution_by_house = false;

    public $first_date = "";
    public $last_date = "";

    public $currentHouseId = null;
    public $house = "";
    public $houses = [];

    // 
    public $show_form = false;

    function mount()
    {
        set_time_limit(0);
        // $this->BASE_URL = env("BASE_URL");
        // $this->token = session()->get("token");
        // $this->userId = session()->get("userId");

        // $this->headers = [
        //     "Authorization" => "Bearer " . $this->token,
        // ];

        // HOUSES
        $this->refreshHouses();

        // PAYS
        $this->refreshCountries();

        // CITIES
        $this->refreshCities();

        // AGENCIES
        $this->refreshAgencies();

        // PAYS
        $this->refreshCountries();

        // CITIES
        $this->refreshCities();
    }

    // HOUSES
    function refreshHouses()
    {
        $houses = House::all(); # = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/house/all")->json();
        $this->houses = $houses;
    }

    function displayCautionOptions()
    {
        if ($this->display_caution_options) {
            $this->display_caution_options = false;
            $this->showCautions = false;
            $this->showPrestations = false;
            $this->generate_caution_by_periode = false;
            $this->generate_caution_by_house = false;
        } else {
            $this->display_caution_options = true;
        }
    }


    function refreshCountries()
    {
        $countries = Country::all(); ## Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/country/all")->json();
        $this->countries = $countries;
    }

    function refreshCities()
    {
        $cities = City::all(); ## Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/city/all")->json();
        $this->cities = $cities;
    }

    function refreshAgencies()
    {
        $agencies = ModelsAgency::all(); ## Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/agency/all")->json();
        $this->agencies_count = count($agencies);
        $this->agencies = $agencies;
    }

    public function render()
    {
        return view('livewire.agency');
    }
}