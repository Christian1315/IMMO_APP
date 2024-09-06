<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\CardType;
use App\Models\City;
use App\Models\Client;
use App\Models\Country;
use App\Models\Proprietor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProprietorController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth']);
    }


    // ################## LES VALIDATIONS #########################

    ##======== PROPRIETOR VALIDATION =======##
    static function proprietor_rules(): array
    {
        return [
            'firstname' => ['required'],
            'lastname' => ['required'],
            'phone' => ['required', "numeric"],
            'email' => ['required', "email"],
            'sexe' => ['required'],
            'piece_number' => ['required'],
            'mandate_contrat' => ['required', "file"],
            'comments' => ['required'],
            'adresse' => ['required'],

            'city' => ['required', 'integer'],
            'country' => ['required', 'integer'],
            'card_type' => ['required', 'integer'],
            'agency' => ['required', 'integer'],
        ];
    }

    static function proprietor_messages(): array
    {
        return [
            'firstname.required' => 'Veuillez précisez le prénom!',
            'lastname.required' => 'Veuillez précisez le nom!',
            'phone.required' => 'Veuillez précisez le phone!',
            'email.required' => 'Veuillez précisez le mail!',
            'sexe.required' => 'Veuillez précisez le sexe!',
            'piece_number.required' => 'Veuillez précisez le numéro de la pièce!',
            'mandate_contrat.required' => 'Veuillez précisez le contrat de location!',
            'mandate_contrat.file' => 'Ce champ doit doit être un fichier!',
            'comments.required' => 'Veuillez précisez un commantaire!',
            'adresse.required' => 'Veuillez précisez l\'adresse!',
            'city.required' => 'Veuillez précisez la ville!',
            'country.required' => 'Veuillez précisez le pays!',
            'card_type.required' => 'Veuillez précisez le type de carte!',

            'city.integer' => 'Ce champ doit être de type entier!',
            'country.integer' => 'Ce champ doit doit être de type entier!',
            'card_type.integer' => 'Ce champ doit doit être de type entier!',

            'agency.required' => "Veillez préciser l'agence",
            'agency.integer' => "L'agence doit être de type entier!",

            'phone.numeric' => 'Ce champ doit doit être de type numeric!',
            'email.email' => 'Ce champ doit doit être de type mail!',
        ];
    }



    ###############===================================###############
    function _AddProprietor(Request $request)
    {
        $formData = $request->all();

        #VALIDATION DES DATAs DEPUIS LA CLASS BASE_HELPER HERITEE PAR Card_HELPER
        $rules = self::proprietor_rules();
        $messages = self::proprietor_messages();
        Validator::make($formData, $rules, $messages)->validate();

        $user = request()->user();

        ###___
        $city = City::find($formData["city"]);
        $country = Country::find($formData["country"]);
        $card_type = CardType::find($formData["card_type"]);
        $agency = Agency::find($formData["agency"]);

        if (!$agency) {
            alert()->error("Echec", "Cette agence n'existe pas");
            return redirect()->back()->withInput();
        }

        if (!$city) {
            alert()->error("Echec", "Cette ville n'existe pas");
            return redirect()->back()->withInput();
        }

        if (!$country) {
            alert()->error("Echec", "Ce pays n'existe pas");
            return redirect()->back()->withInput();
        }

        if (!$card_type) {
            alert()->error("Echec", "Ce type de carte n'existe pas");
            return redirect()->back()->withInput();
        }

        ###____VOYONS SI CE PROPRIETAIRE EXISTE DEJA
        $is_this_proprio_existe = Proprietor::where(["firstname" => $formData["firstname"], "lastname" => $formData["lastname"], "phone" => $formData["phone"], "visible" => 1])->first();
        if ($is_this_proprio_existe) {
            alert()->error("Echec", "Ce proprietaire existe déjà");
            return redirect()->back()->withInput();
        }

        ###__TRAITEMENT DE L'IMAGE
        $mandate_contrat = $request->file("mandate_contrat");
        $file_name = $mandate_contrat->getClientOriginalName();
        $mandate_contrat->move("contrats", $file_name);

        #ENREGISTREMENT DE LA CARTE DANS LA DB
        $formData["mandate_contrat"] = asset("contrats/" . $file_name);
        $formData["owner"] = $user->id;

        ####____CREATION D'UN PROPRIETAIRE
        $proprietor = Proprietor::create($formData);

        if (!$proprietor) {
            alert()->error("Echec", "Une erreure est survenue, veuillez réessayer à nouveau!");
            return redirect()->back()->withInput();
        }

        ###___CREATION DU CLIENT___###
        $client = new Client();
        $client->type = 1;
        $client->city = $formData["city"];
        $client->phone = $formData["phone"];
        $client->email = $formData["email"];
        $client->name = $formData["firstname"] . " " . $formData["lastname"];
        $client->sexe = $formData["sexe"];
        $client->is_proprietor = true;
        $client->comments = $formData["comments"];
        $client->save();
        ###___FIN CREATION DU CLIENT___###

        alert()->success("Succès", "Propriétaire ajouter avec succès!");
        return redirect()->back();
    }

    function UpdateProprietor(Request $request, $id)
    {
        $user = request()->user();
        $formData = $request->all();
        $proprietor = Proprietor::where(["visible" => 1])->find($id);

        if (!$proprietor) {
            alert()->error("Echec", "Ce propriétaire n'existe pas");
            return redirect()->back()->withInput();
        };

        if (!auth()->user()->is_master && !auth()->user()->is_admin) {
            if ($proprietor->owner!=$user->id) {
                alert()->error("Echec", "Ce propriétaire ne vous appartient pas");
                return redirect()->back()->withInput();
            };
        }

        if ($request->get("city")) {
            $city = City::find($request->get("city"));
            if (!$city) {
                alert()->error("Echec", "Cette ville n'existe pas");
                return redirect()->back()->withInput();
            }
        }

        if ($request->get("country")) {
            $country = Country::find($request->get("country"));
            if (!$country) {
                alert()->error("Echec", "Ce pays n'existe pas");
                return redirect()->back()->withInput();
            }
        }

        if ($request->get("card_type")) {
            $card_type = CardType::find($request->get("card_type"));
            if (!$card_type) {
                alert()->error("Echec", "Ce type de carte n'existe pas");
                return redirect()->back()->withInput();
            }
        }

        if ($request->file("mandate_contrat")) {
            $mandate_contrat = $request->file("mandate_contrat");
            $file_name = $mandate_contrat->getClientOriginalName();
            $mandate_contrat->move("contrats", $file_name);

            $formData["mandate_contrat"] = asset("contrats/" . $file_name);
        }

        $proprietor->update($formData);

        alert()->success("Succès", "Propriétaire modifié avec succès");
        return redirect()->back()->withInput();
    }












    #GET ALL PROPRIETOR
    function Proprietors(Request $request)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE PAR Card_HELPER
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };

        #RECUPERATION DE TOUT LES PROPRIETAIRES
        return $this->getProprietor();
    }

    #GET A Proprietor
    function RetrieveProprietor(Request $request, $id)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "GET") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS BASE_HELPER HERITEE PAR Card_HELPER
            return $this->sendError("La methode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };
        #RECUPERATION D'UN PROPRIETAIRE
        return $this->_retrieveProprietor($id);
    }



    function DeleteProprietor(Request $request, $id)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "DELETE") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };
        return $this->proprietorDelete($id);
    }

    function SearchProprietor(Request $request)
    {
        #VERIFICATION DE LA METHOD
        if ($this->methodValidation($request->method(), "POST") == False) {
            #RENVOIE D'ERREURE VIA **sendError** DE LA CLASS Card_HELPER
            return $this->sendError("La méthode " . $request->method() . " n'est pas supportée pour cette requete!!", 404);
        };
        return $this->search($request);
    }
}
