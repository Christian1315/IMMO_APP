<div>
    @can("house.add.type")
    <!-- AJOUT D'UN TYPE DE CHAMBRE -->
    <div class="text-left">
        <button type="button" class="btn btn btn-sm bg-light shadow roundered" data-bs-toggle="modal"
            data-bs-target="#room_type">
            <i class="bi bi-cloud-plus-fill"></i>Ajouter un type de maison
        </button>
    </div>
    <br>
    <!-- Modal room type-->
    <div class="modal fade" id="room_type" aria-labelledby="room_type" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">Type de maison</h5>
                    <button type="button" class="btn-close btn btn-sm btn-light text-red" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x-circle"></i></button>
                </div>
                <form action="{{ route('house.AddHouseType') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="text" required value="{{ old('name') }}" name="name"
                                        placeholder="Le label ...." class="form-control">
                                    @error('house_type_name')
                                    <span class="text-red">{{ $message }}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <textarea required value="{{ old('description') }}" name="description" class="form-control"
                                        placeholder="Description ...."></textarea>
                                    @error('house_type_description')
                                    <span class="text-red">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="w-100 btn-sm btn bg-red"><i class="bi bi-building-check"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @can("house.create")
    <div>
        <div class="d-flex header-bar">
            <h2 class="accordion-header">
                <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#addHouse">
                    <i class="bi bi-cloud-plus-fill"></i> Ajouter
                </button>
            </h2>
        </div>
    </div>

    <!-- ADD HOUSE -->
    <div class="modal fade" id="addHouse" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'une maison </p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i
                            class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('house._AddHouse') }}" method="POST"
                        class="shadow-lg p-3 animate__animated animate__bounce" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="agency" value="{{ $current_agency->id }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Nom</label>
                                    <input type="text" value="{{ old('name') }}" name="name"
                                        placeholder="Nom de la maison" class="form-control">
                                    @error('name')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Latitude</label>
                                    <input type="text" value="{{ old('latitude') }}" name="latitude"
                                        placeholder="Latitude de la maison" class="form-control">
                                    @error('latitude')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Longitude</label>
                                    <input type="text" value="{{ old('longitude') }}" name="longitude"
                                        placeholder="Longitude de la maison" class="form-control">
                                    @error('longitude')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Type</label>
                                    <select class="form-select form-control" name="type"
                                        aria-label="Default select example">
                                        @foreach ($house_types as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Pays</label>
                                    <select class="form-select form-control" value="{{ old('country') }}"
                                        name="country" aria-label="Default select example">
                                        @foreach ($countries as $countrie)
                                        @if ($countrie['id'] == 4)
                                        <option value="{{ $countrie['id'] }}">{{ $countrie['name'] }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('country')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Département</label>
                                    <select class="form-select form-control" value="{{ old('departement') }}"
                                        name="departement" aria-label="Default select example">
                                        @foreach ($departements as $departement)
                                        <option value="{{ $departement['id'] }}">{{ $departement['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('departement')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>

                                <div class="mb-3">
                                    <span class="text-red"><i class="bi bi-geo-fill"></i> Géolocalisation de la
                                        maison</span>
                                    <input type="text" value="{{ old('geolocalisation') }}"
                                        name="geolocalisation" class="form-control"
                                        placeholder="Entrez le lien de géolocalisation de la maison">
                                    @error('geolocalisation')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Une image de la maison</label>
                                    <input type="file" value="{{ old('image') }}" name="image"
                                        class="form-control">
                                    @error('image')
                                    <span class="text-red">{{ $message }}</span>
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
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Ville/Commune</label>
                                    <select class="form-select form-control" value="{{ old('city') }}"
                                        name="city" aria-label="Default select example">
                                        @foreach ($cities as $citie)
                                        @if ($citie['_country']['id'] == 4)
                                        <option value="{{ $citie['id'] }}">{{ $citie['name'] }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error('city')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Quartier</label>
                                    <select class="form-select form-control" value="{{ old('quartier') }}"
                                        name="quartier" aria-label="Default select example">
                                        @foreach ($quartiers as $quartier)
                                        <option value="{{ $quartier['id'] }}">{{ $quartier['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('quartier')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Zone</label>
                                    <select class="form-select form-control" value="{{ old('zone') }}"
                                        name="zone" aria-label="Default select example">
                                        @foreach ($zones as $zone)
                                        <option value="{{ $zone['id'] }}">{{ $zone['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('zone')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Superviseur</label>
                                    <select class="form-select form-control" value="{{ old('supervisor') }}"
                                        name="supervisor" aria-label="Default select example">
                                        @foreach (supervisors() as $supervisor)
                                        <option value="{{ $supervisor['id'] }}">{{ $supervisor['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Propriétaire</label>
                                    <select class="form-select form-control" value="{{ old('proprietor') }}"
                                        name="proprietor" aria-label="Default select example">
                                        @foreach ($proprietors as $proprietor)
                                        <option value="{{ $proprietor['id'] }}">{{ $proprietor['lastname'] }}
                                            {{ $proprietor['firstname'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('proprietor')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Commentaire</label>
                                    <textarea name="comments" value="{{ old('comments') }}" class="form-control"
                                        placeholder="Laissez un commentaire ici" class="form-control" id=""></textarea>
                                    @error('comments')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span>Date d'échéance de paiement du propriétaire</span>
                                    <input value="{{ old('proprio_payement_echeance_date') }}" type="date"
                                        name="proprio_payement_echeance_date" class="form-control" id="">
                                    @error('proprio_payement_echeance_date')
                                    <span class="text-red"> {{ $message }} </span>
                                    @enderror
                                </div>
                                <div class="">
                                    <label for="locative_commission">Commision charge locatives en (%) </label>
                                    <input type="number" name="locative_commission" class="form-control" />
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="w-100 btn-sm btn bg-red"><i class="bi bi-check-circle-fill"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- ### FILTRE ###-->
    <small class="d-block">
        <button data-bs-toggle="modal" data-bs-target="#filtreBySupervisor" class="btn btn-sm bg-light text-dark text-uppercase"><i class="bi bi-funnel"></i> Filtrer par superviseur</button>
        <button data-bs-toggle="modal" data-bs-target="#filtreByPeriod" class="btn mx-2 btn-sm bg-light text-dark text-uppercase"><i class="bi bi-funnel"></i> Filtrer par période</button>
    </small>

    <!-- FILTRE PAR SUPERVISEUR -->
    <div class="modal fade" id="filtreBySupervisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filter par superviseur</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('house.FiltreHouseBySupervisor',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez un superviseur</label>
                                <select required name="supervisor" class="form-control">
                                    @foreach(supervisors() as $supervisor)
                                    <option value="{{$supervisor['id']}}"> {{$supervisor["name"]}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="w-100 btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTRE PAR PERIOD -->
    <div class="modal fade" id="filtreByPeriod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filter par période</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('house.FiltreHouseByPeriode',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label for="">Date de debut</label>
                                <input type="date" required name="debut" class="form-control">
                            </div>
                            <div class="col-6">
                                <label for="">Date de fin</label>
                                <input type="date" required name="fin" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="w-100 btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br><br>

    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <h4 class="">Total: <strong class="text-red"> {{session('filteredHouses')?count(session('filteredHouses')): $houses_count }} </strong> </h4>
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
                            <th class="text-center">Date paiement</th>
                            <th class="text-center">Date création</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('filteredHouses')?session('filteredHouses'):$houses as $house)
                        <tr class="align-items-center">
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td class="text-center"> <span class="badge bg-light text-dark"> {{ $house['name'] }} (<span class="text-red"> {{$house->pre_paid == 1 ? 'prépayé' :''}} {{$house->post_paid == 1 ? 'postpayé':''}} ) </span></span></td>
                            <td class="text-center">
                                @if ($house['latitude'])
                                {{ $house['latitude'] }}
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($house['longitude'])
                                {{ $house['longitude'] }}
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center">{{ $house['Type']['name'] }}</td>
                            <td class="text-center"><button class="btn btn-sm btn-light"> {{ $house['Supervisor']['name'] }}</button></td>
                            <td class="text-center"><button class="btn btn-sm btn-light"> {{ $house['Proprietor']['lastname'] }} {{ $house['Proprietor']['firstname'] }}</button></td>
                            <td class="text-center">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#showRooms"
                                    onclick="show_rooms_fun({{ $house['id'] }})" class="btn btn-sm bg-warning">
                                    <i class="bi bi-eye-fill"></i> &nbsp; Voir
                                </button>
                            </td>

                            <td class="text-center">
                                @if ($house['geolocalisation'])
                                <a title="Voir la localisation" target="_blank"
                                    href="{{ $house['geolocalisation'] }}"
                                    class="btn btn-sm shadow-lg roundered" rel="noopener noreferrer"><i
                                        class="bi bi-eye-fill"></i> <i class="bi bi-geo-fill"></i></a>
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center text-red"><button class="btn btn-sm btn-light"> <i class="bi bi-calendar2-check-fill"></i> {{ \Carbon\Carbon::parse($house->proprio_payement_echeance_date)->locale('fr')->isoFormat('D MMMM YYYY') }}</button> </td>
                            <td class="text-center text-red"><button class="btn btn-sm btn-light"> <i class="bi bi-calendar2-check-fill"></i> {{ \Carbon\Carbon::parse($house->created_at)->locale('fr')->isoFormat('D MMMM YYYY') }}</button> </td>
                            <td class="text-center">
                                <div class="btn-group dropstart">
                                    <button class="btn btn-sm bg-red dropdown-toggle text-uppercase"
                                        style="z-index: 0;" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-kanban-fill"></i> &nbsp; Gérer
                                    </button>
                                    <ul class="dropdown-menu p-2">
                                        @can("house.delete")
                                        <li>
                                            <a href="{{ route('house.DeleteHouse', crypId($house['id'])) }}"
                                                data-confirm-delete="true" class="w-100 btn btn-sm bg-red"><i
                                                    class="bi bi-archive-fill"></i> Supprimer</a>
                                        </li>
                                        @endcan

                                        @can("house.edit")
                                        <li>
                                            <button class="w-100 btn btn-sm bg-warning" data-bs-toggle="modal"
                                                data-bs-target="#updateModal"
                                                onclick="updateModal_fun({{ $house['id'] }})"><i
                                                    class="bi bi-person-lines-fill"></i> Modifier</button>
                                        </li>
                                        @endcan

                                        @can("house.stop.state")
                                        <li>
                                            <a href="/house/{{ crypId($house['id']) }}/{{ crypId($current_agency['id']) }}/stopHouseState"
                                                class="w-100 btn btn-sm bg-warning text-dark"><i
                                                    class="bi bi-sign-stop-fill"></i>&nbsp; Arrêter les
                                                états</a>
                                        </li>
                                        @endcan

                                        @can("house.generate.caution")
                                        <li>
                                            <button class="w-100 btn btn-sm bg-light" data-bs-toggle="modal"
                                                data-bs-target="#cautionModal"
                                                onclick="cautionModal_fun({{ $house['id'] }})"><i
                                                    class="bi bi-file-earmark-pdf-fill"></i> Gestion des cautions
                                            </button>
                                        </li>
                                        @endcan
                                        <li>
                                            <a title="Voir l'image" href="{{ $house['image'] }}" target="_blank"
                                                class="btn btn-sm shadow-lg roundered w-100"
                                                rel="noopener noreferrer">Image <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- ###### MODEL D'AFFICHAGE DES CHAMBRES ###### -->
    <div class="modal fade" id="showRooms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fs-5" id="exampleModalLabel">Maison: <strong> <em class="text-red"
                                id="house_fullname"> </em> </strong> </h6>
                </div>
                <div class="modal-body">
                    <h6 class="">Total de chambre: <em class="text-red" id="house_rooms_count"> </em> </h6>
                    <ul class="list-group" id="house_rooms">

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ###### MODEL DE MODIFICATION ###### -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fs-5" id="exampleModalLabel">Modifier <strong> <em class="text-red"
                                id="update_house_fullname"> </em> </strong> </h6>
                </div>
                <div class="modal-body">
                    <form id="update_form" method="post" class="p-3 animate__animated animate__bounce">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span>Nom</span>
                                    <input id="name" type="text" name="name" placeholder="Nom ..."
                                        class="form-control">
                                </div><br>
                                <div class="mb-3">
                                    <span class="">Longitude</span>
                                    <input id="longitude" type="text" name="longitude"
                                        placeholder="Longitude ..." class="form-control">
                                </div><br>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span>Latitude</span>
                                    <input id="latitude" type="text" name="latitude" placeholder="Latitude"
                                        class="form-control">
                                </div><br>
                                <div class="mb-3">
                                    <span>Géolocalisation</span>
                                    <input id="geolocalisation" type="text" placeholder="Geolocalisation"
                                        name="geolocalisation" class="form-control">
                                </div><br>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <span>Date d'échéance du propriétaire</span>
                                        <input id="proprio_payement_echeance_date" type="date"
                                            name="proprio_payement_echeance_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <span>Commission (en %)</span>
                                        <input id="commission_percent" type="text" placeholder="Commission"
                                            name="commission_percent" class="form-control">
                                    </div>
                                    <div class="">
                                        <label for="locative_commission">Commision charge locatives en (%) </label>
                                        <input type="number" id="locative_commission" name="locative_commission" class="form-control" />
                                    </div>
                                </div>

                                <div class="mb-3 d-flex paid_blocked d-none">
                                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        <input type="checkbox" name="pre_paid" class="btn-check" id="update_pre_paid" autocomplete="off">
                                        <label class="btn bg-dark text-white" for="update_pre_paid">Prépayé</label>
                                    </div>
                                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        <input type="checkbox" name="post_paid" class="btn-check" id="update_post_paid" autocomplete="off">
                                        <label class="btn bg-dark text-white" for="update_post_paid">Post-Payé</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="w-100 btn btn-sm bg-red"><i class="bi bi-check-circle"></i>
                                Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- #### MODEL DE GESTION DES CAUTIONS -->
    <div class="modal fade" id="cautionModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="">Maison : <em class="text-red" id="caution_house_fullname"> </em> </h6>
                </div>
                <div class="modal-body">
                    <form id="caution_form" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <span>Date de début</span>
                                <input name="first_date" type="date" required name="first_date"
                                    class="form-control" id="">
                            </div>
                            <div class="col-md-6">
                                <span class="">Date de fin</span>
                                <input name="last_date" type="date" required name="last_date"
                                    class="form-control" id="">
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


    <script type="text/javascript">
        function show_rooms_fun(id) {
            $('#house_rooms').empty();

            axios.get("{{ env('API_BASE_URL') }}house/" + id + "/retrieve").then((response) => {
                var house = response.data
                var house_fullname = house["name"];
                var house_rooms = house["rooms"]

                $("#house_fullname").html(house_fullname)
                $("#house_rooms_count").html(house_rooms.length)

                for (var i = 0; i < house_rooms.length; i++) {
                    var number = house_rooms[i].number;
                    var loyer = house_rooms[i].loyer;
                    var total_amount = house_rooms[i].total_amount;
                    $('#house_rooms').append("<li class='list-group-item'><strong>N° :</strong>" + number +
                        ", <strong>Loyer :</strong>" + loyer + ", <strong>Montant total :</strong>" +
                        total_amount + " </li>");
                }
            }).catch((error) => {
                alert("une erreure s'est produite")
                console.log(error)
            })
        }

        function updateModal_fun(id) {

            axios.get("{{ env('API_BASE_URL') }}house/" + id + "/retrieve").then((response) => {
                var house = response.data
                var house_fullname = house["name"];

                $("#update_house_fullname").html(house_fullname)

                $("#name").val(house["name"])
                $("#longitude").val(house["longitude"])
                $("#latitude").val(house["latitude"])
                $("#geolocalisation").val(house["geolocalisation"])
                $("#proprio_payement_echeance_date").val(house["proprio_payement_echeance_date"])
                $("#commission_percent").val(house["commission_percent"])

                $("#locative_commission").val(house["locative_commission"])

                $("#proprio_payement_echeance_date").val(house["proprio_payement_echeance_date"])
                $("#update_form").attr("action", "/house/" + house.id + "/update")


                // les pre_paid & post_paid
                $(".paid_blocked").removeClass("d-none")

                let pre_paid = document.getElementById("update_pre_paid")
                let post_paid = document.getElementById("update_post_paid")

                pre_paid.checked = false
                post_paid.checked = false

                if (house.pre_paid) {
                    pre_paid.checked = true
                }

                if (house.post_paid) {
                    post_paid.checked = true
                }


            }).catch((error) => {
                alert("une erreure s'est produite")
                console.log(error)
            })
        }

        function cautionModal_fun(id) {
            axios.get("{{ env('API_BASE_URL') }}house/" + id + "/retrieve").then((response) => {
                var house = response.data
                var house_fullname = house["name"];

                $("#caution_house_fullname").html(house_fullname)
                $("#caution_form").attr("action", "/house/" + id + "/generate_cautions_for_house_by_period")
                $("#caution_form").attr("method", "POST")
                console.log($("#caution_form"))

            }).catch((error) => {
                alert("une erreure s'est produite")
                console.log(error)
            })
        }
    </script>
</div>