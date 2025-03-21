<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Supervisor extends Component
{
    use WithFileUploads;

    public $supervisors = [];

    public $BASE_URL = "";
    public $token = "";
    public $userId;

    public $headers = [];

    public $current_locationId = [];
    public $agent_error = "";

    // 
    public $showAddForm = false;
    public $showAffectationForm = false;

    public $currentActiveSupervisorId;
    public $currentActiveSupervisor = [];

    public $agent = [];

    public $generalError = "";
    public $generalSuccess = "";
    public $allAccountAgents = "";

    // SUPERVISEURS
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

    // AGENT ACCOUNT
    function refreshaAccounts()
    {
        $users = User::with(["supervisors"])->get();
        $account_agents = [];

        foreach ($users as $user) {
            ##recuperation des roles de ce user
            $user_roles = $user->_roles;

            foreach ($user_roles as $user_role) {
                if ($user_role->id == 4) {
                    array_push($account_agents, $user);
                }
            }
        }
        $this->allAccountAgents = $account_agents;
    }

    function mount()
    {
        $this->refreshaAccounts();
        $this->refreshSupervisors();
    }

    function refresh($message)
    {
        $this->generalSuccess = $message;
        $this->showAddForm = false;

        // USERS
        $this->refreshSupervisors();

        // ROLES
        $this->refreshaAccounts();
    }


    public function render()
    {
        return view('livewire.supervisor');
    }
}
