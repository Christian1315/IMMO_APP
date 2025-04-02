<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    function LocativeCharge() {
        return ($this->gardiennage + $this->rubbish + $this->vidange + $this->cleaning);
    }

    function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner");
    }

    function House(): BelongsTo
    {
        return $this->belongsTo(House::class, "house")->where(["visible"=>1])->with(["Proprietor"]);
    }

    function Nature(): BelongsTo
    {
        return $this->belongsTo(RoomNature::class, "nature");
    }

    function Type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, "type");
    }

    function Locations(): HasMany
    {
        return $this->hasMany(Location::class, "room")->with(["Locataire", "House", "Room", "Type"]);
    }

    function buzzy() {
        return count($this->Locations)>0?true:false;
    }
}
