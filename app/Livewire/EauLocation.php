<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class EauLocation extends Component
{
    use WithFileUploads;

    public $current_agency;
    public array $locations = [];
    public array $houses = [];
    public array $current_house = [];
    public int $state = 0;

    private function calculateLocationMetrics($location, $latestFacture): array
    {
        $isLatestFactureStateFacture = $latestFacture->state_facture ?? false;
        
        $paidFactures = $location->WaterFactures->where('paid', true);
        $unpaidFactures = $location->WaterFactures->where('paid', false)
            ->where('id', '!=', $latestFacture->id)
            ->where('state_facture', false);

        return [
            'end_index' => $latestFacture->end_index,
            'current_amount' => $latestFacture->paid ? 0 : $latestFacture->amount,
            'nbr_un_paid_facture_amount' => $isLatestFactureStateFacture ? 0 : $unpaidFactures->count(),
            'un_paid_facture_amount' => $isLatestFactureStateFacture ? 0 : $unpaidFactures->sum('amount'),
            'paid_facture_amount' => $isLatestFactureStateFacture ? 0 : $paidFactures->sum('amount'),
            'total_un_paid_facture_amount' => $isLatestFactureStateFacture ? 0 : $location->WaterFactures->sum('amount'),
            'rest_facture_amount' => $isLatestFactureStateFacture ? 0 : 
                ($location->WaterFactures->sum('amount') - $paidFactures->sum('amount'))
        ];
    }

    private function getEmptyLocationMetrics(): array
    {
        return [
            'end_index' => 0,
            'current_amount' => 0,
            'nbr_un_paid_facture_amount' => 0,
            'un_paid_facture_amount' => 0,
            'water_factures' => [],
            'paid_facture_amount' => 0,
            'total_un_paid_facture_amount' => 0,
            'rest_facture_amount' => 0
        ];
    }

    private function enrichLocationData($location): array
    {
        $locationData = $location->toArray();
        
        $locationData['house_name'] = $location->House->name;
        $locationData['start_index'] = $location->ElectricityFactures->first()?->end_index 
            ?? $location->Room?->electricity_counter_start_index;
        $locationData['locataire'] = $location->Locataire->name . ' ' . $location->Locataire->prenom;
        $locationData['water_factures'] = $location->WaterFactures;
        $locationData['water_factures_states'] = $location->House->WaterFacturesStates;
        $locationData['lastFacture'] = $location->WaterFactures()->first();

        return $locationData;
    }

    public function refreshThisAgencyLocations(): void
    {
        $locations = $this->current_agency->_Locations
            ->where('status', '!=', 3)
            ->filter(fn($location) => $location->Room?->water);

        $this->locations = $locations->map(function ($location) {
            $locationData = $location->WaterFactures->isNotEmpty() 
                ? $this->calculateLocationMetrics($location, $location->WaterFactures->first())
                : $this->getEmptyLocationMetrics();

            return array_merge($this->enrichLocationData($location), $locationData);
        })->values()->all();
    }

    public function refreshThisAgencyHouses(): void
    {
        $this->houses = $this->current_agency->_Locations
            ->map(fn($location) => $location->Room?->water ? $location->House : null)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function mount($agency): void
    {
        set_time_limit(0);
        $this->current_agency = $agency;
        $this->refreshThisAgencyLocations();
        $this->refreshThisAgencyHouses();
    }

    public function render()
    {
        return view('livewire.eau-location');
    }
}
