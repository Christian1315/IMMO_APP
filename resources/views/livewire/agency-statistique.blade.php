<div>
    <!-- TABLEAU DE LISTE -->
    <h4 class="">Total: <strong class="text-red"> {{$houses_count}} </strong> </h4>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive table-responsive-list shadow-lg p-3">
                <table id="myTable" class="table table-striped table-sm">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Nom</th>
                            <th class="text-center">Latitude</th>
                            <th class="text-center">Longitude</th>
                            <th class="text-center">Type de maison</th>
                            <th class="text-center">Superviseur</th>
                            <th class="text-center">Propriétaire</th>
                            <th class="text-center">Mouvements</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($houses as $house)
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index + 1}}</td>
                            <td class="text-center text-red"> <button class="btn btn-sm btn-light">{{$house["name"]}} </button> </td>
                            <td class="text-center"> <button class="btn btn-sm btn-light">@if($house["latitude"]) {{$house["latitude"]}} @else --- @endif </button> </td>
                            <td class="text-center">@if($house["longitude"]) {{$house["longitude"]}} @else --- @endif</td>
                            <td class="text-center">{{$house["Type"]["name"]}}</td>
                            <td class="text-center text-red"> <button class="btn btn-sm btn-light"> {{$house["Supervisor"]["name"]}}</button> </td>
                            <td class="text-center"> <button class="btn btn-sm btn-light"> {{$house["Proprietor"]["lastname"]}} {{$house["Proprietor"]["firstname"]}}</button> </td>

                            <td class="text-center">
                                <a  href="{{route('location.FiltreBeforeStateDateStoped', crypId($house['id']))}}" class="btn btn-sm btn-dark"><i class="bi bi-caret-left-square"></i></a> &nbsp;
                                <a  href="{{route('location.FiltreAfterStateDateStoped', crypId($house['id']))}}" class="btn btn-sm bg-red"><i class="bi bi-caret-right-square"></i></a> 
                                &nbsp;
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>