<?php

namespace App\Http\Controllers;

use App\Models\StopHouseWaterState;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StopHouseWaterStateController extends Controller
{
    #VERIFIONS SI LE USER EST AUTHENTIFIE
    public function __construct()
    {
        $this->middleware(['auth'])->except(["ShowStateImprimeHtml"]);
    }

    function ShowWaterStateImprimeHtml(Request $request, $state)
    {
        set_time_limit(3600);

        $state = StopHouseWaterState::find($state);

        if (!$state) {
            return "Cet état n'existe pas";
        }

        #####_______
        $factures_array = [];
        $factures_paid_array = [];
        $factures_umpaid_array = [];

        foreach ($state->StatesFactures as $facture) {
            if (!$facture->state_facture) {
                if ($facture->paid) {
                    array_push($factures_paid_array, $facture->amount);
                } else {
                    array_push($factures_umpaid_array, $facture->amount);
                }

                ####______
                array_push($factures_array, $facture->amount);
            }
        }

        ####___
        $factures_sum = array_sum($factures_array);
        $paid_factures_sum = array_sum($factures_paid_array);
        $umpaid_factures_sum = array_sum($factures_umpaid_array);

        $pdf = Pdf::loadView('water-state', compact([
            "state",
            "factures_sum",
            "paid_factures_sum",
            "umpaid_factures_sum"
        ]));

        return $pdf->stream();

        // return view("water-state", compact(["state", "factures_sum", "paid_factures_sum", "umpaid_factures_sum"]));
    }
}
