<div>
    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
    <button class="btn btn-sm btn-light text-uppercase" data-bs-toggle="modal" data-bs-target="#generate_electricity_facture"><i class="bi bi-file-earmark-pdf-fill"> </i> Génerer une facture d'électricité </button>
    <br>

    <button class="btn btn-sm bg-red text-white text-uppercase" data-bs-toggle="modal" data-bs-target="#stop_house_electrcity_state"><i class="bi bi-stop-circle"></i> Arrêter les états</button>
    <br>

    <!-- GENERATE ELECTRICITY FACTURE  -->
    <div class="modal fade" id="generate_electricity_facture" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Génerer une facture d'électricité</p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('electricity_facture._GenerateFacture')}}" method="post">
                                @csrf
                                <div class="mb-3">
                                    <span class="text-red">Choisir la location concernée</span>
                                    <select required name="location" class="form-control">
                                        @foreach($locations as $location)
                                        <option value="{{$location['id']}}"> <strong>Maison: </strong> {{$location->House->name}} ; <strong>Index début: </strong> {{count($location->ElectricityFactures)!=0?$location->ElectricityFactures->first()->end_index: $location->Room->electricity_counter_start_index}} ; <strong>Locataire: </strong>{{$location->Locataire->name}} {{$location->Locataire->prenom}}</option>
                                        @endforeach
                                    </select>

                                    @error("location")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="">Index de fin</label>
                                    <input type="number" required name="end_index" class="form-control" placeholder="Tapez l'Index de fin ...">
                                    @error("end_index")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-sm bg-red"><i class="bi bi-card-list"></i> Génerer</button>
                            </form>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GENERATE WATER FACTURE  -->
    <div class="modal fade" id="stop_house_electrcity_state" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Arrêt d'état d'electricité d'une maison</p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{route('house_state._StopStatsOfHouse')}}" method="post">
                                @csrf
                                <div class="mb-3">
                                    <span class="text-red">Choisir la maison concernée</span>
                                    <select required name="house" class="form-control">
                                        @foreach($locations as $location)
                                        <option value="{{$location->House->id}}"> {{$location->House->name}} </option>
                                        @endforeach
                                    </select>

                                    @error("house")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-sm bg-red"><i class="bi bi-stop-circle"></i> Arrêter l'état</button>
                            </form>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <br>
    <div class="row">
        <div class="col-12">
            <h5 class="text-center">Liste des locations ayant de l'électricité dans cette agence</h5>
            <h4 class="">Total: <strong class="text-red"> {{count($locations)}} </strong> </h4>

            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Locataire</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Télephone</th>
                            <th class="text-center">Index début</th>
                            <th class="text-center">Index fin</th>
                            <th class="text-center">P.U</th>
                            <th class="text-center">Total à payer</th>
                            <th class="text-center">Facture à payer</th>
                            <th class="text-center">Montant payé</th>
                            <th class="text-center">Nbr arrièrées </th>
                            <th class="text-center">Arriérés </th>
                            <th class="text-center">Montant dû</th>
                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <th class="text-center">Payer</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr class="align-items-center">
                            <td class="text-center">{{$location["id"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["name"]}} {{$location["Locataire"]["prenom"]}}</td>
                            <td class="text-center">{{$location["House"]["name"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["phone"]}}</td>
                            <td class="text-center">{{$location["Room"]["electricity_counter_start_index"]}}</td>
                            <td class="text-center"> <strong class="text-red"> {{$location["end_index"]?$location["end_index"]:0}}</strong> </td>
                            <td class="text-center"> <strong class=""> {{$location["kilowater_price"]}} </strong> </td>
                            <td class="text-center"> <strong class="text-red shadow btn btn-sm"> <i class="bi bi-currency-exchange"></i> {{$location["total_un_paid_facture_amount"]?$location["total_un_paid_facture_amount"]:0}} fcfa </strong> </td>
                            <td class="text-center"> <strong class="text-success shadow btn btn-sm"> <i class="bi bi-currency-exchange"></i> {{$location["current_amount"]?$location["current_amount"]:0}} fcfa </strong> </td>
                            <td class="text-center"> <strong class="text-success shadow btn btn-sm"> <i class="bi bi-currency-exchange"></i> {{$location["paid_facture_amount"]?$location["paid_facture_amount"]:0}} fcfa </strong> </td>
                            <td class="text-center text-red"> {{$location["nbr_un_paid_facture_amount"]?$location["nbr_un_paid_facture_amount"]:0}}</td>
                            <td class="text-center"> <strong class="text-red shadow btn btn-sm"> <i class="bi bi-currency-exchange"></i> {{$location["un_paid_facture_amount"]?$location["un_paid_facture_amount"]:0}} fcfa </strong> </td>
                            <td class="text-center"> <strong class="text-success shadow btn btn-sm"> <i class="bi bi-currency-exchange"></i> {{$location["rest_facture_amount"]?$location["rest_facture_amount"]:0}} fcfa </strong> </td>
                            
                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <td class="text-center d-flex">
                                <button data-bs-toggle="modal" data-bs-target="#ShowLocationFactures_{{$location['id']}}" class="btn btn-sm bg-red" type="button">
                                    <i class="bi bi-currency-exchange"></i>&nbsp; Payer
                                </button>
                                <button class="btn btn-sm btn-light text-uppercase" data-bs-toggle="modal" data-bs-target="#state_impression_{{$location['id']}}"><i class="bi bi-file-earmark-pdf-fill"> </i> Imprimer</button>
                            </td>
                            @endif
                        </tr>

                        <!-- ###### FACTURES D4ELECTRICITE -->
                        <div class="modal fade" id="ShowLocationFactures_{{$location['id']}}" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <span class="">Location:<strong>Maison: </strong> {{$location->House->name}} ; <strong>Index début: </strong> {{count($location->ElectricityFactures)!=0?$location->ElectricityFactures->first()->end_index: $location->Room->electricity_counter_start_index}} ;<strong>Index fin: </strong>{{$location->end_index}}; <strong>Locataire: </strong>{{$location->Locataire->name}} {{$location->Locataire->prenom}} </span>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            @foreach($location->ElectricityFactures as $facture)
                                            <li class="list-group-item mb-3 ">

                                                <strong>Maison: </strong> {{$location->House->name}} ;
                                                <strong>Index début: </strong> <span class="text-red"> {{$facture->start_index}}</span> ;
                                                <strong>Index fin: </strong> <span class="text-red"> {{$facture->end_index}}</span>;
                                                <strong>Consommation :</strong> <span class="text-red">{{$facture->consomation}}</span> ;
                                                <strong>Montant: </strong> <span class="text-red"><i class="bi bi-currency-exchange"></i> {{$facture->amount}} </span>;
                                                <strong>Description: </strong> <textarea class="form-control" name="" rows="1" placeholder="{{$facture->comments}}" id=""></textarea> ;
                                                <strong>Statut :</strong>
                                                @if($facture->paid) <span class="bg-success">Payé </span> @else
                                                <span class="bg-red">Impayé </span>
                                                <br>
                                                <a href="{{route('electricity_facture._FacturePayement',crypId($facture->id))}}" class="btn btn-sm bg-red"> <i class="bi bi-currency-exchange"></i> Payer maintenant</a>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                        @if(count($location->ElectricityFactures)==0)
                                        <p class="text-center text-red">Aucune facture disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ###### IMPRESSION DES ETATS -->
                        <div class="modal fade" id="state_impression_{{$location['id']}}" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <span class="">Location: <strong>Maison: </strong> {{$location->House->name}} ; <strong>Index début: </strong> {{count($location->ElectricityFactures)!=0?$location->ElectricityFactures->first()->end_index: $location->Room->electricity_counter_start_index}} ;<strong>Index fin: </strong>{{$location->end_index}}; <strong>Locataire: </strong>{{$location->Locataire->name}} {{$location->Locataire->prenom}} </span>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            @foreach($location->House->ElectricityFacturesStates as $state)
                                            <li class="list-group-item mb-3 ">
                                                <strong>Date d'arrêt: </strong> {{$state->state_stoped_day}}
                                                <br>
                                                <a  href="{{route('house_state.ImprimeElectricityHouseState',crypId($state->id))}}" class="btn btn-sm bg-red"><i class="bi bi-file-earmark-pdf-fill"> </i> Imprimer</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @if(count($location->House->ElectricityFacturesStates)==0)
                                        <p class="text-center text-red">Aucun état disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>