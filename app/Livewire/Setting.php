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

    function mount()
    {
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
    
    public function render()
    {
        return view('livewire.setting');
    }
}
