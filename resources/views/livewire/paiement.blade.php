<div>
    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <h4 class="">Total: <strong class="text-red"> {{count($Houses)}} </strong> </h4>
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Montant total récouvré</th>
                            <th class="text-center">Commission</th>
                            <th class="text-center">Dépense totale</th>
                            <th class="text-center">Net à payer</th>
                            <th class="text-center">Date d'arrêt d'état</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Payer</th>
                            <th class="text-center">Impression</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Houses as $house)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index + 1}}</td>
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
                                <button class="btn btn-sm btn-light shadow-lg text-success"><i class="bi bi-currency-exchange"></i> <strong> {{$house["net_to_paid"]!=0?$house["net_to_paid"] : ($house->PayementInitiations->last()? $house->PayementInitiations->last()->amount:0)}} fcfa </strong> </button>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-light shadow-lg text-dark"> <i class="bi bi-calendar-check-fill"></i> <strong> {{$house["house_last_state"]?$house["house_last_state"]["stats_stoped_day"] : ($house->PayementInitiations->last()? $house->PayementInitiations->last()->stats_stoped_day:"---")}} </strong> </button>
                            </td>
                            <td class="text-center">
                                @if($house['house_last_state'])
                                @if($house['house_last_state']["proprietor_paid"])
                                <button disabled class="btn btn-sm bg-light text-success">Payé</button>
                                @elseif ($house->PayementInitiations->last())
                                @if ($house->PayementInitiations->last()->status==3)
                                <button class="btn btn-sm bg-light text-red" title="{{$house->PayementInitiations->last()->rejet_comments}}"> <i class="bi bi-eye"></i> Rejeté</button>
                                @else
                                <button disabled class="btn btn-sm bg-light text-red"> Non payé</button>
                                @endif
                                @else
                                <button disabled class="btn btn-sm bg-light text-red"> Non payé</button>
                                @endif
                                @else

                                @if($house->PayementInitiations->last())
                                @if($house->PayementInitiations->last()->status==3)
                                <button disabled class="btn btn-sm bg-light text-red"> Non payé</button>
                                @else
                                <button disabled class="btn btn-sm bg-light text-red"> Repayé</button>
                                @endif
                                @else
                                ---
                                @endif

                                @endif
                            </td>
                            <td class="text-center">
                                @if($house['house_last_state'])
                                @if($house['house_last_state']["proprietor_paid"])
                                ---
                                @else
                                <button class="btn btn-sm bg-red" data-bs-toggle="modal" data-bs-target="#paid_{{$house['id']}}"><i class="bi bi-currency-exchange"></i>Payer</button>
                                @endif
                                @else
                                @if($house->PayementInitiations->last())
                                @if($house->PayementInitiations->last()->status==3)
                                <button class="btn btn-sm bg-red" data-bs-toggle="modal" data-bs-target="#paid_{{$house['id']}}" title="Opération réjetée pour raison de -- ({{$house->PayementInitiations->last()->rejet_comments}})"><i class="bi bi-currency-exchange"></i> {{$house['net_to_paid']!=0?$house['net_to_paid']:($house->PayementInitiations->last()?$house->PayementInitiations->last()->amount:0)}} Repayer</button>
                                @else
                                ---
                                @endif

                                @else
                                ---
                                @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <a  href="{{route('house.ShowHouseStateImprimeHtml',crypId($house['id']))}}" class="btn text-dark btn-sm bg-light"><i class="bi bi-file-earmark-pdf-fill"></i> Imprimer les états</button>
                            </td>
                        </tr>

                        <!-- ###### MODEL DE PAIEMENT AU PROPRIETAIRE ###### -->
                        <div class="modal fade" id="paid_{{$house['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="">
                                            <strong>Maison: <em class="text-red"> {{$house["name"]}}</em> </strong> <br>
                                        </div>
                                    </div>
                                    <form action="{{route('payement_initiation.InitiatePaiementToProprietor')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce p-3">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="hidden" name="house" value="{{$house->id}}">
                                                <div class="mb-3 p-3">
                                                    <label for="" class="d-block">Montant à payer <span class="text-red"> (fcfa)</span> </label>
                                                    <input type="hidden" name="amount" value="{{$house['net_to_paid']!=0?$house['net_to_paid']:($house->PayementInitiations->last()?$house->PayementInitiations->last()->amount:0)}}" class="form-control">
                                                    <input disabled type="number" value="{{$house['net_to_paid']!=0?$house['net_to_paid']:($house->PayementInitiations->last()?$house->PayementInitiations->last()->amount:0)}}" class="form-control">
                                                    @error("amount")
                                                    <span class="text-red">{{$message}}</span>
                                                    @enderror
                                                    <div class="text-right mt-2">
                                                        <button class="btn btn-sm bg-red">Valider</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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