<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StopHouseElectricityStateController;
use App\Http\Controllers\StopHouseWaterStateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocataireController;
use App\Http\Controllers\LocationElectrictyFactureController;
use App\Http\Controllers\LocationWaterFactureController;
use App\Http\Controllers\PaiementInitiationController;
use App\Http\Controllers\ProprietorController;
use App\Http\Controllers\RightController;
use App\Http\Controllers\RoomController;
use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/mail", function () {
    $data = [
        "subject" => "Test de mail",
        "message" => "Salut Christ! ça va j'espère!",
    ];

    $receiver  = User::findOrFail(1);

    Notification::send($receiver, new SendNotification($data));

    return "Mail Sended....";
});

######============ HOME ROUTE ============#########################
Route::controller(HomeController::class)->group(function () {
    Route::get('/', "Home")->name("home");
});

######============ USERS ROUTE ============#########################
Route::controller(UserController::class)->group(function () {
    Route::match(["GET", "POST"], '/login', "Login")->name("user.login");
    Route::get('/logout', "Logout")->name("logout");
    Route::match(["GET", "POST"], '/demande-reinitialisation', "DemandReinitializePassword")->name("demandeReinitialisation");
    Route::match(["GET", "POST"], '/reinitialisation', "ReinitializePassword")->name("Reinitialisation");
    Route::any('add', 'AddUser')->name("AddUser");
    Route::any('users/{id}/roles', 'GetUserRoles')->name("user.GetUserRoles");
    Route::any('{id}/archive', 'ArchiveAccount')->name("user.ArchiveAccount");

    Route::any('password/demand_reinitialize', 'DemandReinitializePassword');
    Route::any('password/reinitialize', 'ReinitializePassword');

    Route::match(["POST", "GET"], 'attach-user/{user?}', 'AttachRoleToUser')->name("user.AttachRoleToUser"); #Attacher un droit au user 
    Route::post('desattach-user', 'DesAttachRoleToUser')->name("user.DesAttachRoleToUser"); #Attacher un droit au user 

    Route::get('{id}/duplicate', 'DuplicatAccount')->name("user.DuplicatAccount");
    Route::any('{id}/update', 'UpdateCompte')->name("user.UpdateCompte");
    Route::delete('{id}/delete', 'DeleteAccount')->name("user.DeleteAccount");
    Route::match(["GET", "POST"], 'attach-supervisor-to-agent_account/{supervisor}', 'AffectSupervisorToAccountyAgent')->name("user.AffectSupervisorToAccountyAgent"); #Affecter un superviseur à un agent comptable


    ##___
    Route::any('users', 'Users');
    Route::any('users/{id}', 'RetrieveUser');
    Route::any('{id}/password/update', 'UpdatePassword')->name("user.updatePassword");

    Route::any('attach-user', 'AttachRightToUser'); #Attacher un droit au user 


    ###========== RIGHTS ROUTINGS ========###
    Route::controller(RightController::class)->group(function () {
        Route::prefix('right')->group(function () {
            Route::post('add', 'CreateRight')->name("user.CreateRight"); #AJOUT D'UN DROIT'

            Route::any('attach-user/{right}', 'AttachRightToUser')->name("user.AttachRightToUser"); #Attacher un droit au user 
            Route::any('desattach-user/{right}', 'DesAttachRightToUser')->name("user.DesAttachRightToUser"); #Dettacher un droit au user 

            Route::any('{id}/retrieve', 'RetrieveRight')->name("user.RetrieveRight"); #RECUPERATION D'UN DROIT
            Route::any('{id}/delete', 'DeleteRight')->name("user.DeleteRight"); #SUPPRESSION D'UN DROIT
        });
    });
});

###========== PROPRIETAIRE ========###
Route::prefix("proprietor")->group(function () {
    Route::controller(ProprietorController::class)->group(function () {
        Route::post('add', '_AddProprietor')->name("proprietor._AddProprietor"); #AJOUT D'UN PROPRIETAIRE
        Route::any('{id}/update', 'UpdateProprietor')->name("proprietor.UpdateProprietor"); # MODIFICATION D'UN PROPRIETAIRE 

        Route::any('all', 'Proprietors'); #RECUPERATION DE TOUT LES PROPRIETAIRES
        Route::any('{id}/retrieve', 'RetrieveProprietor'); #RECUPERATION D'UN PROPRIETAIRE
        Route::any('{id}/delete', 'DeleteProprietor'); # SUPPRESSION D'UN PROPRIETAIRE  
    });
});
##___

###========== HOUSE ========###
Route::prefix("house")->group(function () {
    Route::controller(HouseController::class)->group(function () {

        Route::post('add-type', 'AddHouseType')->name("house.AddHouseType"); #AJOUT D'UN TYPE DE MAISON

        Route::post('add', '_AddHouse')->name("house._AddHouse"); #AJOUT D'UNE MAISON
        Route::patch('{id}/update', 'UpdateHouse')->name("house.UpdateHouse"); # MODIFICATION D'UNE MAISON  
        Route::delete('{id}/delete', 'DeleteHouse')->name("house.DeleteHouse"); # SUPPRESSION D'UNE MAISON  

        Route::get('/{id}/{agencyId}/stopHouseState', "StopHouseState")->name("stopHouseState");
        Route::post('/{houseId}/generate_cautions_for_house_by_period', "GenerateCautionByPeriod")->name("house.GenerateCautionByPeriod");

        ##=========__ ARRETER LES ETATS DES MAISON ======
        Route::post('stop/{houseId}', 'PostStopHouseState')->name("house.PostStopHouseState"); ####___ARRET DES ETATS D'UNE MAISON;
        Route::get('{houseId}/imprime_house_last_state', "ShowHouseStateImprimeHtml")->name("house.ShowHouseStateImprimeHtml"); # DERNIER ETAT D' une maison 

        // FILTRAGE
        Route::post('filtrebysupervisor/{houseId}', 'FiltreHouseBySupervisor')->name("house.FiltreHouseBySupervisor"); ####___FILTRE DES MAISONS PAR SUPERVISEUR;
        Route::post('filtrebyperiode/{houseId}', 'FiltreHouseByPeriod')->name("house.FiltreHouseByPeriode"); ####___FILTRE DES MAISONS PAR PERIODE;








        ####____
        Route::any('all', 'Houses')->name("house.Houses"); #RECUPERATION DES MAISONS
        Route::any('{agencyId}/all', 'AgenciesHouses')->name("house.AgenciesHouses"); #RECUPERATION DES MAISONS D'UNE AGENCE
        Route::any('{agencyId}/last_state/all', 'AgenciesHousesForTheLastState')->name("house.AgenciesHousesForTheLastState"); #RECUPERATION DES MAISONS D'UNE AGENCE en considerant son dernier arre^t d'etat
        Route::any('{id}/retrieve', 'RetrieveHouse')->name("house.RetrieveHouse"); #RECUPERATION D'UNE MAISON

        Route::any('{agencyId}/{supervisor}/{house}/{action}/performance', "HousePerformance"); # La performance dans une maison 
    });
});


###========== AGENCY ROUTINGS ========###
Route::controller(AgencyController::class)->group(function () {
    Route::prefix('agency')->group(function () {
        Route::any('add', 'AddAgency')->name("AddAgency"); #AJOUT D'UN AGENCY
        Route::any('all', 'Agencys'); #GET ALL AGENCYS
        Route::any('{id}/delete', 'DeleteAgency'); #SUPPRESSION D'UN AGENCY
        Route::any('{id}/update', 'UpdateAgency'); #MODIFICATION D'UN AGENCY
    });
});


// CAUTION HTML
Route::controller(LocationController::class)->group(function () {
    Route::get("{agencyId}/caution_html", "_ShowCautionsByAgency");
    Route::get("{first_date}/{last_date}/caution_html_by_period", "_ShowCautionsByPeriod");
    Route::any("{houseId}/caution_html_by_house", "_ShowCautionsByHouse");
    Route::get("{agencyId}/show_prestation_statistique", "_ShowPrestationStatistique");
    Route::get("/caution", "_ManageCautions");
    Route::get("{houseId}/{first_date}/{last_date}/caution_html_for_house_by_period", "_ShowCautionsForHouseByPeriod");
});

Route::controller(AdminController::class)->group(function () {
    ###___GENERALES AGENCIES ROUTES
    Route::get('/{agency}/manage-agency', "ManageAgency")->name("manage-agency");

    ###___GENERALES ROUTES
    Route::get('count', "AccountSold")->name("count");
    Route::get('setting', "Setting")->name("setting");
    Route::get('/{agency}/paiement', "Paiement")->name("paiement");
    Route::get('/{agency}/initiation', "AgencyInitiation")->name("agency-initiation");

    Route::match(["GET", "POST"], '/{agency}/factures', "LocationFactures")->name("locationFacture");
    Route::get('/{agency}/flitrefactures', "LocationFiltreFactures")->name("facture.LocationFiltreFactures");

    Route::get('/{agency}/caisses', "Caisses")->name("caisses");
    Route::get('/{agency}/{agency_account}/caisse-mouvements', "CaisseMouvements")->name("caisse-mouvements");
    Route::any('{agencyAccount}/agency_mouvements', '_RetrieveAgencyAccountMouvements'); ## RECUPERATION DES MOUVEMENTS D'UN COMPTE

    Route::get('/{agency}/encaisser', "Encaisser")->name("encaisser");
    Route::get('/{agency}/decaisser', "Decaisser")->name("decaisser");

    #######
    Route::get("/{agency}/proprietor", "Proprietor")->name("proprietor");
    Route::get('/{agency}/house', "House")->name("house");

    Route::get('/{agency}/room', "Room")->name("room");
    Route::get('/{agency}/locator', "Locator")->name("locator");
    Route::get('/{agency}/paid_locators', "PaidLocator")->name("paid-locator");
    Route::get('/{agency}/unpaid_locators', "UnPaidLocator")->name("unpaid-locator");
    Route::get('/{agency}/removed_locators', "RemovedLocators")->name("removed-locators");
    Route::get('/{agency}/location', "Location")->name("location");

    Route::get('/{agency}/electricity/locations', "Electricity")->name("electricity");
    Route::get('/{agency}/eau/locations', "Eau")->name("eau");

    Route::get('/{agency}/statistique', "AgencyStatistique")->name("agencyStatistique");

    Route::get('/{agency}/recovery_05_to_echeance_date', "AgencyRecovery05")->name("recovery_05_to_echeance_date");
    Route::get('/{agency}/recovery_10_to_echeance_date', "AgencyRecovery10")->name("recovery_10_to_echeance_date");
    Route::get('/{agency}/recovery_qualitatif', "AgencyRecoveryQualitatif")->name("recovery_10_to_echeance_date");
    Route::get('/{agency}/performance', "AgencyPerformance")->name("performance");

    Route::get('/{agency}/recovery_quelconque_date', "RecoveryAtAnyDate")->name("recovery_quelconque_date");
    Route::post('/{agency}/recovery_quelconque_date_filtrage', "FiltreByDateInAgency")->name("recovery_quelconque_date.FiltreByDateInAgency");

    Route::get('/{agency}/filtrage', "Filtrage")->name("filtrage");


    ###___GENERALES ROUTES
    Route::get('dashbord', "Admin")->name("dashbord");
    Route::get('agency', "Agencies")->name("agency");
    Route::get('/paiement', "PaiementAll")->name("paiementAll");
    Route::get('client', "Client")->name("client");
    Route::get('initiation', "Initiation")->name("initiation");
    Route::get('supervisor', "Supervisors")->name("supervisor");
    Route::get('right', "Rights")->name("right");
    Route::get('statistique', "Statistique")->name("statistique");
    Route::get('{house}/stopState', "StopState")->name("locationFacture");
});

###========== ROOM ========###
Route::prefix("room")->group(function () {
    Route::controller(RoomController::class)->group(function () {
        Route::prefix("type")->group(function () {
            Route::any('add', 'AddRoomType')->name("room.AddType"); ##__AJOUT D'UN TYPE DE CHAMBRE
        });

        Route::prefix("nature")->group(function () {
            Route::any('add', 'AddRoomNature')->name("room.AddRoomNature"); ##__AJOUT D'UNE NATURE
        });

        Route::post('add', '_AddRoom')->name("room._AddRoom"); #AJOUT D'UNE CHAMBRE
        Route::patch('{id}/update', 'UpdateRoom')->name("room.UpdateRoom"); #MODIFICATION D'UNE CHAMBRE 
        Route::delete('{id}/delete', 'DeleteRoom')->name("room.DeleteRoom"); #SUPPRESSION D'UNE CHAMBRE 


        // FILTRAGE
        Route::post('filtrebysupervisor/{agencyId}', 'FiltreRoomBySupervisor')->name("room.FiltreRoomBySupervisor"); ####___FILTRE DES CHAMBRES PAR SUPERVISEUR;
        Route::post('filtrebyhouse/{agencyId}', 'FiltreRoomByHouse')->name("room.FiltreRoomByHouse"); ####___FILTRE DES CHAMBRES PAR MAISON;
    });
});
##___

###========== LOCATAIRE ========###
Route::prefix("locataire")->group(function () {
    Route::controller(LocataireController::class)->group(function () {
        Route::any('add', '_AddLocataire')->name("locator._AddLocataire"); #AJOUT D'UN LOCATAIRE
        Route::any('{id}/update', 'UpdateLocataire')->name("locator.UpdateLocataire"); #RECUPERATION D'UN LOCATAIRE 
        Route::delete('{id}/delete', 'DeleteLocataire')->name("locator.DeleteLocataire"); #SUPPRESSION D'UN LOCATAIRE
        Route::post('{agency}/filtre_by_supervisor', 'FiltreBySupervisor')->name("locator.FiltreBySupervisor"); #FILTRER PAR SUPERVISEUR
        Route::post('{agency}/filtre_by_house', 'FiltreByHouse')->name("locator.FiltreByHouse"); #FILTRER PAR MAISON

        ####___A JOUR 
        Route::post('{agency}/paid/filtre_by_supervisor', 'PaidFiltreBySupervisor')->name("locator.PaidFiltreBySupervisor"); #LOCATAIRE A JOUR FILTRER PAR SUPERVISEUR
        Route::post('{agency}/paid/filtre_by_house', 'PaidFiltreByHouse')->name("locator.PaidFiltreByHouse"); #LOCATAIRE A JOUR FILTRER PAR MAISON

        ####___NON A JOUR (impayés)
        Route::post('{agency}/unpaid/filtre_by_supervisor', 'UnPaidFiltreBySupervisor')->name("locator.UnPaidFiltreBySupervisor"); #LOCATAIRE NON A JOUR FILTRER PAR SUPERVISEUR
        Route::post('{agency}/unpaid/filtre_by_house', 'UnPaidFiltreByHouse')->name("locator.UnPaidFiltreByHouse"); #LOCATAIRE NON A JOUR FILTRER PAR MAISON

        ####___DEMENAGES
        Route::post('{agency}/removed/filtre_by_supervisor', 'RemovedFiltreBySupervisor')->name("locator.RemovedFiltreBySupervisor"); #LOCATAIRE DEMENAGES FILTRER PAR SUPERVISEUR
        Route::post('{agency}/removed/filtre_by_house', 'RemovedFiltreByHouse')->name("locator.RemovedFiltreByHouse"); #LOCATAIRE DEMENAGES FILTRER PAR MAISON





        Route::any('{agencyId}/recovery_05_to_echeance_date', 'Recovery05ToEcheanceDate'); ###_______
        Route::any('{agencyId}/recovery_10_to_echeance_date', 'Recovery10ToEcheanceDate'); ###_______
        Route::any('{agencyId}/recovery_qualitatif', 'RecoveryQualitatif'); ###_______

        // imprimer les taux de recouvrement 05
        Route::any("{agencyId}/imprime_taux_05_agency", "_ImprimeAgencyTaux05");
        Route::any("{agencyId}/{supervisor}/imprime_taux_05_supervisor", "_ImprimeAgencyTaux05_Supervisor");
        Route::any("{agencyId}/{house}/imprime_taux_05_house", "_ImprimeAgencyTaux05_House");

        // imprimer les taux de recouvrement 10
        Route::any("{agencyId}/imprime_taux_10_agency", "_ImprimeAgencyTaux10");
        Route::any("{agencyId}/{supervisor}/imprime_taux_10_supervisor", "_ImprimeAgencyTaux10_Supervisor");
        Route::any("{agencyId}/{house}/imprime_taux_10_house", "_ImprimeAgencyTaux10_House");

        // imprimer les taux de recouvrement qualitatif
        Route::any("{agencyId}/imprime_taux_qualitatif_agency", "_ImprimeAgencyTauxQualitatif");
        Route::any("{agencyId}/{supervisor}/imprime_taux_qualitatif_supervisor", "_ImprimeAgencyTauxQualitatif_Supervisor");
        Route::any("{agencyId}/{house}/imprime_taux_qualitatif_house", "_ImprimeAgencyTauxQualitatif_House");
    });
});
##___


###========== LOCATION ========###
Route::prefix("location")->group(function () {
    Route::controller(LocationController::class)->group(function () {
        Route::prefix("type")->group(function () {
            Route::post('add', '_AaddType')->name("location.AddType"); ##__AJOUT DE TYPE DE LOCATION
        });
        Route::get("{agencyId}/generate_cautions", "_ManageCautions")->name("location._ManageCautions"); #GENERATE CAUTION POUR TOUTE L4AGENCE
        Route::get("location/{locationId}/generate_cautions", "_ManageLocationCautions")->name("location._ManageLocationCautions"); #GENERATE CAUTION UNE LOCATION
        Route::get("location/{locationId}/generate_prorata", "_ManageLocationProrata")->name("location._ManageLocationProrata"); #GENERATE ETAT DES PRORATAS
        Route::post("{agencyId}/prestation_statistique_for_agency_by_period", "_ManagePrestationStatistiqueForAgencyByPeriod")->name("location._ManagePrestationStatistiqueForAgencyByPeriod"); #GENERATE PRESATION BY PERIODE
        Route::post('add', '_AddLocation')->name("location._AddLocation"); #AJOUT D'UNE LOCATION
        Route::patch('{id}/update', 'UpdateLocation')->name("location.UpdateLocation"); #MODIFICATION D'UNE LOCATION
        Route::get('/location/{location}/imprimer', "Imprimer")->name("location.imprimer");
        Route::post('{id}/demenage', 'DemenageLocation')->name("location.DemenageLocation"); #DEMENAGEMENT D'UNE LOCATION 

        Route::any('add-paiement', '_AddPaiement')->name("location._AddPaiement"); #AJOUT D'UN PAIEMENT
        Route::post('{id}/updateStatus', 'UpdateFactureStatus')->name("location.UpdateFactureStatus"); #TRAITEMENT DE LA FACTURE CHANGEMENT DE STATUS

        // FILTRE LOCATION
        Route::post('{agency}/location_filtre_by_supervisor', 'FiltreBySupervisor')->name("location.FiltreBySupervisor"); #FILTRER PAR SUPERVISEUR
        Route::post('{agency}/location_filtre_by_house', 'FiltreByHouse')->name("location.FiltreByHouse"); #FILTRER PAR MAISON
        Route::post('{agency}/location_filtre_by_proprio', 'FiltreByProprio')->name("location.FiltreByProprio"); #FILTRER PAR PROPRIETAIRE

        // IMPRESSION
        Route::post('{agency}/print_all_location_by_supervisor', 'PrintAllLocationBySupervisor')->name("location.PrintAllLocationBySupervisor"); #IMPRRIMER TOUTES LES LOCATIONS PAR SUPERVISEUR
        Route::post('{agency}/print_unpaid_location_by_supervisor', 'PrintUnPaidLocationBySupervisor')->name("location.PrintUnPaidLocationBySupervisor"); #IMPRRIMER TOUTES LES LOCATIONS IMPAYES PAR SUPERVISEUR


        ###___GESTION DES FACTURES D'ELECTRICITE DANS UNE LOCATION
        ##=========__ ARRETER LES ETATS D'ELECTRICITE DES MAISON ======
        Route::any('stop', '_StopStatsOfHouse')->name("location._StopStatsOfHouse");

        ###___electricite
        Route::any('agency/{agencyId}/electricity', 'ElectricityLocations'); ##__RECUPERATION DES LOCATIONS AYANT D'ELECTRICITE
        ###___eau
        Route::any('agency/{agencyId}/water', 'WaterLocations'); ##__RECUPERATION DES LOCATIONS AYANT D'EAU


        ####__STATISTIQUE DES AGENCES (payement avant & apres arret des etats)
        ###___FILTRE
        Route::any('filtre_by_date', '_FiltreByDate'); #FILTRER PAR DATE
        Route::any('{houseId}/filtre_before_stateDate_stoped', 'FiltreBeforeStateDateStoped')->name("location.FiltreBeforeStateDateStoped"); #FILTRER LES PAIEMENTS AVANT ARRET DES ETATS
        Route::any('{houseId}/filtre_after_stateDate_stoped', 'FiltreAfterStateDateStoped')->name("location.FiltreAfterStateDateStoped"); #FILTRER LES PAIEMENTS APRES ARRET DES ETATS

        Route::post('{agency}/filtre_by_supervisor', 'FiltreBySupervisor')->name("locator.FiltreBySupervisor"); #FILTRER PAR SUPERVISEUR

        Route::any('{agency}/filtre_after_echeanceDate', 'FiltreAfterEcheanceDate');
        Route::any('{agency}/filtre_at_any_date', 'FiltreAtAnyDate');



        Route::any("generate_cautions_by_period", "_ManageCautionsByPeriod");
        Route::any("{houseId}/generate_cautions_by_house", "_ManageCautionsByHouse");
        Route::any("{houseId}/generate_cautions_for_house_by_period", "_ManageCautionsForHouseByPeriod");
        Route::any("{houseId}/generate_cautions_for_house_by_period", "_ManageCautionsForHouseByPeriod");
        Route::get("{agencyId}/prestation_statistique", "_ManagePrestationStatistique");
        Route::any("{agencyId}/{houseId}/{action}/imprime_states", "_ImprimeStates");
        Route::any("{houseId}/{action}/imprime_states_all_system", "_ImprimeStatesForAllSystem");
    });
});
##___

##========__PAIEMENT INITIATION MANAGEMENT======
Route::prefix("payement_initiation")->group(function () {
    Route::controller(PaiementInitiationController::class)->group(function () {
        Route::post('initiateToProprio', 'InitiatePaiementToProprietor')->name("payement_initiation.InitiatePaiementToProprietor"); ## INITIER UN PAIEMENT A UN PROPRIETAIRE
        Route::any('{id}/valide', 'ValidePaiementInitiation')->name("payement_initiation.ValidePaiementInitiation"); ## VALIDATION D'UNE INITIATION DE PAIUEMENT
        Route::post('{id}/rejet', 'RejetPayementInitiation')->name("payement_initiation.RejetPayementInitiation"); ## REJET D'UNE INITIATION DE PAIUEMENT
    });
});

##__ACCOUNT_SOLD MANAGEMENT
Route::prefix("sold")->group(function () {
    Route::controller(AgencyController::class)->group(function () {
        Route::post('creditate', '_CreditateAccount')->name("sold._CreditateAccount"); ## CREDITATION DU SOLDE DU COMPTE D'UNE AGENCE
        Route::post('decreditate', '_DeCreditateAccount')->name("sold._DeCreditateAccount"); ## DECREDITATION DU SOLDE DU COMPTE D'UNE AGENCE
    });
});

###___GESTION DES FACTURES D'ELECTRICITE DANS UNE LOCATION
Route::prefix("electricity_facture")->group(function () {
    Route::controller(LocationElectrictyFactureController::class)->group(function () {
        Route::post("generate", "_GenerateFacture")->name("electricity_facture._GenerateFacture"); ##__generer la facture
        Route::any("{id}/payement", "_FacturePayement")->name("electricity_facture._FacturePayement"); ##__Payement d'une facture d'electricité

        ##=========__ ARRETER LES ETATS D'ELECTRICITE DES MAISON ======
        Route::prefix("house_state")->group(function () {
            Route::post('stop', '_StopStatsOfHouse')->name("house_state._StopStatsOfHouse"); ###___ARRETER LES ETATS EN ELECTRICITE D'UNE MAISON
        });

        ###____impression des etats de factures eau-electricité
        Route::get("{state}/show_electricity_state_html", [StopHouseElectricityStateController::class, "ShowStateImprimeHtml"])->name("house_state.ImprimeElectricityHouseState");
    });
});

###___GESTION DES FACTURES D'ELECTRICITE DANS UNE LOCATION
Route::prefix("water_facture")->group(function () {
    Route::controller(LocationWaterFactureController::class)->group(function () {
        Route::post("generate", "_GenerateWateracture")->name("water_facture._GenerateFacture"); ##__generer la facture
        Route::any("{id}/payement", "_FactureWaterPayement")->name("water_facture._FactureWaterPayement"); ##__Payement d'une facture d'electricité

        ##=========__ ARRETER LES ETATS D'ELECTRICITE DES MAISON ======
        Route::prefix("house_state")->group(function () {
            Route::post('stop', '_StopWaterStatsOfHouse')->name("house_state._StopWaterStatsOfHouse"); ###___ARRETER LES ETATS EN ELECTRICITE D'UNE MAISON
        });

        ###____impression des etats de factures eau-electricité
        Route::get("{state}/show_water_state_html", [StopHouseWaterStateController::class, "ShowWaterStateImprimeHtml"])->name("house_state.ShowWaterStateImprimeHtml");
    });
});


####____impression des taux  locataires
##__05
Route::get("{agencyId}/show_taux_05_agency_simple", [LocataireController::class, "_ShowAgencyTaux05_Simple"])->name("taux._ShowAgencyTaux05_Simple");
Route::get("{agencyId}/{supervisor}/show_taux_05_agency_by_supervisor", [LocataireController::class, "_ShowAgencyTaux05_By_Supervisor"])->name("taux._ShowAgencyTaux05_By_Supervisor");
Route::get("{agencyId}/{house}/show_taux_05_agency_by_house", [LocataireController::class, "_ShowAgencyTaux05_By_House"])->name("taux._ShowAgencyTaux05_By_House");

##__10
Route::get("{agencyId}/show_taux_10_agency_simple", [LocataireController::class, "_ShowAgencyTaux10_Simple"])->name("taux._ShowAgencyTaux10_Simple");
Route::get("{agencyId}/{supervisor}/show_taux_10_agency_by_supervisor", [LocataireController::class, "_ShowAgencyTaux10_By_Supervisor"])->name("taux._ShowAgencyTaux10_By_Supervisor");
Route::get("{agencyId}/{house}/show_taux_10_agency_by_house", [LocataireController::class, "_ShowAgencyTaux10_By_House"])->name("taux._ShowAgencyTaux10_By_House");

##__qualitatif
Route::get("{agencyId}/show_taux_qualitatif_simple", [LocataireController::class, "_ShowAgencyTauxQualitatif_Simple"])->name("taux._ShowAgencyTauxQualitatif_Simple");
Route::get("{agencyId}/{supervisor}/show_taux_qualitatif_by_supervisor", [LocataireController::class, "_ShowAgencyTauxQualitatif_By_Supervisor"])->name("taux._ShowAgencyTauxQualitatif_By_Supervisor");
Route::get("{agencyId}/{house}/show_taux_qualitatif_by_house", [LocataireController::class, "_ShowAgencyTauxQualitatif_By_House"])->name("taux._ShowAgencyTauxQualitatif_By_House");







// Route::get("{houseId}/caution_html_by_house", [LocationController::class, "_ShowCautionsByHouse"]);
// Route::get("{agencyId}/show_prestation_statistique", [LocationController::class, "_ShowPrestationStatistique"]);
// Route::get("{agencyId}/{first_date}/{last_date}/show_prestation_statistique_for_agency_by_period", [LocationController::class, "_ShowPrestationStatistique"]);

Route::get("{agencyId}/{houseId}/{action}/locators_state_stoped", [LocationController::class, "_ShowLocatorStateStoped"]);


###___impression du dernier etat d'une maison
// Route::get("{house}/show_house_state_html", [HouseController::class, "ShowHouseStateImprimeHtml"]);
