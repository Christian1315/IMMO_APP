<?php

namespace App\Livewire;

use App\Models\FactureStatus;
use App\Models\LocationType;
use App\Models\PaiementType;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

class Location extends Component
{
    use WithFileUploads;

    public $current_agency;

    public $locations = [];
    public $locations_count = 0;

    public $rooms = [];

    public $card_types = [];
    public $countries = [];
    public $departements = [];

    public $proprietors = [];
    public $houses = [];
    public $locators = [];
    public $locator_types = [];

    public $cities = [];
    public $location_types = [];
    public $location_natures = [];
    public $quartiers = [];
    public $zones = [];
    public $supervisors = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $location_factures = [];
    public $location_rooms = [];
    public $current_location = [];
    public $current_location_for_room = [];

    public $current_locator = [];


    public $paiements_types = [];
    public $factures_status = [];

    // ADD location DATAS
    public $house = "";
    public $room = "";
    public $locataire;
    public $type = 1;
    public $caution_bordereau;
    public $loyer;
    public $water_counter = "";
    public $electric_counter;
    public $prestation = "";
    public $numero_contrat = "";

    public $img_contrat;
    public $caution_water = "";

    public $latest_loyer_date = "";
    public $img_prestation;
    public $caution_number = "";
    public $caution_electric = "";

    public $effet_date = "";
    public $integration_date = "";
    public $comments = "";

    public $location_error;


    // TRAITEMENT DES ERREURS
    public $house_error = "";
    public $room_error = "";
    public $locataire_error = "";
    public $type_error = "";
    public $caution_bordereau_error = "";
    public $loyer_error = "";
    public $water_counter_error = "";
    public $electric_counter_error = "";
    public $prestation_error = "";
    public $numero_contrat_error = "";
    public $img_contrat_error = "";
    public $caution_water_error = "";
    public $latest_loyer_date_error = "";
    public $img_prestation_error = "";
    public $caution_number_error = "";
    public $caution_electric_error = "";

    public $integration_date_error = "";
    public $effet_date_error = "";
    public $comments_error = "";

    // 
    public $show_form = false;
    public $show_demenage_form = false;
    public $show_encaisse_form = false;
    public $show_traitFacture_form = false;

    public $showCautions = false;
    public $cautions_link = "";

    public $click_count = 2;

    public $activeLocationId;
    public $location;
    public $activeFactureId;
    public $activeFacture;

    // MOVING A LOCATION
    public $move_comments = "";
    public $move_comments_error = "";

    // ENCAISSE A LOCATION
    public $facture_code = "";
    public $encaisse_paiement_type = "";
    public $encaisse_facture;
    public $encaisse_mount = "";

    public $encaisse_paiement_type_error = "";
    public $encaisse_mount_error = "";
    public $encaisse_facture_error = "";
    public $facture_code_error = "";

    public $show_prorata_fields = false;
    public $prorata_amount;
    public $prorata_date;
    public $prorata_days;

    public $prorata_amount_error = "";
    public $prorata_date_error = "";
    public $prorata_days_error = "";

    // TRAITEMENT DES FACTURES
    public $trait_facture_status = "";
    public $trait_facture_status_error = "";

    // TYPE DE LOCATION
    public $location_type_name = "";
    public $location_type_description;

    public $location_type_name_error = "";
    public $location_type_description_error;

    public $search = '';
    public $show_facture_liste = false;

    public $generalError = "";
    public $generalSuccess = "";

    public $discounter = false;
    public $show_discounter_info = false;
    public $kilowater_price = 0;
    public $kilowater_price_error;

    public $post_paid = false;
    public $pre_paid = false;

    public $frais_peiture = "";
    public $frais_peiture_error = "";

    public $first_date = "";
    public $last_date = "";
    public $generate_prestation_by_periode = false;

    public $showPrestations = false;

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


    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }

    function refreshThisAgencyLocators()
    {
        $title = 'Suppression de location';
        $text = "Voullez-vous vraiment supprimer ce locataire";
        confirmDelete($title, $text);

        ###___LOCATORS
        $agency_locators = $this->current_agency->_Locataires;

        ##___
        $this->locators = $agency_locators;
    }

    ###___ROOMS
    function refreshThisAgencyRooms()
    {
        $agency_rooms = [];

        foreach ($this->current_agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses as $house) {
                foreach ($house->Rooms as $room) {
                    array_push($agency_rooms, $room);
                }
            }
        }
        $this->rooms = $agency_rooms;
    }


    ###__LOCATIONS
    function refreshThisAgencyLocations()
    {

        $locations = $this->current_agency->_Locations;
        ##___
        $this->locations = $locations;
        $this->locations_count = count($locations);
    }

    ###___LOCATION TYPE
    function refreshLocationTypes()
    {
        $this->location_types = LocationType::all();
    }

    ###___PAIEMENT TYPE
    function refreshPaiementTypes()
    {
        $this->paiements_types = PaiementType::all();
    }

    ###___FACTURES STATUS
    function refreshFactureStatus()
    {
        $this->factures_status = FactureStatus::all();
    }

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        // LOCATIONS
        $this->refreshThisAgencyLocations();

        // ROOMS
        $this->refreshThisAgencyRooms();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // LOCATAIRES
        $this->refreshThisAgencyLocators();

        // CARD TYPES
        $this->refreshPaiementTypes();

        // LOCATION TYPES
        $this->refreshLocationTypes();

        // FACTURES STATUS
        $this->refreshFactureStatus();

        // SUPERVISORS
        $this->refreshSupervisors();
    }

    // LOCATION TYPE ADDING
    function addLocationType()
    {
        $data = [
            "name" => $this->location_type_name,
            "description" => $this->location_type_description,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/location/type/add", $data)->json();

        // dd($response);
        if (!$response["status"]) {
            $errors = $response["erros"];
            if (gettype($errors) == "array") {
                if (array_key_exists("name", $errors)) {
                    $this->location_type_name_error = $errors["name"][0];
                }
                if (array_key_exists("description", $errors)) {
                    $this->location_type_description_error = $errors["description"][0];
                }
            } else {
                $this->generalError = $errors;
                // $this->refresh($errors);
            }
        } else {
            $this->refresh($response["message"]);
        }
    }

    public function searching()
    {
        set_time_limit(0);

        $data = [
            "search" => $this->search
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/location/search", $data)->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayez plus tard!";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
                $this->generalSuccess = "";
            } else {
                $successMsg = $response["message"];
                $this->generalSuccess = $successMsg;
                $this->generalError = "";

                $this->refreshThisAgencyLocations();
            }
        }
    }

    function refresh($message)
    {
        set_time_limit(0);

        $this->generalSuccess = $message;
        $this->show_form = false;
        $this->show_facture_liste = false;

        // NEUTRALISATION DES DATAS
        $this->house = "";
        $this->room = "";
        $this->locataire;
        $this->type = "";
        $this->caution_bordereau = "";
        $this->loyer = "";
        $this->water_counter = "";
        $this->electric_counter;
        $this->prestation = "";
        $this->numero_contrat = "";

        $this->img_contrat;
        $this->caution_water = "";

        $this->latest_loyer_date = "";
        $this->img_prestation;
        $this->caution_number = "";
        $this->caution_electric = "";

        $this->integration_date = "";
        $this->effet_date = "";
        $this->comments = "";

        $this->show_discounter_info = false;
        $this->kilowater_price = 0;
        $this->kilowater_price_error;

        $this->frais_peiture = "";
        $this->frais_peiture_error = "";

        // TRAITEMENT DES ERREURS
        $this->house_error = "";
        $this->room_error = "";
        $this->locataire_error = "";
        $this->type_error = "";
        $this->caution_bordereau_error = "";
        $this->loyer_error = "";
        $this->water_counter_error = "";
        $this->electric_counter_error = "";
        $this->prestation_error = "";
        $this->numero_contrat_error = "";
        $this->img_contrat_error = "";
        $this->caution_water_error = "";
        $this->latest_loyer_date_error = "";
        $this->img_prestation_error = "";
        $this->caution_number_error = "";
        $this->caution_electric_error = "";

        $this->effet_date_error = "";
        $this->comments_error = "";

        // MOVING A LOCATION
        $this->move_comments = "";
        $this->move_comments_error = "";

        // ENCAISSE A LOCATION
        $this->facture_code = "";
        $this->encaisse_paiement_type = "";
        $this->encaisse_facture;
        $this->encaisse_mount = "";

        $this->encaisse_paiement_type_error = "";
        $this->encaisse_mount_error = "";
        $this->encaisse_facture_error = "";
        $this->facture_code_error = "";

        // FACTURES
        $this->refreshCurrentFactures($this->activeLocationId);

        // LOCATIONS
        $this->refreshThisAgencyLocations();


        // ROOMS
        $this->refreshThisAgencyRooms();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // LOCATAIRES
        $this->refreshThisAgencyLocators();

        // CARD TYPES
        $this->refreshPaiementTypes();

        // LOCATION TYPES
        $this->refreshLocationTypes();

        // FACTURES STATUS
        $this->refreshFactureStatus();

        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;
        $this->show_traitFacture_form = false;
    }

    function generateCaution($agencyId)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/location/$agencyId/generate_cautions")->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer plus tard";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->showCautions = true;
                $this->showPrestations = false;
                $this->cautions_link = $response["data"]["caution_html_url"];
            }
        }

        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;

        $this->showPrestations = false;
    }

    function ShowGeneratePrestationByPeriod()
    {
        if ($this->generate_prestation_by_periode) {
            $this->generate_prestation_by_periode = false;
        } else {
            $this->generate_prestation_by_periode = true;
        }
        $this->showPrestations = false;
        $this->showCautions = false;

        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;
    }

    function GeneratePrestationByPeriod()
    {
        set_time_limit(0);


        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/location/" . $this->current_agency['id'] . "/$this->first_date/$this->last_date/prestation_statistique_for_agency_by_period")->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer plus tard";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->showCautions = false;
                $this->showPrestations = true;
                $this->cautions_link = $response["data"]["caution_html_url"];
            }
        }

        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;
        $this->generate_prestation_by_periode = false;
    }

    function showDemenageForm($locationId)
    {
        set_time_limit(0);

        if ($this->show_demenage_form) {
            $this->show_demenage_form = false;
        } else {
            $this->show_demenage_form = true;
        }

        $this->dispatch("demenageLocation", $locationId);
        $this->activeLocationId = $locationId;

        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->showCautions = false;
        $this->showPrestations = false;
        $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        // $this->show_demenage_form = false;
        $this->show_facture_liste = false;
    }

    function showEncaisseForm($locationId)
    {
        set_time_limit(0);

        if ($this->show_encaisse_form) {
            $this->show_encaisse_form = false;
        } else {
            $this->show_encaisse_form = true;
        }

        $this->dispatch("encaisseLocation", $locationId);
        $this->activeLocationId = $locationId;

        ###___
        $this->refreshCurrentLocation($this->activeLocationId);

        if ($this->current_locator["prorata"]) {
            $this->show_prorata_fields = true;
        }
        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->showCautions = false;
        $this->showPrestations = false;
        $this->show_traitFacture_form = false;
        // $this->show_encaisse_form = false;
        $this->show_demenage_form = false;

        $this->show_traitFacture_form = false;
        $this->show_facture_liste = false;

        $this->prorata_date = $this->current_location["locataire"]['prorata_date'];
    }

    function Encaisse()
    {

        set_time_limit(0);
        $this->validate(
            [
                'encaisse_facture' => 'required',
            ],
            [
                "encaisse_facture.required" => "L'image de la facture est réquise!",
            ]
        );

        $encaisse_facture_imgPath = $this->encaisse_facture->store('uploads', "public");
        $encaisse_facture_imgPath_imgUrl = env("APP_URL") . "/storage/" . $encaisse_facture_imgPath;

        $agencyId = $this->current_agency["id"];

        $data = [
            "type" => $this->encaisse_paiement_type,
            "location" => $this->activeLocationId,
            "facture_code" => $this->facture_code,
            "echeance_date" => $this->current_location["echeance_date"],

            // PRORATA DATA
            "prorata_days" => $this->show_prorata_fields ? $this->prorata_days : null,
            "prorata_amount" => $this->show_prorata_fields ? $this->prorata_amount : null,
            "prorata_date" => $this->show_prorata_fields ? $this->prorata_date : null,
        ];

        // dd($data);
        ####______
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/paiement/{$agencyId}/add", $data)->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veillez réessayer à nouveau!";
        } else {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("type", $errors)) {
                        $this->encaisse_paiement_type_error = $errors["type"][0];
                    }

                    if (array_key_exists("facture", $errors)) {
                        $this->encaisse_facture_error = $errors["facture"][0];
                    }

                    if (array_key_exists("facture_code", $errors)) {
                        $this->facture_code_error = $errors["facture_code"][0];
                    }

                    if (array_key_exists("location", $errors)) {
                        $message = $errors;
                        $this->refresh($message);
                    }

                    // prorata
                    if (array_key_exists("prorata_days", $errors)) {
                        $this->prorata_days_error = $errors["prorata_days"][0];
                    }

                    if (array_key_exists("prorata_amount", $errors)) {
                        $this->prorata_amount_error = $errors["prorata_amount"][0];
                    }

                    if (array_key_exists("prorata_date", $errors)) {
                        $this->prorata_date_error = $errors["prorata_date"][0];
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
    }

    function EncaisseForLocation()
    {
        set_time_limit(0);
        $this->validate(
            [
                'encaisse_facture' => 'required',
            ],
            [
                "encaisse_facture.required" => "L'image de la facture est réquise!",
            ]
        );
        // dd($this->encaisse_facture);
        $encaisse_facture_imgPath = $this->encaisse_facture->store('uploads', "public");
        $encaisse_facture_imgPath_imgUrl = env("APP_URL") . "/storage/" . $encaisse_facture_imgPath;

        $agencyId = $this->current_agency["id"];

        $data = [
            "type" => $this->encaisse_paiement_type,
            "location" => $this->location,
            "facture_code" => $this->facture_code,

            // PRORATA DATA
            "prorata_days" => $this->show_prorata_fields ? $this->prorata_days : null,
            "prorata_amount" => $this->show_prorata_fields ? $this->prorata_amount : null,
            "prorata_date" => $this->show_prorata_fields ? $this->prorata_date : null,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/paiement/{$agencyId}/add", $data)->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veillez réessayer à nouveau!";
        } else {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("type", $errors)) {
                        $this->encaisse_paiement_type_error = $errors["type"][0];
                    }

                    if (array_key_exists("facture", $errors)) {
                        $this->encaisse_facture_error = $errors["facture"][0];
                    }

                    if (array_key_exists("facture_code", $errors)) {
                        $this->facture_code_error = $errors["facture_code"][0];
                    }

                    if (array_key_exists("location", $errors)) {
                        $this->location_error = $errors["location"][0];
                    }

                    // prorata
                    if (array_key_exists("prorata_days", $errors)) {
                        $this->prorata_days_error = $errors["prorata_days"][0];
                    }

                    if (array_key_exists("prorata_amount", $errors)) {
                        $this->prorata_amount_error = $errors["prorata_amount"][0];
                    }

                    if (array_key_exists("prorata_date", $errors)) {
                        $this->prorata_date_error = $errors["prorata_date"][0];
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
    }

    function FactureTraitement()
    {
        set_time_limit(0);

        $data = [
            "status" => $this->trait_facture_status,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/facture/{$this->activeFactureId}/updateStatus", $data)->json();
        if (!$response["status"]) {
            $errors = $response["erros"];
            if (gettype($errors) == "array") {
                if (array_key_exists("status", $errors)) {
                    $this->trait_facture_status_error = $errors["status"][0];
                }
            } else {
                $this->generalError = $errors;
            }
        } else {
            $this->refresh($response["message"]);
        }
    }

    public function showFactures(int $locationId)
    {
        set_time_limit(0);

        $this->refreshCurrentFactures($locationId);

        // FERMETURE DES AUTRES 
        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->showCautions = false;
        $this->showPrestations = false;
        // $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;
        $this->show_facture_liste = true;
    }

    public function showFacturesTraitementForm($factureId)
    {
        set_time_limit(0);

        if ($this->show_traitFacture_form) {
            $this->show_traitFacture_form = false;
        } else {
            $this->show_traitFacture_form = true;
        }

        $this->activeFactureId = $factureId;

        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/facture/{$this->activeFactureId}/retrieve")->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayer plus tard!";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->activeFacture = $response["data"];
            }
        }

        // dd($this->activeFacture);
        ##___


        // FERMETURE DES AUTRES 
        $this->show_form = false;
        $this->showCautions = false;
        $this->showPrestations = false;
        // $this->show_traitFacture_form = false;
        $this->show_encaisse_form = false;
        $this->show_demenage_form = false;
    }

    public function delete(int $id)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->delete($this->BASE_URL . "immo/location/{$id}/delete")->json();

        if (!$response["status"]) {
            $this->generalError = $response["erros"];
        } else {
            $this->refresh($response["message"]);
        }
    }

    function Update($id)
    {
        set_time_limit(0);

        ###___RETRIEVE DU ROOM
        // $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/location/$id/retrieve")->json();

        foreach ($this->locations as $location) {
            if ($location["id"] == $id) {
                $this->current_location = $location;
            }
        }

        // dd($this->current_location);
        // $this->current_location_boolean = false;

        $data = [
            "house" => $this->house ? $this->house : $this->current_location["house"]["id"],
            "room" => $this->room ? $this->room : $this->current_location["room"]["id"],
            "locataire" => $this->locataire ? $this->locataire : $this->current_location["locataire"]["id"],
            "type" => $this->type ? $this->type : $this->current_location["type"],
            "water_counter" => $this->water_counter ? $this->water_counter : $this->current_location["water_counter"],
            "electric_counter" => $this->electric_counter ? $this->electric_counter : $this->current_location["electric_counter"],
            "prestation" => $this->prestation ? $this->prestation : $this->current_location["prestation"],
            "numero_contrat" => $this->numero_contrat ? $this->numero_contrat : $this->current_location["numero_contrat"],
            "caution_number" => $this->caution_number ? $this->caution_number : $this->current_location["caution_number"],

            "discounter" => $this->discounter ? $this->discounter : $this->current_location["discounter"],
            "kilowater_price" => $this->kilowater_price ? $this->kilowater_price : $this->current_location["kilowater_price"],

            "pre_paid" => $this->pre_paid ? $this->pre_paid : $this->current_location["pre_paid"],
            "post_paid" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],

            "caution_bordereau" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],
            // "img_contrat" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],
            "img_prestation" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],

            "caution_water" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],
            "frais_peiture" => $this->post_paid ? $this->post_paid : $this->current_location["post_paid"],

            "effet_date" => $this->effet_date ? $this->effet_date : $this->current_location["effet_date"],
            "comments" => $this->comments ? $this->comments : $this->current_location["comments"],
            "caution_electric" => $this->caution_electric ? $this->caution_electric : $this->current_location["caution_electric"],
        ];

        // dd($data);

        ########_________
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/location/$id/update", $data)->json();

        if ($response) {
            if (!$response["status"]) {
                $this->refresh($response["erros"]);
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
        }
    }

    public function render()
    {
        return view('livewire.location');
    }
}
