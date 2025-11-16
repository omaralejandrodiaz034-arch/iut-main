<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Eliminado;
use App\Models\Usuario;

class BackfillEliminadosArchivedBy extends Command
{
    protected $signature = 'eliminados:backfill-archived-by {--dry-run}';
    protected $description = 'Backfill _archived_by inside Eliminado.data using deleted_by user id when missing';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $this->info('Scanning eliminados...');

        $query = Eliminado::query();
        $count = $query->count();
        $this->info("Found {$count} eliminados");

        $progress = $this->output->createProgressBar($count);
        $progress->start();

        Eliminado::cursor()->each(function($e) use ($dry, $progress) {
            $data = $e->data ?? [];
            $hasArchived = is_array($data) && array_key_exists('_archived_by', $data) && ! empty($data['_archived_by']);
            if (! $hasArchived && ! empty($e->deleted_by)) {
                $user = Usuario::find($e->deleted_by);
                if ($user) {
                    $name = $user->nombre_completo ?? $user->correo ?? null;
                    if ($name) {
                        $data['_archived_by'] = $name;
                        if (! $dry) {
                            $e->data = $data;
                            $e->save();
                        }
                    }
                }
            }
            $progress->advance();
        });

        $progress->finish();
        $this->info("\nDone. Run with --dry-run to preview without saving.");
        return 0;
    }
}
