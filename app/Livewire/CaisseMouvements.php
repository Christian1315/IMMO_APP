<?php

namespace App\Livewire;

use App\Models\AgencyAccount;
use App\Models\AgencyAccountSold;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class CaisseMouvements extends Component
{
    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $agency;
    public $agency_account;
    public $agencyAccountsSolds = [];
    public $Account = [];

    function refreshAgencyAccountSolds()
    {
        // $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/account/sold/" . $this->agency_account . "/agency_mouvements")->json();

        $agencyAccount = AgencyAccount::with(["_Account"])->find($this->agency_account);
        if (!$agencyAccount) {
            alert()->error("Echec","Désolé! Cette caisse n'existe pas!");
            return back();
        }

        ###
        $agency_account_mouvements = AgencyAccountSold::with(["_Account", "WaterFacture", "House", "WaterFacture"])->where(["agency_account" => $this->agency_account])->orderBy("visible",'asc')->get();

        $this->agencyAccountsSolds = $agency_account_mouvements;
        $this->Account = $agencyAccount->_Account;
    }

    function mount($agency, $agency_account)
    {
        $this->agency = $agency;

        $this->agency = $agency;
        $this->agency_account = $agency_account;

        $this->refreshAgencyAccountSolds();
    }

    public function render()
    {
        return view('livewire.caisse-mouvements');
    }
}
