<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\CardType;
use App\Models\Country;
use App\Models\Departement;
use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Livewire\Component;
use Livewire\WithFileUploads;

class Locator extends Component
{
    use WithFileUploads;

    public $current_agency;
    public $agency;

    public $locators = [];
    public $old_locators = [];
    public $locators_count = [];

    public $card_types = [];
    public $countries = [];
    public $departements = [];


    public $proprietors = [];
    public $houses = [];
    public $house;

    public $cities = [];
    public $locator_types = [];
    public $locator_natures = [];
    public $quartiers = [];
    public $zones = [];
    public $supervisors = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];


    public $locator_houses = [];
    public $locator_rooms = [];
    public $current_locator = [];
    public $current_locator_boolean = false;
    public $current_locator_for_room = [];


    // ADD locator DATAS
    public $name = "";
    public $prenom = "";
    public $email;
    public $sexe;
    public $phone;
    public $piece_number;
    public $mandate_contrat;
    public $adresse;
    public $card_id = "";
    public $card_type = "";
    public $prorata = false;
    public $show_prorata_info = false;
    public $prorata_date = "";

    public $discounter = false;
    public $show_discounter_info = false;
    public $kilowater_price;


    public $country = "";
    public $departement = "";
    public $comments = "";

    // TRAITEMENT DES ERREURS
    public $name_error = "";
    public $prenom_error = "";
    public $email_error = "";
    public $sexe_error = "";
    public $phone_error = "";
    public $piece_number_error = "";
    public $mandate_contrat_error = "";
    public $adresse_error = "";
    public $card_id_error = "";
    public $card_type_error = "";
    public $country_error = "";
    public $departement_error = "";
    public $comments_error = "";
    public $prorata_date_error = "";
    public $kilowater_price_error = "";

    // 
    public $show_form = false;
    public $click_count = 2;

    public $search = '';
    public $supervisor;

    public $generalError = "";
    public $generalSuccess = "";

    public $start_date = "";
    public $end_date = "";

    public $display_locators_options = false;
    public $show_locators_by_supervisor = false;
    public $show_locators_by_house = false;

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

    function displayLocatorsOptions()
    {
        if ($this->display_locators_options) {
            $this->display_locators_options = false;
        } else {
            $this->display_locators_options = true;
        }
        $this->show_locators_by_house = false;
        $this->show_locators_by_supervisor = false;
    }

    function refreshThisAgencyLocators()
    {
        $title = 'Suppression de locataire';
        $text = "Voullez-vous vraiment supprimer ce locataire";
        confirmDelete($title, $text);

        ###___LOCATORS
        $agency_locators = $this->current_agency->_Locataires;

        ##___
        $this->locators_count = count($agency_locators);
        $this->locators = $agency_locators;
        $this->old_locators = $agency_locators;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $this->houses = $this->current_agency->_Houses;
    }

    function mount($agency)
    {
        $this->current_agency = $agency;

        ###___LOCATORS
        $this->refreshThisAgencyLocators();

        ###___SUPERVISOR
        $this->refreshSupervisors();

        ###____HOUSE AGENCY
        $this->refreshThisAgencyHouses();

        // CARD TYPES
        $card_types = CardType::all();
        $this->card_types = $card_types;

        // PAYS
        $countries = Country::all();
        $this->countries = $countries;

        // DEPARTEMENTS
        $departements = Departement::all();
        $this->departements = $departements;
    }

    function refresh($message) {}

    function showForm()
    {
        if ($this->show_form) {
            $this->show_form = false;
        } else {
            $this->show_form = true;
        }
        $this->locator_houses = [];
        $this->locator_rooms = [];
        $this->current_locator = [];
        $this->current_locator_boolean = false;
        $this->current_locator_for_room = [];
    }

    function addlocator()
    {
        set_time_limit(0);

        $this->validate(
            [
                'mandate_contrat' => 'required',
            ],
            [
                "mandate_contrat.required" => "Le photo est réquise!",
            ]
        );

        $mandate_contrat_imgPath = $this->mandate_contrat->store('uploads', "public");
        $mandate_contrat_imgPath_imgUrl = env("APP_URL") . "/storage/" . $mandate_contrat_imgPath;

        $data = [
            "owner" => $this->userId,
            "agency" => $this->current_agency['id'],
            "name" => $this->name,

            "prorata" => $this->prorata ? true : false,
            "prorata_date" => $this->prorata_date,

            // "discounter" => $this->discounter ? true : false,
            // "kilowater_price" => $this->kilowater_price,

            "prenom" => $this->prenom,
            "email" => $this->email,
            "sexe" => $this->sexe,
            "phone" => $this->phone,
            // "piece_number" => $this->piece_number,
            "adresse" => $this->adresse,
            "card_id" => $this->card_id,
            "card_type" => $this->card_type,
            "departement" => $this->departement,
            "country" => $this->country,
            "comments" => $this->comments,
            "mandate_contrat" => $mandate_contrat_imgPath_imgUrl,
        ];


        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/locataire/add", $data)->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayer à nouveau!";
        } else {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("name", $errors)) {
                        $this->name_error = $errors["name"][0];
                    }
                    if (array_key_exists("prenom", $errors)) {
                        $this->prenom_error = $errors["prenom"][0];
                    }
                    if (array_key_exists("email", $errors)) {
                        $this->email_error = $errors["email"][0];
                    }
                    if (array_key_exists("sexe", $errors)) {
                        $this->sexe_error = $errors["sexe"][0];
                    }
                    if (array_key_exists("phone", $errors)) {
                        $this->phone_error = $errors["phone"][0];
                    }
                    if (array_key_exists("piece_number", $errors)) {
                        $this->piece_number_error = $errors["piece_number"][0];
                    }
                    if (array_key_exists("mandate_contrat", $errors)) {
                        $this->mandate_contrat_error = $errors["mandate_contrat"][0];
                    }
                    if (array_key_exists("adresse", $errors)) {
                        $this->adresse_error = $errors["adresse"][0];
                    }
                    if (array_key_exists("card_id", $errors)) {
                        $this->card_id_error = $errors["card_id"][0];
                    }
                    if (array_key_exists("card_type", $errors)) {
                        $this->card_type_error = $errors["card_type"][0];
                    }
                    if (array_key_exists("card_type", $errors)) {
                        $this->card_type_error = $errors["card_type"][0];
                    }
                    if (array_key_exists("country", $errors)) {
                        $this->country_error = $errors["country"][0];
                    }

                    if (array_key_exists("departement", $errors)) {
                        $this->departement_error = $errors["departement"][0];
                    }
                    if (array_key_exists("comments", $errors)) {
                        $this->comments_error = $errors["comments"][0];
                    }

                    if (array_key_exists("prorata_date", $errors)) {
                        $this->prorata_date_error = $errors["prorata_date"][0];
                    }

                    if (array_key_exists("kilowater_price", $errors)) {
                        $this->kilowater_price_error = $errors["kilowater_price"][0];
                    }
                } else {
                    $this->generalError = $errors;
                }
            } else {
                $this->refresh($response["message"]);
            }
        }
    }

    public function showHouses(int $id)
    {
        set_time_limit(0);

        $this->show_form = false;

        $locator = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/locataire/{$id}/retrieve")->json();
        $this->locator_houses = $locator["data"]["houses"];
        $this->current_locator_for_room = [];
        $this->current_locator_boolean = false;
        $this->current_locator = $locator["data"];
        $this->current_locator_boolean = true;
    }

    function Update($id)
    {
        set_time_limit(0);

        ###___RETRIEVE DU ROOM
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/locataire/$id/retrieve")->json();

        $this->current_locator = $response["data"];
        $this->current_locator_boolean = false;

        $data = [
            "name" => $this->name ? $this->name : $this->current_locator["name"],
            "prenom" => $this->prenom ? $this->prenom : $this->current_locator["prenom"],
            "email" => $this->email ? $this->email : $this->current_locator["email"],
            "phone" => $this->phone ? $this->phone : $this->current_locator["phone"],
            "adresse" => $this->adresse ? $this->adresse : $this->current_locator["adresse"],
        ];

        ########_________
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/locataire/$id/update", $data)->json();

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

    public function showRooms(int $id)
    {
        set_time_limit(0);

        $this->show_form = false;

        $locator = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/locataire/{$id}/retrieve")->json();
        if (!$locator["status"]) {
            $this->locator_rooms = [];
            $this->current_locator = [];
            $this->current_locator_for_room = [];
        } else {
            $this->locator_rooms = $locator["data"]["rooms"];
            $this->current_locator = [];
            $this->current_locator_for_room = $locator["data"];
        }
    }

    public function render()
    {
        return view('livewire.locator');
    }
}
