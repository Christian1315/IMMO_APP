<?php

namespace App\Livewire;

use Livewire\Component;

class Filtrage extends Component
{
    public $agency;

    public $proprietors = [];
    public $locators = [];
    public $locations = [];
    public $rooms = [];
    public $houses = [];
    public $factures = [];

    public $moved_locators = [];

    public $factures_total_amount = 0;

    function mount($agency)
    {
        set_time_limit(0);

        $this->agency = $agency;

        $this->refreshThisAgencyBilan();
    }

    ###___HOUSES
    function refreshThisAgencyBilan()
    {
        $locations = [];
        $locators = [];
        $moved_locators = [];
        $factures = [];
        $rooms = [];
        $factures_total_amount = [];

        foreach ($this->agency->_Houses as $house) {
            foreach ($house->Locations as $location) {
                array_push($locations, $location);
                array_push($locators, $location->Locataire);
                array_push($rooms, $location->Room);

                ###___recuperons les locataires demenagés
                if ($location["move_date"]) {
                    array_push($moved_locators, $location->Locataire);
                }

                foreach ($location->AllFactures->where("state_facture",false) as $facture) {
                    array_push($factures, $facture);
                    array_push($factures_total_amount, $facture["amount"]);
                }
            }
        }

        ####___
        $this->proprietors = $this->agency->_Proprietors;
        $this->houses = $this->agency->_Houses;
        $this->locators  = $this->agency->_Locataires;
        $this->locations  = collect($locations);
        $this->rooms  = $rooms;
        $agency["moved_locators"] = $moved_locators;
        $this->factures = $factures;
        $agency["rooms"] = $rooms;
        $this->factures_total_amount = $factures_total_amount;
    }
    ###____

    public function render()
    {
        return view('livewire.filtrage');
    }
}
