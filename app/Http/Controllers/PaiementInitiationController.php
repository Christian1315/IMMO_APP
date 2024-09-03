<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyAccount;
use App\Models\AgencyAccountSold;
use App\Models\HomeStopState;
use App\Models\House;
use App\Models\PaiementInitiation;
use App\Models\Proprietor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaiementInitiationController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    ####################====== DATAS VALIDATIONS ===========#########
    ##======== PAIEMENT INITIATION VALIDATION =======##
    static function paiement_initiation_rules(): array
    {
        return [
            // 'agency' => ['required', "integer"],
            // 'house' => ['required', "integer"],
            // 'state' => ['required', "integer"],
            // 'proprietor' => ['required', "integer"],
            'amount' => ['required', "numeric"],
        ];
    }

    static function paiement_initiation_messages(): array
    {
        return [
            // 'agency.required' => "L'agence est réquise!",
            // 'agency.integer' => "Ce Champ doit être de type entier!",

            // 'house.required' => "Veuillez préciser la maison!",
            // 'house.integer' => "Ce Champ doit être de type entier!",

            // 'state.required' => "Veuillez préciser l'arrêt d'état pour lequel ce paiement doit être éffectué!",
            // 'state.integer' => "Ce Champ doit être de type entier!",

            // 'proprietor.required' => 'Le proprietaire est réquis!',
            // 'proprietor.integer' => "Ce Champ doit être de type entier!",

            'amount.required' => "Le montant à initier est réquis!",
            'amount.numeric' => "Le montant doit être de type numérique",
        ];
    }

    #INITIATE PAIEMENT TO A PROPRIETOR
    function InitiatePaiementToProprietor(Request $request)
    {
        $formData = $request->all();

        #VALIDATION DES DATAs DEPUIS LA CLASS BASE_HELPER HERITEE PAR Card_HELPER
        $rules = self::paiement_initiation_rules();
        $messages = self::paiement_initiation_messages();

        Validator::make($formData, $rules, $messages)->validate();

        $user = request()->user();

        $house = House::where("visible", 1)->find($formData["house"]);
        if (!$house) {
            alert()->error("Echèc", "Cette maison n'existe pas!");
            return back()->withInput();
        }
        ###___TRAITEMENT DES DATAS
        $proprietor = $house->Proprietor;

        $agency = $proprietor->Agency;
        if (!$agency) {
            alert()->error("Echèc", "Cette agence n'existe pas!");
            return back()->withInput();
        }

        $formData["agency"] = $agency->id;
        $formData["state"] = $house->States->last()->id;
        $formData["agency"] = $agency->id;

        ###__========== VERIFIONS D'ABORD LA SUFFISANCE DU SOLDE DU COIMPTE LOYER POUR EFFECTUER CETTE INITIATION ===========#

        ###__VOYONS D'ABORD S'IL S'AGIT D'UN CHET COMPTABLE

        ##~~on attaque le compte loyer
        $agencyAccount = AgencyAccount::where(["agency" => $formData["agency"], "account" => 4])->first();

        $accountSold = AgencyAccountSold::where(["agency_account" => $agencyAccount->id, "visible" => 1])->first();
        if (!$accountSold) {
            alert()->error("Echèc", "Le compte Loyer de cette agence ne dispose pas encore de sold! Veuillez à ce qu'il soit d'abord créditer!");
            return back()->withInput();
        }

        if ($accountSold->sold < $formData["amount"]) {
            alert()->error("Echèc", "Le sold du compte Loyer de cette agence est insuffisant pour faire cette initiation!");
            return back()->withInput();
        }

        ###__ENREGISTREMENT DU PAIEMENT DANS LA DB
        $formData["manager"] = $user->id;
        $formData["status"] = 1;
        $formData["comments"] = "Initiation de paiement d'une somme de (" . $formData["amount"] . " ) au proprietaire (" . $proprietor->firstname . " " . $proprietor->lastname . " )";
        $PaiementInitiation = PaiementInitiation::create($formData);

        ###___ACTUALISATION DU STATE DANS L'ARRET DES ETATS
        $state = HomeStopState::where(["house" => $formData["house"]])->find($formData["state"]);
        if ($state) {
            $state->proprietor_paid = 1;
            $state->save();
        }

        ##__
        alert()->success("Succès", "Paiement initié au proprietaire (" . $proprietor->firstname . " " . $proprietor->lastname . " ) avec succès!");
        return back()->withInput();
    }

    #VALIDE A PAIEMENT INITIATION 
    function ValidePaiementInitiation(Request $request, $id)
    {
        $user = request()->user();
        $formData = $request->all();

        $PaiementInitiation = PaiementInitiation::find(deCrypId($id));

        ##__Le proprio attaché à cette initiation
        $proprietor = $PaiementInitiation->House->Proprietor;

        if (!$PaiementInitiation) {
            alert()->error("Echèc", "Cette initiation de paiement n'existe pas!");
            return back()->withInput();
        };

        if ($PaiementInitiation->status == 2) {
            alert()->error("Echèc", "Cette initiation de paiement a été déjà validée");
            return back()->withInput();
        };

        $agencyAccount = AgencyAccount::where(["agency" => $PaiementInitiation->agency, "account" => 4])->first();

        $accountSold = AgencyAccountSold::where(["agency_account" => $agencyAccount->id, "visible" => 1])->first();

        if ($accountSold) {
            if ($accountSold->sold < $PaiementInitiation->amount) {
                alert()->error("Echèc", "Le sold du compte Loyer de cette agence est insuffisant pour faire cette initiation!");
                return back()->withInput();
            }
        } else {
            alert()->error("Echèc", "Le Compte Loyer n'est pas actualisé");
            return back()->withInput();
        }

        ####____CHANGEMENT DE STATUS SUR 2(validée)
        $PaiementInitiation->status = 2;

        ###__DECREDITATION DU SOLD DU COMPTE LOYER
        #~~deconsiderons l'ancien sold
        $accountSold->visible = 0;

        // #~~Considerons un nouveau sold
        // $data["owner"] = $user->id;
        // $data["account"] = $agencyAccount->account;

        $data["old_sold"] = $accountSold->sold;
        $data["sold_retrieved"] = $PaiementInitiation->amount;

        $data["sold"] = $data["old_sold"] - $data["sold_retrieved"];
        $data["description"] = "Décaissement du sold du compte Loyer pour initiation de paiement au proprietaire (" . $proprietor->firstname . " " . $proprietor->lastname . " )";
        ###___

        AgencyAccountSold::create([
            "agency_account" => $agencyAccount->id,
            "old_sold" => $data["old_sold"],
            "sold_retrieved" => $data["sold_retrieved"],
            "sold" => $data["sold"],
            "description" => $data["description"],
        ]);

        #####____
        $PaiementInitiation->save();
        $accountSold->save();

        ##___
        alert()->success("Succès", "Initiation de paiement validée avec succès!");
        return back()->withInput();
    }

    #REJETER UN PAIEMENT INITIATION 
    function RejetPayementInitiation(Request $request, $id)
    {
        $user = request()->user();
        $formData = $request->all();

        ####_____
        $PaiementInitiation = PaiementInitiation::find(deCrypId($id));
        if (!$PaiementInitiation) {
            alert()->error("Echec", "Cette initiation de paiement n'existe pas!");
            return back()->withInput();
        };

        if ($PaiementInitiation->status == 2) {
            alert()->error("Echèc", "Cette initiation de paiement a été déjà validée, vous ne pouvez la rejeté");
            return back()->withInput();
        };

        if ($PaiementInitiation->status == 3) {
            alert()->error("Echèc", "Cette initiation de paiement a été déjà rejétée, vous ne pouvez la rejeté à nouveau");
            return back()->withInput();
        };

        ####____CHANGEMENT DE STATUS SUR 3(rejetée)
        $PaiementInitiation->status = 3;
        $PaiementInitiation->rejet_comments = $formData["rejet_comments"];

        ###___ACTUALISATION DU STATE DANS L'ARRET DES ETATS
        $state = HomeStopState::where(["house" => $PaiementInitiation->House->id])->find($PaiementInitiation->House->States->last()->id);
        if ($state) {
            $state->proprietor_paid = 0;
            $state->save();
        }

        ####___
        $PaiementInitiation->save();
        ####___
        alert()->success("Succès", "Initiation de paiement réjetée avec succès!");
        return back()->withInput();
    }
}
