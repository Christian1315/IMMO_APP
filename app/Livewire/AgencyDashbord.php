<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\Room;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AgencyDashbord extends Component
{
    public $agency;
    public $current_agency;

    public $proprietors_count = 0;
    public $houses_count = 0;
    public $locators_count = 0;
    public $locations_count = 0;
    public $rooms_count = 0;
    public $paiement_count = 0;
    public $factures_count = 0;
    public $accountSold_count = 0;
    public $initiation_count = 0;

    function mount($agency)
    {
        set_time_limit(0);

        $this->current_agency = $agency;

        $agency = Agency::find($this->current_agency['id']);

        if (!$agency) {
            return redirect()->back()->with("error", "Cette agence n'existe pas!");
        }
        ###___PROPRIETORS
        $this->proprietors_count = count($agency->_Proprietors);

        ###__TRIONS CEUX QUI SE TROUVENT DANS L'AGENCE ACTUELLE
        ##__on recupere les maisons qui appartiennent aux propriétaires
        ##__ se trouvant dans cette agence

        $agency_houses = []; ###___HOUSES
        $agency_factures = [];
        $agency_paiements = [];
        $agency_locators = $agency->_Locataires; ###___LOCATORS
        $agency_locations = $agency->_Locations; ###___LOCATIONS

        foreach ($agency->_Proprietors as $proprio) {
            $proprio_houses = $proprio->houses;
            foreach ($proprio_houses as $house) {
                array_push($agency_houses, $house);
            }
        }

        foreach ($agency->_Locations as $location) {
            // les factures
            foreach ($location->Factures as $facture) {
                array_push($agency_factures, $facture);
            }

            // paiements
            foreach ($location->Paiements as $paiement) {
                array_push($agency_paiements, $paiement);
            }
        }

        $this->houses_count = count($agency_houses);
        $this->locators_count = count($agency_locators);
        $this->locations_count = count($agency_locations);
        $this->factures_count = count($agency_factures);
        $this->paiement_count = $this->houses_count;

        // ROOMS
        $rooms = Room::all();
        $agency_rooms = [];

        foreach ($agency->_Proprietors as $proprio) {
            $proprio_houses = $proprio->houses;
            foreach ($proprio_houses as $house) {
                array_push($agency_houses, $house);

                $rooms = $house->rooms;
                foreach ($rooms as $room) {
                    array_push($agency_rooms, $room);
                }
            }
        }

        $this->rooms_count = count($agency_rooms);
    }

    public function render()
    {
        return view('livewire.agency-dashbord');
    }
}
