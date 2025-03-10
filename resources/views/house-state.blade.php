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
            color: #fff;
            border: solid 2px #cc3301;
            text-align: center !important;
            padding: 20px;
            background-color: #000;
            --bs-bg-opacity: 0.5
        }

        .text-red {
            color: #cc3301;
        }

        td {
            border: 2px solid #000;
        }

        .bg-red {
            background-color: #cc3301;
            color: #fff;
        }

        tr,
        td {
            align-items: center !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10 shadow-lg bg-light">
                <!-- HEADER -->
                <div class="row">
                    <div class="col-12 px-0 mx-0">
                        <div>
                            <div class="col-12">
                                <h3 class="rapport-title text-uppercase">état de récouvrement</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="d-flex" style="justify-content: space-between;">
                    <div class="text-center">
                        <img src="{{asset('edou_logo.png')}}" alt="" style="width: 100px;" class="img-fluid">
                    </div>
                    <div class="">
                        <h6 class="">Maison : <strong> <em class="text-red"> {{$house["name"]}} </em> </strong> </h6>
                        <h6 class="">Superviseur : <strong> <em class="text-red"> {{$house->Supervisor->name}} </em> </strong> </h6>
                        <h6 class="">Propriétaire : <strong> <em class="text-red"> {{$house->Proprietor->lastname}} {{$house->Proprietor->firstname}}</em> </strong> </h6>
                        <h5 class="text-center">Date d'arrêt: <strong class="text-red"> {{ \Carbon\Carbon::parse($house->PayementInitiations->last()?->state_stoped_day)->locale('fr')->isoFormat('D MMMM YYYY') }} </strong> </h5>
                    </div>
                </div>

                {{-- <br>
                <h5 class="text-center">Date d'arrêt: <strong class="text-red"> {{ \Carbon\Carbon::parse($house->PayementInitiations->last()?->state_stoped_day)->locale('fr')->isoFormat('D MMMM YYYY') }} </strong> </h5> --}}
                <br>

                <!-- les totaux -->
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive table-responsive-list shadow-lg">
                            <table class="table table-striped table-sm">
                                <thead class="bg_dark">
                                    <tr>
                                        <th class="text-center">Maison</th>
                                        <th class="text-center">Montant total récouvré</th>
                                        <th class="text-center">Commission</th>
                                        <th class="text-center">Dépense totale</th>
                                        <th class="text-center">Net à payer</th>
                                        {{-- <th class="text-center">Date d'arrêt d'état</th> --}}
                                        <th class="text-center">Charge locatives</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="align-items-center">
                                        <td class="text-center"> {{$house["name"]}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{$house["total_amount_paid"]}} fcfa </strong> </button>
                                        </td>

                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{$house["commission"]}} fcfa </strong> </button>
                                        </td>

                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg text-red"><i class="bi bi-currency-exchange"></i> <strong> {{$house["last_depenses"]}} fcfa </strong> </button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{$house["net_to_paid"]}} fcfa </strong> </button>
                                        </td>

                                        <td class="text-center">
                                            <strong class="text-red">{{$house->LocativeCharge()}} fcfa</strong>
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
                    <div class="col-12">
                        <div class="table-responsive shadow-lg p-3">
                            <table class="table table-striped table-sm">
                                @if(count($house['locations'])!=0)
                                <thead>
                                    <tr>
                                        <!-- <th class="text-center">N°</th> -->
                                        <th class="text-center">Locataire</th>
                                        <th class="text-center">Téléphone</th>
                                        <th class="text-center">Chambre</th>
                                        <th class="text-center">Loyer Mensuel</th>
                                        <th class="text-center">Nbre de mois payé(s)</th>
                                        <th class="text-center">Montant payé</th>
                                        <th class="text-center">Dernier loyé</th>
                                        <th class="text-center">Mois d'effet</th>
                                        {{-- <th class="text-center text-red">Prorata</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($house->Locations as $location)
                                    <tr class="align-items-center">
                                        <td class="text-center"> <button class="btn btn-sm btn-light"> <strong> {{$location["Locataire"]["name"]}} {{$location["Locataire"]["prenom"]}}</strong> </button> </td>
                                        <td class="text-center">{{$location->Locataire->phone}}</td>
                                        <td class="text-center">{{$location->Room->number}}</td>
                                        <td class="text-center">{{$location->Room->total_amount}}</td>
                                        <td class="text-center">{{$location["_locataire"]["nbr_month_paid"]}}</td>
                                        <td class="text-center">{{$location["_locataire"]["nbr_facture_amount_paid"]}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg"> <i class="bi bi-calendar-check-fill"></i> <strong>{{ \Carbon\Carbon::parse($location["latest_loyer_date"])->locale('fr')->isoFormat('D MMMM YYYY') }} </strong> </button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow-lg"> <i class="bi bi-calendar-check-fill"></i> <strong>{{ \Carbon\Carbon::parse($location["effet_date"])->locale('fr')->isoFormat('D MMMM YYYY') }} </strong> </button>
                                        </td>
                                        {{-- <td class="text-center">
                                            <button class="btn btn-sm btn-light shadow text-red"> <i class="bi bi-calendar-check-fill"></i> <strong>{{$location->Locataire->prorata?$location->Locataire->prorata_date:"---" }}  </strong> </button>
                                        </td> --}}
                                    </tr>
                                    @endforeach


                                    <tr>
                                        <td colspan="3" class="bg-warning"><strong> Chambre libre (s): </strong></td>
                                        <td colspan="5" class="text-right"> <strong class="bg-dark text-white p-1 roundered shadow">= {{count($house["frees_rooms"])}} </strong> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="bg-warning"><strong> Chambre occupée (s): </strong></td>
                                        <td colspan="5" class="text-right"> <strong class="bg-dark text-white p-1 roundered shadow">= {{count($house["busy_rooms"])}} </strong> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="bg-warning"><strong> Chambre libre (s) au début du mois: </strong></td>
                                        <td colspan="5" class="text-right"> <strong class="bg-dark text-white p-1 roundered shadow">= {{count($house["frees_rooms_at_first_month"])}} </strong> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="bg-warning"><strong> Chambre occupée (s) au début du mois: </strong></td>
                                        <td colspan="5" class="text-right"> <strong class="bg-dark text-white p-1 roundered shadow">= {{count($house["busy_rooms_at_first_month"])}} </strong> </td>
                                    </tr>
                                </tbody>
                                @else
                                <p class="text-center text-red">Aucune location!</p>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <br>
                <!--  RAPPORT DE RECOUVREMENT -->
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <h4 class="text-center" style="text-decoration: underline;">Rapport de récouvrement</h4>
                        <div class="p-3 shadow text-justify" style="border: #000 2px solid;border-radius:0px 10px ">
                            {{$state?$state->recovery_rapport:($house->PayementInitiations->last()?$house->PayementInitiations->last()->recovery_rapport:"---")}}
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>

                <br>
                <!-- SIGNATURE SESSION -->
                <div class="text-right">
                    <h5 class="" style="text-decoration: underline;">Signature du Gestionnaire de compte</h5>
                    <br>
                    <hr class="">
                    <br>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    </div>
</body>

</html>