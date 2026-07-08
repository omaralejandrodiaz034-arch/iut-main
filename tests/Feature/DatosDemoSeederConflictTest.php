<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatosDemoSeederConflictTest extends TestCase
{
    public function test_datos_demo_seeder_has_no_merge_conflict_markers(): void
    {
        $path = base_path('database/seeders/DatosDemoSeeder.php');
        $contents = file_get_contents($path);

        $this->assertStringNotContainsString('<<<<<<<', $contents);
        $this->assertStringNotContainsString('=======', $contents);
        $this->assertStringNotContainsString('>>>>>>>', $contents);
    }
}
