<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Livewire\Component;
use Livewire\WithFileUploads;

class EauLocation extends Component
{
    use WithFileUploads;

    public $current_agency;

    public $old_locations = [];
    public $locations = [];

    public $houses = [];
    public $current_house = [];
    public $house;
    public $houseId = 0;
    public $state = 0;

    public $houseStates = [];
    public $currentHouseState = 0;


    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $current_location = [];
    public $activeLocationId;

    public $currentLocationFactures = [];

    public $end_index = "";
    public $end_index_error = "";
    public $location = "";
    public $location_error = "";

    public $generalError = "";
    public $generalSuccess = "";

    public $show_form = false;
    public $show_factures = false;
    public $show_house_for_state_imprime_form = false;
    public $show_state_imprime_form = false;
    public $show_state_imprime = false;

    public $showHouseFom = false;
    public $actualized = false;

    public $state_html_url = "";

    public $locators = [];
    public $locator = [];

    public $supervisors = [];

    public $filtre_by_house = false;
    public $filtre_by_locator = false;
    public $display_filtre_options = false;

    public $forLocation = false;
    public $search = "";

    ####____superviseurs
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

    ###__LOCATIONS
    function refreshThisAgencyLocations()
    {
        $locations = $this->current_agency->_Locations->where("status", "!=", 3)->filter(function ($location) {
            return $location->Room->water;
        });
        ##___
        $agency_locations = [];

        foreach ($locations as $location) {
            if (count($location->WaterFactures) != 0) {
                $latest_facture = $location->WaterFactures[0]; ##__dernier facture de cette location

                ##___Cette variable determine si la derniere facture est pour un arrêt de state

                $is_latest_facture_a_state_facture = false;
                if ($latest_facture->state_facture) {
                    $is_latest_facture_a_state_facture = true; ###__la derniere facture est pour un arrêt de state
                }

                ###___l'index de fin de cette location revient à l'index de fin de sa dernière facture
                $location["end_index"] = $latest_facture->end_index;

                ###___le montant actuel à payer pour cette location revient au montant de sa dernière facture
                ###__quand la dernière facture est payée, le current_amount devient 0 
                $location["current_amount"] = $latest_facture["paid"] ? 0 : $latest_facture["amount"];

                #####______montant payé
                $paid_factures_array = [];

                ###__determinons les arrièrees
                $unpaid_factures_array = [];
                $nbr_unpaid_factures_array = [];
                $total_factures_to_pay_array = [];

                foreach ($location->WaterFactures as $facture) {

                    ###__on recupere toutes les factures sauf la dernière(correspondante à l'arrêt d'état)
                    if ($facture["id"] != $latest_facture["id"]) {
                        ###__on recupere les factures non payés
                        if (!$facture["paid"]) {
                            if (!$facture->state_facture) { ##sauf la dernière(correspondante à l'arrêt d'état)
                                array_push($unpaid_factures_array, $facture["amount"]);
                                array_push($nbr_unpaid_factures_array, $facture);
                            }
                        }
                    }

                    ###__on recupere les factures  payées
                    if ($facture->paid) {
                        array_push($paid_factures_array, $facture["amount"]);
                    }
                    ###____
                    array_push($total_factures_to_pay_array, $facture["amount"]);
                }

                ###__Nbr d'arrieres
                $location["nbr_un_paid_facture_amount"] = $is_latest_facture_a_state_facture ? 0 : count($nbr_unpaid_factures_array);
                ###__Montant d'arrieres
                $location["un_paid_facture_amount"] = $is_latest_facture_a_state_facture ? 0 : array_sum($unpaid_factures_array);

                ###__Montant payés
                $location["paid_facture_amount"] = $is_latest_facture_a_state_facture ? 0 : array_sum($paid_factures_array);

                ##__total amount to paid
                $location["total_un_paid_facture_amount"] = $is_latest_facture_a_state_facture ? 0 : array_sum($total_factures_to_pay_array);

                ###__Montant dû
                $location["rest_facture_amount"] = $location["total_un_paid_facture_amount"] - $location["paid_facture_amount"];
            } else {
                ###___l'index de fin de cette location revient à l'index de fin de sa dernière facture
                $location["end_index"] = 0;

                ###___le montant actuel à payer pour cette location revient montant de sa dernière facture
                ###__quand la dernière facture est payée, le current_amount devient 0 
                $location["current_amount"] =  0;

                ###__Nbr d'arrieres
                $location["nbr_un_paid_facture_amount"] = 0;

                ###__Montant d'arrieres
                $location["un_paid_facture_amount"] = 0;

                ###___
                $location["water_factures"] = [];

                ###__Montant payés
                $location["paid_facture_amount"] = 0;

                ##__total amount to paid
                $location["total_un_paid_facture_amount"] = 0;

                ###__Montant dû
                $location["rest_facture_amount"] = 0;
            }
            // $location["factures"] = $location_factures;
            array_push($agency_locations, $location);
        }

        ####___
        $this->locations = $agency_locations;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Locations->map(function ($location) {
            if ($location->Room->water) {
                return $location->House;
            }
        });
        $this->houses = collect($this->houses)->unique()->values();
    }


    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        $this->refreshThisAgencyLocations();
        $this->refreshThisAgencyHouses();
        $this->refreshSupervisors();
    }

    public function render()
    {
        return view('livewire.eau-location');
    }
}
