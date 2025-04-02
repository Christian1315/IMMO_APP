<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\CardType;
use App\Models\Country;
use App\Models\Departement;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Livewire\Component;
use Livewire\WithFileUploads;

class Locator extends Component
{
    use WithFileUploads;

    public $current_agency;
    public $agency;

    public $locators = [];
    public $old_locators = [];
    public $locators_count = [];

    public $card_types = [];
    public $countries = [];
    public $departements = [];


    public $proprietors = [];
    public $houses = [];
    public $house;

    public $cities = [];
    public $locator_types = [];
    public $locator_natures = [];
    public $quartiers = [];
    public $zones = [];
    public $supervisors = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];


    public $locator_houses = [];
    public $locator_rooms = [];
    public $current_locator = [];
    public $current_locator_boolean = false;
    public $current_locator_for_room = [];


    public $display_locators_options = false;
    public $show_locators_by_supervisor = false;
    public $show_locators_by_house = false;


    function displayLocatorsOptions()
    {
        if ($this->display_locators_options) {
            $this->display_locators_options = false;
        } else {
            $this->display_locators_options = true;
        }
        $this->show_locators_by_house = false;
        $this->show_locators_by_supervisor = false;
    }

    function refreshThisAgencyLocators()
    {
        $title = 'Suppression de locataire';
        $text = "Voullez-vous vraiment supprimer ce locataire";
        confirmDelete($title, $text);

        ###___LOCATORS
        $agency_locators = $this->current_agency->_Locataires;

        ##___
        $this->locators_count = count($agency_locators);
        $this->locators = $agency_locators;
        $this->old_locators = $agency_locators;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }

    function mount($agency)
    {
        $this->current_agency = $agency;

        ###___LOCATORS
        $this->refreshThisAgencyLocators();

        ###____HOUSE AGENCY
        $this->refreshThisAgencyHouses();

        // CARD TYPES
        $card_types = CardType::all();
        $this->card_types = $card_types;

        // PAYS
        $countries = Country::all();
        $this->countries = $countries;

        // DEPARTEMENTS
        $departements = Departement::all();
        $this->departements = $departements;
    }

    public function render()
    {
        return view('livewire.locator');
    }
    
}
