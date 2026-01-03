@extends('layouts.base')

@section('title', 'Editar Movimiento')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Editar Movimiento #{{ $movimiento->id }}</h1>

        <form action="{{ route('movimientos.update', $movimiento->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Bien (opcional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Bien (opcional)</label>
                <select name="bien_id" class="mt-1 block w-full rounded-md border px-3 py-2">
                    <option value="">-- Ninguno --</option>
                    @foreach(\App\Models\Bien::orderBy('descripcion')->get() as $bien)
                        <option value="{{ $bien->id }}" {{ $bien->id == $movimiento->bien_id ? 'selected' : '' }}>
                            {{ $bien->codigo }} - {{ $bien->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sujeto polimórfico -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Entidad relacionada (subject)</label>
                <select name="subject_type" class="mt-1 block w-full rounded-md border px-3 py-2">
                    <option value="">-- Ninguno --</option>
                    <option value="{{ \App\Models\Organismo::class }}" {{ $movimiento->subject_type === \App\Models\Organismo::class ? 'selected' : '' }}>Organismo</option>
                    <option value="{{ \App\Models\UnidadAdministradora::class }}" {{ $movimiento->subject_type === \App\Models\UnidadAdministradora::class ? 'selected' : '' }}>Unidad Administradora</option>
                    <option value="{{ \App\Models\Dependencia::class }}" {{ $movimiento->subject_type === \App\Models\Dependencia::class ? 'selected' : '' }}>Dependencia</option>
                    <option value="{{ \App\Models\Bien::class }}" {{ $movimiento->subject_type === \App\Models\Bien::class ? 'selected' : '' }}>Bien</option>
                    <option value="{{ \App\Models\Usuario::class }}" {{ $movimiento->subject_type === \App\Models\Usuario::class ? 'selected' : '' }}>Usuario</option>
                </select>

                <label class="block text-sm font-medium text-gray-700 mt-2">ID de la entidad</label>
                <input type="number" name="subject_id" class="mt-1 block w-full rounded-md border px-3 py-2"
                       value="{{ $movimiento->subject_id }}" placeholder="ID del sujeto" />
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" name="tipo" class="mt-1 block w-full rounded-md border px-3 py-2"
                       value="{{ $movimiento->tipo }}" required />
            </div>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" name="fecha" class="mt-1 block w-full rounded-md border px-3 py-2"
                       value="{{ optional($movimiento->fecha)->format('Y-m-d') }}" required />
            </div>

            <!-- Usuario -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Usuario (registró)</label>
                <select name="usuario_id" class="mt-1 block w-full rounded-md border px-3 py-2">
                    @foreach(\App\Models\Usuario::orderBy('nombre')->get() as $u)
                        <option value="{{ $u->id }}" {{ $u->id == $movimiento->usuario_id ? 'selected' : '' }}>
                            {{ $u->nombre_completo ?? $u->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Observaciones -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea name="observaciones" class="mt-1 block w-full rounded-md border px-3 py-2" rows="4">{{ $movimiento->observaciones }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                <a href="{{ route('movimientos.index') }}" class="bg-gray-200 px-4 py-2 rounded">Cancelar</a>
            </div>
        </form>
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

