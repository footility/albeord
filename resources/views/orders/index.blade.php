@extends('layouts.app')

@section('title', 'Ordini')

@push('scripts')
    <script src="{{ asset('js/orderScript.js') }}"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Categorie</div>
                <div class="card-body">
                    @foreach($categories as $category)
                        <button class="btn btn-primary mb-2" onclick="mostra({{ $category->id }})">{{ $category->nome }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Carrello</div>
                <div class="card-body" id="carrello_tbl">
                    {{-- Contenuto del carrello --}}
                </div>
            </div>
        </div>
    </div>
@endsection
