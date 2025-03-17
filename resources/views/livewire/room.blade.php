<div>
    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin)
    <!-- AJOUT D'UN TYPE DE CHAMBRE -->
    <div class="text-left">
        <button type="button" class="btn btn btn-sm bg-light shadow roundered" data-bs-toggle="modal" data-bs-target="#room_type">
            <i class="bi bi-cloud-plus-fill"></i>Ajouter un type de chambre
        </button>
        <button type="button" class="btn btn btn-sm bg-light shadow roundered" data-bs-toggle="modal" data-bs-target="#room_nature">
            <i class="bi bi-cloud-plus-fill"></i>Ajouter une nature de chambre
        </button>
    </div>
    <br>
    @endif
    <!-- Modal room type-->
    <div class="modal fade" id="room_type" aria-labelledby="room_type" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">Type de chambre</h5>
                </div>
                <form action="{{route('room.AddType')}}" method="POST">
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
                            <button class="btn btn-sm bg-red"><i class="bi bi-building-check"></i> Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal room nature-->
    <div class="modal fade" id="room_nature" aria-labelledby="room_nature" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5">Nature de chambre</h5>
                </div>
                <form action="{{route('room.AddRoomNature')}}" method="POST">
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
                        <button class="btn btn-sm bg-red"><i class="bi bi-building-check"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FORM HEADER -->
    @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
    <div class="d-flex header-bar">
        <h2 class="accordion-header">
            <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#addRoom">
                <i class="bi bi-cloud-plus-fill"></i> Ajouter une chambre
            </button>
        </h2>
    </div>
    @endif

    <!-- ### FILTRE ###-->
    <small class="d-block">
        <button data-bs-toggle="modal" data-bs-target="#filtreBySupervisor" class="btn btn-sm bg-light text-dark text-uppercase"><i class="bi bi-funnel"></i> Filtrer par superviseur</button>
        <button data-bs-toggle="modal" data-bs-target="#filtreByHouse" class="btn mx-2 btn-sm bg-light text-dark text-uppercase"><i class="bi bi-funnel"></i> Filtrer par maison</button>
    </small>

    <!-- FILTRE PAR SUPERVISEUR -->
    <div class="modal fade" id="filtreBySupervisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filtre par superviseur</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('room.FiltreRoomBySupervisor',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez un superviseur</label>
                                <select required name="supervisor" class="form-control">
                                    @foreach($supervisors as $supervisor)
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
    <div class="modal fade" id="filtreByHouse" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filtre par maison</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('room.FiltreRoomByHouse',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez une maison</label>
                                <select required name="house" class="form-control">
                                    @foreach($houses as $house)
                                    <option value="{{$house['id']}}"> {{$house["name"]}} </option>
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
    <br><br>

    <!-- ADD ROOM -->
    <div class="modal fade" id="addRoom" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'une Chambre</p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('room._AddRoom')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Loyer</label>
                                    <input type="number" name="loyer" value="{{old('loyer')}}" placeholder="Le loyer" class="form-control">
                                    @error("loyer")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Numéro de chambre</label>
                                    <input type="text" value="{{old('number')}}" name="number" placeholder="Numéro de la chambre" class="form-control">
                                    @error("number")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Gardiennage</label>
                                    <input type="number" value="{{old('gardiennage')}}" name="gardiennage" placeholder="Gardiennage ..." class="form-control">
                                    @error("gardiennage")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Ordures</label>
                                    <input type="number" value="{{old('rubbish')}}" name="rubbish" placeholder="Les ordures ..." class="form-control">
                                    @error("rubbish")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Vidange</label>
                                    <input type="number" value="{{old('vidange')}}" name="vidange" placeholder="La vidange ..." class="form-control">
                                    @error("vidange")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span class=""> Photo de la chambre </span>
                                    <input value="{{old('photo')}}" type="file" name="photo" class="form-control">
                                    @error("photo")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>

                                <div class="mb-3">
                                    <input onclick="showWaterInfo_fun();" type="checkbox" name="water" class="btn-check" id="showWaterInfo">
                                    <label class="btn bg-dark" for="showWaterInfo">
                                        Eau ... <br>
                                    </label>
                                </div><br>

                                <div class="water shadow-lg roundered p-2" id="show_water_info">
                                    <div class="form-check">
                                        <input onclick="waterDiscounterInputs_fun()" name="water_discounter" class="form-check-input" type="checkbox" id="water_discounter">
                                        <label for="water_discounter" class="form-check-label">
                                            Décompteur
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input onclick="showWaterConventionnalCounterInputs_fun()" name="water_conventionnal_counter" class="form-check-input" type="checkbox" id="showWaterConventionnalCounterInputs">
                                        <label for="showWaterConventionnalCounterInputs" class="form-check-label" for="flexCheckChecked">
                                            Compteur Conventionnel
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input onclick="show_forage_inputs_fun()" name="forage" class="form-check-input" type="checkbox" id="forage">
                                        <label class="form-check-label" for="forage">
                                            Forage
                                        </label>
                                    </div>

                                    <div class="mb-3" hidden id="show_forage_inputs">
                                        <span for="" class="d-block">Forfait forage</span>
                                        <input type="number" value="{{old('forfait_forage')}}" name="forfait_forage" placeholder="Forfait forage" class="form-control" id="forfait_forage">
                                        @error("forfait_forage")
                                        <span class="text-red">{{$message}}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3" id="water_discounter_inputs" hidden>
                                        <span for="" class="d-block">Prix unitaire par mêtre cube</span>
                                        <input value="{{old('unit_price')}}" type="text" name="unit_price" placeholder="Prix unitaire en mèttre cube" class="form-control" id="unit_price">
                                        @error("unit_price")
                                        <span class="text-red">{{$message}}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3" id="show_water_conventionnal_counter_inputs" hidden>
                                        <span for="" class="d-block">Numéro du compteur</span>
                                        <input value="{{old('water_counter_number')}}" type="text" name="water_counter_number" placeholder="Numéro compteur" class="form-control" id="water_counter_number">
                                        @error("water_counter_number")
                                        <span class="text-red">{{$message}}</span>
                                        @enderror

                                        <div class="">
                                            <span for="" class="d-block">Index du compteur d'eau</span>
                                            <input value="{{old('water_counter_start_index')?old('water_counter_start_index'):0}}" type="text" name="water_counter_start_index" placeholder="Index début ...." class="form-control" id="water_counter_start_index">
                                            @error("water_counter_start_index")
                                            <span class="text-red">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Nettoyage</label>
                                    <input type="text" value="{{old('cleaning')}}" name="cleaning" placeholder="Le nettoyage ..." class="form-control">
                                    @error("cleaning")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Commentaire</label>
                                    <textarea value="{{old('comments')}}" name="comments" rows="1" placeholder="Laisser un commentaire ..." class="form-control" class="form-control" id=""></textarea>
                                    @error("comments")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Maison</label>
                                    <select value="{{old('house')}}" class="form-select form-control" name="house" aria-label="Default select example">
                                        @foreach($houses as $house)
                                        <option value="{{$house['id']}}">{{$house['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("house")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Nature</label>
                                    <select value="{{old('nature')}}" class="form-select form-control" name="nature" aria-label="Default select example">
                                        @foreach($room_natures as $nature)
                                        <option value="{{$nature['id']}}">{{$nature['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("nature")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Type</label>
                                    <select value="{{old('type')}}" class="form-select form-control" name="type" aria-label="Default select example">
                                        @foreach($room_types as $type)
                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("nature")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>

                                <div class="mb-3">
                                    <input onclick="showElectricityInfo_fun()" type="checkbox" name="electricity" class="btn-check" id="btncheck_electricity">
                                    <label class="btn bg-dark" for="btncheck_electricity">
                                        Electricité ... <br>
                                    </label>
                                </div><br>

                                <div class="electricity shadow-lg roundered p-2" id="showElectricityInfo_block" hidden>
                                    <div class="form-check">
                                        <input onclick="showElectricityDiscountInputs_fun()" name="electricity_discounter" class="form-check-input" type="checkbox" id="electricity_decounter_flexCheckChecked">
                                        <label for="electricity_decounter_flexCheckChecked" class="form-check-label" for="electricity_decounter_flexCheckChecked">
                                            Décompteur
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="electricity_card_counter" class="form-check-input" type="checkbox" id="electricity_card_flexCheckDefault">
                                        <label class="form-check-label" for="electricity_card_flexCheckDefault">
                                            Compteur à carte
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input name="electricity_conventionnal_counter" class="form-check-input" type="checkbox" id="electricity_card_conven_flexCheckChecked">
                                        <label class="form-check-label" for="electricity_card_conven_flexCheckChecked">
                                            Compteur Conventionnel
                                        </label>
                                    </div>

                                    <div id="show_electricity_discountInputs" hidden>
                                        <div class="mb-3">
                                            <span for="" class="d-block">Numéro compteur</span>
                                            <input value="{{old('electricity_counter_number')}}" type="text" name="electricity_counter_number" placeholder="Numéro compteur" class="form-control" id="electricity_counter_number">
                                            @error("electricity_counter_number")
                                            <span class="text-red">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <span for="" class="d-block">Prix unitaire</span>
                                            <input value="{{old('electricity_unit_price')}}" type="text" name="electricity_unit_price" placeholder="Prix unitaire par kilowatheure " class="form-control" id="electricity_unit_price">
                                            @error("electricity_unit_price")
                                            <span class="text-red">{{$message}}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <span for="" class="d-block">Index de début</span>
                                            <input value="{{old('electricity_counter_start_index')}}" type="text" name="electricity_counter_start_index" placeholder="Index début ...." class="form-control" id="electricity_counter_start_index">
                                            @error("electricity_counter_start_index")
                                            <span class="text-red">{{$message}}</span>
                                            @enderror
                                        </div>
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

    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <?php
            $rooms = session('filteredRooms') ? session('filteredRooms') : $rooms;
            $buzy_rooms_count = count(
                collect($rooms)->filter(function ($room) {
                    if ($room->buzzy()) {
                        return $room->locations;
                    }
                })
            );
            $free_rooms_count = count($rooms) - $buzy_rooms_count;; ?>
            <h4 class="">Total: <strong class="text-red"> {{count($rooms)}} </strong> | Légende: <button class="btn btn-sm bg-light border border-dark">Libre <i class="bi text-primary bi-geo-alt-fill"></i>; Occupée: <i class="bi text-red bi-geo-alt-fill"></i></button> | <button class="btn btn-sm btn-light"> Nombres de Chambres Occupées: <strong class="text-red">{{$buzy_rooms_count}}</strong> </button>| <button class="btn btn-sm btn-light"> Nombres de Chambres Libres: <strong class="text-primary">{{$free_rooms_count}}</strong> </button> </h4>
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Chambre</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Superviseur</th>
                            <th class="text-center">Loyer brut</th>
                            <th class="text-center">Charge locatives</th>
                            <!-- <th class="text-center">Image</th> -->
                            <th class="text-center">Loyer Total</th>
                            <th class="text-center">Type de Chambre</th>
                            <th class="text-center">Locataires</th>
                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr class="align-items-center">
                            <td class="text-center d-flex">{{$loop->index + 1}} </td>
                            <td class="text-center">{{$room["number"]}} @if($room->buzzy())<i class="bi text-red bi-geo-alt-fill"></i> @else<i class="bi text-primary bi-geo-alt-fill"></i> @endif</td>
                            <td class="text-center">{{$room["House"]["name"]}}</td>
                            <td class="text-center">{{$room["House"]["Supervisor"]["name"]}}</td>
                            <td class="text-center"><span class="badge bg-dark">{{number_format($room["loyer"],2," "," ")}} </span> </td>
                            <td class="text-center"><span class="badge bg-warning">{{number_format($room->LocativeCharge(),2," "," ")}} </span> </td>
                            <!-- <td class="text-center"><a href="{{$room['photo']}}" class="btn btn-sm btn-light" rel="noopener noreferrer"><i class="bi bi-eye"></i></a> -->
                            <td class="text-center"> <button class="btn btn-sm btn-light text-red">{{number_format($room["total_amount"],2," "," ")}} fcfa </button> </td>
                            <td class="text-center">{{$room["Type"]['name']}}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#showLocators" onclick="showLocators_fun({{$room['id']}})">
                                    <i class="bi bi-eye-fill"></i> &nbsp; Voir
                                </button>
                            </td>
                            @if(IS_USER_HAS_MASTER_ROLE(auth()->user()) || auth()->user()->is_master || auth()->user()->is_admin || IS_USER_HAS_SUPERVISOR_ROLE(auth()->user()))
                            <td class="text-center d-flex">
                                <div class="dropdown">
                                    <button class="btn btn-sm bg-red dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-text-paragraph"></i> Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="w-100 dropdown-item btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateModal_fun({{$room['id']}})"><i class="bi bi-person-lines-fill"></i> Modifier</a></li>
                                        <li><a href="{{ route('room.DeleteRoom', crypId($room['id']))}}" class="w-100 dropdown-item btn btn-sm bg-red" data-confirm-delete="true"><i class="bi bi-archive-fill"></i>Supprimer</a></li>
                                        <li><a href="{{$room['photo']}}" class="w-100 dropdown-item btn btn-sm btn-light" rel="noopener noreferrer">Image <i class="bi bi-eye"></i></a></li>
                                    </ul>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ###### MODEL DE LOCATAIRE -->
    <div class="modal fade" id="showLocators" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="text-center">Les Locataires de la chambre : <strong class="text-red" id="room_number"> </strong> </h5>
                </div>
                <div class="modal-body">
                    <h6 class="">Total de locataire: <em class="text-red" id="room_locators_count"></em> </h6>
                    <ul class="list-group" id="room_locators">

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
                    <h6 class="modal-title fs-5" id="exampleModalLabel">Modifier <strong> <em class="text-red" id="update_room_fullname"></em> </strong> </h6>
                </div>
                <div class="modal-body">
                    <form id="update_form" method="POST" class="shadow-lg p-3 animate__animated animate__bounce" enctype="multipart/form-data">
                        @csrf
                        @method("PATCH")
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Loyer</label>
                                    <input type="text" name="loyer" id="loyer" placeholder="Le loyer" class="form-control">

                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Numéro de chambre</label>
                                    <input type="text" id="number" name="number" placeholder="Numéro de la chambre" class="form-control">

                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Gardiennage</label>
                                    <input type="text" id="gardiennage" name="gardiennage" placeholder="Gardiennage ..." class="form-control">

                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Ordures</label>
                                    <input type="text" id="rubbish" name="rubbish" placeholder="Les ordures ..." class="form-control">
                                </div><br>

                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Nettoyage</label>
                                    <input type="text" id="cleaning" name="cleaning" placeholder="Le nettoyage ..." class="form-control">

                                </div><br>
                                <div id="">
                                    <div class="mb-3">
                                        <span for="" class="d-block">Numéro compteur</span>
                                        <input id="electricity_counter_number" type="text" name="electricity_counter_number" placeholder="Numéro compteur" class="form-control" id="">
                                    </div>
                                </div><br>

                                <div class="mb-3">
                                    <label for="" class="d-block">Vidange</label>
                                    <input type="text" id="vidange" name="vidange" placeholder="La vidange ..." class="form-control">

                                </div><br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm bg-red"><i class="bi bi-check-circle"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function showLocators_fun(id) {
            $("#room_locators").empty()
            axios.get("{{env('API_BASE_URL')}}room/" + id + "/retrieve").then((response) => {
                var room = response.data

                var room_number = room["number"];
                var room_locators = room["locataires"]

                $("#room_number").html(room_number)
                $("#room_locators_count").html(room_locators.length)

                for (var i = 0; i < room_locators.length; i++) {
                    var locator_fullname = room_locators[i].name + " " + room_locators[i].prenom;
                    $('#room_locators').append("<li class='list-group-item'>" + locator_fullname + "</li>");
                }
            }).catch((error) => {
                alert("une erreure s'est produite")
                console.log(error)
            })
        }

        function updateModal_fun(id) {
            axios.get("{{env('API_BASE_URL')}}room/" + id + "/retrieve").then((response) => {
                var room = response.data
                var room_fullname = room["number"];

                $("#update_room_fullname").html(room_fullname)

                $("#loyer").val(room["loyer"])
                $("#number").val(room["number"])
                $("#gardiennage").val(room["gardiennage"])
                $("#rubbish").val(room["rubbish"])
                $("#vidange").val(room["vidange"])
                $("#forfait_forage").val(room["forfait_forage"])
                $("#unit_price").val(room["unit_price"])

                $("#water_counter_number").val(room["water_counter_number"])
                $("#water_counter_start_index").val(room["water_counter_start_index"])
                $("#cleaning").val(room["cleaning"])
                $("#comments").val(room["comments"])
                $("#electricity_counter_number").val(room["electricity_counter_number"])

                $("#electricity_unit_price").val(room["electricity_unit_price"])
                $("#electricity_counter_start_index").val(room["electricity_counter_start_index"])
                $("#update_form").attr("action", "/room/" + room.id + "/update")

            }).catch((error) => {
                alert("une erreure s'est produite")
                console.log(error)
            })
        }

        $(document).ready(function() {
            $("#showWaterInfo").click();
        })

        // WATER
        function showWaterInfo_fun() {
            var value = $('#showWaterInfo')[0].checked
            if (value) {
                $('#show_water_info').removeAttr('hidden');
            } else {
                $('#show_water_info').attr("hidden", "hidden");

                //  on annule tous les champs relatifs à l'eau
                document.getElementById("water_discounter").checked = false;
                document.getElementById("showWaterConventionnalCounterInputs").checked = false;
                document.getElementById("forage").checked = false;

                $("#unit_price").val(null)
                $("#forfait_forage").val(null)
                $("#water_counter_number").val(null)
                $("#water_counter_start_index").val(null)
            }
        }

        function waterDiscounterInputs_fun() {
            var value = $('#water_discounter')[0].checked
            if (value) {
                $('#water_discounter_inputs').removeAttr('hidden');
            } else {
                $('#water_discounter_inputs').attr("hidden", "hidden");
                $("#unit_price").val(null)
            }
        }

        function showWaterConventionnalCounterInputs_fun() {
            var value = $('#showWaterConventionnalCounterInputs')[0].checked
            if (value) {
                $('#show_water_conventionnal_counter_inputs').removeAttr('hidden');
            } else {
                $('#show_water_conventionnal_counter_inputs').attr("hidden", "hidden");
                $("#water_counter_start_index").val(null)
                $("#water_counter_number").val(null)
            }
        }

        function show_forage_inputs_fun() {
            var value = $('#forage')[0].checked
            if (value) {
                $('#show_forage_inputs').removeAttr('hidden');
            } else {
                $('#show_forage_inputs').attr("hidden", "hidden");
                $("#forfait_forage").val(null)
            }
        }

        // ELECTRICITY
        function showElectricityInfo_fun() {
            var value = $('#btncheck_electricity')[0].checked
            if (value) {
                $('#showElectricityInfo_block').removeAttr('hidden');
                // $('#show_water_info').attr("hidden", "hidden");
            } else {
                $('#showElectricityInfo_block').attr("hidden", "hidden");

                //  on annule tous les champs relatifs à l'electricité
                document.getElementById("electricity_decounter_flexCheckChecked").checked = false;
                document.getElementById("electricity_card_flexCheckDefault").checked = false;
                document.getElementById("electricity_card_conven_flexCheckChecked").checked = false;

                $("#electricity_unit_price").val(null)
                $("#electricity_counter_number").val(null)
                $("#electricity_counter_start_index").val(null)
            }
        }

        function showElectricityDiscountInputs_fun() {
            var value = $('#electricity_decounter_flexCheckChecked')[0].checked
            if (value) {
                $('#show_electricity_discountInputs').removeAttr('hidden');
            } else {
                $('#show_electricity_discountInputs').attr("hidden", "hidden");
                $("#electricity_unit_price").val(null)
                $("#electricity_counter_number").val(null)
                $("#electricity_counter_start_index").val(null)
            }
        }
    </script>
</div>