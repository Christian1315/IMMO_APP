<div>
    <input type="checkbox" hidden class="btn-check" id="displayLocatorsOptions" onclick="displayLocatorsOptions_fun()">
    <label class="btn btn-light" for="displayLocatorsOptions"><i class="bi bi-funnel"></i>FILTRER LES LOCATAIRES</label>

    <div id="display_filtre_options" hidden>
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
                    <form action="{{route('locator.PaidFiltreBySupervisor',$current_agency->id)}}" method="POST">
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
                        <button type="submit" class="btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
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
                    <form action="{{route('locator.PaidFiltreByHouse',$current_agency->id)}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label>Choisissez une maison</label>
                                <select required name="house" class="form-control">
                                    @foreach($current_agency->_Houses as $house)
                                    <option value="{{$house['id']}}"> {{$house["name"]}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm bg-red mt-2"><i class="bi bi-funnel"></i> Filtrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br><br>

    <!-- TABLEAU DE LISTE -->
    <h4 class="">Total: <strong class="text-red"> {{session()->get("filteredLocators")? count(session()->get("filteredLocators")):count($locators)}} </strong> </h4>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Maison</th>
                            <th class="text-center">Chambre</th>
                            <th class="text-center">Nom</th>
                            <th class="text-center">Prénom</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Numéro de pièce</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Adresse</th>
                            <th class="text-center">Dernier mois payé</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach((session()->get("filteredLocators")?session()->get("filteredLocators"):$locators) as $location)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index + 1}}</td>
                            <td class="text-center text-red"> <strong class=""> {{$location->House->name}}</strong></td>
                            <td class="text-center"> <strong class=""> {{$location->Room->number}}</strong></td>
                            <td class="text-center">{{$location["Locataire"]["name"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["prenom"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["email"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["card_id"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["phone"]}}</td>
                            <td class="text-center">{{$location["Locataire"]["adresse"]}}</td>
                            <td class="text-center"> <button class="btn btn-sm shadow bg-light text-red"> {{$location["latest_loyer_date"]}} </button> </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function displayLocatorsOptions_fun() {
            var value = $('#displayLocatorsOptions')[0].checked
            if (value) {
                $('#display_filtre_options').removeAttr('hidden');
            } else {
                $('#display_filtre_options').attr("hidden", "hidden");
            }
        }
    </script>
</div>