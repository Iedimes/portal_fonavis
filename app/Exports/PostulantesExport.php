<?php

namespace App\Exports;

use App\Models\Postulante; // Asegúrate de que el modelo esté importado
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostulantesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Obtén todos los postulantes y sus datos
        return Postulante::all(); // Asegúrate de ajustar esto a tus necesidades
    }

    public function headings(): array
    {
        return [
            'Orden',
            'Biblio',
            'Exp.',
            'Apellido y Nombre',
            'N° de cedula de identidad',
            'Ingreso',
            'Apellido y Nombre del Conyuge o concubino',
            'N° de cedula de identidad',
            'Ingreso',
            'Ingreso Total',
            'Nivel',
            'Cantidad de Hijos',
            'Discap',
            '3°Edad',
            'Hijo Sosten',
            'Otra Persona a Cargo',
            'Terreno',
            'Residencia',
            'Composición del Grupo Familiar',
            'Documentos Presentados',
        ];
    }
}
