<div>
    @can("locator.create")
    <div class="d-flex header-bar">
        <h2 class="accordion-header">
            <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#addLocator">
                <i class="bi bi-cloud-plus-fill"></i> Ajouter
            </button>
        </h2>
    </div>
    @endcan

    <input type="checkbox" hidden class="btn-check" id="displayLocatorsOptions" onclick="displayLocatorsOptions_fun()">
    <label class="btn btn-light" for="displayLocatorsOptions"><i class="bi bi-file-earmark-pdf-fill"></i>Filtrer les locataires</label>

    <div id="display_locators_options" hidden>
        <button class="btn btn-sm bg-light d-block" data-bs-toggle="modal" data-bs-target="#ShowSearchLocatorsBySupervisorForm"><i class="bi bi-people"></i> Par Sperviseur</button>
        <button class="btn btn-sm bg-light d-block" data-bs-toggle="modal" data-bs-target="#ShowSearchLocatorsByHouseForm"><i class="bi bi-house-check-fill"></i> Par Maison</button>
    </div>
    <!-- FILTRE BY SUPERVISOR -->
    <div class="modal fade" id="ShowSearchLocatorsBySupervisorForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filter par superviseur</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('locator.FiltreBySupervisor',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez un superviseur</label>
                                <select required name="supervisor" class="form-control">
                                    @foreach(supervisors() as $supervisor)
                                    <option value="{{$supervisor['id']}}"> {{$supervisor["name"]}} </option>
                                    @endforeach
                                </select>
                                <br>
                                <button type="submit" class="w-100 btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTRE BY HOUSE -->
    <div class="modal fade" id="ShowSearchLocatorsByHouseForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Filter par maison</p>
                </div>
                <div class="modal-body">
                    <form action="{{route('locator.FiltreByHouse',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez une maison</label>
                                <select required name="house" class="form-control">
                                    @foreach($current_agency->_Houses as $house)
                                    <option value="{{$house['id']}}"> {{$house["name"]}} </option>
                                    @endforeach
                                </select>
                                <br>
                                <button type="submit" class="w-100 btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @can("locator.create")
    <!-- ADD ROOM -->
    <div class="modal fade" id="addLocator" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'un Locataire</p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('locator._AddLocataire')}}" method="POST" class="shadow-lg p-3 animate__animated animate__bounce" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Name</label>
                                    <input type="text" value="{{old('name')}}" name="name" placeholder="Nom ..." class="form-control">
                                    @error("name")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Prénom</label>
                                    <input type="text" value="{{old('prenom')}}" name="prenom" placeholder="Prénom ..." class="form-control">
                                    @error("prenom")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Email</label>
                                    <input type="email" value="{{old('email')}}" name="email" placeholder="Email..." class="form-control">
                                    @error("email")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <select value="{{old('sexe')}}" class="form-select form-control" name="sexe" aria-label="Default select example">
                                    <option value="Maxculin">Maxculin</option>
                                    <option value="Feminin">Feminin</option>
                                </select>
                                @error("sexe")
                                <span class="text-red">{{$message}}</span>
                                @enderror
                                <br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Phone</label>
                                    <input value="{{old('phone')}}" type="phone" name="phone" placeholder="Téléphone ..." class="form-control">
                                    @error("phone")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <!--  -->
                                <div class="mb-3">
                                    <label for="" class="d-block">Id Carte</label>
                                    <input value="{{old('card_id')}}" type="text" name="card_id" class="form-control" placeholder="ID de la carte ....">
                                    @error("card_id")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span>Télécharger le contrat de location</span> <br>
                                    <input value="{{old('mandate_contrat')}}" type="file" name="mandate_contrat" class="form-control">
                                    @error("mandate_contrat")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <div class="btn-group">
                                        <input type="checkbox" onclick="prorataClick_fun();" name="prorata" class="btn-check" id="prorata">
                                        <label class="btn bg-dark text-white" for="prorata">Prorata?</label>
                                    </div>
                                </div><br>
                                <div class="water shadow-lg roundered p-2" id="show_prorata_info" hidden>
                                    <div class="form-check">
                                        <span>Date du Prorata</span>
                                        <input value="{{old('prorata_date')}}" name="prorata_date" id="prorata_date" class="form-control" type="date">
                                        @error("prorata_date")
                                        <span class="text-red">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" name="agency" value="{{$current_agency->id}}" hidden class="form-control">
                                    <input type="text" disabled class="form-control" placeholder="Agence :{{$current_agency['name']}}">
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Adresse</label>
                                    <input value="{{old('adresse')}}" ype="text" name="adresse" class="form-control" placeholder="Adresse ....">
                                    @error("adresse")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>

                                <div class="mb-3">
                                    <label for="" class="d-block">Type</label>
                                    <select value="{{old('card_type')}}" class="form-select form-control" name="card_type" aria-label="Default select example">
                                        @foreach($card_types as $type)
                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("card_type")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Pays</label>
                                    <select value="{{old('country')}}" class="form-select form-control" name="country" aria-label="Default select example">
                                        @foreach($countries as $countrie)
                                        @if($countrie['id']==4)
                                        <option value="{{$countrie['id']}}">{{$countrie['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    @error("country")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Département</label>
                                    <select value="{{old('departement')}}" class="form-select form-control" name="departement" aria-label="Default select example">
                                        @foreach($departements as $departement)
                                        <option value="{{$departement['id']}}">{{$departement['name']}}</option>
                                        @endforeach
                                    </select>
                                    @error("country")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div>
                                <br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Commentaire</label>
                                    <textarea value="{{old('comments')}}" rows="1" name="comments" class="form-control" placeholder="Laissez un commentaire ici" class="form-control" id=""></textarea>
                                    @error("comments")
                                    <span class="text-red">{{$message}}</span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <div class="btn-group">
                                        <input type="checkbox" name="avalisor" class="btn-check" id="avalisor">
                                        <label class="btn bg-dark text-white" for="avalisor">Avaliseur?</label>
                                    </div>
                                </div><br>
                                <div class="d-none p-2" id="show_avalisor_info">
                                    <div class="mb-3">
                                        <label for="" class="d-block">Name</label>
                                        <input type="text" name="ava_name" placeholder="Nom ..." class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="d-block">Prénom</label>
                                        <input type="text" name="ava_prenom" placeholder="Prénom ..." class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="d-block">Phone</label>
                                        <input type="phone" name="ava_phone" placeholder="Phone ..." class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="d-block">Lien parenté</label>
                                        <input type="text" name="ava_parent_link" placeholder="Lien parenté ..." class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn bg-red w-100"><i class="bi bi-check-circle"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <br><br>

    <!-- TABLEAU DE LISTE -->
    <div class="row">
        <div class="col-12">
            <h4 class="">Total: <strong class="text-red"> {{session()->get("locators_filtred")?count(session()->get("locators_filtred")): $locators_count}} </strong> </h4>
            <div class="table-responsive table-responsive-list shadow-lg px-3">
                <table id="myTable" class="table table-striped table-sm">

                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Nom</th>
                            <th class="text-center">Prénom</th>
                            <!-- <th class="text-center">Email</th> -->
                            <!-- <th class="text-center">Pièce ID</th> -->
                            <th class="text-center">Phone</th>
                            <th class="text-center">Avaliseur</th>
                            <th class="text-center">Contrat</th>
                            <th class="text-center">Maisons</th>
                            <th class="text-center">Superviseurs</th>
                            <th class="text-center">Chambres</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach((session()->get("locators_filtred")?session()->get("locators_filtred"):$locators) as $locator)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index + 1}}</td>
                            <td class="text-center"><span class="badge bg-dark">{{$locator["name"]}}</span> </td>
                            <td class="text-center">{{$locator["prenom"]}}</td>
                            <!-- <td class="text-center">{{$locator["email"]}}</td> -->
                            <!-- <td class="text-center">{{$locator["card_id"]}}</td> -->
                            <td class="text-center"><span class="badge text-dark bg-light"> {{$locator["phone"]}}</span> </td>
                            <td class="text-center">
                                @if($locator->avaliseur)
                                <a class='btn btn-sm btn-light shadow' href='#' data-bs-toggle='modal' data-bs-target='#showAvalisor' onclick="showAvalisorModal({{$locator['id']}})"><i class='bi bi-eye-fill'></i></a>
                                @else
                                ---
                                @endif
                            </td>
                            <td class="text-center"><a href="{{$locator['mandate_contrat']}}" class="btn btn-sm btn-light" rel="noopener noreferrer"><i class="bi bi-eye-fill"></i></a>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm bg-light" data-bs-toggle="modal" data-bs-target="#showHouses" onclick="showHouses_fun({{$locator['id']}})">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="form-control w-100" style="height: auto;overflow-y: scroll;height:50px " name="" rows="1" class="form-control" id="">
                                    @if(count($locator->Locations)>0)
                                    <ul class="list-group">
                                        @foreach ($locator->Locations as $location)
                                        <li class=""> <span class="badge bg-dark text-white"> {{ $location->House->Supervisor->name }} </span></li>;
                                        @endforeach
                                    </ul>
                                    @else
                                    ---
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm bg-light" data-bs-toggle="modal" data-bs-target="#showRooms" onclick="showRooms_fun({{$locator['id']}})">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </td>
                            <td class="d-flex">
                                <div class="dropdown">
                                    <button class="btn btn-sm  bg-red dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-gear"></i> Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        @can("locator.edit")
                                        <li><a href="#" class="dropdown-item btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateModal_fun({{$locator->id}})"><i class="bi bi-person-lines-fill"></i> Modifier</a></li>
                                        @endcan
                                        @can("locator.delete")
                                        <li><a href="{{ route('locator.DeleteLocataire', crypId($locator->id)) }}" class="dropdown-item btn btn-sm bg-red" data-confirm-delete="true"><i class="bi bi-archive-fill"></i> &nbsp; Suprimer</a></li>
                                        @endcan
                                        <li><a class="w-100 dropdown-item" href="#">Adresse: {{$locator["adresse"]}}</a></li>
                                        <li><a class="w-100 dropdown-item" href="#">Card ID: {{$locator["card_id"]}}</a></li>
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


    <!-- ###### MODEL DE SHOW AVALISOR ###### -->
    <div class="modal fade" id="showAvalisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Avaliseur du locataire : <strong> <em class="text-red" id="ava_locator_fullmneme"> </em> </strong> </h6>
                </div>
                <h6 class="mx-3">Nom : <strong> <em class="text-red" id="avar_nom"></em> </strong> </h6>
                <h6 class="mx-3">Prénom : <strong> <em class="text-red" id="avar_prenom"></em> </strong> </h6>
                <h6 class="mx-3">Phone : <strong> <em class="text-red" id="avar_phone"></em> </strong> </h6>
                <h6 class="mx-3">Lien parental : <strong> <em class="text-red" id="avar_link"></em> </strong> </h6>
            </div>
        </div>
    </div>

    <!-- ###### MODEL DE SHOW HOUSE ###### -->
    <div class="modal fade" id="showHouses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Liste des maisons du locataire : <strong> <em class="text-red" id="locator_fullname"> </em> </strong> </h6>
                </div>

                <h6 class="mx-3">Total : <strong> <em class="text-red" id="locator_houses_count"></em> </strong> </h6>
                <ul class="list-group" id="locator_houses">

                </ul>
            </div>
        </div>
    </div>

    <!-- ###### MODEL DE SHOW ROOM ###### -->
    <div class="modal fade" id="showRooms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="" id="exampleModalLabel">Liste des chambres du locataire : <strong> <em class="text-red" id="room_locator_fullname"> </em> </strong> </h6>
                </div>
                <h6 class="mx-3">Total : <strong> <em class="text-red" id="locator_rooms_count"></em> </strong> </h6>
                <ul class="list-group" id="locator_rooms">

                </ul>
            </div>
        </div>
    </div>


    @can("locator.edit")
    <!-- ###### MODEL DE MODIFICATION ###### -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fs-5" id="exampleModalLabel">Modifier <strong> <em class="text-red" id="update_locator_fullname"> </em> </strong> </h6>
                </div>
                <div class="modal-body">
                    <form id="update_form" class="shadow-lg p-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Name</label>
                                    <input type="text" id="name" name="name" placeholder="Nom ..." class="form-control">
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Prénom</label>
                                    <input type="text" id="prenom" name="prenom" placeholder="Prénom ..." class="form-control">
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Email</label>
                                    <input type="email" id="email" name="email" placeholder="Email..." class="form-control">
                                </div><br>
                                <select id="sexe" class="form-select form-control" name="sexe" aria-label="Default select example">
                                    <option value="Maxculin">Maxculin</option>
                                    <option value="Feminin">Feminin</option>
                                </select>
                                <br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Phone</label>
                                    <input id="phone" type="phone" name="phone" placeholder="Téléphone ..." class="form-control">
                                </div><br>
                                <!--  -->
                                <div class="mb-3">
                                    <label for="" class="d-block">Id Carte</label>
                                    <input id="card_id" type="text" name="card_id" class="form-control" placeholder="ID de la carte ....">
                                </div><br>
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="d-block">Adresse</label>
                                    <input id="adresse" ype="text" name="adresse" class="form-control" placeholder="Adresse ....">
                                </div><br>

                                <div class="mb-3">
                                    <label for="" class="d-block">Type</label>
                                    <select id="card_type" class="form-select form-control" name="card_type" aria-label="Default select example">
                                        @foreach($card_types as $type)
                                        <option value="{{$type['id']}}">{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Pays</label>
                                    <select id="country" class="form-select form-control" name="country" aria-label="Default select example">
                                        @foreach($countries as $countrie)
                                        @if($countrie['id']==4)
                                        <option value="{{$countrie['id']}}">{{$countrie['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div><br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Département</label>
                                    <select id="departement" class="form-select form-control" name="departement" aria-label="Default select example">
                                        @foreach($departements as $departement)
                                        <option value="{{$departement['id']}}">{{$departement['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                <div class="mb-3">
                                    <label for="" class="d-block">Commentaire</label>
                                    <textarea id="comments" rows="1" name="comments" class="form-control" placeholder="Laissez un commentaire ici" class="form-control"></textarea>
                                </div><br>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="w-100 btn btn-sm bg-red"><i class="bi bi-check-circle"></i> Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>


<script type="text/javascript">
    $("#avalisor").on("click", function() {
        // alert(this.checked)
        if (this.checked) {
            // alert(avalisor.checked)
            $("#show_avalisor_info").removeClass('d-none')
        } else {
            $("#show_avalisor_info").addClass('d-none')
        }
    })

    function showAvalisorModal(id) {
        axios.get("{{env('API_BASE_URL')}}locator/" + id + "/retrieve").then((response) => {
            var locator = response.data
            var avalisor = locator.avaliseur

            var locator_fullname = locator.name + " " + locator.prenom;
            $("#ava_locator_fullmneme").html(locator_fullname)

            $("#avar_nom").html(avalisor.ava_name)
            $("#avar_prenom").html(avalisor.ava_prenom)
            $("#avar_link").html(avalisor.ava_parent_link)
            $("#avar_phone").html(avalisor.ava_phone)

        }).catch((error) => {
            alert("une erreure s'est produite")
            console.log(error)
        })
    }

    function showRooms_fun(id) {
        $("#locator_rooms").empty()
        axios.get("{{env('API_BASE_URL')}}locator/" + id + "/retrieve").then((response) => {
            var locator = response.data

            var locator_fullname = locator.name + " " + locator.prenom;
            var locator_rooms = locator["rooms"]

            $("#room_locator_fullname").html(locator_fullname)
            $("#locator_rooms_count").html(locator_rooms.length)

            for (var i = 0; i < locator_rooms.length; i++) {
                var room = locator_rooms[i];
                $('#locator_rooms').append("<li class='list-group-item'> Numero: <strong><em class='text-red'>" + room.number + " </em> </strong>; Loyer: <strong><em class='text-red'>" + room.loyer + " </em> </strong>  </li>");
            }
        }).catch((error) => {
            alert("une erreure s'est produite")
            console.log(error)
        })
    }

    function showHouses_fun(id) {
        $("#locator_houses").empty()
        axios.get("{{env('API_BASE_URL')}}locator/" + id + "/retrieve").then((response) => {
            var locator = response.data

            // console.log(locator)
            var locator_fullname = locator.name + " " + locator.prenom;
            var locator_houses = locator["houses"]

            $("#locator_fullname").html(locator_fullname)
            $("#locator_houses_count").html(locator_houses.length)

            for (var i = 0; i < locator_houses.length; i++) {
                var locator_fullname = locator_houses[i].name;
                $('#locator_houses').append("<li class='list-group-item'>" + locator_fullname + "</li>");
            }
        }).catch((error) => {
            alert("une erreure s'est produite")
            console.log(error)
        })
    }

    function updateModal_fun(id) {
        axios.get("{{env('API_BASE_URL')}}locator/" + id + "/retrieve").then((response) => {
            var locator = response.data
            var locator_fullname = locator.name + " " + locator.prenom;

            $("#update_locator_fullname").html(locator_fullname)

            $("#name").val(locator["name"])
            $("#prenom").val(locator["prenom"])
            $("#email").val(locator["email"])
            $("#sexe").val(locator["sexe"])
            $("#phone").val(locator["phone"])
            $("#card_id").val(locator["card_id"])
            $("#adresse").val(locator["adresse"])

            $("#card_type").val(locator["card_type"])
            $("#country").val(locator["country"])
            $("#departement").val(locator["departement"])
            $("#comments").val(locator["comments"])

            $("#update_form").attr("action", "/locataire/" + locator.id + "/update")
        }).catch((error) => {
            alert("une erreure s'est produite")
            console.log(error)
        })
    }


    function prorataClick_fun() {
        var value = $('#prorata')[0].checked
        if (value) {
            $('#show_prorata_info').removeAttr('hidden');
        } else {
            $('#show_prorata_info').attr("hidden", "hidden");
            $("#prorata_date").val(null)
        }
    }

    function displayLocatorsOptions_fun() {
        var value = $('#displayLocatorsOptions')[0].checked
        if (value) {
            $('#display_locators_options').removeAttr('hidden');
        } else {
            $('#display_locators_options').attr("hidden", "hidden");
        }
    }
</script>
</div>