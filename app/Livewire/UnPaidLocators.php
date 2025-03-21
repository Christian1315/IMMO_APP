<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Str;

use Livewire\Component;

class UnPaidLocators extends Component
{
    public $current_agency;
    public $agency;

    public $locators_old = [];
    public $locators = [];
    public $locators_count = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $generalError = "";
    public $generalSuccess = "";

    public $supervisors = [];
    public $supervisor;

    public $houses = [];
    public $house;

    public $display_filtre_options = [];

    public $filtre_by_supervisor = [];
    public $filtre_by_house = [];

    public $search = "";
    public $show_form = false;

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


    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }


    function refreshThisAgencyLocators()
    {
        $user = request()->user();
        $agency = Agency::find($this->current_agency->id);
        if (!$agency) {
            return self::sendError("Cette agence n'existe pas!", 404);
        }

        ###___
        $locataires = [];
        ###____
        $locations = $agency->_Locations;
        $now = strtotime(date("Y/m/d", strtotime(now())));

        foreach ($locations as $location) {
            ###__la location
            // $location_previous_echeance_date = strtotime(date("Y/m/d", strtotime($location->previous_echeance_date)));
            $location_echeance_date = strtotime(date("Y/m/d", strtotime($location->echeance_date)));
            ###__derniere facture de la location
            // $location_previous_echeance_date = strtotime(date("Y/m/d", strtotime($location->previous_echeance_date)));

            // $last_facture = $location->Factures->last();
            // if ($last_facture) {
            //     $last_facture_created_date = strtotime(date("Y/m/d", strtotime($last_facture->created_at)));
            //     $last_facture_echeance_date = strtotime(date("Y/m/d", strtotime($last_facture->echeance_date)));

            //     ###__si la date de payement de la dernière facture de la location
            //     ####___est suppérieure à la date d'écheance de la location,
            //     ###___alors ce locataire est en impayé

            //     // $is_location_paid_before_or_after_echeance_date = $last_facture_created_date == $last_facture_echeance_date; ###__quand le paiement a été effectué avant ou après la date d'écheance 
            //     // $is_location_paid_after_echeance_date = $last_facture_created_date < $location_previous_echeance_date; ###_

            //     // s'il n'a pas payé après la date d'echéance de la location
            //     if ($last_facture_created_date < $location_previous_echeance_date) {
            //         array_push($locataires, $location);
            //     }
            // } else {
            //     ###___s'il n'a même pas de facture,
            //     ##__on verifie si on a depassse sa previous date d'echeance
            //     if ($location->previous_echeance_date < $now) {
            //         array_push($locataires, $location);
            //     }
            // }

            if ($location_echeance_date < $now) {
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
        ###___SUPERVISORS
        $this->refreshSupervisors();
        ###___HOUSES
        $this->refreshThisAgencyHouses();
    }

    public function render()
    {
        return view('livewire.un-paid-locators');
    }
}
