<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $table = "locationsnew";
    // protected $guarded = [];
    protected $fillable = [
        "agency",
        'house',
        'room',
        'locataire',
        'type',
        "status",
        "payment_mode",

        "moved_by",
        "move_date",
        "move_comments",

        "suspend_by",
        "suspend_date",
        "suspend_comments",

        "next_loyer_date",

        'caution_bordereau',
        'loyer',

        'pre_paid',
        'post_paid',

        "water_counter",
        "electric_counter",
        "frais_peiture",

        'prestation',
        'numero_contrat',

        'comments',
        'img_contrat',
        'caution_water',
        'echeance_date',
        'latest_loyer_date',
        'effet_date',
        'img_prestation',
        'caution_electric',
        'integration_date',
        'owner',
        'visible',
        "delete_at",
        "caution_number",
        "total_amount",

        "discounter",
        "kilowater_price",

        "water_unpaid",
        "electric_unpaid",

        "previous_echeance_date",
        "prorata_days",
        "prorata_amount"
    ];

    // si cette location est en impayé ou pas
    public function paid()
    {
        $now = strtotime(date("Y/m/d", strtotime(now())));
        $location_echeance_date = strtotime(date("Y/m/d", strtotime($this->echeance_date)));
        if ($location_echeance_date < $now) {
            return 1;
        }else{
            return 0;
        };
    }

    function _Agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, "agency")->where(["visible" => 1]);
    }

    function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner");
    }

    function House(): BelongsTo
    {
        return $this->belongsTo(House::class, "house")->with(["Owner", "Proprietor", "Type", "Supervisor", "City", "Country", "Departement", "Quartier", "Zone"])->where(["visible" => 1]);
    }

    function Locataire(): BelongsTo
    {
        return $this->belongsTo(Locataire::class, "locataire")->with(["Owner", "CardType", "Departement", "Country"])->where(["visible" => 1]);
    }

    function Type(): BelongsTo
    {
        return $this->belongsTo(LocationType::class, "type");
    }

    function Status(): BelongsTo
    {
        return $this->belongsTo(LocationStatus::class, "status");
    }

    function Room(): BelongsTo
    {
        return $this->belongsTo(Room::class, "room")->with(["Owner", "House", "Nature", "Type"])->where(["visible" => 1]);
    }

    function Factures(): HasMany
    {
        return $this->hasMany(Facture::class, "location")->whereNull("state")->with(["Owner", "Location", "Type", "Status", "State"])->orderBy("id", "desc");
    }

    function AllFactures(): HasMany
    {
        return $this->hasMany(Facture::class, "location")->with(["Owner", "Location", "Type", "Status", "State"]);
    }

    function Paiements(): HasMany
    {
        return $this->hasMany(Payement::class, "location")->with(["Module", "Type", "Status", "Facture"]);
    }

    function WaterFactures(): HasMany
    {
        return $this->hasMany(LocationWaterFacture::class, "location")->with(["Location"])->whereNull(["state"])->where(["state_facture" => 0])->orderBy("id", "desc");
    }

    function ElectricityFactures(): HasMany
    {
        return $this->hasMany(LocationElectrictyFacture::class, "location")->with(["Location"])->whereNull(["state"])->where(["state_facture" => 0])->orderBy("id", "desc");
    }

    function Agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, "agency")->where(["visible" => 1])->orderBy("id", "desc");
    }
}
