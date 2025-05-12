<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class Recovery05 extends Component
{
    private const TARGET_DAY = '05';
    private const DATE_FORMAT = 'Y/m/d';

    public $agency;
    public $locators = [];
    public $houses = [];

    public function mount($agency): void
    {
        set_time_limit(0);
        $this->agency = $agency;
        $this->refreshThisAgencyHouses();
        $this->refreshLocators();
    }

    public function refreshLocators(): void
    {
        $this->locators = $this->getLocatorsThatPaidAfterStateStopped();
    }

    public function refreshThisAgencyHouses(): void
    {
        $this->houses = $this->agency->_Houses;
    }

    private function getLocatorsThatPaidAfterStateStopped(): array
    {
        $locators = [];

        foreach ($this->houses as $house) {
            $lastState = $house->States->last();
            
            if (!$lastState) {
                continue;
            }

            $lastStateDate = Carbon::parse($lastState->stats_stoped_day)->format(self::DATE_FORMAT);
            
            foreach ($lastState->Factures as $facture) {
                $location = $facture->Location;
                $echeanceDate = Carbon::parse($facture->echeance_date)->format(self::DATE_FORMAT);
                $previousEcheanceDate = Carbon::parse($location->previous_echeance_date)->format(self::DATE_FORMAT);
                
                if ($this->isValidPaymentDate($lastStateDate, $echeanceDate, $previousEcheanceDate)) {
                    $location->Locataire["locator_location"] = $location;
                    $locators[] = $location->Locataire;
                }
            }
        }

        return $locators;
    }

    private function isValidPaymentDate(string $stateDate, string $echeanceDate, string $previousEcheanceDate): bool
    {
        $dueDay = Carbon::parse($previousEcheanceDate)->format('d');
        
        return $stateDate > $echeanceDate 
            && $echeanceDate <= $previousEcheanceDate 
            && $dueDay === self::TARGET_DAY;
    }

    public function render()
    {
        return view('livewire.recovery05');
    }
}
