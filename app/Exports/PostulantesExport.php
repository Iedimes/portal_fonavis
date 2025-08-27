<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\ProjectHasPostulantes;

class PostulantesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $project;
    protected $postulantes;

    public function __construct($project, $postulantes)
    {
        $this->project = $project;
        $this->postulantes = $postulantes;
    }

    public function collection()
    {
        return $this->postulantes->map(function ($post, $key) {
            // $post es un ProjectHasPostulantes, por lo que accedemos a getPostulante
            $postulante = $post->getPostulante;

            // Buscar cónyuge en los miembros del grupo familiar
            $conyuge = null;
            if ($post->getMembers && $post->getMembers->count() > 0) {
                $conyuge = $post->getMembers->firstWhere('parentesco_id', 1) ??
                          $post->getMembers->firstWhere('parentesco_id', 8);
            }

            return [
                'orden' => $key + 1,
                'biblio' => 1,
                'exp' => $postulante->nexp ?? '',
                'apellido_nombre' => ($postulante->last_name ?? '') . ' ' . ($postulante->first_name ?? ''),
                'cedula' => is_numeric($postulante->cedula ?? '') ?
                           number_format($postulante->cedula, 0, ',', '.') : '',
                'ingreso' => number_format($postulante->ingreso ?? 0, 0, ',', '.'),
                'conyuge_nombre' => $conyuge ?
                                   ($conyuge->getPostulante->last_name ?? '') . ' ' . ($conyuge->getPostulante->first_name ?? '') : '',
                'conyuge_cedula' => $conyuge && is_numeric($conyuge->getPostulante->cedula ?? '') ?
                                   number_format($conyuge->getPostulante->cedula, 0, ',', '.') : '',
                'conyuge_ingreso' => $conyuge ?
                                    number_format($conyuge->getPostulante->ingreso ?? 0, 0, ',', '.') : '',
                'ingreso_total' => number_format(ProjectHasPostulantes::getIngreso($post->postulante_id), 0, ',', '.'),
                'nivel' => ProjectHasPostulantes::getNivel($post->postulante_id),
                'cantidad_hijos' => $postulante->cantidad_hijos ?? 0,
                'discap' => $postulante->discapacidad ?? 'N',
                'tercera_edad' => $postulante->tercera_edad ?? 'N',
                'hijo_sosten' => $postulante->hijo_sosten ?? 'N',
                'otra_persona_cargo' => $postulante->otra_persona_a_cargo ?? 'N',
                'terreno' => $this->project->land_id ? $this->project->getLand->name : 'N',
                'residencia' => $postulante->address ?? '',
                'composicion_familiar' => $postulante->composicion_del_grupo ?? '',
                'documentos_presentados' => $postulante->documentos_presentados ?? '',
                'documentos_faltantes' => '' ,
                'Observacion de Consideracion' =>''
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Orden',
            'Biblio',
            'Exp.',
            'Apellido y Nombre',
            'N° de Cédula de Identidad',
            'Ingreso',
            'Apellido y Nombre del Cónyuge o concubino',
            'N° de Cédula de Identidad',
            'Ingreso',
            'Ingreso Total',
            'Nivel',
            'Cantidad de Hijos',
            'Discap',
            '3° Edad',
            'Hijo Sostén',
            'Otra persona a su cargo',
            'Terreno',
            'Residencia',
            'Composición Familiar',
            'Documentos Presentados',
            'Documentos Faltantes',
            'Observacion de Consideracion'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // Orden
            'B' => 8,  // Biblio
            'C' => 10, // Exp
            'D' => 25, // Apellido y Nombre
            'E' => 18, // Cédula
            'F' => 12, // Ingreso
            'G' => 25, // Cónyuge Nombre
            'H' => 18, // Cónyuge Cédula
            'I' => 12, // Cónyuge Ingreso
            'J' => 15, // Ingreso Total
            'K' => 8,  // Nivel
            'L' => 12, // Cantidad Hijos
            'M' => 8,  // Discap
            'N' => 8,  // 3° Edad
            'O' => 12, // Hijo Sostén
            'P' => 15, // Otra persona
            'Q' => 15, // Terreno
            'R' => 20, // Residencia
            'S' => 25, // Composición
            'T' => 25, // Documentos Presentados
            'U' => 25, // Documentos Faltantes
            'V' => 25, // Observacion de Consideracion
        ];
    }

    public function title(): string
    {
        return 'Lista de Postulantes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E8F5E8'
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insertar información del proyecto en las primeras filas
                $this->addProjectInfo($sheet);

                // Aplicar bordes a toda la tabla
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A12:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ]
                ]);

                // Centrar columnas numéricas
                $sheet->getStyle('A13:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B13:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C13:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E13:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H13:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M13:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function addProjectInfo($sheet)
    {
        // Agregar encabezado del ministerio
        $sheet->mergeCells('A1:V4');
        $sheet->setCellValue('A1', "MINISTERIO DE URBANISMO, VIVIENDA Y HÁBITAT\nPARAGUAY TAVY ÑEMOHENDA, OGA'APO HA TEKOHA\nDirección General Social\nDirección de Postulación, Evaluación y Adjudicación FONAVIS");
        $sheet->getStyle('A1')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'font' => [
                'bold' => true,
                'size' => 10
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F0F8FF'
                ]
            ]
        ]);

        // Información del proyecto
        $sheet->setCellValue('A6', 'Ciudad: ' . ($this->project->getCity->CiuNom ?? 'N'));
        $sheet->setCellValue('A7', 'Departamento: ' . ($this->project->getState->DptoNom ?? 'N'));
        $sheet->setCellValue('A8', 'DENOMINACION DE GRUPO: ' . $this->project->name);
        $sheet->setCellValue('A9', 'Servicio de Asistencia Técnica (SAT): ' . ($this->project->getSat->NucNomSat ?? 'N'));
        $sheet->setCellValue('A10', 'AGOSTO / 2025'); // Puedes hacer esto dinámico

        // Título de la tabla
        $sheet->mergeCells('A11:V11');
        $sheet->setCellValue('A11', 'Lista de Postulantes al Subsidio de la Vivienda Social');
        $sheet->getStyle('A11')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'D4F4DD'
                ]
            ]
        ]);

        // Los encabezados van en la fila 12
        $headings = $this->headings();
        foreach ($headings as $index => $heading) {
            $column = chr(65 + $index); // A, B, C, etc.
            $sheet->setCellValue($column . '12', $heading);
        }

        // Aplicar estilo a los encabezados
        $sheet->getStyle('A12:V12')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E8F5E8'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Insertar los datos a partir de la fila 13
        $data = $this->collection();
        $row = 13;
        foreach ($data as $item) {
            $col = 0;
            foreach ($item as $value) {
                $sheet->setCellValue(chr(65 + $col) . $row, $value);
                $col++;
            }
            $row++;
        }
    }
}
