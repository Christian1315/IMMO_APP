<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyAccount;
use App\Models\AgencyAccountSold;
use App\Models\Facture;
use App\Models\HomeStopState;
use App\Models\House;
use App\Models\ImmoAccount;
use App\Models\Locataire;
use App\Models\Location;
use App\Models\LocationElectrictyFacture;
use App\Models\LocationStatus;
use App\Models\LocationType;
use App\Models\PaiementType;
use App\Models\Payement;
use App\Models\Room;
use App\Models\StopHouseElectricityState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth'])->except(["_ManageCautions", "_ShowCautionsForHouseByPeriod", "_ShowCautionsByPeriod"]);
    }

    ########==================== ROOM TYPE VALIDATION ===================#####
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
            "name.required" => "Le nom du type de la location est réquis!",
            "description.required" => "La description du type de la location est réquise!",
        ];
    }

    ##======== LOCATION VALIDATION =======##
    static function location_rules(): array
    {
        return [
            'agency' => ['required', "integer"],
            'house' => ['required', "integer"],
            'room' => ['required', "integer"],
            'locataire' => ['required', "integer"],
            'type' => ['required', "integer"],

            // 'pre_paid' => ['required', "boolean"],
            // 'post_paid' => ['required', "boolean"],
            // 'discounter' => ['required', "boolean"],

            // 'caution_bordereau' => ["file"],
            // 'loyer' => ['required', "numeric"],
            'water_counter' => ['required'],
            'electric_counter' => ['required'],

            'caution_number' => ['required', 'integer'],

            'prestation' => ['required', "numeric"],
            'numero_contrat' => ['required'],

            // 'comments' => ['required'],
            // 'img_contrat' => ["file"],
            // 'caution_water' => ['required', "numeric"],
            // 'echeance_date' => ['required', "date"],
            // 'latest_loyer_date' => ['required', "date"],
            // 'img_prestation' => ["file"],
            'caution_electric' => ['required', "numeric"],
            'effet_date' => ['required', "date"],
            // 'frais_peiture' => ['required', "numeric"],
        ];
    }

    static function location_messages(): array
    {
        return [
            'agency.required' => 'Veuillez préciser l\'agence!',
            'agency.integer' => "L'agence doit être de type entier",

            'house.required' => 'La maison est réquise!',
            'house.integer' => 'Ce champ doit être de type integer',

            'room.required' => "Le chambre est réquise!",
            'room.integer' => 'Ce champ doit être de type integer',

            'locataire.required' => "Le location est réquis!",
            'locataire.integer' => 'Ce champ doit être de type integer',

            'type.required' => "Le type de location est réquis!",
            'type.integer' => 'Ce champ doit être de type integer',

            // 'caution_bordereau.required' => "Le bordereau de la caution est réquise!",
            'caution_bordereau.file' => "Le bordereau de la caution doit être un fichier!",

            // 'loyer.required' => "Le loyer de la location est réquise!",
            // 'loyer.numeric' => "Ce champ doit être de type numeric!",

            'caution_number.required' => "Le nombre de caution est réquise!",
            'caution_number.integer' => "Le nombre de caution doit être de type integer!",

            'frais_peiture.required' => "Les frais de reprise de peinture sont réquis!",
            'frais_peiture.numeric' => "Ce champ  doit être de caractère numérique!",

            'water_counter.required' => "Le numéro du compteur d'eau est réquis",
            'electric_counter.required' => "Le numéro du compteur électrique est réquis",
            // 'water_counter.numeric' => "Le champ compteur d'eau doit être de caractère numérique",

            'prestation.required' => "La prestation est réquise",
            'prestation.file' => "La prestation doit être un fichier",

            'numero_contrat.required' => "Le numéro du contrat est réquis!",
            'comments.required' => "Le commentaire est réquis",

            'img_contrat.required' => "L'image du contrat est réquise",
            'img_contrat.file' => "L'image du contrat doit être un fichier",

            // 'caution_water.required' => "La caution d'eau est réquise",
            // 'caution_water.numeric' => "La caution d'eau doit être de caractère numérique",

            // 'echeance_date.required' => "La date d'écheance est réquise!",
            // 'echeance_date.date' => "Ce champ doit être de type date",

            // 'latest_loyer_date.required' => "La date du dernier loyer est réquis!",
            // 'latest_loyer_date.date' => "Ce champ doit être de type date",

            'pre_paid.boolean' => "Le champ doit être un booléen!",
            'post_paid.boolean' => "Le champ doit être un booléen",

            'discounter.required' => "Le decompteur est réquis!",
            'discounter.boolean' => "Ce champ doit être de type booléen",

            'img_prestation.file' => "L'image de la prestation doit être un fichier",

            'caution_electric.required' => "La caution d'electricité est réquise!",
            'caution_electric.numeric' => 'Le type de la caution d\'electricité doit être de type numéric!',

            'effet_date.required' => "La date d'effet est réquise!",
            'effet_date.date' => "Ce champ est de type date",
        ];
    }

    ##======== PAIEMENT VALIDATION =======##
    static function paiement_rules(): array
    {
        return [
            'location' => ['required', "integer"],
            'type' => ['required', "integer"],
            // 'month' => ['required', "date"],
            'facture_code' => ['required', "unique:factures,facture_code"],
        ];
    }

    static function paiement_messages(): array
    {
        return [
            'location.required' => 'La location  est réquise!',
            'type.required' => "Le type de paiement est réquis!",
            'location.integer' => "Ce champ doit être de type entier!",
            'type.integer' => "Ce champ doit être de type entier!",
            // 'month.required' => "Ce champ est réquis!",
            // 'month.date' => "Ce champ doit être de type date!",

            'facture_code.required' => "Veillez préciser le code de la facture!",
            'facture_code.unique' => "Ce code de facture existe déjà!",
        ];
    }

    ########################===================== FIN DES VALIDATIONS ======================###############

    ########################===================== DEBUT DES METHODES ======================###############

    ####____AJOUT D'UN TYPE DE LOCATION
    static function _AaddType(Request $request)
    {
        // validation
        $formData = $request->all();
        Validator::make($request->all(), self::room_type_rules(), self::room_type_messages())->validate();


        $type = LocationType::create($formData);
        alert()->success("Succès", "Type de location ajouté avec succès!");
        return back();
    }

    // AGENCY CAUTION MANAGEMENT
    function _ManageCautions(Request $request, $agencyId)
    {
        $agency = Agency::where("visible", 1)->find(deCrypId($agencyId));
        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back();
        }
        $locations = $agency->_Locations;

        $cautions_eau = [];
        $cautions_electricity = [];
        $cautions_loyer = [];

        foreach ($locations as $location) {
            array_push($cautions_electricity, $location->caution_electric);
            array_push($cautions_eau, $location->caution_water);
            array_push($cautions_loyer, ($location->caution_number * $location->loyer));
        }
        alert()->success('Succès', "Caution générées avec succès!");
        return view("cautions", compact(["locations", "cautions_eau", "cautions_electricity", "cautions_loyer"]));
    }

    #####___GENERATION DES PRESTATION PAR PERIODE
    function _ManagePrestationStatistiqueForAgencyByPeriod(Request $request, $agencyId)
    {
        ##__
        $formData = $request->all();

        $agency = Agency::where("visible", 1)->find(deCrypId($agencyId));
        if (!$agency) {
            alert()->error("Echec", "Désolé! Cette Agence n'existe pas!");
            return back();
        }

        $prestations = [];

        ####____
        $locations = $agency->_Locations->whereBetween("created_at", [$request->first_date, $request->last_date]);
        foreach ($locations as $location) {
            array_push($prestations, $location->prestation);
        }

        return view("prestation-statistique", compact(["locations", "prestations", "agency"]));
    }

    function _AddLocation(Request $request)
    {
        $formData = $request->all();
        #VERIFICATION DE LA METHOD
        Validator::make($formData, self::location_rules(), self::location_messages())->validate();

        ####___VERIFIONS S'IL Y A ELECTRICITE OU PAS
        // if ($request->discounter == true) {
        //     Validator::make(
        //         $formData,
        //         [
        //             "kilowater_price" => ["required", "numeric"],
        //         ],
        //         [
        //             "kilowater_price.required" => "Veuillez préciser le prix du kilowatère!",
        //             "kilowater_price.date" => "Ce champ est de type numérique!",
        //         ]
        //     )->validate();
        // } else {
        //     $formData["kilowater_price"] = 0;
        // }


        $user = request()->user();

        ###___TRAITEMENT DES DATAS
        $house = House::find($formData["house"]);
        $room = Room::find($formData["room"]);
        $locataire = Locataire::find($formData["locataire"]);
        $type = LocationType::find($formData["type"]);
        $agency = Agency::find($formData["agency"]);

        ##__LES INFOS LIEES AU COMPTEUEr LECTRIQUE D'UNE LOCATION REVIENNENT AUX INFOS ELECYTRICQUE DE LMA CHAMBRE CHOISIE
        $formData["discounter"] = $room->electricity ? true : false;
        $formData["kilowater_price"] = $room->electricity ? ($room->electricity_unit_price ? $room->electricity_unit_price : 0) : 0;
        ###___

        if ($request->pre_paid == $request->post_paid) {
            alert()->error("Echec", "Veuillez choisir soit l'option pré-payé, soit le post-payé!");
            return back()->withInput();
        }

        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        }

        if (!$room) {
            alert()->error("Echec", "Cette chambre n'existe pas!");
            return back()->withInput();
        } else {
            if ($room->House->id != $request->house) {
                alert()->error("Echec", "Cette chambre n'appartient pas à la maison " . House::find($request->house)->name);
                return back()->withInput();
            }
        }

        if (!$locataire) {
            alert()->error("Echec", "Ce locataire n'existe pas!");
            return back()->withInput();
        }

        if (!$type) {
            alert()->error("Echec", "Ce type de location n'existe pas!");
            return back()->withInput();
        }

        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        ####___ TRAITEMENT DE LA CHAMBRE
        if (!$room) {
            alert()->error("Echec", "Cette chambre n'existe pas!");
            return back()->withInput();
        }

        // $room_location = Location::where(["room" => $formData["room"], "house" => $formData["house"]])->first();
        // if ($room_location && $room_location->status != 3) {
        //     alert()->error("Echec", "Cette chambre est déjà occupée!");
        //     return back()->withInput();
        // }

        ##___TRAITEMENT DES IMAGES
        if ($request->file("caution_bordereau")) {
            $caution_bordereau = $request->file("caution_bordereau");
            $caution_bordereauName = $caution_bordereau->getClientOriginalName();
            $caution_bordereau->move("caution_bordereaus", $caution_bordereauName);
            $formData["caution_bordereau"] = asset("caution_bordereaus/" . $caution_bordereauName);
        }

        if ($request->file("img_contrat")) {
            $img_contrat = $request->file("img_contrat");
            $img_contratName = $img_contrat->getClientOriginalName();
            $img_contrat->move("img_contrats", $img_contratName);
            $formData["img_contrat"] = asset("img_contrats/" . $img_contratName);
        }

        if ($request->file("img_prestation")) {
            $img_prestation = $request->file("img_prestation");
            $img_prestationName = $img_contrat->getClientOriginalName();
            $img_prestation->move("img_prestations", $img_prestationName);
            $formData["img_prestation"] = asset("img_prestations/" . $img_prestationName);
        }

        #ENREGISTREMENT DU LOCATION DANS LA DB
        $formData["owner"] = $user->id;

        ##__
        $formData["loyer"] = $room->total_amount;

        $formData["pre_paid"] = $request->pre_paid ? true : false;
        $formData["post_paid"] = $request->post_paid ? true : false;
        $formData["comments"] = $request->comments ? $request->comments : "---";
        $formData["frais_peiture"] = $request->frais_peiture ? $request->frais_peiture : 0;

        if ($formData["pre_paid"] == $formData["post_paid"]) {
            alert()->error("Echec", "Veuillez choisir soit l'option *prepayée* ou *postpayée*");
            return back()->withInput();
        }

        ###__DETERMIONONS LA DATE D'ECHEANCE
        $echeance_date = "";
        // $effet_date = $formData["integration_date"]; ##__ date d'effet n'est rien d'autre que la date d'intégration

        // dd($formData);
        if ($formData["pre_paid"]) {
            ##__En pre-payé, la date d'echeance revient à la date
            ##__de prise d'effet + 1 mois (date d'intégration)
            $integration_date_timestamp_plus_one_month = strtotime("+1 month", strtotime($formData["effet_date"]));
            $echeance_date = date("Y/m/d", $integration_date_timestamp_plus_one_month);
        } elseif ($formData["post_paid"]) {
            ##__En post-payé, la date d'echeance revient à la date
            ##__de prise d'effet (date d'intégration) + 2 mois
            $integration_date_timestamp_plus_one_month = strtotime("+2 month", strtotime($formData["effet_date"]));
            $echeance_date = date("Y/m/d", $integration_date_timestamp_plus_one_month);
        }

        $formData["integration_date"] = $formData["effet_date"];

        $formData["previous_echeance_date"] = $echeance_date;
        $formData["echeance_date"] = $echeance_date;
        $formData["latest_loyer_date"] = $formData["effet_date"];

        ####___DESORMAIS LA DATE DU PROCHAIN REVIENT A LA DATE PAIEMENT D'ECHEANCE 
        $formData["next_loyer_date"] = date("Y/m/d", strtotime("+1 month", strtotime($formData["latest_loyer_date"])));
        $location = Location::create($formData);

        ###___FORMATION DE LA PROCHAINE DATE DE LOYER
        // ça revient à la précedente date de loyer + 1 mois

        // $location_latest_loyer_timestamp_plus_one_month = strtotime("+1 month", strtotime($location->latest_loyer_date));
        // $location_next_loyer_date = date("Y/m/d", $location_latest_loyer_timestamp_plus_one_month);
        // $location->next_loyer_date = $location_next_loyer_date;

        ###__DETERMIONONS LA DATE DU DERNIER LOYE PAYE (ça revient à la date d'effet aui n'est rien d'autre que la date d'intégration)
        $location->latest_loyer_date = $location->integration_date; ##(date d'effet)

        $location->save();
        ###___
        alert()->success("Succès", "Location ajoutée avec succès!!");
        return back()->withInput();
    }

    ###___MODIFIER UNE LOCATION
    function UpdateLocation(Request $request, $id)
    {
        $user = request()->user();

        $formData = $request->all();

        $location = Location::where(["visible" => 1])->find(deCrypId($id));

        if (!$location) {
            alert()->error("Echec", "Cette location n'existe pas!");
            return back()->withInput();
        };


        if (!auth()->user()->is_master && !auth()->user()->is_admin) {
            if ($location->owner != $user->id) {
                alert()->error("Echec", "Cette location ne vous appartient pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU HOUSE
        if ($request->get("house")) {
            $house = House::find($request->get("house"));
            if (!$house) {
                alert()->error("Echec", "Cette location maison n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DE LA CHAMBRE
        if ($request->get("room")) {
            $room = Room::find($request->get("room"));

            if (!$room) {
                alert()->error("Echec", "Cette chambre n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU LOCATAIRE
        if ($request->get("locataire")) {
            $locataire = Locataire::find($request->get("locataire"));
            if (!$locataire) {
                alert()->error("Echec", "Ce locataire n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU TYPE DE LOCATION
        if ($request->get("type")) {
            $type = LocationType::find($request->get("type"));
            if (!$type) {
                alert()->error("Echec", "Ce type de location n'existe pas!");
                return back()->withInput();
            }
        }
        ####____TRAITEMENT DU CAUTION BORDEREAU
        if ($request->file("caution_bordereau")) {
            $caution_bordereau = $request->file("caution_bordereau");
            $caution_bordereauName = $caution_bordereau->getClientOriginalName();
            $caution_bordereau->move("caution_bordereaus", $caution_bordereauName);
            $formData["caution_bordereau"] = asset("caution_bordereaus/" . $caution_bordereauName);
        } else {
            $formData["caution_bordereau"] = $location->caution_bordereau;
        }

        ####____TRAITEMENT DE L'IMAGE DU CONTRAT
        if ($request->file("img_contrat")) {
            $img_contrat = $request->file("img_contrat");
            $img_contratName = $img_contrat->getClientOriginalName();
            $img_contrat->move("img_contrats", $img_contratName);
            $formData["img_contrat"] = asset("img_contrats/" . $img_contratName);
        } else {
            $formData["img_contrat"] = $location->img_contrat;
        }


        ####____TRAITEMENT DE L'IMAGE DE LA PRESTATION
        if ($request->file("img_prestation")) {
            $img_prestation = $request->file("img_prestation");
            $img_prestationName = $img_contrat->getClientOriginalName();
            $img_prestation->move("img_prestations", $img_prestationName);
            $formData["img_prestation"] = asset("img_prestations/" . $img_prestationName);
        } else {
            $formData["img_prestation"] = $location->img_prestation;
        }


        ####____TRAITEMENT DU STATUS DE LOCATION
        if ($request->get("status")) {
            $status = LocationStatus::find($request->get("status"));
            if (!$status) {
                alert()->error("Echec", "Ce status de location n'existe pas!");
                return back()->withInput();
            }

            #===SI LE STATUS EST **SUSPEND**=====#
            if ($request->get("status") == 2) {
                if (!$request->get("suspend_comments")) {
                    alert()->error("Echec", "Veuillez préciser la raison de suspenssion de cette location!");
                    return back()->withInput();
                }
                $formData["suspend_date"] = now();
                $formData["suspend_by"] = $user->id;
            }

            #===SI LE STATUS EST **MOVED**=====#
            if ($request->get("status") == 3) {
                if (!$request->get("move_comments")) {
                    alert()->error("Echec", "Veuillez préciser la raison de demenagement de cette location!");
                    return back()->withInput();
                }
                $formData["move_date"] = now();
                $formData["visible"] = 0;
                $formData["delete_at"] = now();
            }
        }

        $location->update($formData);

        ####____
        alert()->success("Succès", "Location modifiée avec succès!");
        return back()->withInput();
    }

    // IMPRESSION
    function Imprimer(Request $request, $locationId)
    {
        $location = Location::where("visible", 1)->find(deCrypId($locationId));
        return view("imprimer", compact("location"));
    }

    ####____DEMENAGEMENT
    function DemenageLocation(Request $request, $locationId)
    {
        #####____VALIDATION DES DATAS
        $formData = $request->all();
        Validator::make($formData, ["move_comments" => "required"], ["move_comments.required" => "Le commentaire est réquis!"])->validate();

        $user = request()->user();
        $location = Location::where(["visible" => 1])->find(deCrypId($locationId));

        if (!$location) {
            alert()->error("Echec", "Cette location n'existe pas!");
            return back()->withInput();
        };

        $house = House::find($location->house);

        ###___DERNIERE DATE D'ARRET DES ETATS DE CETTE MAISON ##
        $state_stop_date_of_this_house = HomeStopState::orderBy("id", "desc")->where(["house" => $location->house])->get();
        if (count($state_stop_date_of_this_house) != 0) { ##Quand la maison dispose d'une date des arrets des etats

            ###__DATE D'ARRET DES ETATS DE CETTE MAISON
            $state_stop_date_of_this_house = strtotime($state_stop_date_of_this_house[0]->stats_stoped_day);

            ###__LES LOCATIONS DE CETTE MAISON
            $this_house_locations = $house->Locations;

            ###__LES PAIEMENTS LIES A LA LOCATION DE CETTE MAISON
            $locations_that_paid_before_state_stoped_day = [];
            $locations_that_paid_after_state_stoped_day = [];

            foreach ($this_house_locations as $this_house_location) {
                ###__RECUPERONS LES LOCATIONS AYANT PAYES
                $location_payements = Payement::with(["Status", "Location", "Facture"])->where(["location" => $this_house_location->id])->get();

                ##__TRAITEMENT DE LA DATE DE PAIEMENT( puis filtrer les locations avant et après paiement)
                foreach ($location_payements as $location_payement) {
                    $location_payement_date = strtotime($location_payement->created_at);

                    if ($location_payement_date < $state_stop_date_of_this_house) {
                        array_push($locations_that_paid_before_state_stoped_day, $this_house_location);
                    } else {
                        array_push($locations_that_paid_after_state_stoped_day, $this_house_location);
                    }
                }
            };

            ###___Verifions si ce locataire fait parti des locataires qui ont payé
            ###___après arret des etats

            $result = false;
            foreach ($locations_that_paid_after_state_stoped_day as $locations_that_paid_after_state_stoped_day_) {
                if ($locations_that_paid_after_state_stoped_day_->locataire == $location->locataire) {
                    $result = true;
                }
            }

            if ($result) {
                alert()->error("Echec", "Ce locataire a effectué des paiements après l'arrêt des états! Vous ne pouvez pas le démenager!");
                return back()->withInput();
            }
        }

        $formData["move_date"] = now();
        $formData["status"] = 3;
        // $formData["visible"] = 1;

        $location->update($formData);

        ####____
        alert()->success("Succès", "Locataire demenagé avec succès");
        return back()->withInput();
    }

    ###____ENCAISSEMENT
    function _AddPaiement(Request $request)
    {
        $formData = $request->all();
        $user = request()->user();

        #####______VALIDATION DES DATAS 
        $rules = self::paiement_rules();
        $messages = self::paiement_messages();

        Validator::make($formData, $rules, $messages)->validate();

        ###___TRAITEMENT DES DATAS
        $location = Location::with(["House", "Locataire", "Room"])->find($formData["location"]);
        $type = PaiementType::find($formData["type"]);

        $formData["module"] = 2;
        $formData["status"] = 1;
        $formData["amount"] = $location->loyer;
        $formData["comments"] = "Encaissement de loyer à la date " . now() . " pour le locataire (" . $location->Locataire->name . " " . $location->Locataire->prenom . " ) habitant la chambre (" . $location->Room->number . ") de la maison (" . $location->House->name . " ) par <<" . $user->name . ">> ";

        ###__GESTION DE LA REFERENCE
        // $payement_count = count(Payement::all());
        // $formData["reference"] = "REF_" . $payement_count . rand(0, 100) . "/" . substr($type->name, 0, 3); ###__ON RECUPERE LES TROIS PREMIERES LETTRES DE LA CATEGORIE DU DOSSIER QU'ON CONCATENE AVEC LE RAND

        ###__
        if (!$location) {
            alert()->error("Echec", "Cette location n'existe pas!");
            return back()->withInput();
        }

        if (!$type) {
            alert()->error("Echec", "Ce type de paiement n'existe pas!");
            return back()->withInput();
        }

        ###___TRAITEMENT DU PAIEMENT SI LE LOCATAIRE EST UN PRORATA
        if ($location->Locataire->prorata) {
            Validator::make(
                $formData,
                [
                    "prorata_amount" => ["required", "numeric"],
                    "prorata_days" => ["required", "numeric"],
                    "prorata_date" => ["required", "date"],
                ],
                [
                    "prorata_amount.required" => "Veuillez préciser le montant du prorata",
                    "prorata_amount.numeric" => "Ce champ doit être de format numérique",

                    "prorata_days.required" => "Veuillez préciser le nombre de jour du prorata",
                    "prorata_days.numeric" => "Ce champ doit être de format numérique",

                    "prorata_date.required" => "Veuillez préciser la date de jour du prorata",
                    "prorata_date.date" => "Ce champ doit être de format date",
                ]
            )->validate();


            ###___CHANGEMENT D'ETAT DU LOCATAIRE(NOTIFIONS Q'IL N'EST PLUS UN PRORATA)
            $locataire = Locataire::find($location->locataire);

            $locataire->prorata = false;
            $locataire->save();
        }

        ###__ENREGISTREMENT DE LA FACTURE DE PAIEMENT DANS LA DB
        if ($request->file("facture")) {
            $factureFile = $request->file("facture");
            $fileName = $factureFile->getClientOriginalName();
            $factureFile->move("factures", $fileName);
            $formData["facture"] = asset("factures/" . $fileName);
        } else {
            $formData["facture"] = $location->facture;
        }
        ##___

        $factureDatas = [
            "owner" => $user->id,
            "echeance_date" => $location['next_loyer_date'],
            // "payement" => $Paiement->id,
            "location" => $formData["location"],
            "type" => 1,
            "facture" => $formData["facture"],
            "begin_date" => null,
            "end_date" => null,
            "comments" => $formData["comments"],
            "amount" => $formData["amount"],
            "facture_code" => $formData["facture_code"],
            "is_penality" => $request->get("is_penality") ? true : false ##__Préciser si cette facture est liée à une pénalité ou pas
        ];

        $facture = Facture::create($factureDatas);
        ###_____

        ####__ACTUALISATION DE LA LOCATION
        // AJOUT D'UN MOIS DE PLUS SUR LA DERNIERE DATE DE LOYER
        $location_next_loyer_timestamp_plus_one_month = strtotime("+1 month", strtotime($location->next_loyer_date));
        $location_echeance_date_timestamp_plus_one_month = strtotime("+1 month", strtotime($location->echeance_date));

        $location_next_loyer_date = date("Y/m/d", $location_next_loyer_timestamp_plus_one_month);
        $location_echeance_date = date("Y/m/d", $location_echeance_date_timestamp_plus_one_month);

        $location->latest_loyer_date = $location->next_loyer_date; ##__la dernière date de loyer revient maintenant au next_loyer_date
        $location->next_loyer_date = $location_next_loyer_date; ##__le next loyer date est donc incrementé de 1 mois
        $location->echeance_date = $location_echeance_date;
        ###__

        ###___INCREMENTATION DU COMPTE LOYER

        $agency_rent_account = AgencyAccount::where(["agency" => $location->agency, "account" => env("LOYER_ACCOUNT_ID")])->first();

        if (!$agency_rent_account) {
            alert()->error("Echec", "Ce compte n'existe pas! Vous ne pouvez pas le créditer!");
            return back()->withInput();
        }

        $formData["agency_account"] = $agency_rent_account->id;

        $formData["description"] = "Encaissement de paiement à la date " . $facture->created_at . " par le locataire (" . $location->Locataire->name . " " . $location->Locataire->prenom . " ) habitant la chambre (" . $location->Room->number . ") de la maison (" . $location->House->name . " )";
        $formData["sold"] = $formData["amount"];

        ###___VERIFIONS LE SOLD ACTUEL DU COMPTE ET VOYONS SI ça DEPPASE OU PAS LE PLAFOND

        // $accountSold = AccountSold::where(["account" => $id, "visible" => 1])->first();
        $accountSold = AgencyAccountSold::where(["agency_account" => $agency_rent_account->id, "visible" => 1])->first();

        $account = $agency_rent_account->_Account;

        ###___
        if ($accountSold) { ##__Si ce compte dispose déjà d'un sold
            $formData["old_sold"] = $accountSold->sold;
            $formData["sold_added"] = $accountSold->sold_added;

            ##__voyons si le sold atteint déjà le plafond de ce compte
            if ($accountSold->sold >= $account->plafond_max) {
                alert()->error("Echec", "Le sold de ce compte (" . $account->name . ") a déjà atteint son plafond! Vous ne pouvez plus le créditer");
                return back()->withInput();
            } else {
                # voyons si en ajoutant le montant actuel **$formData["sold"]** au sold du compte
                # ça depasserait le plafond maximum du compte
                if (($accountSold->sold + $formData["sold"]) > $account->plafond_max) {
                    alert()->error("Echec", "L'ajout de ce montant au sold de ce compte (" . $account->name . ") dépasserait son plafond! Veuillez diminuer le montant");
                    return back()->withInput();
                }
            }

            ###__creditation proprement dite du compte
            #__Deconsiderons l'ancien sold
            $accountSold->visible = 0;
            $accountSold->delete_at = now();
            $accountSold->save();

            #__Construisons un nouveau sold(en se basant sur les datas de l'ancien sold)
            $formData["account"] = $accountSold->account; ##__ça revient à l'ancien compte
            $formData["sold"] = $accountSold->sold + $formData["sold"];

            $accountSold = AgencyAccountSold::create($formData);
        } else {
            # voyons si en ajoutant le montant actuel **$formData["sold"]** au sold du compte
            # ça depasserait le plafond maximum du compte
            if ($formData["sold"] > $account->plafond_max) {
                alert()->error("Echec", "L'ajout de ce montant au sold de ce compte (" . $account->name . ") dépasserait son plafond! Veuillez diminuer le montant");
                return back()->withInput();
            }

            # on le crée
            $accountSold = AgencyAccountSold::create($formData);
        }


        ####___ACTUALISATION MAINTENANT LA LOCATION
        $location->save();
        ##___

        ###__
        alert()->success("Succès", "Paiement ajouté avec succès!!");
        return back()->withInput();
    }

    ####_____UpdateFactureStatus
    function UpdateFactureStatus(Request $request, $id)
    {
        if (!$request->get("status")) {
            return $this->sendError("Veuillez préciser le status de la facture", 505);
        }

        $facture = Facture::find(deCrypId($id));

        if (!$facture) {
            alert()->error("Echec", "Désolé! Ctte facture n'existe pas!");
            return back()->withInput();
        }

        $facture->status = $request->status;
        $facture->save();

        ###_______
        alert()->success("Succès", "Facture traitée avec succès");
        return back()->withInput();
    }

    ####____ARRET D'ETAT D'UN E?AISON
    function _StopStatsOfHouse(Request $request)
    {
        $user = request()->user();
        $formData = $request->all();

        $formData["owner"] = $user->id;

        $house = House::where(["visible" => 1])->find($formData["house"]);
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        };

        if (count($house->Locations) == 0) {
            alert()->error("Echec", "Cette maison ne dispose d'aucune location! Son arrêt d'état ne peut donc être éffectué");
            return back()->withInput();
        }

        ###_____VERIFIONS D'ABORD SI CETTE HOUSE DISPOSAIT DEJA D'UN ETAT
        $this_house_state = StopHouseElectricityState::orderBy("id", "desc")->where(["house" => $formData["house"]])->first();

        if (!$this_house_state) { ##Si cette maison ne dispose pas d'arrêt d'etat
            ##__ON CREE SON PREMIER ARRET D'ETAT
            $data["house"] = $formData["house"];
            $data["owner"] = $formData["owner"];
            $data["state_stoped_day"] = now();
            $state = StopHouseElectricityState::create($data);
        } else {
            ##S'il dispose d'un arret d'etat déjà

            ##__On verifie si la date d'aujourd'hui atteint ou depasse
            ##__la date de l'arret precedent + 20 jours

            $precedent_arret_date = strtotime($this_house_state->state_stoped_day);
            $now = strtotime(now());
            $twenty_days = 5 * 24 * 3600;

            // if ($now < ($precedent_arret_date + $twenty_days)) {
            //     return self::sendError("La précedente date d'arrêt des états de cette maison ne depasse pas encore 5 jours! Vous ne pouvez donc pas éffectuer un nouveau arrêt d'etats pour cette maison pour le moment", 505);
            // }

            ###__ON ARRËTE LES ETATS
            $data["owner"] = $formData["owner"];
            $data["house"] = $formData["house"];
            $data["state_stoped_day"] = now();
            $state =  StopHouseElectricityState::create($data);
        }

        ###____ ACTUALISONS LES STATES DES FACTURES

        foreach ($house->Locations as $location) {

            // ACTUALISONS LES INDEX DE DEBUT EN ELECTRICITE DE CHAQUE CHAMBRE DE LA MAISON
            $location_factures = $location->ElectricityFactures;

            $location_room = Room::find($location->Room->id);

            if (count($location_factures) != 0) {
                ###__dernière facture de la location à l'arrêt de cet état
                $last_facture = $location_factures[0];

                ###___l'index de fin de la chambre revient désormais à
                ###___ celui de la dernière facture à l'arrêt de cet état
                $location_room->electricity_counter_start_index = $last_facture->end_index;
                $location_room->save();
            }

            // ACTUALISONS LES STATES DES FACTURES
            foreach ($location_factures as $facture) {
                $electricty_facture = LocationElectrictyFacture::find($facture->id);
                if (!$electricty_facture->state) {
                    $electricty_facture->state = $state->id;
                    $electricty_facture->save();
                }
            }

            ###___Génerons une dernière facture pour cette maison pour actualiser les infos de la dernière facture à l'arrêt de cet etat

            $stateFactureData = [
                "owner" => $user->id,
                "location" => $location->id,
                "end_index" => $location_room->electricity_counter_start_index,
                "amount" => 0,
                "state_facture" => 1,
                "state" => $state->id,
            ];
            LocationElectrictyFacture::create($stateFactureData);
        }

        ####___
        alert()->success("Succès", "L'état en électricité de cette maison a été arrêté avec succès!");
        return back()->withInput();
    }

    ####___PAIEMENTS LIES A L'ARRET DES ETATS
    function FiltreAfterStateDateStoped(Request $request, $houseId)
    {
        $house = House::where(["visible" => 1])->find(deCrypId($houseId));
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        }

        ###___DERNIERE DATE D'ARRET DES ETATS DE CETTE MAISON

        $last_state = $house->States->last();
        if (!$last_state) {
            alert()->error("Echec", "Aucun état n'a été arrêté dans cette maison!");
            return back()->withInput();
        }

        ###__DATE DU DERNIER ARRET DE CETTE MAISON
        $state_stop_date_of_this_house = date("Y/m/d", strtotime($last_state->stats_stoped_day));

        ###___LES FACTURES DU DERNIER ETAT
        $last_state_factures = $last_state->Factures;

        ###__LES PAIEMENTS LIES A LA LOCATION DE CETTE MAISON
        $locators_that_paid_before_state_stoped_day = [];
        $locators_that_paid_after_state_stoped_day = [];

        $amount_total_to_paid_before_array = [];
        $amount_total_to_paid_after_array = [];

        foreach ($last_state_factures as $facture) {
            $location_payement_date = date("Y/m/d", strtotime($facture->created_at));

            if ($location_payement_date < $state_stop_date_of_this_house) {
                $data["name"] = $facture->Location->Locataire->name;
                $data["prenom"] = $facture->Location->Locataire->prenom;
                $data["email"] = $facture->Location->Locataire->email;
                $data["phone"] = $facture->Location->Locataire->phone;
                $data["adresse"] = $facture->Location->Locataire->adresse;
                $data["comments"] = $facture->Location->Locataire->comments;
                $data["payement_date"] = $location_payement_date;
                $data["month"] = $facture->Location->next_loyer_date;
                $data["amount_paid"] = $facture->amount;

                ##___
                array_push($amount_total_to_paid_before_array, $data["amount_paid"]);
                array_push($locators_that_paid_before_state_stoped_day, $data);
            } else {
                $data["name"] = $facture->Location->Locataire->name;
                $data["prenom"] = $facture->Location->Locataire->prenom;
                $data["email"] = $facture->Location->Locataire->email;
                $data["phone"] = $facture->Location->Locataire->phone;
                $data["adresse"] = $facture->Location->Locataire->adresse;
                $data["comments"] = $facture->Location->Locataire->comments;
                $data["payement_date"] = $location_payement_date;
                $data["month"] = $facture->Location->next_loyer_date;
                $data["amount_paid"] = $facture->amount;

                ####_______
                array_push($amount_total_to_paid_after_array, $data["amount_paid"]);
                array_push($locators_that_paid_after_state_stoped_day, $data);
            }
        }

        ###____
        $locationsFiltered["beforeStopDate"] = $locators_that_paid_before_state_stoped_day;
        $locationsFiltered["afterStopDate"] = $locators_that_paid_after_state_stoped_day;

        $locationsFiltered["afterStopDateTotal_to_paid"] =  array_sum($amount_total_to_paid_after_array);
        $locationsFiltered["beforeStopDateTotal_to_paid"] =  array_sum($amount_total_to_paid_before_array);

        $locationsFiltered["total_locators"] = count($locationsFiltered["beforeStopDate"]) + count($locationsFiltered["afterStopDate"]);

        // dd($locationsFiltered);
        ####____
        return view("locators.locator-after-stop-date", compact("locationsFiltered", "house"));
    }

    ####___PAIEMENTS LIES A L'ARRET DES ETATS
    function FiltreBeforeStateDateStoped(Request $request, $houseId)
    {
        $house = House::where(["visible" => 1])->find(deCrypId($houseId));
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        }

        ###___DERNIERE DATE D'ARRET DES ETATS DE CETTE MAISON

        $last_state = $house->States->last();
        if (!$last_state) {
            alert()->error("Echec", "Aucun état n'a été arrêté dans cette maison!");
            return back()->withInput();
        }

        ###__DATE DU DERNIER ARRET DE CETTE MAISON
        $state_stop_date_of_this_house = date("Y/m/d", strtotime($last_state->stats_stoped_day));

        ###___LES FACTURES DU DERNIER ETAT
        $last_state_factures = $last_state->Factures;

        ###__LES PAIEMENTS LIES A LA LOCATION DE CETTE MAISON
        $locators_that_paid_before_state_stoped_day = [];
        $locators_that_paid_after_state_stoped_day = [];

        $amount_total_to_paid_before_array = [];
        $amount_total_to_paid_after_array = [];

        foreach ($last_state_factures as $facture) {
            $location_payement_date = date("Y/m/d", strtotime($facture->created_at));

            if ($location_payement_date < $state_stop_date_of_this_house) {
                $data["name"] = $facture->Location->Locataire->name;
                $data["prenom"] = $facture->Location->Locataire->prenom;
                $data["email"] = $facture->Location->Locataire->email;
                $data["phone"] = $facture->Location->Locataire->phone;
                $data["adresse"] = $facture->Location->Locataire->adresse;
                $data["comments"] = $facture->Location->Locataire->comments;
                $data["payement_date"] = $location_payement_date;
                $data["month"] = $facture->Location->next_loyer_date;
                $data["amount_paid"] = $facture->amount;

                ##___
                array_push($amount_total_to_paid_before_array, $data["amount_paid"]);
                array_push($locators_that_paid_before_state_stoped_day, $data);
            } else {
                $data["name"] = $facture->Location->Locataire->name;
                $data["prenom"] = $facture->Location->Locataire->prenom;
                $data["email"] = $facture->Location->Locataire->email;
                $data["phone"] = $facture->Location->Locataire->phone;
                $data["adresse"] = $facture->Location->Locataire->adresse;
                $data["comments"] = $facture->Location->Locataire->comments;
                $data["payement_date"] = $location_payement_date;
                $data["month"] = $facture->Location->next_loyer_date;
                $data["amount_paid"] = $facture->amount;

                ####_______
                array_push($amount_total_to_paid_after_array, $data["amount_paid"]);
                array_push($locators_that_paid_after_state_stoped_day, $data);
            }
        }

        ###____
        $locationsFiltered["beforeStopDate"] = $locators_that_paid_before_state_stoped_day;
        $locationsFiltered["afterStopDate"] = $locators_that_paid_after_state_stoped_day;

        $locationsFiltered["afterStopDateTotal_to_paid"] =  array_sum($amount_total_to_paid_after_array);
        $locationsFiltered["beforeStopDateTotal_to_paid"] =  array_sum($amount_total_to_paid_before_array);

        $locationsFiltered["total_locators"] = count($locationsFiltered["beforeStopDate"]) + count($locationsFiltered["afterStopDate"]);

        // dd($locationsFiltered);
        ####____
        return view("locators.locator-before-stop-date", compact("locationsFiltered", "house"));
    }












    #GET ALL LocationS
    function Locations(Request $request)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE 
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        #RECUPERATION DE TOUT LES LocationS
        return $this->getLocations();
    }

    #TOUTES LES LOCATIONS AYANT D'ELECTRICITE
    function ElectricityLocations(Request $request, $agencyId)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE 
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->getAllElectricityLocations($request, $agencyId);
    }

    #TOUTES LES LOCATIONS AYANT D'EAU
    function WaterLocations(Request $request, $agencyId)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE 
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->getAllWaterLocations($request, $agencyId);
    }

    #GET AN Location
    function RetrieveLocation(Request $request, $id)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE 
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        #RECUPERATION DE LA Location
        return $this->_retrieveLocation($id);
    }

    function DeleteLocation(Request $request, $id)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "DELETE") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };
        return $this->locationDelete($id);
    }

    function _ImprimeStates(Request $request, $agencyId, $houseId, $action)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->imprimeStates($request, $agencyId, $houseId, $action);
    }

    function _ImprimeStatesForAllSystem(Request $request, $houseId, $action)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->imprimeStatesForAllSystem($request, $houseId, $action);
    }

    function _ShowLocatorStateStoped(Request $request, $agencyId, $houseId, $action)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->locatorsStateStoped($request,  $agencyId, $houseId, $action);
    }

    function _ManagePrestationStatistique(Request $request, $agencyId)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->managePrestationStatistique($request, $agencyId);
    }



    function _ShowPrestationStatistique(Request $request, $agencyId)
    {
        $prestations = [];

        ####____
        $agency = Agency::find($agencyId);
        if (!$agency) {
            return redirect()->back()->with('error', "Cette agence n'existe pas!");
        }

        ####____
        $locations = $agency->_Locations; # Location::where(["agency" => $agencyId])->with(["Owner", "House", "Locataire", "Type", "Status", "Room"])->get();
        foreach ($locations as $location) {
            array_push($prestations, $location->prestation);
        }

        return view("prestation-statistique", compact(["locations", "prestations", "agency"]));
    }

    function _ShowPrestationStatistiqueForAgencyByPeriod(Request $request, $agencyId, $first_date, $last_date)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        ###___
        return $this->showPrestationStatistiqueForAgencyByPeriod($request, $agencyId, $first_date, $last_date);
    }


    function _ShowCautionsByAgency(Request $request, $agencyId)
    {
        if ($agencyId == "admin") {
            $locations = Location::with(["Owner", "House", "Locataire", "Type", "Status", "Room"])->get();
        } else {
            $locations = Location::where(["agency" => $agencyId])->with(["Owner", "House", "Locataire", "Type", "Status", "Room"])->get();
        }

        $cautions_eau = [];
        $cautions_electricity = [];
        $cautions_loyer = [];

        foreach ($locations as $location) {
            array_push($cautions_electricity, $location->caution_electric);
            array_push($cautions_eau, $location->caution_water);
            array_push($cautions_loyer, ($location->caution_number * $location->loyer));
        }
        return view("cautions", compact(["locations", "cautions_eau", "cautions_electricity", "cautions_loyer"]));
    }

    // PERIOD CAUTIONS MANAGEMENT
    function _ManageCautionsByPeriod(Request $request)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "POST") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->manageCautionsByPeriode($request);
    }

    function _ShowCautionsByPeriod(Request $request, $first_date, $last_date)
    {
        ###___
        $locations = Location::with(["Owner", "House", "Locataire", "Type", "Status", "Room"])->whereBetween('created_at', [$first_date, $last_date])->get();

        ###_____
        $cautions_eau = [];
        $cautions_electricity = [];
        $cautions_loyer = [];

        foreach ($locations as $location) {
            array_push($cautions_electricity, $location->caution_electric);
            array_push($cautions_eau, $location->caution_water);
            array_push($cautions_loyer, ($location->caution_number * $location->loyer));
        }
        ###_______

        return view("cautions", compact(["locations", "cautions_eau", "cautions_electricity", "cautions_loyer"]));
    }

    // HOUSE CAUTIONS MANAGEMENT
    function _ManageCautionsByHouse(Request $request, $houseId)
    {
        return $this->manageCautionsByHouse($request, $houseId);
    }

    function _ManageCautionsForHouseByPeriod(Request $request, $houseId)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "POST") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->manageCautionsForHouseByPeriod($request, $houseId);
    }


    function _ShowCautionsByHouse(Request $request, $houseId)
    {
        ###___
        $house = House::find($houseId);
        ###___

        $locations = $house->Locations;

        ###_____
        $cautions_eau = [];
        $cautions_electricity = [];
        $cautions_loyer = [];

        foreach ($locations as $location) {
            array_push($cautions_electricity, $location->caution_electric);
            array_push($cautions_eau, $location->caution_water);
            array_push($cautions_loyer, ($location->caution_number * $location->loyer));
        }
        ###_______

        return view("cautions", compact(["locations", "cautions_eau", "cautions_electricity", "cautions_loyer"]));
    }

    function _ShowCautionsForHouseByPeriod(Request $request, $houseId, $first_date, $last_date)
    {
        // dd($houseId);
        ###___
        $locations = Location::where(["house" => $houseId])->where('created_at', ">=", $first_date)->where('created_at', ">=", $last_date)->get();

        ###_____
        $cautions_eau = [];
        $cautions_electricity = [];
        $cautions_loyer = [];

        foreach ($locations as $location) {
            array_push($cautions_electricity, $location->caution_electric);
            array_push($cautions_eau, $location->caution_water);
            array_push($cautions_loyer, ($location->caution_number * $location->loyer));
        }
        ###_______

        return view("cautions", compact(["locations", "cautions_eau", "cautions_electricity", "cautions_loyer"]));
    }

    function SearchLocation(Request $request)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "POST") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        return $this->search($request);
    }
}
