<?php

namespace App\Http\Controllers;

use App\Models\Organismo;
use App\Services\CodigoUnicoService;
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
            'codigo' => ['nullable', 'string', 'max:8'],
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
        // Usamos tu servicio para obtener el código real disponible en todo el sistema
        $codigoSugerido = CodigoUnicoService::obtenerSiguienteCodigo();

        return view('organismos.create', compact('codigoSugerido'));
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
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) {
                    // Validación cruzada usando tu servicio
                    if (CodigoUnicoService::codigoExiste($value)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail('Este código ya está en uso por: '.$ubicacion['tabla'].' ('.$ubicacion['nombre'].')');
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ], [
            'codigo.required' => 'El código del organismo es requerido',
            'codigo.size' => 'El código debe tener exactamente 8 dígitos',
            'codigo.regex' => 'El código solo puede contener números',
        ]);

        $organismo = Organismo::create($validated);

        // Reservar 50 códigos para las unidades de este organismo
        try {
            CodigoUnicoService::reservarCodigosParaOrganismo($organismo->id, 50);
        } catch (\Exception $e) {
            // Log del error pero no fallar la creación del organismo
            \Log::warning("No se pudieron reservar códigos para organismo {$organismo->id}: ".$e->getMessage());
        }

        return redirect()->route('organismos.index')->with('success', 'Organismo creado correctamente');
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(Organismo $organismo)
    {
        return view('organismos.edit', compact('organismo'));
    }

    /**
     * Actualizar un organismo.
     */
    public function update(Request $request, Organismo $organismo)
    {
        // Validar que no se cambie el código si ya tiene unidades
        if ($request->has('codigo') && $request->codigo !== $organismo->codigo) {
            if ($organismo->unidadesAdministradoras()->count() > 0) {
                return back()->withErrors(['codigo' => 'No se puede cambiar el código porque el organismo ya tiene unidades asociadas.'])->withInput();
            }
        }

        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($organismo) {
                    // Validar si existe en otros sitios, pero ignorar el registro actual
                    if (CodigoUnicoService::codigoExiste($value, 'organismos', $organismo->id)) {
                        $ubicacion = CodigoUnicoService::obtenerUbicacionCodigo($value);
                        $fail('No puedes usar este código. Ya pertenece a: '.$ubicacion['tabla']);
                    }
                },
            ],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $organismo->update($validated);

        return redirect()->route('organismos.index')->with('success', 'Organismo actualizado');
    }

    // Los demás métodos (show, exportPdf, destroy) se mantienen igual...

    public function show(Organismo $organismo)
    {
        $organismo->load(['unidadesAdministradoras']);

        return view('organismos.show', compact('organismo'));
    }

    public function exportPdf(Organismo $organismo)
    {
        $organismo->load('unidadesAdministradoras');
        $pdf = Pdf::loadView('organismos.pdf', ['organismo' => $organismo])->setPaper('letter');
        $fileName = 'organismo_'.Str::slug($organismo->codigo).'.pdf';

        return $pdf->download($fileName);
    }

    public function destroy(Organismo $organismo)
    {
        return response()->json(['message' => 'No está permitido eliminar organismos.'], 403);
    }

    /**
     * Generar reporte PDF de organismos con filtros aplicados.
     */
    public function generarReporte(Request $request)
    {
        $validated = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:8'],
        ]);

        $query = Organismo::with(['unidadesAdministradoras']);

        if (!empty($validated['buscar'])) {
            $buscar = $validated['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        if (!empty($validated['codigo'])) {
            $query->where('codigo', 'like', '%' . $validated['codigo'] . '%');
        }

        $organismos = $query->orderBy('nombre')->get();
        $now = now();

        return $this->fpdf->downloadOrganismosListado(
            'reporte_organismos_general_' . $now->format('dmY_His') . '.pdf',
            'REPORTE DE ORGANISMOS',
            'Listado general de organismos',
            $now->format('d/m/Y H:i'),
            $organismos
        );
    }
}
