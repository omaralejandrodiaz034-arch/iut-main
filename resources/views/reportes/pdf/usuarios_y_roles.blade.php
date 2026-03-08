@extends('reportes.pdf')

@section('contenido')
<table>
    <thead>
        <tr>
            <th style="width: 12%;" class="text-center">Cédula</th>
            <th style="width: 28%;">Nombre completo</th>
            <th style="width: 25%;">Correo electrónico</th>
            <th style="width: 15%;" class="text-center">Rol</th>
            <th style="width: 10%;" class="text-center">Estado</th>
            <th style="width: 10%;" class="text-center">Admin</th>
        </tr>
    </thead>
    <tbody>
    @php 
        $totalActivos = 0;
        $totalAdmins = 0;
    @endphp
    @forelse($usuarios as $user)
        @php
            if($user->activo) $totalActivos++;
            if($user->is_admin) $totalAdmins++;
        @endphp
        <tr>
            <td class="text-center"><strong>{{ $user->cedula }}</strong></td>
            <td>{{ $user->nombre_completo }}</td>
            <td class="small">{{ $user->correo }}</td>
            <td class="text-center">
                <span style="background: #e6e6e6; padding: 2px 6px; border-radius: 3px; font-size: 9px;">
                    {{ $user->rol->nombre ?? 'N/D' }}
                </span>
            </td>
            <td class="text-center">
                @if($user->activo)
                    <span style="color: green;">✓ Activo</span>
                @else
                    <span style="color: red;">✗ Inactivo</span>
                @endif
            </td>
            <td class="text-center">
                @if($user->is_admin)
                    <span style="color: #003366; font-weight: bold;">SÍ</span>
                @else
                    <span style="color: #999;">No</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No hay usuarios para mostrar.</td>
        </tr>
    @endforelse
    </tbody>
</table>

@if($usuarios->isNotEmpty())
    <div class="mt-2 small">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none; padding: 5px;">
                    <strong>Total de usuarios:</strong> {{ $usuarios->count() }}
                </td>
                <td style="border: none; padding: 5px;">
                    <strong>Usuarios activos:</strong> {{ $totalActivos }}
                </td>
                <td style="border: none; padding: 5px;">
                    <strong>Administradores:</strong> {{ $totalAdmins }}
                </td>
            </tr>
        </table>
    </div>
@endif
@endsection
