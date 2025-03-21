<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Performance extends Component
{
    public $agency;

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $houses = [];
    public $house = [];

    ###___chambres occupées
    public $all_busy_rooms = [];
    ###___chambres libre
    public $all_frees_rooms_at_first_month = [];

    public $generalSuccess = "";
    public $generalError = "";

    public $showTaux = false;
    public $display_taux_options = false;

    public $generate_caution_by_supervisor = false;
    public $generate_taux_by_supervisor = false;
    public $generate_taux_by_house = false;

    public $supervisors = [];
    public $supervisor = [];
    public $supervisor_error = '';

    public $month = "";
    public $end_date = "";

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
        $this->houses = $this->agency->_Houses;
    }

    function mount($agency)
    {
        set_time_limit(0);

        $this->agency = $agency;

        $this->GenerateAgencyPerformance();
        $this->refreshSupervisors();
        $this->refreshThisAgencyHouses();
    }

    ###___AGENCY PERFORMANCE
    function GenerateAgencyPerformance()
    {
        ####___Toutes les maisons de l'agence
        $new_houses_data = [];

        $all_frees_rooms = [];
        $all_busy_rooms = [];
        $all_frees_rooms_at_first_month = [];
        $all_busy_rooms_at_first_month = [];

        ####___HOUSES
        $houses = [];
        $house = null;

        ####___traitement des houses
        $houses = $this->agency->_Houses;

        ####____
        foreach ($houses as $house) {
            $creation_date = date("Y/m/d", strtotime($house["created_at"]));
            $creation_time = strtotime($creation_date);
            $first_month_period = strtotime("+1 month", strtotime($creation_date));

            $frees_rooms = [];
            $busy_rooms = [];
            $busy_rooms_at_first_month = [];
            $frees_rooms_at_first_month = [];

            foreach ($house->Rooms as $room) {
                $is_this_room_buzy = false; #cette variable determine si cette chambre est occupée ou pas(elle est occupée lorqu'elle se retrouve dans une location de cette maison)
                ##__parcourons les locations pour voir si cette chambre s'y trouve

                foreach ($house->Locations as $location) {
                    if ($location->Room->id == $room->id) {
                        $is_this_room_buzy = true;

                        ###___verifions la période d'entrée de cette chambre en location
                        ###__pour determiner les chambres vide dans le premier mois
                        $location_create_date = strtotime(date("Y/m/d", strtotime($location["created_at"])));
                        ##on verifie si la date de creation de la location est inférieure à la date du *$first_month_period*

                        if ($location_create_date < $first_month_period) {

                            array_push($busy_rooms_at_first_month, $room);
                            array_push($all_busy_rooms_at_first_month, $room);
                        } else {
                            array_push($frees_rooms_at_first_month, $room);
                            array_push($all_frees_rooms_at_first_month, $room);
                        }
                    }
                }

                ###__
                if ($is_this_room_buzy) { ##__quand la chambre est occupée
                    array_push($busy_rooms, $room);
                    array_push($all_busy_rooms, $room);
                } else {
                    array_push($frees_rooms, $room); ##__quand la chambre est libre
                    array_push($all_frees_rooms, $room);
                }
            }

            $house["busy_rooms"] = $busy_rooms;
            $house["frees_rooms"] = $frees_rooms;
            $house["busy_rooms_at_first_month"] = $busy_rooms_at_first_month;
            $house["frees_rooms_at_first_month"] = $frees_rooms_at_first_month;

            ####____
            array_push($new_houses_data, $house);
        }

        #####_______

        foreach ($this->houses as $house) {
            array_push($this->all_busy_rooms, $house["busy_rooms"]);
            array_push($this->all_frees_rooms_at_first_month, $house["busy_rooms_at_first_month"]);
        };
    }

    ###____
    public function render()
    {
        return view('livewire.performance');
    }
}
