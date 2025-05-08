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

    public $locations = [];

    public $houses = [];
    public $current_house = [];
    // public $house;
    public $state = 0;

    ###__LOCATIONS
    function refreshThisAgencyLocations()
    {
        $locations = $this->current_agency->_Locations->where("status", "!=", 3)->filter(function ($location) {
            return $location->Room?$location->Room->water:null;
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


            $location["house_name"] = $location->House->name;
            $location["start_index"] = count($location->ElectricityFactures) != 0 ? $location->ElectricityFactures->first()->end_index : ($location->Room?$location->Room->electricity_counter_start_index:null);
            // $location["end_index"] = $location->end_index;
            $location["locataire"] = $location->Locataire->name ." ". $location->Locataire->prenom;
            $location["water_factures"] = $location->WaterFactures;
            $location["water_factures_states"] = $location->House->WaterFacturesStates;
            $location["lastFacture"] = $location->WaterFactures()?$location->WaterFactures()->first():null;

            array_push($agency_locations, $location);
        }

        ####___
        $this->locations = $agency_locations;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $_houses = $this->current_agency->_Locations->map(function ($location) {
            if ($location->Room) {
                if ($location->Room->water) {
                    return $location->House;
                }
            }

        });
        $this->houses = collect($_houses)->unique()->values();
    }


    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        $this->refreshThisAgencyLocations();
        $this->refreshThisAgencyHouses();
    }

    public function render()
    {
        return view('livewire.eau-location');
    }
}
