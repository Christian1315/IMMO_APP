<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Filtrage extends Component
{
    public $agency;

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $proprietors = [];
    public $locators = [];
    public $locations = [];
    public $rooms = [];
    public $houses = [];
    public $supervisors = [];

    public $factures = [];
    public $factures_total_amount = [];
    public $show_factures = false;

    public $show_moved_locators = false;
    public $moved_locators = [];

    public $start_date = "";
    public $end_date = "";

    public $generaleSuccess = "";
    public $generalError = "";

    public $showTaux = false;
    public $display_taux_options = false;

    public $generate_caution_by_supervisor = false;
    public $generate_taux_by_supervisor = false;
    public $generate_taux_by_house = false;

    public $supervisor = [];
    public $supervisor_error = '';

    // REFRESH SUPERVISOR
    function refreshSupervisors()
    {
        $users = User::with(["account_agents"])->get();
        $supervisors = [];

        foreach ($users as $user) {
            $user_roles = $user->_roles; ##recuperation des roles de ce user

            foreach ($user_roles as $user_role) {
                if ($user_role->id == env("SUPERVISOR_ROLE_ID")) {
                    array_push($supervisors, $user);
                }
            }
        }
        $this->supervisors = array_unique($supervisors);
    }

    function mount($agency)
    {
        set_time_limit(0);

        $this->agency = $agency;

        $this->refreshThisAgencyBilan();
        $this->refreshSupervisors();
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
