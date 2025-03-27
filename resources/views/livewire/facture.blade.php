<div>
    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <small>
                <button data-bs-toggle="modal" data-bs-target="#ShowSearchLocatorsByHouseForm" class="btn btn-sm bg-light text-dark text-uppercase"><i class="bi bi-file-pdf-fill"></i> Prestation par période</button>
            </small>

            <!-- FILTRE BY PERIOD -->
            <div class="modal fade" id="ShowSearchLocatorsByHouseForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <p class="" id="exampleModalLabel">Générer par période</p>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('location._ManagePrestationStatistiqueForAgencyByPeriod', crypId($agency->id))}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <span>Date de début</span>
                                        <input type="date" required name="first_date" class="form-control" id="">
                                    </div>
                                    <div class="col-md-6">
                                        <span class="">Date de fin</span>
                                        <input type="date" required name="last_date" class="form-control" id="">
                                    </div>
                                </div>
                                <br>
                                <div class="text-center">
                                    <button type="submit" class="w-100 text-center bg-red btn btn-sm"><i class="bi bi-funnel"></i> Génerer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <h4 class="">Total: <strong class="text-red"> {{count($factures)}} </strong> </h4>
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">Code</th>
                            <th class="text-center">Superviseur</th>
                            <th class="text-center">Faturier</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Chambre</th>
                            <th class="text-center">Locataire</th>
                            <th class="text-center">Facture</th>
                            <th class="text-center">Montant</th>
                            <th class="text-center">Echéance</th>
                            <th class="text-center">Commentaire</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factures as $facture)
                        <tr class="align-items-center">
                            <td class="text-center "><span class="badge text-red btn-light"> {{$facture["facture_code"]?$facture["facture_code"]:'--'}}</span></td>
                            <td class="text-center text-red"><button class="btn btn-sm btn-light"> {{$facture["Location"]["House"]["Supervisor"]["name"]}}</button></td>
                            <td class="text-center">{{$facture["Owner"]["name"]}}</td>
                            <td class="text-center"> <button class="btn btn-sm btn-light">{{$facture["Location"]["House"]["name"]}} </button> </td>
                            <td class="text-center">{{$facture["Location"]["Room"]["number"]}} </td>
                            <td class="text-center"><button class="btn btn-sm btn-light">{{$facture["Location"]["Locataire"]["name"]}} {{$facture["Location"]["Locataire"]["prenom"]}} </button> </td>
                            <td class="text-center"> <a target="__blank" href="{{$facture['facture']}}" class="btn btn-sm btn-light shadow-sm"><i class="bi bi-eye"></i></a>
                            </td>
                            <td class="text-center">{{$facture['amount']}}</td>
                            <td class="text-center text-red"><button class="btn btn-sm btn-light text-red"> <b>{{$facture['echeance_date']}} </b></button> </td>
                            <td class="text-center">
                                <textarea name="" rows="1" class="form-control" id="" placeholder="{{$facture->comments}}"></textarea>
                            </td>
                            <td class="text-center"><span class="@if($facture->status==2) bg-success @elseif($facture->status==3 || $facture->status==4)  bg-danger @else bg-warning @endif">{{$facture->Status->name}} </span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>