<?php

use App\Models\Facture;
use App\Models\Product;
use App\Models\Right;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

function supervisors()
{
    $users = User::with(["account_agents"])->get()->filter(function ($user) {
        return $user->hasRole('Superviseur');
    });
    return $users;
}

#####========= ROLES ======####
function IS_USER_HAS_SUPERVISOR_ROLE($user)
{
    if (in_array(env("SUPERVISOR_ROLE_ID"), $user->_roles->pluck("id")->toArray())) {
        return true;
    }

    return false;
}

function IS_USER_HAS_ACCOUNT_AGENT_ROLE($user)
{
    if (in_array(env("ACCOUNT_AGENT_ROLE_ID"), $user->_roles->pluck("id")->toArray())) {
        return true;
    }

    return false;
}

function IS_USER_HAS_ACCOUNT_CHIEF_ROLE($user)
{
    if (in_array(env("ACCOUNT_CHIEF_ROLE_ID"), $user->_roles->pluck("id")->toArray())) {
        return true;
    }

    return false;
}

function IS_USER_HAS_MASTER_ROLE($user)
{
    if (in_array(env("MASTER_ROLE_ID"), $user->_roles->pluck("id")->toArray())) {
        return true;
    }

    return false;
}

function crypId($id)
{
    return Crypt::encrypt($id);
}

function deCrypId($id)
{
    return Crypt::decrypt($id);
}

function Is_dates_equals($date1, $date2)
{
    $date1_str = strtotime($date1);
    $date2_str = strtotime($date2);

    $date1_transformed = date("Y/m/d", $date1_str);
    $date2_transformed = date("Y/m/d", $date2_str);

    if ($date1_transformed == $date2_transformed) {
        return true;
    } else {
        return false;
    }
}

function Calcul_Perfomance(int $nbr_buzy_rooms, int $nbr_free_rooms)
{
    if ($nbr_free_rooms == 0) {
        $total = 0;
    } else {
        $total = ($nbr_buzy_rooms / $nbr_free_rooms) * 100;
    }
    return $total;
}

function Change_date_to_text($date)
{
    return  \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('MMMM YYYY');
}

function userCount()
{
    return count(User::all()) + 1;
}

function NumersDivider($a, $b)
{
    return $b != 0 ? ($a / $b) * 100 : 0;
}

function Custom_Timestamp()
{
    $date = new DateTimeImmutable();
    $micro = (int)$date->format('Uu'); // Timestamp in microseconds
    return $micro;
}

function Get_Username($user, $type)
{
    $created_date = $user->created_at;

    $year = explode("-", $created_date)[0];
    $an = substr($year, -2);
    $tierce = substr(Custom_Timestamp(), -3);

    $username =  $type . $an . userCount() . $tierce;
    return $username;
}

function Add_Number($user, $type)
{
    $created_date = $user->created_at;

    $year = explode("-", $created_date)[0]; ##RECUPERATION DES TROIS PREMIERS LETTRES DU USERNAME
    $an = substr($year, -2);

    $number = "DGT" . $type . $an . userCount();
    return $number;
}

function Send_Notification($receiver, $subject, $message)
{
    $data = [
        "subject" => $subject,
        "message" => $message,
    ];

    Notification::send($receiver, new SendNotification($data));
}

function Send_Notification_Via_Mail($email, $subject, $message)
{
    $data = [
        "subject" => $subject,
        "message" => $message,
    ];
    Notification::route("mail", $email)->notify(new SendNotification($data));
}

##Ce Helper permet de creér le passCode de réinitialisation de mot de passe
function Get_passCode($user, $type)
{
    $created_date = $user->created_at;

    $year = explode("-", $created_date)[0]; ##RECUPERATION DES TROIS PREMIERS LETTRES DU USERNAME
    $an = substr($year, -2);
    $timestamp = substr(Custom_Timestamp(), -3);

    $passcode =  $timestamp . $type . $an . userCount();
    return $passcode;
}

##======== CE HELPER PERMET DE VERIFIER SI LE USER EST UN SIMPLE ADMIN OU PAS ==========## 
function Is_User_An_Admin($userId)
{ #
    $user = User::where(['id' => $userId, 'is_admin' => 1])->get();
    if (count($user) == 0) {
        return false;
    }
    return true; #Sil est un Simple Admin
}

##======== CE HELPER PERMET DE VERIFIER SI LE USER EST UN SUPER ADMIN OU PAS ==========## 
function Is_User_A_Super_Admin($userId)
{ #
    $user = User::where(['id' => $userId, 'is_super_admin' => 1])->get();
    if (count($user) == 0) {
        return false;
    }
    return true; #Sil est un Super Admin
}

function Is_User_A_SimpleAdmin_Or_SuperAdmin($userId)
{
    if (Is_User_An_Admin($userId) || Is_User_A_Super_Admin($userId)) {
        return true; #S'il s'agit d'un Simple Admin ou d'un Super Admin
    }
    return false; #S'il n'est ni l'un nil'autre
}

function GET_USER_ROLES($userId)
{
    $roles = UserRole::with(["role", "user"])->where(["user_id" => $userId])->get();
    return $roles;
}


##======== CE HELPER PERMET DE VERIFIER SI LE USER EST A LE ROLR D'UN MASTER OU PAS ==========## 
function Is_User_Has_A_Master_Role($userId)
{
    $user_roles = GET_USER_ROLES($userId);
    $result = false;
    foreach ($user_roles as $user_role) {
        if ($user_role->role_id == 4) {
            $result = true;
        }
    }
    ##____
    return $result;
}

##======== CE HELPER PERMET DE VERIFIER SI LE USER A LE ROLE D'UN CHEF COMPTABLE OU PAS ==========## 
function Is_User_Has_A_Chief_Accountant_Role($userId)
{ #
    $user_roles = GET_USER_ROLES($userId);
    $result = false;
    foreach ($user_roles as $user_role) {
        if ($user_role->role_id == 3) {
            $result = true;
        }
    }
    ##____
    return $result;
}

##======== CE HELPER PERMET DE VERIFIER SI LE USER A LE ROLE D'UN AGENT COMPTABLE OU PAS ==========## 
function Is_User_Has_An_Agent_Accountant_Role($userId)
{
    $user_roles = GET_USER_ROLES($userId);
    $result = false;
    foreach ($user_roles as $user_role) {
        if ($user_role->role_id == 2) {
            $result = true;
        }
    }
    ##____
    return $result;
}

##======== CE HELPER PERMET DE VERIFIER SI LE USER A LE ROLE D'UN SUPERVISEUR OU PAS ==========## 
function Is_User_Has_A_Supervisor_Role($userId)
{
    $user_roles = GET_USER_ROLES($userId);
    $result = false;
    foreach ($user_roles as $user_role) {
        if ($user_role->role_id == 1) {
            $result = true;
        }
    }
    ##____
    return $result;
}



##======== CE HELPER PERMET DE RECUPERER LES DROITS D'UN UTILISATEUR ==========## 
function User_Rights($rangId, $profilId)
{ #
    $rights = Right::with(["action", "profil", "rang"])->where(["rang" => $rangId, "profil" => $profilId])->get();
    return $rights;
}

##======== CE HELPER PERMET DE RECUPERER TOUTS LES DROITS PAR DEFAUT ==========## 
function All_Rights()
{ #
    $allrights = Right::with(["action", "profil", "rang"])->get();
    return $allrights;
}


#######____GET HOUSE DETAIL ======######
function GET_HOUSE_DETAIL($house)
{
    $nbr_month_paid = 0;
    $total_amount_paid = 0;

    $house_factures_nbr_array = [];
    $house_amount_nbr_array = [];

    ####_____DERNIER ETAT DE CETTE MAISON
    $house_last_state = $house->States->last();

    $locations = $house->Locations->where("status", "!=", 3);

    ###___DERTERMINONS LE NOMBRE DE FACTURE ASSOCIEE A CETTE MAISON
    foreach ($locations as $key => $location) {
        if ($house_last_state) {
            ###___quand il y a arrêt d'etat
            ###__on recupere les factures du dernier arrêt des etats de la maison
            $last_state_date = $house_last_state->created_at;
            $now = now();
            $location_factures = Facture::where(["location" => $location->id, "state_facture" => 0])->whereBetween("created_at", [$last_state_date, $now])->get();
        } else {
            ###___s'il n'y a pas de dernier état, on prends en compte toutes les factures de la maison
            $location_factures = $location->Factures;
        }

        foreach ($location_factures as $facture) {
            array_push($house_factures_nbr_array, $facture);

            if ($location->prorata_amount) {
                array_push($house_amount_nbr_array, $location->prorata_amount);
            } else {
                array_push($house_amount_nbr_array, $facture->amount);
            };
        }

        ####_____REFORMATION DU LOCATAIRE DE CETTE LOCATION
        ###____
        $houses = $location->House;
        $rooms = $location->Room;

        $nbr_month_paid_array = [];
        $nbr_facture_amount_paid_array = [];
        ####___________

        ########===========     ====================####

        ###__s'il n'y a pas d'état, on tient compte de tout les factures
        ##___liées à cette location
        foreach ($location->Factures as $facture) {
            array_push($nbr_month_paid_array, $facture);
            array_push($nbr_facture_amount_paid_array, $facture->amount);
        }

        ####_____
        $locataire["nbr_month_paid_array"] = count($nbr_month_paid_array);
        $locataire["nbr_facture_amount_paid_array"] = array_sum($nbr_facture_amount_paid_array);
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
    $house["last_depenses"] = array_sum($last_state_depenses_array);
    $house["actuel_depenses"] = array_sum($current_state_depenses_array);
    $house["total_amount_paid"] = $total_amount_paid;
    $house["house_last_state"] = $house_last_state;
    $house["nbr_month_paid"] = $nbr_month_paid;
    $house["commission"] = ($house["total_amount_paid"] * $house->commission_percent) / 100;
    ####________

    $house["net_to_paid"] = $house["total_amount_paid"] - ($house["actuel_depenses"] + $house["commission"]);
    // dd($house["actuel_depenses"]);

    ####____RAJOUTONS LES INFOS DE TAUX DE PERFORMANCE DE LA MAISON
    $creation_date = date("Y/m/d", strtotime($house["created_at"]));
    $creation_time = strtotime($creation_date);
    $first_month_period = strtotime("+1 month", strtotime($creation_date));

    $frees_rooms = [];
    $busy_rooms = [];
    $frees_rooms_at_first_month = [];
    $busy_rooms_at_first_month = [];

    foreach ($house->Rooms as $room) {

        $is_this_room_buzy = false; #cette variable determine si cette chambre est occupée ou pas(elle est occupée lorqu'elle se retrouve dans une location de cette maison)
        ##__parcourons les locations pour voir si cette chambre s'y trouve

        foreach ($house->Locations as $location) {
            if ($location->Room->id == $room->id) {
                $is_this_room_buzy = true;

                ###___verifions la période d'entrée de cette chambre en location
                ###__pour determiner les chambres vide dans le premier mois
                $location_create_date = strtotime(date("Y/m/d", strtotime($location["created_at"])));
                ##on verifie si la date de creation de la location est comprise entre le *$creation_time* et le *$first_month_period* de la maison 
                if ($creation_time < $location_create_date && $location_create_date < $first_month_period) {
                    array_push($busy_rooms_at_first_month, $room);
                } else {
                    array_push($frees_rooms_at_first_month, $room);
                }
            }
        }


        ###__
        if ($is_this_room_buzy) { ##__quand la chambre est occupée
            array_push($busy_rooms, $room);
        } else {
            array_push($frees_rooms, $room); ##__quand la chambre est libre
        }
    }

    $house["busy_rooms"] = $busy_rooms;
    $house["frees_rooms"] = $frees_rooms;
    $house["busy_rooms_at_first_month"] = $busy_rooms_at_first_month;
    $house["frees_rooms_at_first_month"] = $frees_rooms_at_first_month;

    $house["last_payement_initiation"] = $house_last_state ? ($house_last_state->PaiementInitiations ? $house_last_state->PaiementInitiations->last() : []) : [];

    return $house;
}

#######____GET HOUSE DETAIL ======######
function GET_HOUSE_DETAIL_FOR_THE_LAST_STATE($house)
{
    $nbr_month_paid = 0;
    $total_amount_paid = 0;

    $house_factures_nbr_array = [];
    $house_amount_nbr_array = [];
    ####_____DERNIER ETAT DE CETTE MAISON
    $house_last_state = $house->States->last();
    $locations = $house->Locations;

    // $house = GET_HOUSE_DETAIL($house);
    ###___DERTERMINONS LE NOMBRE DE FACTURE ASSOCIEE A CETTE MAISON
    foreach ($locations as $key =>  $location) {
        ###___quand il y a arrêt d'etat
        ###__on recupere les factures du dernier arrêt des etats de la maison
        $location_factures = [];
        if ($house_last_state) {
            $location_factures = Facture::where(["location" => $location->id, "state" => $house_last_state->id, "state_facture" => 0])->get();

            foreach ($location_factures as $facture) {
                array_push($house_factures_nbr_array, $facture);
                array_push($house_amount_nbr_array, $facture->amount);
            }
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
    $house["last_depenses"] = array_sum($last_state_depenses_array);
    $house["actuel_depenses"] = array_sum($current_state_depenses_array);
    $house["total_amount_paid"] = $total_amount_paid;
    $house["house_last_state"] = $house_last_state;
    $house["nbr_month_paid"] = $nbr_month_paid;
    $house["commission"] = ($house["total_amount_paid"] * $house->commission_percent) / 100;
    ####________

    $house["net_to_paid"] = $house["total_amount_paid"] - ($house["last_depenses"] + $house["commission"]);

    ####____RAJOUTONS LES INFOS DE TAUX DE PERFORMANCE DE LA MAISON
    $creation_date = date("Y/m/d", strtotime($house["created_at"]));
    $creation_time = strtotime($creation_date);
    $first_month_period = strtotime("+1 month", strtotime($creation_date));

    $frees_rooms = [];
    $busy_rooms = [];
    $frees_rooms_at_first_month = [];
    $busy_rooms_at_first_month = [];

    foreach ($house->Rooms as $room) {

        $is_this_room_buzy = false; #cette variable determine si cette chambre est occupée ou pas(elle est occupée lorqu'elle se retrouve dans une location de cette maison)
        ##__parcourons les locations pour voir si cette chambre s'y trouve

        foreach ($house->Locations as $location) {
            if ($location->Room->id == $room->id) {
                $is_this_room_buzy = true;

                ###___verifions la période d'entrée de cette chambre en location
                ###__pour determiner les chambres vide dans le premier mois
                $location_create_date = strtotime(date("Y/m/d", strtotime($location["created_at"])));
                ##on verifie si la date de creation de la location est comprise entre le *$creation_time* et le *$first_month_period* de la maison 
                if ($creation_time < $location_create_date && $location_create_date < $first_month_period) {
                    array_push($busy_rooms_at_first_month, $room);
                } else {
                    array_push($frees_rooms_at_first_month, $room);
                }
            }
        }


        ###__
        if ($is_this_room_buzy) { ##__quand la chambre est occupée
            array_push($busy_rooms, $room);
        } else {
            array_push($frees_rooms, $room); ##__quand la chambre est libre
        }
    }

    $house["busy_rooms"] = $busy_rooms;
    $house["frees_rooms"] = $frees_rooms;
    $house["busy_rooms_at_first_month"] = $busy_rooms_at_first_month;
    $house["frees_rooms_at_first_month"] = $frees_rooms_at_first_month;

    $house["_amount"] = $house['net_to_paid'] != 0 ? $house['net_to_paid'] : ($house->PayementInitiations->last() ? $house->PayementInitiations->last()->amount : 0);

    return $house;
}
