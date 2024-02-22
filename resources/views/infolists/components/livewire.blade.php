<div>
    @livewire(
        $getContent(),
        [
            'record' => $getRecord(),
        ],
        key('venditio-admin_livewire_'.$getContentName())
    )
</div>
