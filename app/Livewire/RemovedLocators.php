<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Str;

use Livewire\Component;

class RemovedLocators extends Component
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
            $user_roles = $user->roles; ##recuperation des roles de ce user

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
        // $locataires = [];
        ###____
        $locataires = $agency->_Locations->where("status", 3)->filter(function ($query) {
            return $query->Locataire;
        });
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
        return view('livewire.removed-locators');
    }
}
