<?php

namespace App\Livewire;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Livewire\Component;

class PaidLocators extends Component
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
    public $show_form = "";

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
        $locataires = [];
        ###____

        $locations = $this->current_agency->_Locations;
        $now = strtotime(date("Y/m/d", strtotime(now())));

        foreach ($locations as $location) {
            ###__la location
            // $location_previous_echeance_date = strtotime(date("Y/m/d", strtotime($location->previous_echeance_date)));
            $location_echeance_date = strtotime(date("Y/m/d", strtotime($location->echeance_date)));

            ###__derniere facture de la location
            // $last_facture = $location->Factures->last();
            // if ($last_facture) {
            //     $last_facture_created_date = strtotime(date("Y/m/d", strtotime($last_facture->created_at)));
            //     $last_facture_echeance_date = strtotime(date("Y/m/d", strtotime($last_facture->echeance_date)));
            //     $location_previous_echeance_date = strtotime(date("Y/m/d", strtotime($location->previous_echeance_date)));

            //     // return $location_previous_echeance_date;##1722211200
            //     // return $now;##1714435200
            //     ###__si la date de payement de la dernière facture de la location
            //     ####___est inférieure ou égale à la date d'écheance de la location,
            //     ###___alors ce locataire est à jour

            //     // $is_location_paid_before_today = $last_facture_created_date > $location_previous_echeance_date; ###__quand le paiement a été effectué avant aujourd'hui
            //     // $is_location_paid_at_echeance_date = $last_facture_created_date <= $location_previous_echeance_date; ###__quand le paiement a été effectué exactement à la date d'écheance

            //     // return $is_location_paid_at_echeance_date;
            //     if ($last_facture_created_date > $location_previous_echeance_date) {
            //         array_push($locataires, $location);
            //     }
            // } 

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
        ###___SUPERVISORS
        $this->refreshSupervisors();
        ###___HOUSES
        $this->refreshThisAgencyHouses();
    }

    function refresh($message)
    {
        set_time_limit(0);
        $this->generalSuccess = $message;

        ###___LOCATORS
        $this->refreshThisAgencyLocators();
    }

    function showForm()
    {
        if ($this->show_form) {
            $this->show_form = false;
        } else {
            $this->show_form = true;
        }
    }

    public function searching()
    {
        set_time_limit(0);

        $data = [
            "search" => $this->search
        ];

        $result = collect($this->locators)->filter(function ($locator) {
            return Str::contains(strtolower($locator['name']), strtolower($this->search));
        })->all();

        if (count($result) == 0) {
            $result = collect($this->locators)->filter(function ($locator) {
                return Str::contains(strtolower($locator['prenom']), strtolower($this->search));
            })->all();

            if (count($result) == 0) {
                $result = collect($this->locators)->filter(function ($locator) {
                    return Str::contains(strtolower($locator['phone']), strtolower($this->search));
                })->all();

                if (count($result) == 0) {
                    $result = collect($this->locators)->filter(function ($locator) {
                        return Str::contains(strtolower($locator['email']), strtolower($this->search));
                    })->all();
                }
            }
        }

        $this->locators = $result;
        $this->generalSuccess = "Résultat de votre recherche";
    }

    public function render()
    {
        return view('livewire.paid-locators');
    }
}
