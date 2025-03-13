<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Gestion de scautions</title>

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
                                <h3 class="rapport-title text-uppercase">états des cautions</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <img src="{{asset('edou_logo.png')}}" alt="" style="width: 100px;" class="img-fluid">
                </div>

                <br>
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="text-center">Maison</th>
                            <th scope="col" class="text-center">Chambre</th>
                            <th scope="col" class="text-center">Frais de peinture</th>
                            <th scope="col" class="text-center">Locataire</th>
                            <th scope="col" class="text-center">Caution Electrique</th>
                            <th scope="col" class="text-center">Caution Eau</th>
                            <th scope="col" class="text-center">Caution Loyer</th>
                            <th scope="col" class="text-center">Totaux(fcfa) </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{$location->House->name}}</td>
                            <td class="text-center">{{$location->Room->number}}</td>
                            <td class="text-center bg-warning">{{$location->frais_peiture}}</td>
                            <td class="text-center">{{$location->Locataire->name}} {{$location->Locataire->prenom}}</td>
                            <td class="text-center">{{$location->caution_electric}}</td>
                            <td class="text-center">{{$location->caution_water}}</td>
                            <td class="text-center"> <strong class="d-block">{{$location->caution_number*$location->loyer}} </strong> ({{$location->caution_number}}X{{$location->loyer}})</td>
                            <td class="text-center bg-warning"> <strong> {{$location->caution_water+$location->caution_electric}} </strong> </td>
                        </tr>

                        <tr>
                            <td class="bg-red shadow-lg">Totaux: </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="bg-warning"> <strong>{{$cautions_electricity}} </strong> </td>
                            <td class="bg-warning"> <strong>{{$cautions_eau}} </strong> </td>
                            <td class="bg-warning"> <strong>{{$cautions_loyer}} </strong> </td>
                        </tr>

                        <br>
                        <p class="text-center">
                            Arrêté le présent état à la somme de <em class="text-red">{{$cautions_electricity + $cautions_eau + $cautions_loyer}} cfa</em>
                        </p>
                    </tbody>
                </table>

                <br>
                <!-- BORDEREAU DE CAUTION -->
                <div class="row">
                    <div class="col-12 text-center">
                        <img src="{{$location->caution_bordereau}}" class="shadow" style="border:none;border-radius:10px" width="500" alt="" srcset="">
                    </div>
                </div>

                <br>
                <p class="text-center">
                    Arrêté le présent état à la somme de <em class="text-red">{{ $cautions_electricity + $cautions_eau + $cautions_loyer}} cfa</em>
                </p>

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