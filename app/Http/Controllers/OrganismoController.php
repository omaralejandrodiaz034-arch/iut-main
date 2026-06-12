<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Services\CodigoJerarquicoService;
use App\Services\FpdfReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganismoController extends Controller
{
    protected FpdfReportService $fpdf;

    public function __construct(FpdfReportService $fpdf)
    {
        $this->fpdf = $fpdf;
    }

    /**
     * Listar todos los organismos con filtros.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:'.CodigoJerarquicoService::TOTAL_ORGANISMO],
            'nombre' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Organismo::query();

        if (! empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                    ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        if (! empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%'.$validated['codigo'].'%');
        }

        if (! empty($validated['nombre'])) {
            $query->where('nombre', 'like', '%'.$validated['nombre'].'%');
        }

        $organismos = $query->orderBy('nombre')->paginate(10)->appends($request->query());

        return view('organismos.index', compact('organismos', 'validated'));
    }

    /**
     * Mostrar formulario para crear organismo con código sugerido.
     */
    public function create()
    {
        try {
            // Usar el nuevo servicio jerárquico
            $codigoSugerido = CodigoJerarquicoService::generarCodigoOrganismo();
            $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($codigoSugerido);
        } catch (\Exception $e) {
            $codigoSugerido = null;
            $codigoLegible = null;
        }

        return view('organismos.create', compact('codigoSugerido', 'codigoLegible'));
    }

    /**
     * Guardar un nuevo organismo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'size:'.CodigoJerarquicoService::TOTAL_ORGANISMO,
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    // El organismo debe tener el formato X0000000
                    if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO) !== str_repeat('0', CodigoJerarquicoService::TOTAL_ORGANISMO - CodigoJerarquicoService::LONG_ORGANISMO)) {
                        $fail('El código del organismo debe terminar con zeros en las posiciones de unidad, dependencia y bien.');

                        return;
                    }

                    if (substr($value, 0, CodigoJerarquicoService::LONG_ORGANISMO) === '0') {
                        $fail('El código del organismo no puede empezar en cero.');

                        return;
                    }

                    // Validación cruzada usando el nuevo servicio
                    if (CodigoJerarquicoService::codigoExiste($value)) {
                        $fail('Este código ya está en uso por otro organismo.');
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ], [
            'codigo.required' => 'El código del organismo es requerido',
            'codigo.size' => 'El código debe tener exactamente '.CodigoJerarquicoService::TOTAL_ORGANISMO.' dígitos',
            'codigo.regex' => 'El código solo puede contener números',
        ]);

        $organismo = Organismo::create($validated);

        // Nota: No se necesita reservar rangos porque el código es jerárquico
        // Los códigos de unidades se generan automáticamente basados en el código del organismo

        return redirect()->route('organismos.index')
            ->with('success', 'Organismo creado correctamente. Código: '.
                CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo));
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(Organismo $organismo)
    {
        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo);

        return view('organismos.edit', compact('organismo', 'codigoLegible'));
    }

    /**
     * Actualizar un organismo.
     */
    public function update(Request $request, Organismo $organismo)
    {
        // Validar que no se cambie el código si ya tiene unidades
        if ($request->has('codigo') && $request->codigo !== $organismo->codigo) {
            if ($organismo->unidadesAdministradoras()->count() > 0) {
                return back()->withErrors([
                    'codigo' => 'No se puede cambiar el código porque el organismo ya tiene unidades asociadas.',
                ])->withInput();
            }
        }

        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'size:'.CodigoJerarquicoService::TOTAL_ORGANISMO,
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($organismo) {
                    // El organismo debe tener el formato X0000000
                    if (substr($value, CodigoJerarquicoService::LONG_ORGANISMO) !== str_repeat('0', CodigoJerarquicoService::TOTAL_ORGANISMO - CodigoJerarquicoService::LONG_ORGANISMO)) {
                        $fail('El código del organismo debe terminar con zeros en las posiciones de unidad, dependencia y bien.');

                        return;
                    }

                    if (substr($value, 0, CodigoJerarquicoService::LONG_ORGANISMO) === '0') {
                        $fail('El código del organismo no puede empezar en cero.');

                        return;
                    }

                    // Validar si existe en otros sitios, ignorando el registro actual
                    $existe = false;

                    if ($value !== $organismo->codigo) {
                        $existe = CodigoJerarquicoService::codigoExiste($value);
                    }

                    if ($existe) {
                        $fail('No puedes usar este código. Ya pertenece a otro organismo.');
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $organismo->update($validated);

        return redirect()->route('organismos.index')
            ->with('success', 'Organismo actualizado correctamente');
    }

    /**
     * Mostrar detalles del organismo con estadísticas.
     */
    public function show(Organismo $organismo)
    {
        $organismo->load(['unidadesAdministradoras']);

        // Obtener estadísticas jerárquicas
        $stats = [
            'total_unidades' => $organismo->unidadesAdministradoras()->count(),
            'total_dependencias' => 0,
            'total_bienes' => 0,
            'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo),
            'capacidad_maxima' => pow(10, CodigoJerarquicoService::LONG_UNIDAD),
            'porcentaje_uso_unidades' => 0,
        ];

        // Calcular uso de unidades
        if ($stats['capacidad_maxima'] > 0) {
            $stats['porcentaje_uso_unidades'] = round(
                ($stats['total_unidades'] / $stats['capacidad_maxima']) * 100,
                2
            );
        }

        // Calcular totales recursivos
        foreach ($organismo->unidadesAdministradoras as $unidad) {
            $stats['total_dependencias'] += $unidad->dependencias()->count();

            foreach ($unidad->dependencias as $dependencia) {
                $stats['total_bienes'] += $dependencia->bienes()->count();
            }
        }

        return view('organismos.show', compact('organismo', 'stats'));
    }

    /**
     * Exportar PDF de un organismo específico.
     */
    public function exportPdf(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras.dependencias.bienes');
        $codigoLegible = CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo);

        $pdf = Pdf::loadView('organismos.pdf', [
            'organismo' => $organismo,
            'codigoLegible' => $codigoLegible,
        ])->setPaper('letter');

        $fileName = 'organismo_'.Str::slug($organismo->codigo).'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Eliminar organismo (con verificación de dependencias).
     */
    public function destroy(Organismo $organismo)
    {
        // Verificar si tiene unidades asociadas
        if ($organismo->unidadesAdministradoras()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el organismo porque tiene unidades asociadas.',
                'total_unidades' => $organismo->unidadesAdministradoras()->count(),
            ], 409);
        }

        $organismo->delete();

        return response()->json([
            'message' => 'Organismo eliminado correctamente',
        ], 200);
    }

    /**
     * Generar reporte PDF de organismos con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:'.CodigoJerarquicoService::TOTAL_ORGANISMO],
        ]);

        $query = Organismo::with(['unidadesAdministradoras.dependencias.bienes']);

        if (! empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        if (! empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%'.$validated['codigo'].'%');
        }

        $organismos = $query->orderBy('nombre')->get();

        // Formatear códigos para el reporte
        foreach ($organismos as $organismo) {
            $organismo->codigo_legible = CodigoJerarquicoService::formatearCodigoLegible($organismo->codigo);
        }

        $now = now();

        return $this->fpdf->downloadOrganismosListado(
            'reporte_organismos_general_'.$now->format('dmY_His').'.pdf',
            'REPORTE DE ORGANISMOS',
            'Listado general de organismos',
            $now->format('d/m/Y H:i'),
            $organismos
        );
    }

    /**
     * API: Obtener el siguiente código disponible (para AJAX).
     */
    public function obtenerSiguienteCodigo()
    {
        try {
            $codigo = CodigoJerarquicoService::generarCodigoOrganismo();

            return response()->json([
                'success' => true,
                'codigo' => $codigo,
                'codigo_legible' => CodigoJerarquicoService::formatearCodigoLegible($codigo),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
