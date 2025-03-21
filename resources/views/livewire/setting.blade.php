<div>
    <div class="d-flex header-bar">
        <small>
            <button type="button" class="btn btn-sm bg-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                <i class="bi bi-cloud-plus-fill"></i> Ajouter un utilisateur
            </button>
        </small>
    </div>
    <br>
    
    <!-- AJOUT D'UN USER -->
    <div class="modal fade" id="staticBackdrop" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="">Ajout d'un utilisateur</p>
                    <button type="button" class="btn btn-sm text-red" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('AddUser')}}" class="shadow-lg p-3 animate__animated animate__bounce">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <select required value="{{old('agency')}}" name="agency" class="select2 form-control mb-1">
                                    <option>Choisir une agence</option>
                                    @foreach($agencies as $agency)
                                    <option value="{{$agency['id']}}">{{$agency['name']}} </option>
                                    @endforeach
                                </select>
                                @error("agency")
                                <span class="text-red"> {{$message}} </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" name="name" value="{{old('name')}}" placeholder="Nom/Prénom ...." class="form-control">
                                    @error("name")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <span class="text-red"> {{$username_error}} </span>
                                    <input type="text" name="username" value="{{old('username')}}" placeholder="Identifiant(username)" class="form-control">
                                    @error("username")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <select value="{{old('profil')}}" name="profil" class="form-select mb-3 form-control">
                                    <option>Choisir un profil</option>
                                    @foreach($profils as $profil)
                                    <option value="{{$profil['id']}}">{{$profil['name']}} -- (<span class="text-red">{{$profil['description']}}</span>) </option>
                                    @endforeach
                                </select>
                                @error("profil")
                                <span class="text-red"> {{$message}} </span>
                                @enderror
                            </div>
                            <!--  -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="phone" value="{{old('phone')}}" name="phone" placeholder="Téléphone ..." class="form-control">
                                    @error("phone")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <div class="mb-3">
                                    <input type="text" value="{{old('email')}}" placeholder="Votre Adresse mail ..." name="email" class="form-control">
                                    @error("email")
                                    <span class="text-red"> {{$message}} </span>
                                    @enderror
                                </div><br>
                                <select value="{{old('rang')}}" name="rang" class="form-select mb-3 form-control">
                                    <option>Choisir un rang</option>
                                    @foreach($rangs as $rang)
                                    <option value="{{$rang['id']}}">{{$rang['name']}} -- (<span class="text-red">{{$rang['description']}}</span>) </option>
                                    @endforeach
                                </select>
                                @error("rang")
                                <span class="text-red"> {{$message}} </span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer justify-center">
                            <button type="submit" class="btn bg-dark">Enregistrer</button>
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
            <h4 class="">Total: <strong class="text-red"> {{count($users)}} </strong> </h4>
            <div class="table-responsive table-responsive-list shadow-lg">
                <table id="myTable" class="table table-striped table-sm table-bordered">
                    <thead class="bg_dark">
                        <tr>
                            <th class="text-center">N°</th>
                            <th class="text-center">Nom/Prénom</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Agence</th>
                            <th class="text-center">Date de création</th>
                            <th class="text-center">Rôles</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        @if($user->is_archive)
                        <tr disabled class="align-items-center shadow my-2 bg-secondary" style="background-color:#F6F6F6;border: solid 1px #000">
                            <td class="text-center">{{$loop->index + 1}}</td>
                            <td class="text-center">{{$user["name"]}}</td>
                            <td class="text-center">{{$user["email"]}}</td>
                            <td class="text-center">{{$user["phone"]}}</td>
                            <td class="text-center">
                                @if($user["agency"])
                                {{$user->_Agency->name}}
                                @else
                                ----
                                @endif
                            </td>
                            <td class="text-center text-red"><i class="bi bi-calendar2-check-fill"></i> {{date("d/m/Y",strtotime($user["created_at"]))}}</th>
                            <td class="text-center ">
                                <button disabled class="btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#seeRoles">
                                    <i class="bi bi-eye-fill"></i> &nbsp;
                                    Voir
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="btn-group dropstart">
                                    <button disabled type="button" class="btn btn-sm bg-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden"> <i class="bi bi-kanban"></i> Gérer </span>
                                    </button>
                                    <ul class="dropdown-menu">

                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @else
                        <tr class="align-items-center">
                            <td class="text-center">{{$loop->index +1}}</td>
                            <td class="text-center">{{$user["name"]}}</td>
                            <td class="text-center">{{$user["email"]}}</td>
                            <td class="text-center">{{$user["phone"]}}</td>
                            <td class="text-center">
                                @if($user["agency"])
                                {{$user->_Agency->name}}
                                @else
                                ----
                                @endif
                            </td>
                            <td class="text-center text-red"><i class="bi bi-calendar2-check-fill"></i> {{date("d/m/Y",strtotime($user["created_at"]))}}</th>
                            <td class="text-center ">
                                <a href="{{route('user.GetUserRoles',crypId($user->id))}}" target="__blank" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye-fill"></i> &nbsp;
                                    Voir
                                </a>
                            </td>
                            <td class="text-center">
                                <!-- Split dropstart button -->
                                <div class="btn-group dropstart">
                                    <button type="button" class="btn btn-sm bg-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden"> <i class="bi bi-kanban"></i> Gérer </span>
                                    </button>
                                    <ul class="dropdown-menu p-2">
                                        <a href="{{route('user.ArchiveAccount',crypId($user->id))}}" class="w-100 btn btn-sm btn-success">Archiver</a>
                                        <a href="{{route('user.AttachRoleToUser',crypId($user->id))}}" target="__blank" class="w-100 btn btn-sm btn-light">Affecter rôle</a>
                                        <a href="{{route('user.DuplicatAccount',crypId($user->id))}}" class="w-100 btn btn-sm btn-dark">Duppliquer</a>
                                        <button class="w-100 btn btn-sm bg-warning" data-bs-toggle="modal" data-bs-target="#updateModal_{{$user['id']}}"><i class="bi bi-person-lines-fill"></i> Modifier</button>
                                        <a href="{{route('user.DeleteAccount',crypId($user->id))}}" data-confirm-delete="true" class="w-100 btn btn-sm bg-red"><i class="bi bi-archive-fill"></i> Supprimer</a>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endif

                        <!-- ###### MODEL DE MODIFICATION ###### -->
                        <div class="modal fade" id="updateModal_{{$user['id']}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content shadow-lg p-3 animate__animated animate__bounce">
                                    <div class="modal-header">
                                        <h6 class="modal-title fs-5" id="exampleModalLabel">Modifier <strong> <em class="text-red"> {{$user['name']}} </em> </strong> </h6>
                                    </div>

                                    <div class="modal-body">
                                        <form action="{{route('user.UpdateCompte',crypId($user->id))}}">
                                            @csrf
                                            <div class="col-md-12">
                                                <span class="">Choisir une agence </span>
                                                <select required name="agency" class="form-select mb-3 form-control">
                                                    <option placeholder="{{$user['__agency']?$user['__agency']['id']:''}}">{{$user['__agency']?$user['__agency']['name']:''}}</option>
                                                    @foreach($agencies as $agency)
                                                    <option value="{{$agency['id']}}">{{$agency['name']}} </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <span>Nom/Prénom</span>
                                                        <input type="text" name="name" value="{{$user['name']}}" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <span class="">Identifiant(Username)</span>
                                                        <input type="text" name="username" value="{{$user['username']}}" class="form-control">
                                                    </div><br>
                                                    <span>Choisir un profil</span>
                                                    <select name="profil" class="form-select mb-3 form-control">
                                                        @foreach($profils as $profil)
                                                        <option value="{{$profil['id']}}" @if($profil->id==$user->profil->id) selected @endif >{{$profil['name']}} -- (<span class="text-red">{{$profil['description']}}</span>) </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <span>Téléphone</span>
                                                        <input type="phone" name="phone" value="{{$user['phone']}}" class="form-control">
                                                    </div><br>
                                                    <div class="mb-3">
                                                        <span>Adresse mail</span>
                                                        <input type="text" value="{{$user['email']}}" name="email" class="form-control">
                                                    </div><br>
                                                    <span class="text-red"> {{$rang_error}} </span>
                                                    <span>Choisir un rang</span>
                                                    <select name="rang" class="form-select mb-3 form-control">
                                                        @foreach($rangs as $rang)
                                                        <option value="{{$rang['id']}}" @if($rang->id==$user->rang->id) selected @endif >{{$rang['name']}} -- (<span class="text-red">{{$rang['description']}}</span>) </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm bg-red" data-bs-dismiss="modal">Fermer</button>
                                                <button type="submit" class="btn btn-sm bg-dark">Modifier</button>
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