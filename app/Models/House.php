<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        "agency",
        "name",
        "latitude",
        "longitude",
        "comments",
        "proprietor",
        "type",
        "supervisor",
        "city",
        "country",
        "departement",
        "quartier",
        "zone",
        "owner",
        "proprio_payement_echeance_date",
        "geolocalisation",
        "commission_percent",
        "image",
        "locative_commission",
        "pre_paid",
        "post_paid",
        "recovery_date",
    ];

    function LocativeCharge()
    {
        $locatives =  $this->Locations->where("status", "!=", 3)->sum(function ($location) {
            return $location->Room->LocativeCharge();
        });

        return $locatives;
    }

    function _Agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, "agency");
    }

    function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner");
    }

    function Proprietor(): BelongsTo
    {
        return $this->belongsTo(Proprietor::class, "proprietor")->with(["Agency"]);
    }

    function Type(): BelongsTo
    {
        return $this->belongsTo(HouseType::class, "type");
    }

    function Supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, "supervisor");
    }

    function City(): BelongsTo
    {
        return $this->belongsTo(City::class, "city");
    }

    function Country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country");
    }

    function Departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class, "departement");
    }

    function Quartier(): BelongsTo
    {
        return $this->belongsTo(Quarter::class, "quartier");
    }

    function Zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, "proprietor");
    }

    function Rooms(): HasMany
    {
        return $this->hasMany(Room::class, "house")->where(["visible" => 1])->with(["Owner", "Nature", "Type", "House"]);
    }

    function Locations(): HasMany
    {
        return $this->hasMany(Location::class, "house")->with(["Locataire", "Type", "Status", "Room", "Factures", "AllFactures", "WaterFactures", "ElectricityFactures"]);
    }

    function States(): HasMany
    {
        return $this->hasMany(HomeStopState::class, "house")->with(["Owner", "CdrAccountSolds", "Factures"]);
    }

    function CurrentDepenses(): HasMany
    {
        return $this->hasMany(AgencyAccountSold::class, "house")->whereNull("state");
    }

    function AllStatesDepenses(): HasMany
    {
        return $this->hasMany(AgencyAccountSold::class, "house");
    }

    function PayementInitiations(): HasMany
    {
        return $this->hasMany(PaiementInitiation::class, "house");
    }

    function ElectricityFacturesStates(): HasMany
    {
        return $this->hasMany(StopHouseElectricityState::class, "house")->orderBy("id", "desc");
    }

    function WaterFacturesStates(): HasMany
    {
        return $this->hasMany(StopHouseWaterState::class, "house");
    }
}
