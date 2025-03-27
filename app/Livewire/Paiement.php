<?php

namespace App\Livewire;

use App\Models\Facture;
use App\Models\PaiementStatus;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class Paiement extends Component
{
    use WithFileUploads;
    public $current_agency;

    public $Houses = [];
    public $Houses_count = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $Houses_solds = [];
    public $current_paiement = [];
    public $proprietors = [];
    public $Houses_status = [];

    public $show_paiement_form;
    public $show_traitPaiement_form = false;

    // TRAITEMENT DES ERREURS
    public $status_error = "";

    public $generalError = "";
    public $generalSucess = "";

    public $currentHouse = [];

    public $sold = '';
    public $sold_error = '';

    public $proprietor = 0;
    public $proprietor_error = '';
    public $showCautions = false;
    public $house_state_html_url;
    public $current_state = null;

    public $total_amount_recovery = 0;

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        // Houses
        $this->refreshHouses();
    }

    function refreshHouses()
    {
        ####_____
        $this->Houses = $this->current_agency->_Houses;

        ######______
        foreach ($this->Houses as $house) {
            GET_HOUSE_DETAIL_FOR_THE_LAST_STATE($house);
        }
    }

    function refresStatus()
    {
        $Houses_status = PaiementStatus::all();
        $this->Houses_status = $Houses_status;
    }

    function refresCurrentHouse($houseId)
    {
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/house/$houseId/retrieve")->json();
        if (!$response["status"]) {
            $this->currentHouse = [];
        } else {
            $this->currentHouse = $response["data"];
        }
    }

    function refresh($message)
    {
        set_time_limit(0);

        $this->generalSucess = $message;
        $this->show_paiement_form = false;

        // paiement
        $this->refreshHouses();

        // STATUS
        $this->refresStatus();
    }

    function showPaiementForm($id = null, $state = null, $total_amount_recovery = null)
    {
        if ($this->show_paiement_form) {
            $this->show_paiement_form = false;
        } else {
            $this->show_paiement_form = true;
        }

        if ($id) {
            $this->refresCurrentHouse($id);
        }

        if ($state) {
            $this->current_state = $state;
        }

        if ($total_amount_recovery) {
            $this->total_amount_recovery = $total_amount_recovery;
        }

        $this->sold = $this->total_amount_recovery;
    }

    function Initiate_Sold()
    {
        set_time_limit(0);

        $data = [
            "agency" => $this->current_agency["id"],
            "proprietor" => $this->currentHouse["proprietor"]["id"],
            "amount" => $this->sold,

            "house" => $this->currentHouse["id"],
            "state" => $this->current_state,
        ];

        ###____
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/payement_initiation/initiateToProprio", $data)->json();

        ####___________
        if (!$response["status"]) {
            $errors = $response["erros"];
            if (gettype($errors) == "array") {

                if (array_key_exists("amount", $errors)) {
                    $this->sold_error = $errors["amount"][0];
                }

                if (array_key_exists("proprietor", $errors)) {
                    $this->proprietor_error = $errors["proprietor"][0];
                }

                if (array_key_exists("agency", $errors)) {
                    $this->generalError = $errors["agency"][0];
                }
            } else {
                $this->generalError = $errors;
            }
        } else {
            $this->refresh($response["message"]);
        }
    }

    function ImprimeHouseStates($houseId)
    {
        set_time_limit(0);
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/house/$houseId/imprime_last_state")->json();
        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayer plus tard";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->showCautions = true;
                $this->house_state_html_url = $response["data"]["house_state_html_url"];
            }
        }
    }

    public function render()
    {
        return view('livewire.paiement');
    }
}
