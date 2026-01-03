@extends('layouts.base')

@section('title', 'Crear Organismo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear Nuevo Organismo</h1>
            
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-100 border border-red-300 p-4">
                    <ul class="text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('organismos.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-form-input id="codigo" name="codigo" label="Código" :value="old('codigo')" required placeholder="Ej: ORG-001" help="Código único del organismo" />
                </div>

                <div>
                    <x-form-input name="nombre" label="Nombre" :value="old('nombre')" required placeholder="Nombre del organismo" help="Nombre completo del organismo" />
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Guardar
                    </button>
                    <a href="{{ route('organismos.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('codigo').addEventListener('input', function (e) {
        const regex = /^[0-9\-]*$/;
        if (!regex.test(e.target.value)) {
            e.target.value = e.target.value.replace(/[^0-9\-]/g, '');
        }
    });
</script>
@endsection
