<?php

namespace App\Livewire;

use Livewire\Component;

class PaidLocators extends Component
{
    public $current_agency;
    public $agency;

    public $locators_old = [];
    public $locators = [];
    public $locators_count = [];

    public $houses = [];

    public $display_filtre_options = [];

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }

    function refreshThisAgencyLocators()
    {
        $locataires = [];
        ###____

        $locations = $this->current_agency->_Locations;
        $now = strtotime(date("Y/m/d", strtotime(now())));

        foreach ($locations as $location) {
            ###__la location
            $location_echeance_date = strtotime(date("Y/m/d", strtotime($location->echeance_date)));

            if ($location_echeance_date > $now) {
                array_push($locataires, $location);
            }
        }

        ##___
        $this->locators_count = count($locataires);
        $this->locators = $locataires;
    }

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        ###___LOCATORS
        $this->refreshThisAgencyLocators();
        
        ###___HOUSES
        $this->refreshThisAgencyHouses();
    }
    
    public function render()
    {
        return view('livewire.paid-locators');
    }
}
