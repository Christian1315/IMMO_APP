<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Etats de maison</title>
    <style>
        * {
            font-family: "Poppins";
        }

        .title {
            text-decoration: underline;
            font-size: 25px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .rapport-title {
            color: #000;
            /* border: solid 2px #cc3301; */
            text-align: center !important;
            padding: 10px;
            background-color: rgb(159, 160, 161) !important;
            /* --bs-bg-opacity: 0.5 */
        }

        .text-red {
            color: #cc3301;
        }

        tr th {
            font-size: 10px !important;
        }

        td {
            border: 2px solid #000;
        }

        td.text {
            border: none !important;
        }

        .bg-red {
            background-color: #cc3301;
            color: #fff;
        }

        tr,
        td {
            align-items: center !important;
        }

        .header {
            margin-top: 100px;
        }

    </style>
</head>

<body>
    <div class="container bg-light">
        <div class="row shadow-lg" style="padding-inline: 20px;">
            <!-- HEADER -->
            <div class="row _header px-5">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text">
                                <img src="{{public_path('edou_logo.png')}}" alt="" style="width: 70px;" class="rounded img-fluid">
                            </td>
                            <td class="text"></td>
                            <td class="text"></td>
                            <td class="text"></td>
                            <td class="text">
                                <h3 class="rapport-title text-uppercase">état de récouvrement</h3>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <!-- <br>
            <br> -->

            @php
            $recovery_date = $house->post_paid?
            date("Y/m/d", strtotime("-1 month", strtotime($house->house_last_state->created_at))):
            $house->house_last_state->created_at;
            @endphp

            <!-- infos liés à la maison -->
            <div class="row">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text">
                                <div class="mt-3">
                                    <h6 class="">Mois de recouvrement: <strong> <em class="text-red"> {{ \Carbon\Carbon::parse($house->house_last_state->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }} </em> </strong> </h6>
                                    <h6 class="">Mois récouvré: <strong> <em class="text-red"> {{ \Carbon\Carbon::parse($recovery_date)->locale('fr')->isoFormat('D MMMM YYYY') }} </em> </strong> </h6>
                                    <div class="mr-5 p-1" style="border: 2px solid #000;">
                                        <div class=""><strong class="">Taux = </strong> [ Nbre de locataires ayant payés ( <em class="text-red"> {{count($paid_locataires)}} </em> )] / [ Nbre de locataires total ( <em class="text-red"> {{count($un_paid_locataires)}} </em> )] = <em class="bg-warning">{{NumersDivider(count($paid_locataires),count($un_paid_locataires))}} % </em> </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text">
                                <div class="">
                                    <h6 class="">Maison : <strong> <em class="text-red"> {{$house["name"]}} </em> </strong> </h6>
                                    <h6 class="">Superviseur : <strong> <em class="text-red"> {{$house->Supervisor->name}} </em> </strong> </h6>
                                    <h6 class="">Propriétaire : <strong> <em class="text-red"> {{$house->Proprietor->lastname}} {{$house->Proprietor->firstname}} ({{$house->Proprietor->phone}})</em> </strong> </h6>
                                    <h6 class="">Date d'arrêt: <strong> <em class="text-red"> {{ \Carbon\Carbon::parse($house->PayementInitiations->last()?->state_stoped_day)->locale('fr')->isoFormat('D MMMM YYYY') }} </em> </strong> </h6>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <br>
            <br>

            <!-- les totaux -->
            <div class="row">
                <table class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Montant total récouvré</th>
                            <th class="text-center">Commission</th>
                            <th class="text-center">Dépense totale</th>
                            <th class="text-center">Charge locatives</th>
                            <th class="text-center">Net à payer</th>
                            {{-- <th class="text-center">Date d'arrêt d'état</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="align-items-center">
                            <td class="text-center"> {{$house["name"]}}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{ number_format($house["total_amount_paid"],2,","," ") }} fcfa </strong> </button>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{number_format($house["commission"],2,","," ")}} fcfa </strong> </button>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-light shadow-lg text-red"><i class="bi bi-currency-exchange"></i> <strong> {{number_format($house["last_depenses"],2,","," ")}} fcfa </strong> </button>
                            </td>

                            <td class="text-center">
                                <strong class="text-red">{{number_format($house->LocativeCharge(),2,","," ")}} fcfa</strong>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{number_format($house["net_to_paid"],2,","," ")}} fcfa </strong> </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br><br><br>
        </div>
    </div>

    <!-- les locataires -->
    <div class="row">
        <table class="table table-striped table-sm" style="margin-inline-end: 50px!important;">
            @if(count($locations)>0)
            <thead>
                <tr>
                    <!-- <th class="text-center">N°</th> -->
                    <th class="text-center">Locataire</th>
                    <th class="text-center">Téléphone</th>
                    <th class="text-center">Chambre</th>
                    <th class="text-center">Loyer Mensuel</th>
                    <th class="text-center">Prorata</th>
                    <th class="text-center">Nbre de mois payé(s)</th>
                    <th class="text-center">Montant payé</th>
                    <th class="text-center">Dernier mois payé</th>
                    <th class="text-center">Début du contrat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($locations as $location)
                <tr class="align-items-center">
                    <td class="text-center"> <button class="btn btn-sm btn-light"> <strong> {{$location["Locataire"]["name"]}} {{$location["Locataire"]["prenom"]}}</strong> </button> </td>
                    <td class="text-center">{{$location->Locataire->phone}}</td>
                    <td class="text-center">{{$location->Room->number}}</td>
                    <td class="text-center"><span class="badge bg-light text-red"> {{number_format($location->Room->total_amount,2,","," ")}} </span></td>
                    <td class="text-center"><span class="badge bg-light text-red">{{$location->prorata_amount>0?number_format($location->prorata_amount,2,","," "):'--'}} </span></td>
                    <td class="text-center">{{$location["_locataire"]?($location->prorata_amount>0?'--':$location["_locataire"]["nbr_month_paid"]):00}}</td>
                    <td class="text-center"><span class="badge bg-light text-red">{{number_format($location["_locataire"]?
                                            ($location->prorata_amount>0?
                                                $location->prorata_amount:
                                                $location["_locataire"]["nbr_facture_amount_paid"]
                                            ):00,2,","," ")}}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light shadow-lg"> <i class="bi bi-calendar-check-fill"></i> <strong class="text-red">{{ \Carbon\Carbon::parse($location["latest_loyer_date"])->locale('fr')->isoFormat('MMMM YYYY') }} </strong> </button>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light shadow-lg"> <i class="bi bi-calendar-check-fill"></i> <strong class="text-red">{{ \Carbon\Carbon::parse($location["effet_date"])->locale('fr')->isoFormat('D MMMM YYYY') }} </strong> </button>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="3" class="bg-warning text-center"><strong> Détails des dépenses: </strong></td>
                    <td colspan="5" class="text-left">
                        <ul class="">
                            @forelse($house->house_depenses as $depense)
                            <li class=""><strong class="text-red">{{number_format($depense->sold_retrieved,2,","," ")}} fcfa</strong> - {{$depense->description}}</li>
                            @empty
                            <li>Aucune dépense éffectuée dans la maison!</li>
                            @endforelse
                        </ul>
                    </td>
                </tr>
            </tbody>
            @else
            <p class="text-center text-red">Aucune location!</p>
            @endif
        </table>
    </div>

    <br>
    <!--  RAPPORT DE RECOUVREMENT -->
    <div class="row">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text"></td>
                    <td class="text"></td>
                    <td class="text">
                        <h4 class="text-center" style="text-decoration: underline;">Rapport de récouvrement</h4>
                        <div class="p-3 shadow text-justify" style="border: #000 2px solid;border-radius:5px ">
                            {{$state?$state->recovery_rapport:($house->PayementInitiations->last()?$house->PayementInitiations->last()->recovery_rapport:"---")}}
                        </div>
                    </td>
                    <td class="text"></td>
                    <td class="text"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <!-- SIGNATURE SESSION -->
    <div class="text-right">
        <h5 class="" style="text-decoration: underline;">Le Chef d'Agence</h5>
        <br>
        <hr class="" style="width: 100px!important;position:absolute;right:0">
        <br>
    </div>
</body>

</html>