<div>
    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin)
    <!-- AJOUT D'UN TYPE DE CHAMBRE -->
    <div class="text-left">
        <button type="button" class="btn btn btn-sm bg-light shadow roundered" data-bs-toggle="modal" data-bs-target="#room_type">
            <i class="bi bi-cloud-plus-fill"></i>Ajouter un type de maison
        </button>
    </div>
    <br>
    @endif
    <!-- Modal room type-->
    <div class="modal fade" id="room_type" aria-labelledby="room_type" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">Type de maison</h5>
                </div>
                <form action="{{route('house.AddHouseType')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" required value="{{old('name')}}" name="name" placeholder="Le label ...." class="form-control">
                                    @error("house_type_name")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <textarea required value="{{old('description')}}" name="description" class="form-control" placeholder="Description ...."></textarea>
                                    @error("house_type_description")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-dark"><i class="bi bi-building-check"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin)
    <div>
        <div class="d-flex header-bar">
            <h2 class="accordion-header">
                <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#addHouse">
                    <i class="bi bi-cloud-plus-fill"></i> Ajouter
                </button>
            </h2>
        </div>
    </div>
    @endif
    <!-- ADD HOUSE -->
    <div class="modal fade" id="addHouse" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'une maison </p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('house._AddHouse')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce">
                        @csrf
                        <input type="hidden" name="agency" value="{{$current_agency->id}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Nom</label>
                                    <input type="text" value="{{old('name')}}" name="name" placeholder="Nom de la maison" class="form-control">
                                    @error("name")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Latitude</label>
                                    <input type="text" value="{{old('latitude')}}" name="latitude" placeholder="Latitude de la maison" class="form-control">
                                    @error("latitude")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Longitude</label>
                                    <input type="text" value="{{old('longitude')}}" name="longitude" placeholder="Longitude de la maison" class="form-control">
                                    @error("longitude")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Type</label>
                                    <select class="form-select form-control" name="type" aria-label="Default select example">
                                        @foreach($house_types as $type)
                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("type")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Pays</label>
                                    <select class="form-select form-control" value="{{old('country')}}" name="country" aria-label="Default select example">
                                        @foreach($countries as $countrie)
                                        @if($countrie['id']==4)
                                        <option value="{{$countrie['id']}}">{{$countrie['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error("country")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Département</label>
                                    <select class="form-select form-control" value="{{old('departement')}}" name="departement" aria-label="Default select example">
                                        @foreach($departements as $departement)
                                        <option value="{{$departement['id']}}">{{$departement['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("departement")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>

                                <div class="mb-3">
                                    <span class="text-red"><i class="bi bi-geo-fill"></i> Géolocalisation de la maison</span>
                                    <input type="text" value="{{old('geolocalisation')}}" name="geolocalisation" class="form-control" placeholder="Entrez le lien de géolocalisation de la maison">
                                    @error("geolocalisation")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Ville/Commune</label>
                                    <select class="form-select form-control" value="{{old('city')}}" name="city" aria-label="Default select example">
                                        @foreach($cities as $citie)
                                        @if($citie['_country']['id'] == 4)
                                        <option value="{{$citie['id']}}">{{$citie['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error("city")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Quartier</label>
                                    <select class="form-select form-control" value="{{old('quartier')}}" name="quartier" aria-label="Default select example">
                                        @foreach($quartiers as $quartier)
                                        <option value="{{$quartier['id']}}">{{$quartier['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("quartier")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Zone</label>
                                    <select class="form-select form-control" value="{{old('zone')}}" name="zone" aria-label="Default select example">
                                        @foreach($zones as $zone)
                                        <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("zone")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Superviseur</label>
                                    <select class="form-select form-control" value="{{old('supervisor')}}" name="supervisor" aria-label="Default select example">
                                        @foreach($supervisors as $supervisor)
                                        <option value="{{$supervisor['id']}}">{{$supervisor['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("supervisor")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Propriétaire</label>
                                    <select class="form-select form-control" value="{{old('proprietor')}}" name="proprietor" aria-label="Default select example">
                                        @foreach($proprietors as $proprietor)
                                        <option value="{{$proprietor['id']}}">{{$proprietor['lastname']}} {{$proprietor['firstname']}}</option>
                                        @endforeach
                                    </select>
                                    @error("proprietor")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Commentaire</label>
                                    <textarea name="comments" value="{{old('comments')}}" class="form-control" placeholder="Laissez un commentaire ici" class="form-control" id=""></textarea>
                                    @error("comments")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="">
                                    <span>Date d'échéance de paiement du propriétaire</span>
                                    <input value="{{old('proprio_payement_echeance_date')}}" type="date" name="proprio_payement_echeance_date" class="form-control" id="">
                                    @error("proprio_payement_echeance_date")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn bg-red">Enregistrer</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <h4 class="">Total: <strong class="text-red"> {{$houses_count}} </strong> </h4>
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Nom</th>
                            <th class="text-center">Latitude</th>
                            <th class="text-center">Longitude</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Superviseur</th>
                            <th class="text-center">Propriétaire</th>
                            <th class="text-center">Chambres</th>
                            <th class="text-center"><i class="bi bi-geo-fill"></i></th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($houses as $house)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index + 1}}</td>
                            <td class="text-center"> {{$house["name"]}}</td>
                            <td class="text-center"> @if($house["latitude"]) {{$house["latitude"]}} @else --- @endif</td>
                            <td class="text-center">@if($house["longitude"]) {{$house["longitude"]}} @else --- @endif</td>
                            <td class="text-center">{{$house["Type"]["name"]}}</td>
                            <td class="text-center">{{$house["Supervisor"]["name"]}}</td>
                            <td class="text-center">{{$house["Proprietor"]["lastname"]}} {{$house["Proprietor"]["firstname"]}}</td>
                            <td class="text-center">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#showRooms_{{$house['id']}}" class="btn btn-sm bg-warning">
                                    <i class="bi bi-eye-fill"></i> &nbsp; Voir
                                </button>
                            </td>
                            <td class="text-center">
                                @if($house['geolocalisation'])
                                <a href="{{$house['geolocalisation']}}" class="btn btn-sm shadow-lg roundered" target="_blank" rel="noopener noreferrer"><i class="bi bi-eye-fill"></i> <i class="bi bi-geo-fill"></i></a>
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group dropstart">
                                    <button class="btn btn-sm bg-red dropdown-toggle text-uppercase" style="z-index: 0;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-kanban-fill"></i> &nbsp; Gérer
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin)
                                        <li>
                                            <a href="{{route('house.DeleteHouse', crypId($house['id']))}}" data-confirm-delete="true" class="btn btn-sm bg-red"><i class="bi bi-archive-fill"></i> Supprimer</a>
                                        </li>

                                        <li>
                                            <button class="btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#updateModal_{{$house['id']}}"><i class="bi bi-person-lines-fill"></i> Modifier</button>
                                        </li>
                                        @endif

                                        @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                                        <li>
                                            <a target="_blank" href="/house/{{crypId($house['id'])}}/{{crypId($current_agency['id'])}}/stopHouseState" class="btn btn-sm bg-warning text-dark"><i class="bi bi-sign-stop-fill"></i>&nbsp; Arrêter les états</a>
                                        </li>
                                        @endif

                                        <li>
                                            <button class="btn btn-sm bg-light" data-bs-toggle="modal" data-bs-target="#cautionModal_{{$house['id']}}"><i class="bi bi-file-earmark-pdf-fill"></i> Gestion des cautions </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- ###### MODEL D'AFFICHAGE DES MAISONS ###### -->
                        <div class="modal fade" id="showRooms_{{$house['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Chambre: <strong> <em class="text-red"> {{$house['name']}}</em> </strong> </h6>
                                    </div>
                                    <div class="modal-body">
                                        <h6 class="">Total de chambre: <em class="text-red"> {{count($house->Rooms)}}</em> </h6>
                                        <ul class="list-group">
                                            @foreach($house->Rooms as $room)
                                            <li class="list-group-item"><strong>N° :</strong> {{$room->number}}, <strong>Loyer :</strong> {{$room->loyer}}, <strong>Montant total :</strong> {{$room->total_amount}} </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ###### MODEL DE MODIFICATION ###### -->
                        <div class="modal fade" id="updateModal_{{$house['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Modifier <strong> <em class="text-red"> {{$house['name']}}</em> </strong> </h6>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('house.UpdateHouse',$house['id'])}}" method="post" class="p-3 animate__animated animate__bounce">
                                            @csrf
                                            @method("PATCH")
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <span>Nom</span>
                                                        <input value="{{$house['name']}}" type="text" name="name" placeholder="Nom ..." class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <span class="">Longitude</span>
                                                        <input value="{{$house['longitude']}}" type="text" name="longitude" placeholder="Longitude ..." class="form-control">
                                                    </div><br>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <span>Latitude</span>
                                                        <input value="{{$house['latitude']}}" type="text" name="latitude" placeholder="Latitude" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <span>Géolocalisation</span>
                                                        <input value="{{$house['geolocalisation']}}" type="text" placeholder="Geolocalisation" name="geolocalisation" class="form-control">
                                                    </div><br>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <span>Date d'échéance du propriétaire</span>
                                                            <input value="{{$house['proprio_payement_echeance_date']}}" type="date" name="adresse" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <span>Commission (en %)</span>
                                                            <input value="{{$house['commission_percent']}}" type="text" placeholder="Commission" name="commission_percent" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-sm bg-dark">Modifier</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- #### MODEL DE GESTION DES CAUTIONS -->
                        <div class="modal fade" id="cautionModal_{{$house['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="">Maison : <em class="text-red"> {{$house["name"]}} </em> </h6>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('house.GenerateCautionByPeriod',crypId($house->id))}}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span>Date de début</span>
                                                    <input name="first_date" type="date" required name="first_date" class="form-control" id="">
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="">Date de fin</span>
                                                    <input name="last_date" type="date" required name="last_date" class="form-control" id="">
                                                </div>
                                                <br>
                                            </div>
                                            <br>
                                            <div class="text-center">
                                                <button type="submit" class="w-100 text-center bg-red btn btn-sm">Génerer</button>
                                            </div>
                                        </form>
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