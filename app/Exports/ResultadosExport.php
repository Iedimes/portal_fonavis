<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResultadosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    /**
     * Define los encabezados del Excel.
     */
    public function headings(): array
    {
        return [
            'N°', 
            'Nombre del Proyecto', 
            'Descripción', 
            'SAT', 
            'DEPARTAMENTO', 
            'DISTRITO', 
            'MODALIDAD', 
            'Estado', 
            'Fecha de Creación', 
            'Última Actualización',
        ];
    }

    /**
     * Mapea cada fila para exportar.
     */
    public function map($project): array
    {
        return [
            $project->id,
            $project->name,
            $project->getEstado->record ?? 'Sin Descripción',
            $project->getsat->NucNomSat,
            $project->getstate->DptoNom,
            $project->getcity->CiuNom,
            $project->getmodality->name,
            $project->getEstado->getstage->name ?? 'Sin Estado',
            $project->created_at->format('d/m/Y'), // Formato de fecha
            $project->updated_at->format('d/m/Y'), // Formato de fecha
        ];
    }

    /**
     * Estilos para el documento.
     */
    public function styles(Worksheet $sheet)
    {
        // Estilos para la cabecera
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0000'], // Rojo
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Estilo de cuadrícula para los datos
        $lastRow = count($this->results) + 1; // +1 para incluir la fila de encabezados
        $sheet->getStyle('A2:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Estilo general para todo el contenido
        $sheet->getStyle('A2:J' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
            ],
        ]);

        // Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}