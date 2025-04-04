<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\House;
use App\Models\Room;
use App\Models\RoomNature;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    ##======== ROOM TYPE VALIDATION =======##
    static function room_type_rules(): array
    {
        return [
            "name" => ["required"],
            "description" => ["required"],
        ];
    }

    static function room_type_messages(): array
    {
        return [
            "name.required" => "Le nom du type de la chambre est réquis!",
            "description.required" => "La description du type de la chambre est réquise!",
        ];
    }

    ##======== ROOM ADD VALIDATION =======##
    static function room_rules(): array
    {
        return [
            "house" => ["required", "integer"],
            "nature" => ["required", "integer"],
            "type" => ["required", "integer"],
            "loyer" => ["required", "numeric"],
            "number" => ["required"],
        ];
    }

    static function room_messages(): array
    {
        return [
            "house.required" => "Veuillez préciser la maison!",
            "nature.required" => "Veuillez préciser la nature de la chambre!",
            "type.required" => "Veuillez préciser le type de la chambre!",

            "house.integer" => "Ce champ doit être de type entier!",
            "nature.integer" => "Ce champ doit être de type entier!",
            "type.integer" => "Ce champ doit être de type entier!",

            "loyer.required" => "Le loyer est réquis",
            "loyer.numeric" => "Le loyer doit être de type numérique!",

            "number.required" => "Le numéro de la chambre est réquis",

            "gardiennage.required" => "Ce Champ est réquis!",

            "home_banner.boolean" => "Ce Champ est un booléen!",
        ];
    }

    // ###### FORAGE VALIDATION #########
    static function forage_rules(): array
    {
        return [
            "forfait_forage" => ["required", "numeric"],
        ];
    }

    static function forage_messages(): array
    {
        return [
            "forfait_forage.required" => "Le forfait forage est réquis!",
            "forfait_forage.numeric" => "Le forfait forage doit être de type numérique!",
        ];
    }

    ##======== CONVENTIONNEL COUNTER VALIDATION =======##
    static function conven_counter_water_rules(): array
    {
        return [
            "water_counter_number" => ["required"],
            "water_counter_start_index" => ["required", "numeric"],
        ];
    }

    static function conven_counter_water_messages(): array
    {
        return [
            "water_counter_number.required" => "Le numéro du compteur est réquis!",
            "water_counter_start_index.required" => "L'index de début du compteur est réquis!",

            "water_counter_start_index.numeric" => "Ce Champ est doit être de type numérique!",
        ];
    }

    ##======== DISCONTER WATER VALIDATION =======##
    static function discounter_water_rules(): array
    {
        return [
            "unit_price" => ["required", "numeric"],
        ];
    }

    static function discounter_water_messages(): array
    {
        return [
            "unit_price.required" => "Le prix unitaire du compteur electrique est réquis!",
            "unit_price.numeric" => "Le prix unitaire du compteur electrique doit être de type numérique!",
        ];
    }

    ##======== ELECTRICITY DISCOUNTER VALIDATION =======##
    static function electricity_discounter_rules(): array
    {
        return [
            "electricity_unit_price" => ["required", "numeric"],
            "electricity_counter_number" => ["required"],
            "electricity_counter_start_index" => ["required", "numeric"],
        ];
    }

    static function electricity_discounter_messages(): array
    {
        return [
            "electricity.required" => "L'electricité est réquise",
            "electricity_unit_price.required" => "Le prix unitaire de l'electricité est réquis",
            "electricity_counter_number.required" => "Le numéro du compteur d'electricité est réquis",
            "electricity_counter_start_index.required" => "L'index de debut du compteur électrique est réquis",

            "electricity_unit_price.numeric" => "Le prix unitaire d'electricité doit être de type numérique",
            "electricity_counter_start_index.numeric" => "L'index de debut du compteur électrique doit être de type numérique!",
        ];
    }


    ##################========== ROOM METHOD =============##############
    public function AddRoomType(Request $request)
    {
        $formData = $request->all();
        $rules = self::room_type_rules();
        $messages = self::room_type_messages();
        Validator::make($formData, $rules, $messages)->validate();

        RoomType::create($formData);
        alert()->success("Succès", "Type de chambre ajouté avec succès!");
        return back()->withInput();
    }

    public function AddRoomNature(Request $request)
    {
        $formData = $request->all();
        $rules = self::room_type_rules();
        $messages = self::room_type_messages();

        Validator::make($formData, $rules, $messages)->validate();

        RoomNature::create($formData);
        alert()->success("Succès", "Nature de chambre ajoutée avec succès!");
        return back()->withInput();
    }

    function _AddRoom(Request $request)
    {

        try {
            DB::beginTransaction();
            $formData = $request->all();

            #####_____VALIDATION
            $rules = self::room_rules();
            $messages = self::room_messages();
            Validator::make($formData, $rules, $messages)->validate();

            $user = request()->user();

            ###____TRAITEMENT DU HOUSE
            $house = House::where(["visible" => 1])->find($formData["house"]);
            if (!$house) {
                alert()->error("Echec", "Cette maison n'existe pas!");
                return back()->withInput();
            }

            ###____TRAITEMENT DU HOUSE NATURE
            $nature = RoomNature::find($formData["nature"]);
            if (!$nature) {
                alert()->error("Echec", "Cette nature de chambre n'existe pas!");
                return back()->withInput();
            }

            ###____TRAITEMENT DU HOUSE TYPE
            $type = RoomType::find($formData["type"]);
            if (!$type) {
                alert()->error("Echec", "Ce type de chambre n'existe pas!");
                return back()->withInput();
            }

            ###___

            if ($request->water) {

                ###____
                if ($request->get("forage")) {
                    $rules = self::forage_rules();
                    $messages = self::forage_messages();
                    Validator::make($formData, $rules, $messages)->validate();
                }

                ###____
                if ($request->get("water_conventionnal_counter")) {
                    $rules = self::conven_counter_water_rules();
                    $messages = self::conven_counter_water_messages();
                    Validator::make($formData, $rules, $messages)->validate();
                }


                ###____
                if ($request->get("water_discounter")) {
                    if ($request->get("water_discounter")) {
                        $rules = self::discounter_water_rules();
                        $messages = self::discounter_water_messages();
                        Validator::make($formData, $rules, $messages)->validate();
                    }
                }
            }

            ###____
            if ($request->electricity) {

                if ($request->electricity_discounter) {
                    $rules = self::electricity_discounter_rules();
                    $messages = self::electricity_discounter_messages();
                    Validator::make($formData, $rules, $messages)->validate();
                }
            }


            ###____TRAITEMENT DE L'IMAGE
            if ($request->file("photo")) {
                $photo = $request->file("photo");
                $photoName = $photo->getClientOriginalName();
                $photo->move("room_images", $photoName);
                $formData["photo"] = asset("room_images/" . $photoName);
            }

            #ENREGISTREMENT DE LA CARTE DANS LA DB
            $formData["gardiennage"] = $request->gardiennage ? $request->gardiennage : 0;
            $formData["vidange"] = $request->vidange ? $request->vidange : 0;

            $formData["owner"] = $user->id;
            $formData["water"] = $request->water ? 1 : 0;
            $formData["water_discounter"] = $request->water_discounter ? 1 : 0;
            $formData["forage"] = $request->forage ? 1 : 0;
            $formData["forfait_forage"] = $request->forfait_forage ? $request->forfait_forage : 0;
            $formData["water_counter_number"] = $request->water_counter_number ? $request->water_counter_number : "--";
            $formData["water_conventionnal_counter"] = $request->water_conventionnal_counter ? 1 : 0;
            $formData["water_counter_start_index"] = $request->water_counter_start_index ? $request->water_counter_start_index : 0;

            $formData["electricity"] = $request->electricity ? 1 : 0;
            $formData["electricity_discounter"] = $request->electricity_discounter ? 1 : 0;
            $formData["electricity_conventionnal_counter"] = $request->electricity_conventionnal_counter ? 1 : 0;
            $formData["electricity_card_counter"] = $request->electricity_card_counter ? 1 : 0;
            $formData["electricity_counter_number"] = $request->electricity_counter_number ? $request->electricity_counter_number : "--";
            $formData["electricity_counter_start_index"] = $request->electricity_counter_start_index ? $request->electricity_counter_start_index : 0;


            $formData["cleaning"] = $request->cleaning ? $request->cleaning : 0;
            $formData["comments"] = $request->comments ? $request->comments : "---";
            $formData["rubbish"] = $request->rubbish ? $request->rubbish : 0;


            $formData["total_amount"] = $formData["loyer"] + $formData["gardiennage"] + $formData["rubbish"] + $formData["vidange"] + $formData["cleaning"];

            Room::create($formData);

            DB::commit();
            alert()->success("Succès", "Chambre ajoutée avec succès!!");
            return back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            alert()->error("Error", "Une erreure est survenue");
            return back()->withInput();
        }
    }

    ###___FILTRE BY SUPERVISOR
    function FiltreRoomBySupervisor(Request $request, $agency)
    {
        $user = request()->user();
        $agency = Agency::find($agency);

        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        ####____
        $supervisor = User::find($request->supervisor);
        if (!$supervisor) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        $rooms = [];
        foreach ($agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses->where("supervisor", $request->supervisor) as $house) {
                foreach ($house->Rooms as $room) {
                    array_push($rooms, $room);
                }
            }
        }

        if (count($rooms) == 0) {
            alert()->error("Echèc", "Aucun résultat trouvé");
            // Session::forget("filteredHouses");
            return back()->withInput();
        }

        session()->flash("filteredRooms", collect($rooms));

        alert()->success("Succès", "Chambres filtrées par superviseur avec succès!");
        return back()->withInput();
    }

    ###___FILTRE BY HOUSE
    function FiltreRoomByHouse(Request $request, $agency)
    {
        $user = request()->user();
        $agency = Agency::find($agency);

        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        ####____
        $house = House::find($request->house);
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        }

        $rooms = [];
        foreach ($agency->_Proprietors as $proprio) {
            foreach ($proprio->Houses as $house) {
                if ($house->id == $request->house) {
                    foreach ($house->Rooms as $room) {
                        array_push($rooms, $room);
                    }
                }
            }
        }

        if (count($rooms) == 0) {
            alert()->error("Echèc", "Aucun résultat trouvé");
            // Session::forget("filteredHouses");
            return back()->withInput();
        }

        session()->flash("filteredRooms", $rooms);
        $msg = "Chambres filtrées filtrées par maison  $house->name avec succès!";
        alert()->success("Succès", $msg);
        return back()->withInput();
    }


    function UpdateRoom(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = request()->user();
            $formData = $request->all();
            $room = Room::where(["visible" => 1])->find($id);
            if (!$room) {
                alert()->error("Echec", "Cette Chambre n'existe pas!");
                return back()->withInput();
            };

            if (!auth()->user()->is_master && !auth()->user()->is_admin) {
                if ($room->owner != $user->id) {
                    alert()->error("Echec", "Cette Chambre n'a pas été crée par vous! Vous ne pouvez pas la modifier");
                    return back()->withInput();
                }
            }

            ###____TRAITEMENT DU HOUSE
            if ($request->get("house")) {
                $house = House::where(["visible" => 1])->find($request->get("house"));
                if (!$house) {
                    alert()->error("Echec", "Cette Chambre n'existe pas!");
                    return back()->withInput();
                }
            }

            ###____TRAITEMENT DU HOUSE NATURE
            if ($request->get("nature")) {
                $nature = RoomNature::find($request->get("nature"));
                if (!$nature) {
                    alert()->error("Echec", "Cette nature de chambre n'existe pas!");
                    return back()->withInput();
                }
            }

            ###____TRAITEMENT DU ROOM TYPE
            if ($request->get("type")) {
                $type = RoomType::find($request->get("type"));
                if (!$type) {
                    alert()->error("Echec", "Ce type de chambre n'existe pas!");
                    return back()->withInput();
                }
            }
            $formData["total_amount"] = $formData["loyer"] + $formData["gardiennage"] + $formData["rubbish"] + $formData["vidange"] + $formData["cleaning"];
            $formData["electricity_counter_number"] = $room->electricity_counter_number;

            #ENREGISTREMENT DE LA CARTE DANS LA DB
            $room->update($formData);

            DB::commit();
            alert()->success("Succès", "Chambre modifiée avec succès!");
            return back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            alert()->error("Error", "Une erreure est survenue");
            return back()->withInput();
        }
    }

    function DeleteRoom(Request $request, $id)
    {
        $user = auth()->user();
        $room = Room::where(["visible" => 1])->find(deCrypId($id));
        if (!$room) {
            alert()->error("Echec", "Cette Chambre n'existe pas!");
            return back()->withInput();
        };

        if (!auth()->user()->is_master && !auth()->user()->is_admin) {
            if ($room->owner != $user->id) {
                alert()->error("Echec", "Cette Chambre ne vous appartient pas!");
                return back()->withInput();
            }
        }

        $room->visible = 0;
        $room->delete_at = now();
        $room->save();

        alert()->success("Succès", "Chambre supprimée avec succès!");
        return back();
    }
}
