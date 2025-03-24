<?php

namespace App\Livewire;

use App\Models\Agency;
use App\Models\Profil;
use App\Models\Rang;
// use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithFileUploads;

use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class Setting extends Component
{
    use WithFileUploads;

    public $users = [];

    public $rangs = [];
    public $actions = [];
    public $profils = [];
    public $agencies = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $current_locationId = [];

    // LES DATAS
    public $name;
    public $username;
    public $phone;
    public $email;
    public $agency;

    public $rang;
    public $profil;

    public $role;


    // LES ERREURES
    public $name_error = "";
    public $username_error = "";
    public $phone_error = "";
    public $email_error = "";
    public $agency_error = "";

    public $rang_error = "";
    public $profil_error = "";
    public $role_error = "";

    // 
    public $showAddForm = false;
    public $showUserRoles = false;

    public $showRoleForm = false;

    public $currentActiveUserId;
    public $currentActiveUser = [];

    public $currentUserRoles = [];

    public $allRoles = [];

    public $search = '';

    public $generalError = "";
    public $generalSuccess = "";

    function mount()
    {
        $user = auth()->user();

        // dd($user->hasRole('Super Administrateur'));
        
        // RANGS
        $this->refreshRangs();

        // PROFILS
        $this->refreshProfils();

        // AGENCIES
        $this->refreshAgencies();

        // USERS
        $this->refreshUsers();

        // ROLES
        $this->refreshRoles();
    }

    // AGENCIES
    function refreshAgencies()
    {
        $agencies = Agency::all();
        $this->agencies = $agencies;
    }

    // PROFILS
    function refreshProfils()
    {
        $profils = Profil::all();
        $this->profils = $profils;
    }

    // RANG
    function refreshRangs()
    {
        $rangs = Rang::all();
        $this->rangs = $rangs;
    }

    // USERS
    function refreshUsers()
    {
        $title = 'Suppression de l\'utilisateur!';
        $text = "Voulez-vous vraiment supprimer cet utilisateur?";
        confirmDelete($title, $text);

        $users = User::where("visible",1)->get();
        $this->users = $users;
    }

    // ROLES
    function refreshRoles()
    {
        $roles = Role::all();
        $this->allRoles = $roles;
    }

    public function searching()
    {
        set_time_limit(0);

        $this->showAddForm = false;
        $data = [
            "search" => $this->search
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "user/{$this->userId}/search", $data)->json();


        if (!$response) {
            $this->generalError = "Une erreure est survenue! Veuillez réessayez plus tard!";
        } else {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
                $this->generalSuccess = "";
            } else {
                $successMsg = $response["message"];
                $this->generalSuccess = $successMsg;
                $this->generalError = "";

                $this->users = $response["data"];
            }
        }
    }

    function ShowAddForm()
    {
        set_time_limit(0);

        $this->generalError = "";

        if (!$this->showAddForm) {
            $this->showAddForm = true;
        } else {
            $this->showAddForm = false;
        }
        $this->showUserRoles = false;
        $this->showRoleForm = false;
    }

    function ShowAffectRoleForm($id)
    {
        set_time_limit(0);

        $this->generalError = "";

        $this->currentActiveUserId = $id;
        $user = Http::withHeaders($this->headers)->get($this->BASE_URL . "user/users/$id")->json();
        if (!$user["status"]) {
            $this->currentActiveUser = [];
        } else {
            $this->currentActiveUser = $user["data"];
        }

        if (!$this->showRoleForm) {
            $this->showRoleForm = true;
        } else {
            $this->showRoleForm = false;
        }
        $this->showAddForm = false;
        $this->showUserRoles = false;
    }

    function AffectRole()
    {
        set_time_limit(0);

        $data = [
            "user_id" => $this->currentActiveUserId,
            "role_id" => $this->role,
        ];
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "role/attach-user", $data)->json();
        // dd($response);
        if ($response) {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
        }
    }

    function Archiver($id)
    {
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "user/$id/archive")->json();
        if ($response) {
            if (!$response["status"]) {
                $this->generalError = $response["erros"];
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
        }
    }

    function Dupliquer($id)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "user/$id/duplicate")->json();
        if ($response) {
            if (!$response["status"]) {
                $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->refresh("Une erreure est survenue! Veuillez bien réessayer!");
        }
    }

    public function Delete(int $id)
    {
        set_time_limit(0);

        $response = Http::withHeaders($this->headers)->delete($this->BASE_URL . "user/{$id}/delete")->json();

        if (!$response["status"]) {
            $this->generalError = $response["erros"];
        } else {
            $this->refresh($response["message"]);
        }
    }

    function seeRoles($userId)
    {
        $this->generalError = "";
        $this->currentActiveUserId = $userId;
        $user = User::find($userId);## Http::withHeaders($this->headers)->get($this->BASE_URL . "user/users/$userId")->json();
        $this->currentUserRoles = $user->roles;
        $this->currentActiveUser = $user;
        if (!$this->showUserRoles) {
            $this->showUserRoles = true;
        } else {
            $this->showUserRoles = false;
        }

        $this->showRoleForm = false;
        $this->showAddForm = false;
    }

    function refresh($message)
    {
        $this->generalSuccess = $message;
        // neutralisation des datas
        $this->name = "";
        $this->username = "";
        $this->phone = "";
        $this->email = "";
        $this->role;

        // LES ERREURES
        $this->name_error = "";
        $this->username_error = "";
        $this->phone_error = "";
        $this->email_error = "";

        $this->role_error = "";
        $this->showAddForm = false;

        // RANGS
        $this->refreshRangs();

        // PROFILS
        $this->refreshProfils();

        // AGENCIES
        $this->refreshAgencies();

        // USERS
        $this->refreshUsers();

        // ROLES
        $this->refreshRoles();
    }

    function remove($roleId)
    {
        set_time_limit(0);

        $data = [
            "user_id" => $this->currentActiveUserId,
            "role_id" => $roleId,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "role/desattach-user", $data)->json();
        // dd($response);

        if ($response) {
            if (!$response["status"]) {
                if (array_key_exists("erros", $response)) {
                    $this->generalError = $response["erros"];
                }
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez éssayer à nouveau";
        }

        $this->showAddForm = false;
    }

    function addUser()
    {
        set_time_limit(0);

        $data = [
            "owner" => $this->userId,
            "name" => $this->name,
            "username" => $this->username,
            "email" => $this->email,
            "phone" => $this->phone,
            "rang" => $this->rang,
            "profil" => $this->profil,
            "agency" => $this->agency,
        ];

        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "user/add", $data)->json();
        if ($response) {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("name", $errors)) {
                        $this->name_error = $errors["name"][0];
                    }

                    if (array_key_exists("username", $errors)) {
                        $this->username_error = $errors["username"][0];
                    }
                    if (array_key_exists("phone", $errors)) {
                        $this->phone_error = $errors["phone"][0];
                    }
                    if (array_key_exists("email", $errors)) {
                        $this->email_error = $errors["email"][0];
                    }

                    if (array_key_exists("profil", $errors)) {
                        $this->profil_error = $errors["profil"][0];
                    }
                    if (array_key_exists("rang", $errors)) {
                        $this->rang_error = $errors["rang"][0];
                    }

                    if (array_key_exists("agency", $errors)) {
                        $this->agency_error = $errors["agency"][0];
                    }
                } else {
                    $this->generalError = $response["message"];
                }
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
        }
    }

    function Update($id)
    {
        set_time_limit(0);

        ###___RETRIEVE DU USER
        $response = Http::withHeaders($this->headers)->get($this->BASE_URL . "user/users/$id")->json();

        $this->currentActiveUser = $response["data"];

        $data = [
            "owner" => $this->userId,
            "name" => $this->name ? $this->name : $this->currentActiveUser["name"],
            "username" => $this->username ? $this->username : $this->currentActiveUser["username"],
            "email" => $this->email ? $this->email : $this->currentActiveUser["email"],
            "phone" => $this->phone ? $this->phone : $this->currentActiveUser["phone"],
            "rang" => $this->rang ? $this->rang : $this->currentActiveUser["rang_id"],
            "profil" => $this->profil ? $this->profil : $this->currentActiveUser["profil_id"],
            "agency" => $this->agency ? $this->agency : $this->currentActiveUser["agency"],
        ];


        ########_________
        $response = Http::withHeaders($this->headers)->post($this->BASE_URL . "user/$id/update", $data)->json();
        if ($response) {
            if (!$response["status"]) {
                $errors = $response["erros"];
                if (gettype($errors) == "array") {
                    if (array_key_exists("name", $errors)) {
                        $this->name_error = $errors["name"][0];
                    }

                    if (array_key_exists("username", $errors)) {
                        $this->username_error = $errors["username"][0];
                    }
                    if (array_key_exists("phone", $errors)) {
                        $this->phone_error = $errors["phone"][0];
                    }
                    if (array_key_exists("email", $errors)) {
                        $this->email_error = $errors["email"][0];
                    }

                    if (array_key_exists("profil", $errors)) {
                        $this->profil_error = $errors["profil"][0];
                    }
                    if (array_key_exists("rang", $errors)) {
                        $this->rang_error = $errors["rang"][0];
                    }

                    if (array_key_exists("agency", $errors)) {
                        $this->agency_error = $errors["agency"][0];
                    }
                } else {
                    $this->generalError = $response["message"];
                }
            } else {
                $this->refresh($response["message"]);
            }
        } else {
            $this->generalError = "Une erreure est survenue! Veuillez bien réessayer!";
        }
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
