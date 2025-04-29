<?php

namespace App\Livewire;

use App\Models\FactureStatus;
use App\Models\LocationType;
use App\Models\PaiementType;
use Livewire\Component;

class Location extends Component
{

    public $current_agency;

    public $locations = [];
    public $locations_count = 0;

    public $rooms = [];

    public $card_types = [];
    public $countries = [];
    public $departements = [];

    public $proprietors = [];
    public $houses = [];
    public $locators = [];
    public $locator_types = [];

    public $cities = [];
    public $location_types = [];
    public $location_natures = [];
    public $quartiers = [];
    public $zones = [];

    public $location_factures = [];
    public $location_rooms = [];
    public $current_location = [];
    public $current_location_for_room = [];

    public $paiements_types = [];
    public $factures_status = [];

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }

    function refreshThisAgencyLocators()
    {
        $title = 'Suppression de location';
        $text = "Voullez-vous vraiment supprimer ce locataire";
        confirmDelete($title, $text);

        ###___LOCATORS
        $agency_locators = $this->current_agency->_Locataires;

        ##___
        $this->locators = $agency_locators;
    }

    ###___ROOMS
    function refreshThisAgencyRooms()
    {
        $agency_rooms = [];

        foreach ($this->current_agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses as $house) {
                foreach ($house->Rooms as $room) {
                    array_push($agency_rooms, $room);
                }
            }
        }
        $this->rooms = $agency_rooms;
    }


    ###__LOCATIONS
    function refreshThisAgencyLocations()
    {

        $locations = $this->current_agency->_Locations;
        // dd($locations[0]);
        ##___
        $this->locations = $locations;
        $this->locations_count = count($locations);
    }

    ###___LOCATION TYPE
    function refreshLocationTypes()
    {
        $this->location_types = LocationType::all();
    }

    ###___PAIEMENT TYPE
    function refreshPaiementTypes()
    {
        $this->paiements_types = PaiementType::all();
    }

    ###___FACTURES STATUS
    function refreshFactureStatus()
    {
        $this->factures_status = FactureStatus::all();
    }

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        // LOCATIONS
        $this->refreshThisAgencyLocations();

        // ROOMS
        $this->refreshThisAgencyRooms();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // LOCATAIRES
        $this->refreshThisAgencyLocators();

        // CARD TYPES
        $this->refreshPaiementTypes();

        // LOCATION TYPES
        $this->refreshLocationTypes();

        // FACTURES STATUS
        $this->refreshFactureStatus();

    }

    public function render()
    {
        return view('livewire.location');
    }
}
