<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgentAccountSupervisor;
use App\Models\Right;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRight;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use USER_HELPER;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth'])->only([
            "UpdatePassword",
            "Logout",
            "AttachRightToUser",
            "DesAttachRightToUser",
            "ArchiveAccount",
            "DuplicatAccount",
            "GetAllSupervisors",
            "AddUser",
            "Users",
            "UpdateCompte",
            "DeleteAccount"
        ]);

        $this->middleware(["Check_If_User_Has_A_Master_Role"])->only([
            // "UpdateCompte",
            "AttachRightToUser",
            "DesAttachRightToUser",
            "ArchiveAccount",
            "DuplicatAccount"
        ]);
    }

    #########################======= LES VALIDATIONS ============##########

    ##======== REGISTER VALIDATION =======##
    static function register_rules(): array
    {
        return [
            'name' => 'required',
            'username' => 'required',
            'phone' => ['required', 'numeric'],
            'agency' => ['required', 'integer'],
            // 'email' => ['required', 'email', Rule::unique('users')],
            'email' => ['required', 'email'],
            // 'profil' => ['required', 'integer'],
            // 'rang' => ['required', 'integer'],
        ];
    }

    static function register_messages(): array
    {
        return [
            'agency.required' => "Choisissez une agence",
            'agency.integer' => "Ce champ doit être un entier",
            'name.required' => 'Le nom entier de l\'utilisateur est réquis!',
            'username.required' => 'L\'identifiant(uersname) est réquis!',
            'email.required' => 'Le champ Email est réquis!',
            'email.email' => 'Ce champ est un mail!',
            // 'email.unique' => 'Un compte existe déjà au nom de ce mail!',
            // 'phone.unique' => 'Un compte existe déjà au nom de ce phone',
            'phone.required' => 'Le phone est réquis!',
            'phone.numeric' => 'Le phone doit être de type numéric!',

            'agency.required' => "Veillez préciser l'agence",
            'agency.integer' => "L'agence doit être de type entier!",

            // 'profil.required' => "Veillez préciser le profil",
            // 'profil.integer' => "Le profil doit être de type entier!",

            // 'rang.required' => "Veillez préciser le rang",
            // 'rang.integer' => "Le rang doit être de type entier!",
        ];
    }

    ##======== LOGIN VALIDATION =======##
    static function login_rules(): array
    {
        return [
            'account' => 'required',
            'password' => 'required',
        ];
    }

    static function login_messages(): array
    {
        return [
            'account.required' => 'Le champ Username est réquis!',
            'password.required' => 'Le champ Password est réquis!',
        ];
    }

    ##======== AFFECT SUPERVISOR VALIDATION =======##
    static function affect_supervisor_rules(): array
    {
        return [
            'supervisor' => ['required', 'integer'],
            'agent_account' => ['required', 'integer'],
        ];
    }

    static function affect_supervisor_messages(): array
    {
        return [
            "supervisor.required" => "Veuillez bien préciser le superviseur!",
            "supervisor.integer" => "Ce champ doit être de type entier!",

            "agent_account.required" => "Veuillez bien préciser l'agent comptable!",
            "agent_account.integer" => "Ce champ doit être de type entier!"
        ];
    }

    ############################====== FIN VALIDATIONS ===========################

    ############################====== LES METHODES ============################

    #AJOUT D'UN UTILISATEUR
    function AddUser(Request $request)
    {
        $formData = $request->all();

        // VALIDATION
        Validator::make($request->all(), self::register_rules(), self::register_messages())->validate();

        $user = request()->user();
        $formData['pass_default'] = Custom_Timestamp();
        $formData['password'] = $formData['username'];
        $formData['owner'] = $user->id;
        // $formData['rang_id'] = $formData['rang'];
        // $formData['profil_id'] = $formData['profil'];

        #ENREGISTREMENT
        $create_user = User::create($formData);

        try {
            Send_Notification(
                $create_user,
                "Création de compte sur IMMO ERP",
                "Votre compte à été crée avec succès sur IMMO ERP. Veuillez utiliser cet identifiant pour vous connecter : " . $formData['username'],
            );
        } catch (\Throwable $th) {
            throw $th;
        }

        alert()->success('Succès', "Utilisateur ajouté avec succès!!");
        return redirect()->back();
    }

    #CONNEXION DU USER
    function Login(Request $request)
    {
        #####___
        if ($request->method() == "GET") {
            return redirect()->route("home");
        }

        #VALIDATION DES DATAs
        $rules = self::login_rules();
        $messages = self::login_messages();

        Validator::make($request->all(), $rules, $messages)->validate();

        #AUTHENTIFICATION DU USER
        if (is_numeric($request->get('account'))) {
            $credentials  =  ['phone' => $request->get('account'), 'password' => $request->get('password')];
        } elseif (filter_var($request->get('account'), FILTER_VALIDATE_EMAIL)) {
            $credentials  =  ['email' => $request->get('account'), 'password' => $request->get('password')];
        } else {
            $credentials  =  ['username' => $request->get('account'), 'password' => $request->get('password')];
        }

        if (Auth::attempt($credentials)) { #SI LE USER EST AUTHENTIFIE
            $user = Auth::user();

            //   REGENERATION DE LA SESSION POUR L'UTILISATEUR
            $request->session()->regenerate();

            #RENVOIE D'ERREURE VIA **sendResponse** DE LA CLASS BASE_HELPER
            alert()->success('Succès', "Vous etes connecté(e) avec succès!!");
            return redirect()->route("dashbord");
        } else {
            alert()->error('Echec', "Connexion échouée!!");
            return redirect()->back()->withInput();
        }
    }

    function Logout(Request $request)
    {
        // °°°°°°°°°°° DECONNEXION DU USER
        Auth::logout();

        // °°°°°°°°° SUPPRESION DES SESSIONS
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // °°°°°° REDIRECTION °°°°°°°°°°°°
        alert()->success('Succès', "Vous êtes déconnecté avec succès!");
        return redirect()->route("home");
    }

    #ARCHIVER UN COMPTE
    function ArchiveAccount(Request $request, $userId)
    {
        $user = User::findOrFail(deCrypId($userId));
        if (!$user) {
            alert()->error('Echec', "Ce compte n'existe pas");
            return redirect()->back();
        }

        if ($user->is_archive) {
            alert()->error('Echec', "Desolé! Ce compte est déjà archivé!");
            return redirect()->back();
        }
        $user->is_archive = true;
        $user->save();

        alert()->success('Succès', "Compte archivé avec succès!");
        return redirect()->back();
    }

    #DEMANDE DE REINITIALISATION D'UN PASSWORD
    function DemandReinitializePassword(Request $request)
    {
        ###____GET METHOD
        if ($request->method() == "GET") {
            return view("reinitialisationDemande");
        }

        ####___VALIDATION
        Validator::make(
            $request->all(),
            [
                "email" => ["required", "email"]
            ],
            [
                "email.required" => "Ce champ est réquis",
                "email.email" => "Ce champ doit être un mail",
            ]
        )->validate();

        $email = $request->email;
        $user = User::where(['email' => $email])->first();

        if (!$user) {
            alert()->error('Echec', "Ce compte n'existe pas");
            return redirect()->back();
        };

        #
        $pass_code = Get_passCode($user, "PASS");
        $user->pass_code = $pass_code;
        $user->pass_code_active = 1;
        $user->save();

        $message = "Demande de réinitialisation éffectuée avec succès! sur PERFECT ERP! Voici vos informations de réinitialisation de password ::" . $pass_code;

        #=====ENVOIE D'EMAIL =======~####
        try {
            Send_Notification(
                $user,
                "DEMANDE DE REEINITIALISATION DE MOT DE PASSE EFFECTUEE SUR PERFECT ERP",
                $message,
            );
        } catch (\Throwable $th) {
            //throw $th;
        }

        alert()->success('Succès', "Demande de réinitialisation éffectuée avec succès!");
        return redirect()->route("Reinitialisation");
    }

    #REINITIALISER UN PASSWORD
    function ReinitializePassword(Request $request)
    {
        ###____GET METHOD
        if ($request->method() == "GET") {
            return view("reinitialisation");
        }

        ####___VALIDATION
        Validator::make(
            $request->all(),
            [
                "pass_code" => ["required"],
                "password" => [
                    "required",
                    "confirmed",
                    Password::min(6),
                    // ->letters()
                    // ->mixedCase()
                    // ->numbers(),
                    Rule::unique("users", "id")
                ]
            ],
            [
                "pass_code.required" => "Ce Code est réquis",
                "password.required" => "Entrez un nouveau mot de passe",
                "password.required" => "Choisissez un autre mot de passe",
                "password.confirmed" => "Les mots de passe ne sont pas conforme",
                "password.min" => "Le mot de passe doit comporter au moins 6 caractères",
            ]
        )->validate();

        $user = User::where(['pass_code' => $request->pass_code])->first();
        if (!$user) {
            alert()->error('Echec', "Ce code n'est pas celui qui vous a été envoyé!");
            return redirect()->back();
        };

        #Voyons si le passs_code envoyé par le user est actif
        if ($user->pass_code_active == 0) {
            alert()->error('Echec', "Ce Code a déjà été utilisé une fois! Veuillez faire une autre demande de réinitialisation");
            return redirect()->back();
        }

        #UPDATE DU PASSWORD
        $user->update(['password' => $request->password]);

        #SIGNALONS QUE CE pass_code EST DEJA UTILISE
        $user->pass_code_active = 0;
        $user->save();

        $message = "Réinitialisation de password éffectuée avec succès sur PERFECT ERP!";

        #=====ENVOIE D'EMAIL =======~####
        try {
            Send_Notification(
                $user,
                "REEINITIALISATION EFFECTUEE SUR PERFECT ERP",
                $message,
            );
        } catch (\Throwable $th) {
            //throw $th;
        }
        alert()->success('Succès', "Réinitialisation éffectuée avec succès! Connectez-vous maintenant");
        return redirect()->route("home");
    }

    #DUPLIQUER UN COMPTE
    function DuplicatAccount(Request $request, $userId)
    {
        $account = User::findOrFail(deCrypId($userId));
        if (!$account) {
            alert()->error('Echec', "Cet utilisateur n'existe pas!");
            return redirect()->back();
        }

        $datas = [
            "owner" => request()->user() ? request()->user()->id : null,
            "name" => $account["name"],
            'username' => $account["username"],
            'email' => $account["email"],
            'password' => $account["password"],
            'organisation' => $account["organisation"],
            "phone" => $account["phone"],
            "is_archive" => 0,
            "profil_id" => $account["profil_id"],
            "rang_id" => $account["rang_id"],
            "pass_code_active" => $account["pass_code_active"],
            "pass_code" => $account["pass_code"],
            "compte_actif" => $account["compte_actif"],
            "active_compte_code" => $account["active_compte_code"],
            "organisation" => $account["organisation"],
            "is_super_admin" => $account["is_super_admin"],
            "is_admin" => $account["is_admin"]
        ];
        $account_duplicated = User::create($datas);

        ###___REAFFECTATION DES DROITS DU COMPTE ARCHIVE AU COMPTE DUPLIQUE
        $user_rights = UserRight::where(["user_id" => $account->id])->get();

        foreach ($user_rights as $user_right) {
            $user_right->user_id = $account_duplicated->id;
            $user_right->save();
        }

        ###__ARCHIVONS ENSUITE LE COMPTE
        $account->is_archive = true;
        $account->save();
        ###__

        alert()->success('Succès', "Compte dupliqué avec succès");
        return redirect()->back();
    }

    #MODIFIER UN COMPTE
    function UpdateCompte(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        if (!$user) {
            alert()->error('Echec', "Cet utilisateur n'existe pas!");
            return redirect()->back();
        }

        ###___MODIFICATION
        $user->update($request->all());
        ###___

        if ($userId == 1 || $userId == 2) {## quand c'est un admin ou un master
            $user->update(["agency" => null]);
        }
        
        // pour une modification de mot de passe on passe à une deconnection
        if ($request->password) {
            alert()->success('Succès', 'Mot de passe modifié avec succès!');
            return redirect()->route("logout");
        }

        // update des roles
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        $user->assignRole($request->input('roles'));

        alert()->success('Succès', 'Compte modifié avec succès!');
        return redirect()->back();
    }

    function DeleteAccount(Request $request, $userId)
    {
        $account = User::findOrFail(deCrypId($userId));
        if (!$account) {
            alert()->error('Echec', "Ce compte n'existe pas!");
            return redirect()->back();
        }

        if ($account->id == 1 || $account->id == 2) {
            alert()->error('Echec', 'Désolé! Vous ne pouvez pas supprimer ce compte!');
            return redirect()->back();
        }

        ###____
        $account->visible = 0;
        $account->save();

        alert()->success('Succès', 'Compte supprimé avec succès!');
        return redirect()->back();
    }

    ###___AFFECTATION D'UN SUPERVISEUR A UN AGENT COMPTABLE
    function AffectSupervisorToAccountyAgent(Request $request, $supervisor)
    {
        $user = User::findOrFail(deCrypId($supervisor));
        if (!$user) {
            alert()->error('Echec', "Ce superviseur n'existe pas!");
            return redirect()->back();
        }

        ####___GET REQUEST
        if ($request->method() == "GET") {
            $agents = [];

            foreach (User::all() as $user) {
                ##recuperation des roles de ce user
                $user_roles = $user->roles;

                foreach ($user_roles as $user_role) {
                    if ($user_role->id == env("ACCOUNT_AGENT")) {
                        array_push($agents, $user);
                    }
                }
            }
            return view("users.affect-agent-comptable", compact("agents", "user"));
        }

        ###___VALIDATION DES DATAS

        $formData = [
            "agent_account" => $request->agent,
            "supervisor" => $request->user_id,
        ];

        $rules = self::affect_supervisor_rules();
        $messages = self::affect_supervisor_messages();

        Validator::make($formData, $rules, $messages)->validate();



        $agent_account = User::find($formData["agent_account"]);
        $supervisor = User::find($formData["supervisor"]);

        // return $agent_account;
        if (!$agent_account) {
            alert()->error('Echec', "Cet agent comptable n'existe pas!");
            return redirect()->back();
        }
        if (!$supervisor) {
            alert()->error('Echec', "Ce superviseur n'existe pas!");
            return redirect()->back();
        }

        ####____VERIFICATION DE L'EXISTENCE DE L'AFFECTATION
        $is_this_affectation_existe = AgentAccountSupervisor::where([
            "supervisor" => $formData["supervisor"],
            "agent_account" => $formData["agent_account"],
        ])->first();

        if ($is_this_affectation_existe) {
            alert()->error('Echec', "Cette affectation existe déjà!");
            return redirect()->back();
        }

        ###__VERIFICATION DU VRAI ROLE D'UN AGENT COMPTABLE
        $agent_account_roles = $agent_account->roles; ##recuperation des roles de ce user

        ###__CETTE VARIABLE DEFINI SI CET UTILISATEUR DISPOSE VRAIMENT DU ROLE D'UN AGENT COMPTABLE 
        $is_this_user_really_agent_account =  false;

        foreach ($agent_account_roles as $user_role) {
            if ($user_role->id == env("ACCOUNT_AGENT")) {
                $is_this_user_really_agent_account = true;
            }
        }

        if (!$is_this_user_really_agent_account) {
            alert()->error('Echec', "Désolé! L'utilisateur ( " .  $agent_account['name'] . " ) ne dispose vraiment pas du rôle d'un agent comptable");
            return redirect()->back();
        }

        ###__VERIFICATION DU VRAI ROLE DU SUPERVISEUR
        $supervisor_roles = $supervisor->roles; ##recuperation des roles de ce user

        ###__CETTE VARIABLE DEFINI SI CET UTILISATEUR DISPOSE VRAIMENT DU ROLE D'UN AGENT COMPTABLE 
        $is_this_user_really_supervisor =  false;

        foreach ($supervisor_roles as $user_role) {
            if ($user_role->id == env("SUPERVISOR_ROLE_ID")) {
                $is_this_user_really_supervisor = true;
            }
        }

        if (!$is_this_user_really_supervisor) {
            alert()->error('Echec', "Désolé! L'utilisateur ( " .  $supervisor['name'] . " ) ne dispose vraiment pas du rôle d'un superviseur!");
            return redirect()->back();
        }

        ##########___ AFFECTATION PROPREMENT DITE #############
        $affectation = AgentAccountSupervisor::create($formData);

        if ($affectation) {
            alert()->success('Succès', "Affectation éffectuée avec succès!");
            return redirect()->back();
        } else {
            alert()->success('Echec', "Affectation échouée! Veuillez éssayer à nouveau!");
            return redirect()->back();
        }
    }

    // RETRIEVE USER
    function RetrieveUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    ###____ET USER ROLES
    function GetUserRoles(Request $request, $userId)
    {
        $user = User::findOrFail(deCrypId($userId));
        if (!$user) {
            alert()->error('Echec', "Cet utilisateur n'existe pas!");
            return redirect()->back();
        }

        $user->load("_roles");
        // dd($user->_roles);
        return view("users.roles", compact("user"));
    }

    ###____ATTACHER UN ROLE
    function AttachRoleToUser(Request $request, $userId)
    {
        $user = User::findOrFail(deCrypId($userId));
        if (!$user) {
            alert()->error('Echec', "Cet utilisateur n'existe pas!");
            return redirect()->back();
        }

        ##__GET REQUEST
        if ($request->method() == "GET") {
            $roles = Role::all();
            return view("users.affect-role", compact("roles", "user"));
        }

        ###__POST REQUEST
        $current_user = request()->user();
        $formData = $request->all();

        $current_user = request()->user();
        if ($current_user->is_admin) {
            $user = User::where(['id' => $formData['user_id']])->get();
        } else {
            $user = User::where(['id' => $formData['user_id'], 'owner' => $current_user->id])->get();
        }


        $role = role::where('id', $formData['role_id'])->get();
        if (count($role) == 0) {
            alert()->error('Echec', "Cet rôle n'existe pas!");
            return redirect()->back();
        };

        $is_this_attach_existe = UserRole::where(["user_id" => $formData['user_id'], "role_id" => $formData['role_id']])->first();
        if ($is_this_attach_existe) {
            alert()->error('Echec', "Cet utilisateur dispose déjà de ce role!");
            return redirect()->back();
        }
        ##__

        $user_role = new UserRole();
        $user_role->user_id = $formData['user_id'];
        $user_role->role_id = $formData['role_id'];
        $user_role->save();

        ###___
        alert()->success('Succès', "Rôle affecté avec succès!!");
        return redirect()->back();
    }

    ###____RETIRER UN ROLE
    function DesAttachRoleToUser(Request $request)
    {
        $current_user = request()->user();
        $formData = $request->all();

        if ($current_user->is_admin) {
            $user = User::where(['id' => $formData['user_id']])->get();
        } else {
            $user = User::where(['id' => $formData['user_id'], 'owner' => $current_user->id])->get();
        }
        if (count($user) == 0) {
            alert()->error('Echec', "Cet utilisateur n'existe pas!");
            return redirect()->back();
        };

        $role = Role::where('id', $formData['role_id'])->get();
        if (count($role) == 0) {
            alert()->error('Echec', "Ce rôle n'existe pas!");
            return redirect()->back();
        };

        ###___retrait du role qui lui a été affecté par defaut
        $user_role = UserRole::where(["user_id" => $formData['user_id'], "role_id" => $formData['role_id']])->first();
        if (!$user_role) {
            alert()->error('Echec', "Ce user ne dispose pas de ce role!");
            return redirect()->back();
        }

        $user_role->delete();

        ###___
        alert()->success('Succès', "Rôle retiré avec succès!!");
        return redirect()->back();
    }
}
