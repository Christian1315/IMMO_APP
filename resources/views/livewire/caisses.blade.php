<div>
    <style>
        input {
            font-weight: bold !important;
            font-size: 30px;
        }
    </style>
    <h4 class="">Total: {{count($agencyAccounts)}} </h4>
    <div class="row">
        @foreach($agencyAccounts as $agency_account)
        <div class="col-sm-3">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title text-red">{{$agency_account['_Account']['name']}}</h5>
                    <p class="card-text">{{substr($agency_account['_Account']['description'],0,20)}} ...</p>
                    <h5 class="">Plafond: <strong class="text-red"> {{$agency_account['_Account']["plafond_max"]}} </strong> </h5>

                    @if($agency_account->AgencyCurrentSold)
                    <input disabled type="text" class="form-control" value="Solde : {{$agency_account->AgencyCurrentSold->sold}}">
                    @else
                    <input disabled type="text" class="form-control" value="Solde : 0">
                    @endif

                    <br>
                    <a href="/{{crypId($agency['id'])}}/{{$agency_account['id']}}/caisse-mouvements"  class="btn btn-sm bg-red">Mouvements</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>