<x-templates.agency :title="'Filtrage'" :active="'filtrage'" :agency="$agency">

    <!-- HEADER -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Panel de <em class="text-red">bilan </em></h1>
    </div>
    <br>

    <livewire:filtrage :agency=$agency />

</x-templates.agency>