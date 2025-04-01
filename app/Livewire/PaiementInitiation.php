<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class PaiementInitiation extends Component
{

    public $agency;

    public $initiations = [];
    public $initiations_count = [];

    public $current_initiation = [];
    public $proprietors = [];

    function refreshInitiations()
    {
        $agency_initiations = $this->agency->_PayementInitiations;
        ###___
        $this->initiations = $agency_initiations;
        $this->initiations_count = count($agency_initiations);
    }

    function refreshThisAgencyProprietors()
    {
        ###___PROPRIETORS
        $this->proprietors = $this->agency->_Proprietors;
    }

    function mount($agency)
    {
        set_time_limit(0);

        $this->agency = $agency;

        // initiation
        $this->refreshInitiations();

        // PROPRIETAIRES
        $this->refreshThisAgencyProprietors();
    }

    function refresh($message)
    {
        set_time_limit(0);

        // initiation
        $this->refreshInitiations();

        // PROPRIETAIRES
        $this->refreshThisAgencyProprietors();
    }

    public function render()
    {
        return view('livewire.paiement-initiation');
    }
}
