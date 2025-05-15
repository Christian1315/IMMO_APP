<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgencyAccount;
use App\Models\AgencyAccountSold;
use App\Models\House;
use App\Models\Location;
use App\Models\LocationWaterFacture;
use App\Models\Room;
use App\Models\StopHouseWaterState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;

class LocationWaterFactureController extends Controller
{
    private const WATER_STATE_DAYS = 5;
    private const WATER_STATE_SECONDS = 5 * 24 * 3600;

    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Generate a water bill for a location
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function generateWaterBill(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $location = Location::where(["visible" => 1])->find($request->location);

            if (!$location) {
                throw new Exception("Cette location n'existe pas!");
            }

            $startIndex = $this->getStartIndex($location);
            $endIndex = $request->end_index;
            $consumption = $endIndex - $startIndex;

            if ($consumption < 0) {
                throw new Exception("Désolé! L'index de fin est inférieur à celui de début");
            }

            $amount = $this->calculateAmount($consumption, $location->Room->unit_price);
            $comments = $this->generateComments($location, $user);

            LocationWaterFacture::create([
                'location' => $location->id,
                'start_index' => $startIndex,
                'end_index' => $endIndex,
                'consomation' => $consumption,
                'amount' => $amount,
                'comments' => $comments,
                'owner' => $user->id
            ]);

            DB::commit();
            return $this->handleSuccess("Facture d'eau générée avec succès!");

        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleError($e->getMessage());
        }
    }

    /**
     * Process water bill payment
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function processWaterBillPayment(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $facture = LocationWaterFacture::where("visible", 1)->findOrFail($id);
            $location = $facture->Location;
            $agency = $location->_Agency;

            $agencyAccount = AgencyAccount::where([
                "agency" => $agency->id,
                "id" => env("ELECTRICITY_WATER_ACCOUNT_ID")
            ])->first();

            if (!$agencyAccount) {
                throw new Exception("Ce compte d'agence n'existe pas! Vous ne pouvez pas le créditer!");
            }

            if (!$this->canProcessPayment($facture, $agencyAccount)) {
                throw new Exception("Impossible de traiter le paiement. Vérifiez les plafonds du compte.");
            }

            $this->updateAgencyAccountSold($facture, $agencyAccount, $location);
            $facture->update(['paid' => true]);

            DB::commit();
            return $this->handleSuccess("La facture d'eau de montant ({$facture->amount}) a été payée avec succès!");

        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleError($e->getMessage());
        }
    }

    /**
     * Stop water stats for a house
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function stopWaterStatsOfHouse(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $this->validateHouseRequest($request);

            $house = House::where(["visible" => 1])->findOrFail($request->house);

            if (count($house->Locations) === 0) {
                throw new Exception("Cette maison n'appartient à aucune location! Son arrêt d'état ne peut donc être effectué");
            }

            $state = $this->createOrUpdateHouseState($house, $user);
            $this->updateHouseWaterStats($house, $state, $user);

            DB::commit();
            return $this->handleSuccess("L'état en eau de cette maison a été arrêté avec succès!");

        } catch (Exception $e) {
            DB::rollBack();
            return $this->handleError($e->getMessage());
        }
    }

    private function getStartIndex(Location $location): int
    {
        try {
            $factures = $location->WaterFactures;
            return count($factures) > 0 
                ? $factures[0]->end_index 
                : $location->Room->water_counter_start_index;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération de l'index de départ: " . $e->getMessage());
        }
    }

    private function calculateAmount(int $consumption, int $unitPrice): int
    {
        try {
            return $consumption * $unitPrice;
        } catch (Exception $e) {
            throw new Exception("Erreur lors du calcul du montant: " . $e->getMessage());
        }
    }

    private function generateComments(Location $location, $user): string
    {
        try {
            return "Génération de facture d'eau pour le locataire << {$location->Locataire->name} {$location->Locataire->prenom}>> " .
                   "de la maison << {$location->House->name} >> à la date " . now() . " par << {$user->name}>>";
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la génération des commentaires: " . $e->getMessage());
        }
    }

    private function canProcessPayment(LocationWaterFacture $facture, AgencyAccount $agencyAccount): bool
    {
        try {
            $account = $agencyAccount->_Account;
            $agencyAccountSold = AgencyAccountSold::where([
                "agency_account" => $agencyAccount->id, 
                "visible" => 1
            ])->first();

            if ($agencyAccountSold && $agencyAccountSold->sold >= $account->plafond_max) {
                throw new Exception("Le sold de ce compte ({$account->name}) a déjà atteint son plafond!");
            }

            $newSold = ($agencyAccountSold ? $agencyAccountSold->sold : 0) + $facture->amount;
            if ($newSold > $account->plafond_max) {
                throw new Exception("L'ajout de ce montant au sold de ce compte ({$account->name}) dépasserait son plafond!");
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la vérification du paiement: " . $e->getMessage());
        }
    }

    private function updateAgencyAccountSold(LocationWaterFacture $facture, AgencyAccount $agencyAccount, Location $location): void
    {
        try {
            $agencyAccountSold = AgencyAccountSold::where([
                "agency_account" => $agencyAccount->id, 
                "visible" => 1
            ])->first();

            $oldSold = $agencyAccountSold ? $agencyAccountSold->sold : 0;
            
            if ($agencyAccountSold) {
                $agencyAccountSold->update([
                    'visible' => 0,
                    'delete_at' => now()
                ]);
            }

            AgencyAccountSold::create([
                'agency_account' => $agencyAccount->id,
                'old_sold' => $oldSold,
                'sold' => $oldSold + $facture->amount,
                'sold_added' => $facture->amount,
                'description' => "Paiement de la facture d'eau de montant ({$facture->amount}) pour la maison {$location->House->name}!!"
            ]);
        } catch (QueryException $e) {
            throw new Exception("Erreur lors de la mise à jour du solde du compte: " . $e->getMessage());
        }
    }

    private function validateHouseRequest(Request $request): void
    {
        try {
            Validator::make($request->all(), [
                'house' => ['required', "integer"],
            ], [
                'house.required' => 'La maison est requise!',
                'house.integer' => "Ce champ doit être de type entier!",
            ])->validate();
        } catch (Exception $e) {
            throw new Exception("Erreur de validation: " . $e->getMessage());
        }
    }

    private function createOrUpdateHouseState(House $house, $user): StopHouseWaterState
    {
        try {
            $this_house_state = StopHouseWaterState::orderBy("id", "desc")
                ->where(["house" => $house->id])
                ->first();

            if (!$this_house_state) {
                return StopHouseWaterState::create([
                    'house' => $house->id,
                    'owner' => $user->id,
                    'state_stoped_day' => now()
                ]);
            }

            return StopHouseWaterState::create([
                'owner' => $user->id,
                'house' => $house->id,
                'state_stoped_day' => now()
            ]);
        } catch (QueryException $e) {
            throw new Exception("Erreur lors de la création/mise à jour de l'état de la maison: " . $e->getMessage());
        }
    }

    private function updateHouseWaterStats(House $house, StopHouseWaterState $state, $user): void
    {
        try {
            foreach ($house->Locations as $location) {
                $this->updateLocationWaterStats($location, $state, $user);
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour des statistiques d'eau: " . $e->getMessage());
        }
    }

    private function updateLocationWaterStats(Location $location, StopHouseWaterState $state, $user): void
    {
        try {
            $location_factures = $location->WaterFactures;
            $location_room = $location->Room;

            foreach ($location_factures as $facture) {
                if (!$facture->state) {
                    $facture->update(['state' => $state->id]);
                }
            }

            if (count($location_factures) > 0) {
                $last_facture = $location_factures[0];
                $location_room->update([
                    'water_counter_start_index' => $last_facture->end_index
                ]);
            }

            LocationWaterFacture::create([
                'owner' => $user->id,
                'location' => $location->id,
                'end_index' => $location_room->water_counter_start_index,
                'amount' => 0,
                'state_facture' => 1,
                'state' => $state->id,
            ]);
        } catch (QueryException $e) {
            throw new Exception("Erreur lors de la mise à jour des statistiques de location: " . $e->getMessage());
        }
    }

    private function handleError(string $message): RedirectResponse
    {
        alert()->error("Echec", $message);
        return back()->withInput();
    }

    private function handleSuccess(string $message): RedirectResponse
    {
        alert()->success("Succès", $message);
        return back()->withInput();
    }
}
