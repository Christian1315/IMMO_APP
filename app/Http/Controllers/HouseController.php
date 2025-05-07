<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyAccountSold;
use App\Models\City;
use App\Models\Country;
use App\Models\Departement;
use App\Models\Facture;
use App\Models\HomeStopState;
use App\Models\House;
use App\Models\HouseType;
use App\Models\Proprietor;
use App\Models\Quarter;
use App\Models\User;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use function Spatie\LaravelPdf\Support\pdf;

// use Spatie\LaravelPdf\Facades\Pdf;

// use function Spatie\LaravelPdf\Support\pdf;

class HouseController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth']);

        set_time_limit(0);
    }

    ######============== VALIDATION DES DATAS ===============######
    ##======== HOUSE VALIDATION =======##

    static function house_rules(): array
    {
        return [
            'agency' => ['required'],
            'name' => ['required'],
            'proprio_payement_echeance_date' => ['required', "date"],

            'proprietor' => ['required', "integer"],
            'type' => ['required', "integer"],
            'city' => ['required', "integer"],
            'country' => ['required', "integer"],
            'departement' => ['required', "integer"],
            'quartier' => ['required', "integer"],
            'zone' => ['required', "integer"],
            'supervisor' => ['required', "integer"],
        ];
    }

    static function house_messages(): array
    {
        return [
            'agency.required' => "L'agence est réquise",
            'proprio_payement_echeance_date.required' => "La date d'écheance du payement du propriétaire est réquise!",
            'proprio_payement_echeance_date.date' => "Ce champ doit être de type date",


            'proprietor.required' => "Le propriétaire est réquis",
            'type.required' => "Le type de la chambre est réquis",
            'city.required' => "La ville est réquise",
            'country.required' => "Le Pays est réquis",
            'departement.required' => "Le departement est réquis",
            'quartier.required' => "Le quartier est réquis",
            'zone.required' => "La zone est réquise",
            'supervisor.required' => "Le superviseur est réquis",

            'proprietor.integer' => 'Ce champ doit être de type entier!',
            'type.integer' => 'Ce champ doit être de type entier!',
            'city.integer' => 'Ce champ doit être de type entier!',
            'country.integer' => 'Ce champ doit être de type entier!',
            'departement.integer' => 'Ce champ doit être de type entier!',
            'quartier.integer' => 'Ce champ doit être de type entier!',
            'zone.integer' => 'Ce champ doit être de type entier!',
            'supervisor.integer' => 'Ce champ doit être de type entier!',
        ];
    }

    ###__ADD HOUSE
    function _AddHouse(Request $request)
    {
        #VALIDATION DES DATAs DEPUIS LA CLASS BASE_HELPER HERITEE PAR Card_HELPER
        $formData = $request->all();
        Validator::make(
            $formData,
            self::house_rules(),
            self::house_messages()
        )->validate();

        $user = request()->user();

        ###___TRAITEMENT DES DATAS
        $proprietor = Proprietor::where(["visible" => 1])->find($formData["proprietor"]);
        $type = HouseType::find($formData["type"]);
        $city = City::find($formData["city"]);
        $country = Country::find($formData["country"]);
        $departement = Departement::find($formData["departement"]);
        $quartier = Quarter::find($formData["quartier"]);
        $zone = Zone::find($formData["zone"]);
        $user_supervisor = User::find($formData["supervisor"]);

        if (!$proprietor) {
            alert()->error("Echec", "Ce Propriétaire n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$type) {
            alert()->error("Echec", "Ce Type de chambre n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$city) {
            alert()->error("Echec", "Cette ville n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$country) {
            alert()->error("Echec", "Ce pays n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$departement) {
            alert()->error("Echec", "Ce departement n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$quartier) {
            alert()->error("Echec", "Ce quartier n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$zone) {
            alert()->error("Echec", "Cette zone n'existe pas!");
            return redirect()->back()->withInput();
        }

        if (!$user_supervisor) {
            alert()->error("Echec", "Ce superviseur n'existe pas!");
            return redirect()->back()->withInput();
        }

        ##__VERIFIONS SI LE UER_SUPERVISOR DISPOSE VRAIMENT DU ROLE D'UN SUPERVISEUR
        if (!$user_supervisor->hasRole("Superviseur")) {
            alert()->error("Echec", "Ce utilisateur choisi comme superviseur ne dispose vraiment pas le rôle d'un superviseur!");
            return redirect()->back()->withInput();
        }

        ###__TRAITEMENT DE L'IMAGE
        if ($request->hasFile('image')) {
            $image = $request->file("image");
            $image_name = $image->getClientOriginalName();
            $image->move("houses_images", $image_name);

            #ENREGISTREMENT DE LA CARTE DANS LA DB
            $formData["owner"] = $user->id;
            $formData["image"] = asset("houses_images/" . $image_name);
        }

        if ($request->pre_paid == $request->post_paid) {
            alert()->error("Echec", "Veuillez choisir soit l'option *prepayée* ou *postpayée*");
            return back()->withInput();
        }

        $formData["pre_paid"] = $request->pre_paid ? true : false;
        $formData["post_paid"] = $request->post_paid ? true : false;

        $formData["locative_commission"] = $request->locative_commission ? $request->locative_commission : 10;

        House::create($formData);

        alert()->success("Succès", "Maison ajoutée avec succès");
        return redirect()->back()->withInput();
    }

    ###___FILTRE BY SUPERVISOR
    function FiltreHouseBySupervisor(Request $request, $agency)
    {
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

        $houses = [];
        ###____

        $houses = $agency->_Houses->where("supervisor", $request->supervisor);

        if (count($houses) == 0) {
            alert()->error("Echèc", "Aucun résultat trouvé");
            // Session::forget("filteredHouses");
            return back()->withInput();
        }

        // Session::forget("filteredHouses");
        session()->flash("filteredHouses", $houses);

        alert()->success("Succès", "Maisons filtrées par superviseur avec succès!");
        return back()->withInput();
    }

    ###___FILTRE BY PERIOD
    function FiltreHouseByPeriod(Request $request, $agency)
    {
        $user = request()->user();
        $agency = Agency::find($agency);

        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        $houses = $agency->_Houses->whereBetween("created_at", [$request->debut, $request->fin]);

        if (count($houses) == 0) {
            alert()->error("Echèc", "Aucun résultat trouvé");
            // Session::forget("filteredHouses");
            return back()->withInput();
        }

        // Session::forget("filteredHouses");
        session()->flash("filteredHouses", $houses);

        $debut = \Carbon\Carbon::parse($request->debut)->locale('fr')->isoFormat('MMMM YYYY');
        $fin = \Carbon\Carbon::parse($request->fin)->locale('fr')->isoFormat('MMMM YYYY');
        $msg = "Maisons filtrées par période du $debut au $fin avec succès!";
        alert()->success("Succès", $msg);
        return back()->withInput();
    }

    ###___ADD HOUSE TYPE
    function AddHouseType(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                "name" => 'required',
                "description" => 'required',
            ],
            [
                "name.required" => "Le nom est requis",
                "description.required" => "La description est requise"
            ]
        )->validate();

        HouseType::create($request->all());

        alert()->success("Succès", "Type de maison ajouté avec succès");
        return redirect()->back()->withInput();
    }

    #GENERER CAUTIONS PAR PERIODE
    function GenerateCautionByPeriod(Request $request, $houseId)
    {
        $house = House::find($houseId);
        if (!$house) {
            alert()->error("Echec", "Désolé! Cette maison n'existe pas!");
            return back()->withInput();
        }

        ##__
        $formData = $request->all();

        ###__
        Validator::make(
            $formData,
            [
                "first_date" => ["required", "date"],
                "last_date" => ["required", "date"],
            ],
            [
                "first_date.required" => "Ce Champ est réquis!",
                "last_date.required" => "Ce Champ est réquis!",

                "first_date.date" => "Ce Champ est une date!",
                "last_date.date" => "Ce Champ est une date!",
            ]
        )->validate();

        ##__
        $data["caution_html_url"] = env("APP_URL") . "/$house->id/" . $formData['first_date'] . "/" . $formData['last_date'] . "/caution_html_for_house_by_period";

        alert()->success("Succès", "Cautions generées en pdf avec succès!");
        alert()->html('<b>Succès</b>', "Cautions generées en pdf avec succès, <a target='__blank' href=" . $data['caution_html_url'] . ">Ouvrez le lien ici</a>", 'success');

        return back()->withInput();
    }

    #ARRETER LES ETATS DES MAISON
    function PostStopHouseState(Request $request, $houseId)
    {
        $user = request()->user();
        $formData = $request->all();

        $formData["owner"] = $user->id;

        $house = House::with(["Locations", "Proprietor"])->where(["visible" => 1])->find(deCrypId($houseId));
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back()->withInput();
        };

        $formData["house"] = $house->id;

        if (count($house->Locations) == 0) {
            alert()->error("Echec", "Cette maison n'appartient à aucune location! Son arrêt d'état ne peut donc être éffectué");
            return back()->withInput();
        }

        ###_____VERIFIONS D'ABORD SI CETTE HOUSE DISPOSAIT DEJA D'UN ETAT
        $this_house_state = HomeStopState::orderBy("id", "desc")->where(["house" => $formData["house"]])->get();

        if (count($this_house_state) == 0) { ##Si cette maison ne dispose pas d'arrêt d'etat
            ##__ON CREE SON PREMIER ARRET D'ETAT
            $data["owner"] = $formData["owner"];
            $data["house"] = $formData["house"];
            $data["recovery_rapport"] = $formData["recovery_rapport"];

            $data["stats_stoped_day"] = now();
            $state = HomeStopState::create($data);
        } else { ##S'il dispose d'un arret d'etat déjà
            $this_house_state = $this_house_state[0];
            ##__On verifie si la date d'aujourd'hui atteint ou depasse
            ##__la date de l'arret precedent + 20 jours
            $precedent_arret_date = strtotime($this_house_state->stats_stoped_day);
            $now = strtotime(now());
            $twenty_days = 5 * 24 * 3600;

            // if ($now < ($precedent_arret_date + $twenty_days)) {
            //     return self::sendError("La précedente date d'arrêt des états de cette maison ne depasse pas encore 5 jours! Vous ne pouvez donc pas éffectuer un nouveau arrêt d'etats pour cette maison pour le moment", 505);
            // }

            ###__ON ARRËTE LES ETATS
            $data["owner"] = $formData["owner"];
            $data["house"] = $formData["house"];
            $data["recovery_rapport"] = $formData["recovery_rapport"];
            $data["stats_stoped_day"] = now();
            $state = HomeStopState::create($data);
        }

        ###____ ACTUALISONS LES STATES DES FACTURES
        foreach ($house->Locations as $location) {
            // ACTUALISONS LES STATES DES FACTURES

            foreach ($location->Factures as $facture) {
                $facture = Facture::find($facture->id);
                if (!$facture->state) {
                    $facture->state = $state->id;
                    $facture->save();
                }
            }

            ###___Génerons une dernière facture pour cette maison pour actualiser les infos de la dernière facture à l'arrêt de cet etat
            $stateFactureData = [
                "owner" => $formData["owner"],
                "location" => $location->id,
                "amount" => 0,
                "state_facture" => 1,
                "state" => $state->id,
            ];

            Facture::create($stateFactureData);

            ####________ACTUALISONS LES MOUVEMENTS DES COMPTES DANS CET ETAT
            $house_account_depenses = $house->AllStatesDepenses;
            foreach ($house_account_depenses as $account) {
                if (!$account->state) {
                    $_res = AgencyAccountSold::find($account->id);
                    $_res->state = $state->id;
                    $_res->save();
                }
            }
        }

        ###____envoie de mail au proprietaire

        try {
            Send_Notification_Via_Mail(
                $house->Proprietor->email,
                "Etat de récouvrement",
                "L'état de récouvrement de la maison " . $house->name . " vient d'être arrêté! Voici un rapport de recouvrement qui l'accompagne :" . $formData["recovery_rapport"]
            );
        } catch (\Throwable $th) {
            //throw $th;
        }

        #####_____
        alert()->success("Succès", "Arrêt d'état effectué avec succès!");
        return back()->withInput();
    }

    function UpdateHouse(Request $request, $id)
    {
        $user = request()->user();
        $formData = $request->all();
        $house = House::where(["visible" => 1])->find($id);
        if (!$house) {
            alert()->error("Echec", "Cette Maison n'existe pas!");
            return back()->withInput();
        };

        ####____TRAITEMENT DU PROPRIETAIRE
        if ($request->get("proprietor")) {
            $proprietor = Proprietor::where(["visible" => 1])->find($request->get("proprietor"));

            if (!$proprietor) {
                alert()->error("Echec", "Ce Proprietaire n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU TYPE DE PROPRIETAIRE
        if ($request->get("type")) {
            $type = HouseType::find($request->get("type"));

            if (!$type) {
                alert()->error("Echec", "Ce type de proprietaire n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU SUPERVISEUR
        if ($request->get("supervisor")) {
            $user_supervisor = User::find($request->get("supervisor"));

            ##__VERIFIONS SI LE UER_SUPERVISOR DISPOSE VRAIMENT DU ROLE D'UN SUPERVISEUR
            $user_roles = $user_supervisor->roles; ##recuperation des roles de ce user_supervisor
            $is_this_user_supervisor_has_supervisor_role = false; ##cette variable permet de verifier si user_supervisor dispose vraiment du rôle d'un superviseur

            foreach ($user_roles as $user_role) {
                if ($user_role->id == 3) {
                    $is_this_user_supervisor_has_supervisor_role = true;
                }
            }

            if (!$is_this_user_supervisor_has_supervisor_role) {
                alert()->error("Echec", "Ce utilisateur choisi comme superviseur ne dispose vraiment pas le rôle d'un superviseur!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU CITY
        if ($request->get("city")) {
            $city = City::find($request->get("city"));
            if (!$city) {
                alert()->error("Echec", "Cette ville n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU COUNTRY
        if ($request->get("country")) {
            $country = Country::find($request->get("country"));
            if (!$country) {
                alert()->error("Echec", "Ce pays n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU DEPARTEMENT
        if ($request->get("departement")) {
            $departement = Departement::find($request->get("departement"));

            if (!$departement) {
                alert()->error("Echec", "Ce département n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DU QUARTIER
        if ($request->get("quartier")) {
            $quartier = Quarter::find($request->get("quartier"));
            if (!$quartier) {
                alert()->error("Echec", "Ce quartier n'existe pas!");
                return back()->withInput();
            }
        }

        ####____TRAITEMENT DE LA ZONE
        if ($request->get("zone")) {
            $zone = Zone::find($request->get("zone"));
            if (!$zone) {
                alert()->error("Echec", "Cette zone n'existe pas!");
                return back()->withInput();
            }
        }

        if ($request->pre_paid == $request->post_paid) {
            alert()->error("Echec", "Veuillez choisir soit l'option *prepayée* ou *postpayée*");
            return back()->withInput();
        }

        $formData["pre_paid"] = $request->pre_paid ? true : false;
        $formData["post_paid"] = $request->post_paid ? true : false;

        $house->update($formData);

        alert()->success("Succès", "Maison modifiée avec succès!");
        return back()->withInput();
    }

    function DeleteHouse(Request $request, $id)
    {
        // $user = request()->user();
        $house = House::where(["visible" => 1])->find(deCrypId($id));
        if (!$house) {
            alert()->error("error", "Cette maison n'existe pas!");
            return back();
        };

        if (count($house->Rooms) > 0) {
            alert()->error("error", "Cette maison dispose de chambre(s)! Veuillez bien les supprimer d'abord");
            return back();
        }

        if (count($house->Locations) > 0) {
            alert()->error("error", "Cette maison dispose de location(s)! Veuillez bien les supprimer d'abord");
            return back();
        }

        $house->visible = 0;
        $house->delete_at = now();
        $house->save();

        alert()->success("Succès", "Maison supprimée avec succès!");
        return back();
    }

    ####____SHOW HOUSE STOP PAGE
    function StopHouseState(Request $request, $houseId, $agencyId)
    {
        $agency = Agency::where("visible", true)->find(deCrypId($agencyId));
        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas!");
            return back();
        }

        $house = House::where("visible", true)->find(deCrypId($houseId));
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back();
        }

        ####_____
        return view("admin.stop-house-state", compact(["house", "agency"]));
    }

    ####_____IMPRIME HOUSE STATE
    function ShowHouseStateImprimeHtml(Request $request, $houseId)
    {
        set_time_limit(3600);
        $house = House::with(["Locations", "Rooms"])->where("visible", 1)->find(deCrypId($houseId));
        if (!$house) {
            alert()->error("Echec", "Cette maison n'existe pas!");
            return back();
        }

        // Chambres non occupées
        $buszy_rooms = $house->Rooms->filter(fn($room) => count($room->Locations) == 0);
        // dd($buszy_rooms);

        $nbr_month_paid = 0;
        $total_amount_paid = 0;

        $house_factures_nbr_array = [];
        $house_amount_nbr_array = [];

        ####_____DERNIER ETAT DE CETTE MAISON
        $house_last_state = $house->States->last();
        if (!$house_last_state) {
            if ($house->PayementInitiations->last()) {
                if ($house->PayementInitiations->last()->state) { ####__quand l'initiation de payement n'est pas rejetée
                    ####____
                    alert()->error("Echec", "Cette maison ne dispose d'aucun arrêt d'état");
                    return back();
                }
            }

            ####____
            alert()->error("Echec", "Cette maison ne dispose d'aucun arrêt d'état");
            return back();
        }

        $locations = $house->Locations->where("status", "!=", 3);

        ###___DERTERMINONS LE NOMBRE DE FACTURE ASSOCIEE A CETTE MAISON
        foreach ($locations as $key =>  $location) {
            ###___quand il y a arrêt d'etat
            ###__on recupere les factures du dernier arrêt des etats de la maison

            if ($house_last_state) {
                $location_factures = Facture::where(["location" => $location->id, "state" => $house_last_state->id, "state_facture" => 0])->get();
            } else {
                $location_factures = Facture::where(["location" => $location->id, "old_state" => $house->PayementInitiations->last()?->old_state, "state_facture" => 0])->get();
            }

            foreach ($location_factures as $facture) {
                array_push($house_factures_nbr_array, $facture);
                if ($location->prorata_amount) {
                    array_push($house_amount_nbr_array, $location->prorata_amount);
                } else {
                    array_push($house_amount_nbr_array, $facture->amount);
                };
                // array_push($house_amount_nbr_array, $facture->amount);
            }

            ####_____REFORMATION DU LOCATAIRE DE CETTE LOCATION
            ###____
            $houses = $location->House;
            $rooms = $location->Room;

            $nbr_month_paid_array = [];
            $nbr_facture_amount_paid_array = [];
            ####___________

            foreach ($location_factures as $facture) {
                array_push($nbr_month_paid_array, $facture);
                array_push($nbr_facture_amount_paid_array, $facture->amount);
            }

            ####_____
            $locataire["nbr_month_paid"] = count($nbr_month_paid_array);
            $locataire["nbr_facture_amount_paid"] = array_sum($nbr_facture_amount_paid_array);
            ####____

            $locataire["houses"] = $houses;
            $locataire["rooms"] = $rooms;
            ####___FIN FORMATION DU LOCATAIRE

            ###
            $location["_locataire"] = $locataire;
        }

        ###__ le nombre de mois payé revient au nombre de factures generées
        $nbr_month_paid = count($house_factures_nbr_array);

        ###__ le montant total payé revient à la somme totale des montants des factures generées

        $total_amount_paid = array_sum($house_amount_nbr_array);

        ####___last depenses
        $last_state_depenses_array = [];
        $last_state_depenses = [];
        if ($house_last_state) {
            $last_state_depenses = $house_last_state->CdrAccountSolds;
        }
        foreach ($last_state_depenses as $depense) {
            array_push($last_state_depenses_array, $depense->sold_retrieved);
        }

        ###___current depenses
        $current_state_depenses_array = [];
        $current_state_depenses = $house->CurrentDepenses;
        foreach ($current_state_depenses as $depense) {
            array_push($current_state_depenses_array, $depense->sold_retrieved);
        }

        ###__
        $house["house_depenses"] = collect($last_state_depenses);
        $house["last_depenses"] = array_sum($last_state_depenses_array);
        $house["actuel_depenses"] = array_sum($current_state_depenses_array);
        $house["total_amount_paid"] = $total_amount_paid;
        $house["house_last_state"] = $house_last_state;
        $house["nbr_month_paid"] = $nbr_month_paid;
        $house["commission"] = ($house["total_amount_paid"] * $house->commission_percent) / 100;
        $house["locative_commission"] = ($house->LocativeCharge() * $house->locative_commission) / 100;
        ####________

        $house["net_to_paid"] = $house["total_amount_paid"] - ($house["last_depenses"] + $house["commission"] + $house["locative_commission"]);

        // 
        $state = $house_last_state;

        // gestion des locataires
        ###___

        $paid_locataires = $locations->map(function ($location) use ($state) {
            $stop_date = date("Y/m/d", strtotime($state->state_stoped_day));
            $last_facture_date = date("Y/m/d", $location->StateFactures->last()?->created_at);

            // il a payé avant la date d'arrêt des états
            if ($last_facture_date < $stop_date) {
                return $location->Locataire;
            }
        });

        $un_paid_locataires = $locations->map(function ($location) use ($state) {
            $stop_date = date("Y/m/d", strtotime($state->state_stoped_day));
            $last_facture_date = date("Y/m/d", $location->StateFactures->last()?->created_at);

            // il n'a pas payé avant la date d'arrêt des états
            if ($stop_date < $last_facture_date) {
                return $location->Locataire;
            }
        });

        ###____
        $now = strtotime(date("Y/m/d", strtotime(now())));

        // return view("house-state", compact(["house","locations", "state","paid_locataires","un_paid_locataires"]));

        $pdf = Pdf::loadView('house-state', compact([
            "house",
            "locations",
            "state",
            "paid_locataires",
            "un_paid_locataires",
            "buszy_rooms"
        ]));

        return $pdf->stream();
        // return $pdf->download("etat-$house->name.pdf");
    }
}
