<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\City;
use App\Models\Country;
use App\Models\Departement;
use App\Models\House as ModelsHouse;
use App\Models\HouseType;
use App\Models\Quarter;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;


class House extends Component
{
    use WithFileUploads;

    public $agency;
    public $current_agency;
    public $houses = [];
    public $currentHouseId = null;
    public $houses_count = [];

    // 
    public $countries = [];
    public $proprietors = [];
    public $cities = [];
    public $house_types = [];
    public $departements = [];
    public $quartiers = [];
    public $zones = [];
    public $supervisors = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $house_rooms = [];
    public $current_house = [];
    public $current_house_boolean = false;
    public $current_house_for_caution = [];

    // ADD PROPRIO DATAS
    public $name = "";
    public $latitude = "";
    public $longitude = "";
    public $type = "";
    public $country = "";
    public $city = "";
    public $departement = "";
    public $quartier = "";
    public $zone;
    public $supervisor = "";
    public $proprietor = "";
    public $geolocalisation = "";
    public $comments = "";

    // TRAITEMENT DES ERREURS
    public $name_error = "";
    public $latitude_error = "";
    public $longitude_error = "";
    public $type_error = "";
    public $country_error = "";
    public $departement_error = "";
    public $city_error = "";
    public $quartier_error = "";
    public $zone_error = "";
    public $supervisor_error = "";
    public $proprietor_error = "";
    public $comments_error = "";

    public $proprio_payement_echeance_date;
    public $proprio_payement_echeance_date_error;

    public $search = '';

    public $generalError = "";
    public $generalSuccess = "";

    // 
    public $show_form = false;

    public $house_type_name = "";
    public $house_type_description = "";

    public $house_type_name_error = "";
    public $house_type_description_error = "";

    public $generate_caution_by_periode = false;

    public $showCautions = false;
    public $cautions_link = "";

    public $first_date = "";
    public $last_date = "";

    public $commission_percent;

    ####___ GESTION DES CAUTIONS
    function ShowGenererHouseCautionByPeriod($houseId)
    {
        $this->currentHouseId = $houseId;

        $this->current_house_for_caution = ModelsHouse::find($houseId);

        $this->generate_caution_by_periode = true;
        $this->showCautions = false;
        $this->show_form = false;
    }

    function GenerateCautionByPeriode()
    {
        set_time_limit(0);

        $data = [
            "first_date" => $this->first_date,
            "last_date" => $this->last_date,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/location/$this->currentHouseId/generate_cautions_for_house_by_period", $data)->json();

        // dd( $response);
        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer plus tard";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->showCautions = true;
                $this->cautions_link = $response["data"]["caution_html_url"];
            }
        }

        $this->generate_caution_by_periode = false;
        $this->show_form = false;
    }

    ###___PROPRIETORS
    function refreshThisAgencyProprietors()
    {
        $this->proprietors = $this->current_agency->_Proprietors;
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $title = 'Suppression d\'une maison!';
        $text = "Voulez-vous vraiment supprimer cette maison?";
        confirmDelete($title, $text);

        // $agency = $this->current_agency;

        // $agency_houses = [];
        // foreach ($agency->_Proprietors as $proprio) {
        //     foreach ($proprio->Houses as $house) {
        //         array_push($agency_houses, $house);
        //     }
        // }
        $this->houses = $this->current_agency->_Houses;
        $this->houses_count = count($this->houses);
    }

    // COUNTRIES
    function refreshCountries()
    {
        $countries = Country::all();
        $this->countries = $countries;
    }

    // COUNTRIES
    function refreshCities()
    {
        $cities = City::all();
        $this->cities = $cities;
    }

    // TYPES DE MAISON
    function refreshTypes()
    {
        $house_types = HouseType::all();
        $this->house_types = $house_types;
    }

    // REFRESH DEPARTEMENT
    function refreshDepartements()
    {
        $departements = Departement::all();
        $this->departements = $departements;
    }

    // QUARTIERS
    function refreshQuartiers()
    {
        $quartiers = Quarter::all();
        $this->quartiers = $quartiers;
    }

    // REFRESH ZONE
    function refreshZones()
    {
        $zones = Zone::all();
        $this->zones = $zones;
    }

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

    function mount($agency)
    {
        $this->current_agency = $agency;
        ###___PROPRIETORS
        $this->refreshThisAgencyProprietors();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // PAYS
        $this->refreshCountries();

        // CITIES
        $this->refreshCities();

        // HOUSES TYPES
        $this->refreshTypes();

        // DEPARTEMENTS
        $this->refreshDepartements();

        // QUARTIER
        $this->refreshQuartiers();

        // ZONE
        $this->refreshZones();

        // SUPERVISEUR
        $this->refreshSupervisors();
    }

    function refresh($message)
    {
        set_time_limit(0);

        $this->generalSuccess = $message;
        $this->show_form = false;

        // #### neutralisation des infos
        $this->name = "";
        $this->name_error = "";

        $this->latitude = "";
        $this->latitude_error = "";

        $this->longitude = "";
        $this->longitude_error = "";

        $this->type = "";
        $this->type_error = "";

        $this->country = "";
        $this->country_error = "";

        $this->departement = "";
        $this->departement_error = "";

        $this->city = "";
        $this->city_error = "";

        $this->quartier = "";
        $this->quartier_error = "";

        $this->zone = "";
        $this->zone_error = "";

        $this->supervisor = "";
        $this->supervisor_error = "";

        $this->proprietor = "";
        $this->proprietor_error = "";

        $this->comments = "";
        $this->comments_error = "";

        ###___PROPRIETORS
        $this->refreshThisAgencyProprietors();

        // MAISONS
        $this->refreshThisAgencyHouses();

        // PAYS
        $this->refreshCountries();

        // CITIES
        $this->refreshCities();

        // HOUSES TYPES
        $this->refreshTypes();

        // DEPARTEMENTS
        $this->refreshDepartements();

        // QUARTIER
        $this->refreshQuartiers();

        // ZONE
        $this->refreshZones();

        // SUPERVISEUR
        $this->refreshSupervisors();
    }

    function showForm()
    {
        if ($this->show_form) {
            $this->show_form = false;
        } else {
            $this->show_form = true;
        }

        $this->current_house = [];
        $this->current_house_boolean = false;
        $this->showCautions = false;
        $this->generate_caution_by_periode = false;
    }


    function addHouse()
    {
        set_time_limit(0);

        $data = [
            "owner" => $this->userId,
            "name" => $this->name,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "type" => $this->type,
            "country" => $this->country,
            "departement" => $this->departement,
            "city" => $this->city,
            "quartier" => $this->quartier,
            "zone" => $this->zone,
            "supervisor" => $this->supervisor,

            "proprietor" => $this->proprietor,
            "geolocalisation" => $this->geolocalisation,
            "comments" => $this->comments,
            "proprio_payement_echeance_date" => $this->proprio_payement_echeance_date,
        ];


        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/house/add", $data)->json();

        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayez plus tard!";
        } else {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("name", $errors)) {
                        $this->name_error = $errors["name"][0];
                    }
                    if (array_key_exists("latitude", $errors)) {
                        $this->latitude_error = $errors["latitude"][0];
                    }
                    if (array_key_exists("longitude", $errors)) {
                        $this->longitude_error = $errors["longitude"][0];
                    }
                    if (array_key_exists("type", $errors)) {
                        $this->type_error = $errors["type"][0];
                    }
                    if (array_key_exists("country", $errors)) {
                        $this->country_error = $errors["country"][0];
                    }
                    if (array_key_exists("departement", $errors)) {
                        $this->departement_error = $errors["departement"][0];
                    }
                    if (array_key_exists("city", $errors)) {
                        $this->city_error = $errors["city"][0];
                    }
                    if (array_key_exists("quartier", $errors)) {
                        $this->quartier_error = $errors["quartier"][0];
                    }
                    if (array_key_exists("zone", $errors)) {
                        $this->zone_error = $errors["zone"][0];
                    }
                    if (array_key_exists("supervisor", $errors)) {
                        $this->supervisor_error = $errors["supervisor"][0];
                    }
                    if (array_key_exists("proprietor", $errors)) {
                        $this->proprietor_error = $errors["proprietor"][0];
                    }
                    if (array_key_exists("comments", $errors)) {
                        $this->comments_error = $errors["comments"][0];
                    }
                    if (array_key_exists("proprio_payement_echeance_date", $errors)) {
                        $this->proprio_payement_echeance_date_error = $errors["proprio_payement_echeance_date"][0];
                    }
                } else {
                    $this->generalError = $errors;
                }
            } else {
                $this->refresh($response["message"]);
            }
        }
    }

    public function retrieve(int $id)
    {
        set_time_limit(0);

        $house = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/house/{$id}/retrieve")->json();
        if (!$house["status"]) {
            $this->house_rooms = [];
            $this->current_house = [];
            $this->current_house_boolean = false;
        } else {
            $this->house_rooms = $house["data"]["rooms"];
            $this->current_house = $house["data"];
            $this->current_house_boolean = true;
        }

        $this->show_form = false;
    }

    public function delete(int $id)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->delete($this->BASE_URL . "immo/house/{$id}/delete")->json();

        if (!$response["status"]) {
            // return redirect("/house")->with("error", $response["erros"]);
            $this->refresh($response["erros"]);
        }
        // return redirect("/house")->with("success", $response["message"]);
        $this->refresh($response["message"]);
    }


    function Update($id)
    {
        set_time_limit(0);

        ###___RETRIEVE DU HOUSE
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/house/$id/retrieve")->json();

        $this->current_house = $response["data"];
        $this->current_house_boolean = false;

        $data = [
            "name" => $this->name ? $this->name : $this->current_house["name"],
            "latitude" => $this->latitude ? $this->latitude : $this->current_house["latitude"],
            "longitude" => $this->longitude ? $this->longitude : $this->current_house["longitude"],
            "geolocalisation" => $this->geolocalisation ? $this->geolocalisation : $this->current_house["geolocalisation"],
            "proprio_payement_echeance_date" => $this->proprio_payement_echeance_date ? $this->proprio_payement_echeance_date : $this->current_house["proprio_payement_echeance_date"],
            "commission_percent" => $this->commission_percent ? $this->commission_percent : $this->current_house["commission_percent"],
        ];
        ########_________
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/house/$id/update", $data)->json();

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
        return view('livewire.house');
    }
}
