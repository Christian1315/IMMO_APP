<?php

namespace App\Livewire;

use App\Models\RoomNature;
use App\Models\RoomType;
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
    }

    public function render()
    {
        return view('livewire.room');
    }
}
