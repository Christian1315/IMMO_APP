<?php

namespace App\Livewire;

use App\Models\RoomNature;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;


class Room extends Component
{
    use WithFileUploads;

    public $agency;
    public $current_agency;

    public $rooms = [];
    public $rooms_count = [];

    // 
    public $countries = [];
    public $proprietors = [];
    public $houses = [];

    public $cities = [];
    public $room_types = [];
    public $room_natures = [];
    public $departements = [];
    public $quartiers = [];
    public $zones = [];
    public $supervisors = [];


    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $room_locations = [];
    public $current_room = [];
    public $current_room_boolean = false;


    public $show_water_info = true;
    public $show_electricity_info = false;

    // ADD ROOM DATAS
    public $water = true;
    public $electricity = false;

    public $loyer = "";
    public $number = "";
    public $gardiennage;
    public $rubbish;
    public $vidange;
    public $photo;

    public $cleaning;
    public $comments = "";
    public $house = "";
    public $nature = "";
    public $type;

    #water info
    public $show_forage_inputs = false;
    public $show_water_conventionnal_counter_inputs = false;
    public $water_discounter_inputs = false;

    public $water_card_counter;
    public $unit_price;
    public $unit_price_error;

    public $water_conventionnal_counter = false;
    public $water_discounter = false;
    public $forage = false;
    public $forfait_forage = 0;
    public $water_counter_start_index = 0;
    public $water_counter_number = "0";

    #electricity info
    public $show_electricity_discountInputs;
    public $electricity_card_counter;
    public $electricity_conventionnal_counter;
    public $electricity_discounter;
    public $electricity_counter_start_index;
    public $electricity_counter_number;


    // TRAITEMENT DES ERREURS
    public $loyer_error = "";
    public $number_error = "";
    public $gardiennage_error = "";
    public $vidange_error = "";
    public $cleaning_error = "";
    public $nature_error = "";
    public $type_error = "";
    public $rubbish_error = "";
    public $house_error = "";
    public $photo_error = "";
    public $comments_error = "";

    public $forfait_forage_error;
    public $water_counter_start_index_error;
    public $water_counter_number_error;

    public $electricity_counter_start_index_error;
    public $electricity_counter_number_error;

    // TYPE DE ROOM
    public $room_type_name = "";
    public $room_type_description;

    public $room_type_name_error = "";
    public $room_type_description_error;

    // NATURE DE ROOM
    public $room_nature_name = "";
    public $room_nature_description;

    public $room_nature_name_error = "";
    public $room_nature_description_error;

    public $electricity_unit_price = "";
    public $electricity_unit_price_error = "";

    public $search = '';

    public $generalError = "";
    public $generalSuccess = "";

    // 
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

    ###___ROOMS
    function refreshThisAgencyRooms()
    {
        $title = 'Suppression de la chambre!';
        $text = "Voulez-vous vraiment supprimer cette chambre?";
        confirmDelete($title, $text);

        ###__TRIONS CEUX QUI SE TROUVENT DANS L'AGENCE ACTUELLE
        ##__on recupere les maisons qui appartiennent aux propriétaires
        ##__ se trouvant dans cette agence
        $agency_rooms = [];

        foreach ($this->current_agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses as $house) {
                foreach ($house->Rooms as $room) {
                    array_push($agency_rooms, $room);
                }
            }
        }
        $this->rooms = $agency_rooms;
        $this->rooms_count = count($this->rooms);
    }

    ###___HOUSES
    function refreshThisAgencyHouses()
    {
        $title = 'Suppression d\'une maison!';
        $text = "Voulez-vous vraiment supprimer cette maison?";
        confirmDelete($title, $text);

        $agency = $this->current_agency;

        $agency_houses = [];
        foreach ($agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses as $house) {
                array_push($agency_houses, $house);
            }
        }
        $this->houses = $agency_houses;
    }

    function showWaterInfo()
    {
        if ($this->water) {
            $this->show_water_info = true;
        } else {
            $this->show_water_info = false;
        }
    }

    function showElectricityInfo()
    {
        if ($this->electricity) {
            $this->show_electricity_info = true;
        } else {
            $this->show_electricity_info = false;
        }
    }

    function showForageInputs()
    {
        if ($this->forage) {
            $this->show_forage_inputs = true;
        } else {
            $this->show_forage_inputs = false;
        }
    }

    function showWaterConventionnalCounterInputs()
    {
        if ($this->water_conventionnal_counter) {
            $this->show_water_conventionnal_counter_inputs = true;
        } else {
            $this->show_water_conventionnal_counter_inputs = false;
        }
    }

    function waterDiscounterInputs()
    {
        if ($this->water_discounter) {
            $this->water_discounter_inputs = true;
        } else {
            $this->water_discounter_inputs = false;
        }
    }

    function showElectricityDiscountInputs()
    {
        if ($this->electricity_discounter) {
            $this->show_electricity_discountInputs = true;
        } else {
            $this->show_electricity_discountInputs = false;
        }
    }

    function mount($agency)
    {
        set_time_limit(0);
        $this->current_agency = $agency;

        ###___ROOMS
        $this->refreshThisAgencyRooms();

        // MAISONS
        $this->refreshThisAgencyHouses();


        // roomS TYPES
        $room_types = RoomType::all();
        $this->room_types = $room_types;

        // ROOM NATURE
        $room_natures = RoomNature::all();
        $this->room_natures = $room_natures;

        // SUPERVISEURS
        $this->refreshSupervisors();
    }

    function showForm()
    {
        if ($this->show_form) {
            $this->show_form = false;
        } else {
            $this->show_form = true;
        }
        $this->current_room = [];
        $this->current_room_boolean = false;
    }

    function refresh() {
        
    }

    // ROOM TYPE ADDING
    function addRoomType()
    {

        $data = [
            "name" => $this->room_type_name,
            "description" => $this->room_type_description,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/room/type/add", $data)->json();

        // dd($response);
        if (!$response["status"]) {
            $errors = $response["erros"];
            if (gettype($errors) == "array") {
                if (array_key_exists("name", $errors)) {
                    $this->room_type_name_error = $errors["name"][0];
                }
                if (array_key_exists("description", $errors)) {
                    $this->room_type_description_error = $errors["description"][0];
                }
            } else {
                $this->generalError = $errors;
            }
        } else {
            $this->refresh($response["message"]);
        }
    }

    // ROOM NATURE ADDING
    function addRoomNature()
    {

        ###___
        $data = [
            "name" => $this->room_nature_name,
            "description" => $this->room_nature_description,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/room/nature/add", $data)->json();

        // dd($response);
        if (!$response["status"]) {
            $errors = $response["erros"];
            if (gettype($errors) == "array") {
                if (array_key_exists("name", $errors)) {
                    $this->room_nature_name_error = $errors["name"][0];
                }
                if (array_key_exists("description", $errors)) {
                    $this->room_nature_description_error = $errors["description"][0];
                }
            } else {
                $this->generalError = $errors;
            }
        } else {
            $this->refresh($response["message"]);
        }
    }

    function addRoom()
    {
        set_time_limit(0);

        $this->validate(
            [
                'photo' => 'required',
            ],
            [
                "photo.required" => "Le photo est réquise!",
            ]
        );

        $photo_imgPath = $this->photo->store('uploads', "public");
        $photo_imgPath_imgUrl = env("APP_URL") . "/storage/" . $photo_imgPath;

        ###__

        $data = [
            "owner" => $this->userId,
            "loyer" => $this->loyer,
            "water" => $this->water,
            "electricity" => $this->electricity,
            "number" => $this->number,
            "gardiennage" => $this->gardiennage,
            "rubbish" => $this->rubbish,
            "vidange" => $this->vidange,
            "cleaning" => $this->cleaning,
            "nature" => $this->nature,
            "type" => $this->type,
            "house" => $this->house,
            "comments" => $this->comments,
            "photo" => $photo_imgPath_imgUrl,

            #eau
            "unit_price" => $this->unit_price,
            "water_discounter" => $this->water_discounter ? true : false,
            "water_counter_number" => $this->water_discounter,
            "water_conventionnal_counter" => $this->water_conventionnal_counter ? true : false,
            "forage" => $this->forage ? true : false,
            "forfait_forage" => $this->forfait_forage,
            "water_counter_start_index" => $this->water_counter_start_index,

            #electricity
            "electricity_unit_price" => $this->electricity_unit_price,
            "electricity_card_counter" => $this->electricity_card_counter ? true : false,
            "electricity_conventionnal_counter" => $this->electricity_conventionnal_counter ? true : false,
            "electricity_discounter" => $this->electricity_discounter ? true : false,
            "electricity_counter_start_index" => $this->electricity_counter_start_index ? $this->electricity_counter_start_index : 0,
            "electricity_counter_number" => $this->electricity_counter_number ? $this->electricity_counter_number : 0,
        ];
        // dd($data);

        // if ($this->water) {

        // }

        //  else {

        //     ###___ELECTRICITY
        //     $data = [
        //         "owner" => $this->userId,
        //         "loyer" => $this->loyer,
        //         "water" => $this->water,
        //         "electricity" => $this->electricity,
        //         "number" => $this->number,
        //         "gardiennage" => $this->gardiennage,
        //         "rubbish" => $this->rubbish,
        //         "vidange" => $this->vidange,
        //         "cleaning" => $this->cleaning,
        //         "nature" => $this->nature,
        //         "type" => $this->type,
        //         "house" => $this->house,
        //         "comments" => $this->comments,
        //         "photo" => $photo_imgPath_imgUrl,
        //         "electricity_unit_price" => $this->electricity_unit_price,

        //         #electricity
        //         "electricity_card_counter" => $this->electricity_card_counter ? true : false,
        //         "electricity_conventionnal_counter" => $this->electricity_conventionnal_counter ? true : false,
        //         "electricity_discounter" => $this->electricity_discounter ? true : false,
        //         "electricity_counter_start_index" => $this->electricity_counter_start_index,
        //         "electricity_counter_number" => $this->electricity_counter_number,
        //     ];
        // }

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/room/add", $data)->json();

        if (!$response) {
            $this->generalError = "Désolé! Une erreure est survenue, veuillez réessayer plus tard";
        } else {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("loyer", $errors)) {
                        $this->loyer_error = $errors["loyer"][0];
                    }
                    if (array_key_exists("number", $errors)) {
                        $this->number_error = $errors["number"][0];
                    }
                    if (array_key_exists("gardiennage", $errors)) {
                        $this->gardiennage_error = $errors["gardiennage"][0];
                    }
                    if (array_key_exists("rubbish", $errors)) {
                        $this->rubbish_error = $errors["rubbish"][0];
                    }
                    if (array_key_exists("vidange", $errors)) {
                        $this->vidange_error = $errors["vidange"][0];
                    }
                    if (array_key_exists("photo", $errors)) {
                        $this->photo_error = $errors["photo"][0];
                    }
                    if (array_key_exists("cleaning", $errors)) {
                        $this->cleaning_error = $errors["cleaning"][0];
                    }
                    if (array_key_exists("comments", $errors)) {
                        $this->comments_error = $errors["comments"][0];
                    }
                    if (array_key_exists("house", $errors)) {
                        $this->house_error = $errors["house"][0];
                    }
                    if (array_key_exists("nature", $errors)) {
                        $this->nature_error = $errors["nature"][0];
                    }
                    if (array_key_exists("type", $errors)) {
                        $this->type_error = $errors["type"][0];
                    }

                    // eau
                    if (array_key_exists("unit_price", $errors)) {
                        $this->unit_price_error = $errors["unit_price"][0];
                    }
                    if (array_key_exists("forfait_forage", $errors)) {
                        $this->forfait_forage_error = $errors["forfait_forage"][0];
                    }
                    if (array_key_exists("water_counter_number", $errors)) {
                        $this->water_counter_number_error = $errors["water_counter_number"][0];
                    }
                    if (array_key_exists("water_counter_start_index", $errors)) {
                        $this->water_counter_start_index_error = $errors["water_counter_start_index"][0];
                    }


                    // electricity
                    if (array_key_exists("electricity_counter_number", $errors)) {
                        $this->electricity_counter_number_error = $errors["electricity_counter_number"][0];
                    }
                    if (array_key_exists("electricity_counter_start_index", $errors)) {
                        $this->electricity_counter_start_index_error = $errors["electricity_counter_start_index"][0];
                    }
                    if (array_key_exists("electricity_unit_price", $errors)) {
                        $this->electricity_unit_price_error = $errors["electricity_unit_price"][0];
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

        $room = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/room/{$id}/retrieve")->json();

        if (!$room["status"]) {
            $this->room_locations = [];
            $this->current_room = [];
            $this->current_room_boolean = false;
        } else {
            $this->room_locations = $room["data"]["locations"];
            $this->current_room = $room["data"];
            $this->current_room_boolean = false;
        }

        $this->show_form = false;
    }

    public function delete(int $id)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->delete($this->BASE_URL . "immo/room/{$id}/delete")->json();

        if (!$response["status"]) {
            $this->generalError = $response["erros"];
        }
        $this->refresh($response["message"]);
    }

    function Update($id)
    {
        set_time_limit(0);

        ###___RETRIEVE DU ROOM
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "immo/room/$id/retrieve")->json();

        $this->current_room = $response["data"];
        $this->current_room_boolean = false;

        $data = [
            "number" => $this->number ? $this->number : $this->current_room["number"],
            "gardiennage" => $this->gardiennage ? $this->gardiennage : $this->current_room["gardiennage"],
            "rubbish" => $this->rubbish ? $this->rubbish : $this->current_room["rubbish"],
            "vidange" => $this->vidange ? $this->vidange : $this->current_room["vidange"],
            "cleaning" => $this->cleaning ? $this->cleaning : $this->current_room["cleaning"],
        ];


        ########_________
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "immo/room/$id/update", $data)->json();
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
        return view('livewire.room');
    }
}
