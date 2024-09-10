<div>

    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin)
    <!-- AJOUT D'UN TYPE DE CHAMBRE -->
    <div class="text-left">
        <button type="button" class="btn btn btn-sm bg-light shadow roundered" data-bs-toggle="modal" data-bs-target="#location_type">
            <i class="bi bi-cloud-plus-fill"></i>Ajouter un type de location
        </button>
    </div>
    <br>
    <!-- Modal room type-->
    <div class="modal fade" id="location_type" aria-labelledby="location_type" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">Type de location</h5>
                </div>
                <form action="{{route('location.AddType')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" required name="name" placeholder="Le label ...." class="form-control">
                                </div><br>
                                <div class="mb-3">
                                    <textarea required name="description" class="form-control" placeholder="Description ...."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-red btntsm"><i class="bi bi-building-check"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <small>
        <a type="button" target="_blank" href="{{route('location._ManageCautions',crypId($current_agency->id))}}" class="btn btn-sm bg-light text-dark text-uppercase"><i class="bi bi-file-earmark-pdf-fill"></i> Génerer les états des cautions </a> &nbsp;
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
                    <form action="{{route('location._ManagePrestationStatistiqueForAgencyByPeriod', crypId($current_agency->id))}}" method="POST">
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

    <div class="d-flex header-bar">
        <small>
            <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#addLocation">
                <i class="bi bi-cloud-plus-fill"></i> Ajouter
            </button>
        </small>
    </div>
    <br><br>
    @endif

    <!-- ADD LOCATION -->
    <div class="modal fade" id="addLocation" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'une location</p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('location._AddLocation')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="d-block" for="">Agence</label>
                                    <input type="hidden" name="agency" value="{{$current_agency->id}}">
                                    <input type="text" disabled class="form-control" placeholder="Agence :{{$current_agency['name']}}">
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Maison</label>
                                    <select class="form-select form-control" onchange="houseSelect()" id="houseSelection" name="house" aria-label="Default select example">
                                        @foreach($houses as $house)
                                        <option value="{{$house['id']}}" @if(old('house')==$house['id']) selected @endif>{{$house['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("house")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <br>

                                <!-- SPIN -->
                                <div class="spinner-border" id="loading" role="status">
                                    <span class="visually-hidden text-red">Loading...</span>
                                </div>
                                <!-- SPIN -->

                                <div class="mb-3" id="roomsShow" hidden>
                                    <label class="d-block" for="">Chambre</label>
                                    <select class="form-select form-control" name="room" id="rooms" aria-label="Default select example">

                                    </select>
                                    @error("room")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <br>

                                <div class="mb-3">
                                    <label class="d-block" for="">Locataire</label>
                                    <select class="form-select form-control" name="locataire" aria-label="Default select example">
                                        @foreach($locators as $locator)
                                        <option value="{{$locator['id']}}" @if(old('locator')==$locator['id']) selected @endif>{{$locator['name']}} {{$locator['prenom']}}</option>
                                        @endforeach
                                    </select>
                                    @error("locataire")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Type</label>
                                    <select class="form-select form-control" name="type">
                                        @foreach($location_types as $type)
                                        <option value="{{$type['id']}}" @if(old('type')==$type['id']) selected @endif>{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("type")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <br>
                                <div class="mb-3">
                                    <span>Uploader le bordereau du caution</span><br>
                                    <input required type="file" name="caution_bordereau" class="form-control">
                                    @error("caution_bordereau")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Caution d'électricité</label>
                                    <span class="text-center text-red"> {{$caution_electric_error}} </span>
                                    <input value="{{old('caution_electric')}}" type="text" name="caution_electric" class="form-control" placeholder="Caution d'électricité...">
                                    @error("caution_electric")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Numéro du compteur eau ...</label>
                                    <input value="{{old('water_counter')}}" type="text" name="water_counter" placeholder="Compteur eau..." class="form-control">
                                    @error("water_counter")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Prestation</label>
                                    <input value="{{old('prestation')}}" type="number" name="prestation" placeholder="La prestation..." class="form-control">
                                    @error("prestation")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block">Numéro contrat</label>
                                    <input value="{{old('numero_contrat')}}" type="text" name="numero_contrat" placeholder="Numéro du contrat..." class="form-control">
                                    @error("numero_contrat")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span>Uploader le contrat</span><br>
                                    <input required type="file" name="img_contrat" class="form-control">
                                    @error("img_contrat")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Caution eau</label>
                                    <input value="{{old('caution_water')}}" type="text" name="caution_water" class="form-control" placeholder="Caution eau ....">
                                    @error("caution_water")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Numéro du compteur électrique</label>
                                    <input value="{{old('electric_counter')}}" type="text" name="electric_counter" class="form-control" placeholder="Compteur électricité ....">
                                    @error("electric_counter")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>

                                <!-- <div class="mb-3">
                                <span>Date du dernier loyer payé</span><br>
                                <span class="text-center text-red"> {{$latest_loyer_date_error}} </span>
                                <input wire:model="latest_loyer_date" type="date" name="latest_loyer_date" class="form-control" placeholder="Dernier loyer payé ....">
                            </div><br> -->
                                <div class="mb-3">
                                    <span>Uploader l'image de la prestation</span><br>
                                    <input required type="file" name="img_prestation" class="form-control">
                                    @error("img_prestation")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Nbr de caution loyer</label>
                                    <input value="{{old('caution_number')}}" type="number" name="caution_number" class="form-control" placeholder="Nombre de caution loyer ....">
                                    @error("caution_number")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span>Date d'effet</span><br>
                                    <input value="{{old('effet_date')}}" type="date" name="effet_date" class="form-control" placeholder="Date de prise d'effet ....">
                                    @error("effet_date")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span>Frais de reprise de peinture</span><br>
                                    <input value="{{old('frais_peiture')}}" type="text" name="frais_peiture" class="form-control" placeholder="Frais de reprise de peinture ....">
                                    @error("frais_peiture")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label class="d-block" for="">Commentaire</label>
                                    <textarea value="{{old('comments')}}" name="comments" class="form-control" placeholder="Laissez un commentaire ici" class="form-control" id=""></textarea>
                                    @error("comments")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>

                                <div class="mb-3 d-flex">
                                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        <input type="checkbox" name="pre_paid" class="btn-check" id="pre_paid" autocomplete="off">
                                        <label class="btn bg-dark text-white" for="pre_paid">Prépayé</label>
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        <input type="checkbox" name="post_paid" class="btn-check" id="post_paid" autocomplete="off">
                                        <label class="btn bg-dark text-white" for="post_paid">Post-Payé</label>
                                    </div>
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

    @if(IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()) && !IS_USER_HAS_MASTER_ROLE(auth()->user()) && !auth()->user()->is_master && !auth()->user()->is_admin)
    <div class="d-flex header-bar">
        <small>
            <button type="button" class="btn btn-sm bg-red" data-bs-toggle="modal" data-bs-target="#encaisse_for_supervisor">
                <i class="bi bi-currency-exchange"></i> Encaisser un loyer
            </button>
        </small>
    </div>

    <!-- ENCAISSEMENT LORSQUE LE USER EST UN SUPERVISEUR -->
    <div class="modal fade" id="encaisse_for_supervisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fs-5" id="exampleModalLabel">Encaissement </h6>
                </div>
                <form action="{{route('location._AddPaiement')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce p-3" enctype="multipart/form-data">
                    @csrf
                    <div class="row p-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Selectionnez la location concernée </label>
                                <select name="location" onchange="locationSelection()" id="location_selected" class="form-select form-control" aria-label="Default select example">
                                    <option value="">** **</option>
                                    @foreach($locations as $location)
                                    <!-- les locations dont le user est superviseur de la maison -->
                                    @if ($location->House->supervisor==auth()->user()->id)
                                    <option value="{{$location['id']}}" @if($location->id==old('location') ) selected @endif>
                                        <strong>Maison: <em class="text-red"> {{$location['House']["name"]}}</em> </strong>;
                                        <strong>Chambre: <em class="text-red"> {{$location['Room']['number']}} </em> </strong>;
                                        <strong>Locataire: <em class="text-red"> {{$location['Locataire']['name']}} {{$location['Locataire']['prenom']}}</em> </strong>
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('location')
                                <span class="text-red">{{$message}}</span>
                                @enderror
                            </div>
                            <!-- SPIN -->
                            <div class="spinner-border" id="loading" role="status" hidden>
                                <span class="visually-hidden text-red">Loading...</span>
                            </div>
                            <!-- SPIN -->
                            <div class="mb-3">
                                <label>Type de paiement </label>
                                <select name="type" class="form-select form-control" aria-label="Default select example">
                                    @foreach($paiements_types as $type)
                                    <option value="{{$type['id']}}" name="type">{{$type["name"]}}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <span class="text-red">{{$message}}</span>
                                @enderror
                            </div>


                            <div class="mb-3" id="encaisse_date_info" hidden>
                                <span>Date ou mois pour lequel vous voulez encaisser pour cette location</span>
                                <input id="encaisse_date" disabled value="" class="form-control">
                            </div>
                            <div id="prorata_infos" hidden>
                                <div class="">
                                    <span class="text-primary">Ce locataire est un prorata(veuillez renseigner ses infos)</span>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="d-block">Nbre de jour du prorata</label>
                                    <input value="{{old('prorata_days')}}" id="prorata_days" name="prorata_days" placeholder="Nbre de jour du prorata ..." class="form-control">
                                    @error('prorata_days')
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="d-block">Montant du prorata</label>
                                    <input value="{{old('prorata_amount')}}" id="prorata_amount" name="prorata_amount" placeholder="Montant du prorata ..." class="form-control">
                                    @error('prorata_amount')
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="d-block">Date du prorata</label>
                                    <input type="hidden" name="prorata_date" id="prorata_date" type="date" class="form-control">
                                    <input disabled id="prorata_date" type="date" class="form-control" hidden>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span>Uploader la facture ici</span> <br>
                                <input type="file" required name="facture" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="" class="d-block">Code de facture</label>
                                <input value="{{old('facture_code')}}" required name="facture_code" placeholder="Code facture ...." class="form-control">
                                @error('facture_code')
                                <span class="text-red">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm bg-red"><i class="bi bi-check-all"></i> Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br>
    @endif

    <!-- TABLEAU DE LISTE -->
    @if(!IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
    <div class="row">
        <div class="col-12">
            <h4 class="">Total: <strong class="text-red"> {{$locations_count}} </strong> </h4>
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Chambre</th>
                            <th class="text-center">Locataire</th>
                            <th class="text-center">Contrat</th>
                            <th class="text-center">Loyer</th>
                            <!-- <th class="text-center">Echéance actuelle</th> -->
                            <th class="text-center">Echeance</th>
                            <th class="text-center">Commentaire</th>
                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index+1}}</td>
                            <td class="text-center">{{$location["House"]["name"]}}</td>
                            <td class="text-center">{{$location["Room"]["number"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["name"]}} {{$location["Locataire"]["prenom"]}}</td>
                            <td class="text-center"> <a target="_blank" href="{{$location['img_contrat']}}" class="btn btn-sm text-dark btn-light" rel="noopener noreferrer"><i class="bi bi-eye"></i></a>
                            </td>
                            <td class="text-center">{{$location["loyer"]}}</td>
                            <!-- <td class="text-center text-red"><small> <i class="bi bi-calendar2-check-fill"></i> {{$location["latest_loyer_date"]}}</small> </td> -->
                            <td class="text-center text-red"><small> <i class="bi bi-calendar2-check-fill"></i> {{$location["next_loyer_date"]}}</small> </td>
                            <td class="text-center">
                                <textarea name="" rows="1" class="form-control" id="">{{$location["comments"]}}</textarea>
                            </td>

                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <td class="text-center">
                                <div class="btn-group dropstart">
                                    <button class="btn bg-red btn-sm dropdown-toggle" style="z-index: 0;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-kanban-fill"></i> &nbsp; Gérer
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button data-bs-toggle="modal" data-bs-target="#encaisse_{{$location['id']}}" class="btn btn-sm bg-dark">
                                                Encaisser
                                            </button>
                                        </li>
                                        <li>
                                            <button data-bs-toggle="modal" data-bs-target="#demenage_{{$location['id']}}" class="btn btn-sm bg-red">
                                                Démenager
                                            </button>
                                        </li>

                                        <li>
                                            <button class="btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#updateModal_{{$location['id']}}"><i class="bi bi-person-lines-fill"></i> Modifier</button>
                                        </li>
                                        <li>
                                            <button class="btn btn-sm btn-light text-dark" data-bs-toggle="modal" data-bs-target="#shoFactures_{{$location['id']}}">Gérer les factures</button>
                                        </li>
                                        <li>
                                            <a target="_blank" href="{{route('location.imprimer',crypId($location['id']))}}" class="btn btn-sm bg-secondary text-white"><i class="bi bi-file-earmark-pdf-fill"></i> Imprimer rapport</a>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                            @endif
                        </tr>

                        <!-- ###### MODEL D'ENCAISSEMENT ###### -->
                        <div class="modal fade" id="encaisse_{{$location['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Encaissement </h6>
                                    </div>
                                    <div class="modal-body">
                                        <div class="">
                                            <strong>Maison: <em class="text-red"> {{$location['House']["name"]}}</em> </strong> <br>
                                            <strong>Chambre: <em class="text-red"> {{$location['Room']['number']}} </em> </strong> <br>
                                            <strong>Locataire: <em class="text-red"> {{$location['Locataire']['name']}} {{$location['Locataire']['prenom']}}</em> </strong>
                                        </div>
                                    </div>
                                    <form action="{{route('location._AddPaiement')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce p-3" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="location" value="{{$location->id}}">

                                        <div class="row p-3">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label>Type de paiement </label>
                                                    <select name="type" class="form-select form-control" aria-label="Default select example">
                                                        @foreach($paiements_types as $type)
                                                        <option value="{{$type['id']}}" name="type" @if($type->id==$location->Type->id) selected @endif>{{$type["name"]}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('type')
                                                    <span class="text-red">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <span>Date ou mois pour lequel vous voulez encaisser pour cette location</span>
                                                    <input disabled value="{{Change_date_to_text($location['next_loyer_date'])}}" class="form-control">
                                                </div>

                                                @if($location->Locataire->prorata)
                                                <div class="">
                                                    <span class="text-primary">Ce locataire est un prorata(veuillez renseigner ses infos)</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="" class="d-block">Nbre de jour du prorata</label>
                                                    <input value="{{old('prorata_days')}}" name="prorata_days" placeholder="Nbre de jour du prorata ..." class="form-control">
                                                    @error('prorata_days')
                                                    <span class="text-red">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="" class="d-block">Montant du prorata</label>
                                                    <input value="{{old('prorata_amount')}}" name="prorata_amount" placeholder="Montant du prorata ..." class="form-control">
                                                    @error('prorata_amount')
                                                    <span class="text-red">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="" class="d-block">Date du prorata</label>
                                                    <input value="{{$location->Locataire->prorata_date}}" name="prorata_date" type="date" class="form-control" hidden>
                                                    <input value="{{$location->Locataire->prorata_date}}" disabled type="date" class="form-control">
                                                </div>
                                                @endif
                                                <div class="mb-3">
                                                    <span>Uploader la facture ici</span> <br>
                                                    <input type="file" required name="facture" class="form-control">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="" class="d-block">Code de facture</label>
                                                    <input value="{{old('facture_code')}}" name="facture_code" placeholder="Code facture ...." class="form-control">
                                                    @error('facture_code')
                                                    <span class="text-red">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-sm bg-red"><i class="bi bi-check-all"></i> Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <!-- ###### MODEL DE DEMENAGEMENT ###### -->
                        <div class="modal fade" id="demenage_{{$location['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Démenagement </h6>
                                    </div>
                                    <div class="modal-body">
                                        <div class="">
                                            <strong>Maison: <em class="text-red"> {{$location['House']["name"]}}</em> </strong> <br>
                                            <strong>Chambre: <em class="text-red"> {{$location['Room']['number']}} </em> </strong> <br>
                                            <strong>Locataire: <em class="text-red"> {{$location['Locataire']['name']}} {{$location['Locataire']['prenom']}}</em> </strong>
                                        </div>
                                    </div>
                                    <form action="{{route('location.DemenageLocation',crypId($location['id']))}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce p-3">
                                        @csrf
                                        <div class="p-2">
                                            <textarea name="move_comments" required class="form-control" placeholder="Donner une raison justifiant ce déménagement"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-sm bg-red"><i class="bi bi-check-all"></i> Valider</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ###### MODEL DE SHOW DES FACTURES ###### -->
                        <div class="modal fade" id="shoFactures_{{$location['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Factures </h6>
                                    </div>
                                    <div class="modal-body">
                                        <div class="">
                                            <strong>Maison: <em class="text-red"> {{$location['House']["name"]}}</em> </strong> <br>
                                            <strong>Chambre: <em class="text-red"> {{$location['Room']['number']}} </em> </strong> <br>
                                            <strong>Locataire: <em class="text-red"> {{$location['Locataire']['name']}} {{$location['Locataire']['prenom']}}</em> </strong>
                                        </div>
                                        <div>
                                            <ul class="list-group">
                                                @foreach($location->Factures as $facture)
                                                <li class="list-group-item mb-3 "> <strong>Code :</strong> {{$facture->facture_code}};
                                                    <strong>Statut :</strong> <span class="@if($facture->status==2) bg-success @elseif($facture->status==3 || $facture->status==4)  bg-danger @else bg-warning @endif">{{$facture->Status->name}} </span> ;
                                                    <strong>Montant: </strong> {{$facture->amount}};
                                                    <strong>Fichier: </strong> <a href="{{$facture->facture}}" class="btn btn-sm btn-light" target="_blank" rel="noopener noreferrer"><i class="bi bi-eye"></i></a>;
                                                    <strong>Date d'écheance: </strong> {{Change_date_to_text($facture->echeance_date)}};
                                                    <strong>Description: </strong> <textarea class="form-control" name="" rows="1" placeholder="{{$facture->comments}}" id=""></textarea> ;
                                                    <strong>Traitement: </strong><br>
                                                    <form action="{{route('location.UpdateFactureStatus',crypId($facture->status))}}" method="post">
                                                        @csrf
                                                        <select required name="status" class="form-select form-control" aria-label="Default select example">
                                                            @foreach($factures_status as $status)
                                                            <option value="{{$status['id']}}" @if($status['id']==$facture->id) selected @endif>{{$status["name"]}}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-sm bg-red"> <i class="bi bi-check-all"></i> Traiter</button>
                                                    </form>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @if(count($location->Factures)==0)
                                            <p class="text-center text-red">Aucune facture disponible</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ###### MODEL DE MODIFICATION ###### -->
                        <div class="modal fade" id="updateModal_{{$location['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Modification </h6>
                                    </div>
                                    <div class="modal-body">
                                        <div class="">
                                            <strong>Maison: <em class="text-red"> {{$location['House']["name"]}}</em> </strong> <br>
                                            <strong>Chambre: <em class="text-red"> {{$location['Room']['number']}} </em> </strong> <br>
                                            <strong>Locataire: <em class="text-red"> {{$location['Locataire']['name']}} {{$location['Locataire']['prenom']}}</em> </strong>
                                        </div>
                                        <form action="{{route('location.UpdateLocation',crypId($location->id))}}" method="POST" class="shadow-lg">
                                            @csrf
                                            @method("PATCH")
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="" class="d-block">Maison</label>
                                                        <select class="form-select form-control" name="house" aria-label="Default select example">
                                                            @foreach($houses as $house)
                                                            <option value="{{$house['id']}}" @if($location->house==$house['id']) selected @endif>{{$house['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>

                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Chambre</label>
                                                        <select class="form-select form-control" name="room" aria-label="Default select example">
                                                            @foreach($rooms as $room)
                                                            <option value="{{$room['id']}}" @if($location->room==$room['id']) selected @endif>{{$room['number']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>

                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Locataire</label>
                                                        <select class="form-select form-control" name="locataire" aria-label="Default select example">
                                                            @foreach($locators as $locator)
                                                            <option value="{{$locator['id']}}" @if($location->locataire==$locator['id']) selected @endif>{{$locator['name']}} {{$locator['prenom']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Type</label>
                                                        <select class="form-select form-control" name="type">
                                                            @foreach($location_types as $type)
                                                            <option value="{{$type['id']}}" @if($location->type==$type['id']) selected @endif>{{$type['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <div class="mb-3">
                                                        <span>Uploader le bordereau du caution</span><br>
                                                        <input type="file" name="caution_bordereau" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Caution d'électricité</label>
                                                        <input value="{{$location->caution_electric}}" type="text" name="caution_electric" class="form-control" placeholder="Caution d'électricité...">

                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Numéro du compteur eau ...</label>
                                                        <input value="{{$location->water_counter}}" type="text" name="water_counter" placeholder="Compteur eau..." class="form-control">
                                                    </div><br>

                                                </div>
                                                <!--  -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <span>Uploader le contrat</span><br>
                                                        <input type="file" name="img_contrat" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Caution eau</label>
                                                        <input value="{{$location->caution_water}}" type="text" name="caution_water" class="form-control" placeholder="Caution eau ....">

                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Numéro du compteur électrique</label>
                                                        <input value="{{$location->electric_counter}}" type="text" name="electric_counter" class="form-control" placeholder="Compteur électricité ....">
                                                    </div><br>

                                                    <div class="mb-3">
                                                        <span>Uploader l'image de la prestation</span><br>
                                                        <input type="file" name="img_prestation" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Nbr de caution loyer</label>
                                                        <input value="{{$location->caution_number}}" type="number" name="caution_number" class="form-control" placeholder="Nombre de caution loyer ....">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <span>Frais de reprise de peinture</span><br>
                                                        <input value="{{$location->frais_peiture}}" type="text" name="frais_peiture" class="form-control" placeholder="Frais de reprise de peinture ....">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block" for="">Prestation</label>
                                                        <input value="{{$location->prestation}}" type="number" name="prestation" placeholder="La prestation..." class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <label class="d-block">Numéro contrat</label>
                                                        <input value="{{$location->numero_contrat}}" type="text" name="numero_contrat" placeholder="Numéro du contrat..." class="form-control">
                                                    </div><br>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-sm bg-red">Modifier</button>
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
    @endif



    <script type="text/javascript">
        $(document).ready(function() {
            // locationSelection($('#location_selected').val())
            houseSelect($('#houseSelection').val())
        })

        function locationSelection(val = null) {
            var locationSelected = val ? val : $('#location_selected').val()

            $('#loading').removeAttr('hidden');

            axios.get("{{env('API_BASE_URL')}}location/" + locationSelected + "/retrieve").then((response) => {
                var location = response.data
                var location_locataire = location["locataire"]

                $("#encaisse_date_info").removeAttr("hidden")
                $("#encaisse_date").val(location.next_loyer_date)

                // alert(location_locataire)
                if (location_locataire.prorata) {
                    $("#prorata_infos").removeAttr("hidden")
                    $("#prorata_date").removeAttr("hidden")
                    $("#prorata_date").val(location_locataire.prorata_date)
                }
            }).catch(() => {
                alert("une erreure s'est produite")
            })
        }


        function houseSelect(_val = null) {
            var houseSelected = _val ? _val : $('#houseSelection').val()
            $('#rooms').empty();

            $('#loading').removeAttr('hidden');

            axios.get("{{env('API_BASE_URL')}}house/" + houseSelected + "/retrieve").then((response) => {
                // alert("gogo "+houseSelected)
                var house_rooms = response.data["rooms"];
                for (var i = 0; i < house_rooms.length; i++) {
                    var val = house_rooms[i].id;
                    var text = house_rooms[i].number;
                    $('#rooms').append("<option value=" + val + ">" + text + "</option>");
                }

                $('#roomsShow').removeAttr("hidden");
                $('#loading').attr("hidden", "hidden");

            }).catch(() => {
                alert("une erreure s'est produite")
            })
        }

        function discounterClick_fun() {
            var value = $('#discounter')[0].checked
            if (value) {
                $('#show_discounter_info').removeAttr('hidden');
            } else {
                $('#show_discounter_info').attr("hidden", "hidden");
            }
        }
    </script>
</div>