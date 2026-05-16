<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tema;
use App\Models\Negocio;
use Illuminate\Support\Facades\DB;

class TemaSeeder extends Seeder
{
    public function run(): void
    {
        $temas = [
            ['nombre' => 'Wolf Tech', 'color_primario' => '#1E293B', 'color_secundario' => '#38BDF8', 'color_fondo' => '#F8FAFC'],
            ['nombre' => 'Dental Fresh', 'color_primario' => '#0D9488', 'color_secundario' => '#2DD4BF', 'color_fondo' => '#F0FDFA'],
            ['nombre' => 'Midnight Premium', 'color_primario' => '#4F46E5', 'color_secundario' => '#818CF8', 'color_fondo' => '#F5F3FF'],
            ['nombre' => 'Eco Nature', 'color_primario' => '#059669', 'color_secundario' => '#34D399', 'color_fondo' => '#ECFDF5'],
            ['nombre' => 'Classic Elegance', 'color_primario' => '#7C3AED', 'color_secundario' => '#A78BFA', 'color_fondo' => '#FBF9FF'],
            ['nombre' => 'Sunset Clinic', 'color_primario' => '#EA580C', 'color_secundario' => '#FB923C', 'color_fondo' => '#FFF7ED'],
        ];

        foreach ($temas as $index => $datosTema) {
            $tema = Tema::create($datosTema);

            // Si es el primer tema y existe el negocio 1, creamos la relación
            if ($index === 0) {
                $negocio = Negocio::find(1);
                if ($negocio) {
                    // Sincronizamos en la tabla pivote
                    $negocio->temas()->attach($tema->id, [
                        'tipografia' => 'Ubuntu',
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}