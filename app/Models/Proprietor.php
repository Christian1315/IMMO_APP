<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proprietor extends Model
{
    
    use HasFactory;
    protected $fillable = [
        'agency',
        'firstname',
        'lastname',
        'phone',
        'email',
        'sexe',
        'piece_number',
        'mandate_contrat',
        'comments',
        'adresse',
        'city',
        'country',
        'card_type',
        "owner",
        "piece_file"
    ];

    function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner");
    }

    function City(): BelongsTo
    {
        return $this->belongsTo(City::class, "city");
    }

    function Country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country");
    }

    function TypeCard(): BelongsTo
    {
        return $this->belongsTo(CardType::class, "card_type");
    }

    function Agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, "agency")->where(["visible" => 1]);
    }

    function Houses(): HasMany
    {
        return $this->hasMany(House::class, "proprietor")->where(["visible" => 1])->with(["Rooms", "Locations", "Type", "Supervisor", "Proprietor"]);
    }
}
