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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PostulantesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $project;
    protected $postulantes;
    protected $ingresosTotales;
    protected $niveles;
    protected $otrosIngresos;

    public function __construct($project, $postulantes, $ingresosTotales = [], $niveles = [], $otrosIngresos = [])
    {
        $this->project = $project;
        $this->postulantes = $postulantes;
        $this->ingresosTotales = $ingresosTotales;
        $this->niveles = $niveles;
        $this->otrosIngresos = $otrosIngresos;
    }

    public function collection()
    {
        // Devolver colección vacía porque manejaremos los datos en addProjectInfo
        return new Collection();
    }

    public function headings(): array
    {
        // Los encabezados se insertarán manualmente en addProjectInfo
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // Orden
            'B' => 8,  // Biblio
            'C' => 10, // Exp
            'D' => 25, // Apellido y Nombre
            'E' => 18, // Cédula
            'F' => 20, // Ingreso (Titular)
            'G' => 25, // Cónyuge Nombre
            'H' => 18, // Cónyuge Cédula
            'I' => 20, // Cónyuge Ingreso
            'J' => 20, // Otros Ingresos
            'K' => 22, // Ingreso Total
            'L' => 8,  // Nivel
            'M' => 12, // Cantidad Hijos
            'N' => 8,  // Discap
            'O' => 8,  // 3° Edad
            'P' => 12, // Hijo Sostén
            'Q' => 15, // Otra persona
            'R' => 15, // Terreno
            'S' => 20, // Residencia
            'T' => 25, // Composición
            'U' => 25, // Documentos Presentados
            'V' => 25, // Documentos Faltantes
            'W' => 25, // Motivo
            'X' => 25, // Observacion de Consideracion
            'Y' => 8,  // Califica
        ];
    }

    public function title(): string
    {
        return 'Lista de Postulantes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Los estilos se aplicarán en registerEvents
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insertar toda la información del proyecto y datos
                $this->addProjectInfo($sheet);

                // Aplicar bordes a toda la tabla de datos
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Solo aplicar bordes a la tabla de datos (desde fila 15)
                if ($lastRow > 20) {
                    $sheet->getStyle('A20:' . $lastColumn . $lastRow)->applyFromArray([
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

                    // Centrar columnas generales
                    $sheet->getStyle('A21:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E21:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(false);
                    $sheet->getStyle('H21:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(false);
                    $sheet->getStyle('L21:Q' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('Y21:Y' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Formato numérico, alineación a la derecha y sin wrapText para los 4 campos de Ingreso (evita recortes y ###)
                    $sheet->getStyle('F21:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setWrapText(false);
                    $sheet->getStyle('F21:F' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

                    $sheet->getStyle('I21:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setWrapText(false);
                    $sheet->getStyle('I21:K' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                }
            },
        ];
    }

    private function addProjectInfo($sheet)
    {
        // Insertar imagen del logo
        $this->addLogo($sheet);

        // Dirección General Social en la fila 10
        $sheet->mergeCells('A10:Y10');
        $sheet->setCellValue('A10', 'Dirección General Social');
        $sheet->getStyle('A10')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 11
            ]
        ]);

        // Dirección de Postulación, Evaluación y Adjudicación FONAVIS en la fila 11
        $sheet->mergeCells('A11:Y11');
        $sheet->setCellValue('A11', 'Dirección de Postulación, Evaluación y Adjudicación FONAVIS');
        $sheet->getStyle('A11')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 11
            ]
        ]);

        // Departamento de Análisis de Postulantes de Grupos Organizados en la fila 12
        $sheet->mergeCells('A12:Y12');
        $sheet->setCellValue('A12', 'Departamento de Análisis de Postulantes de Grupos Organizados');
        $sheet->getStyle('A12')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 11
            ]
        ]);

        // Título de la tabla en la fila 13
        $sheet->mergeCells('A13:Y13');
        $sheet->setCellValue('A13', 'Lista de Postulantes al Subsidio de la Vivienda Social');
        $sheet->getStyle('A13')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ]);

        // Información del proyecto en las filas siguientes
        $sheet->setCellValue('A15', 'Ciudad: ' . ($this->project->getCity->CiuNom ?? 'N/A'));
        $sheet->setCellValue('A16', 'Departamento: ' . ($this->project->getState->DptoNom ?? 'N/A'));
        $sheet->setCellValue('A17', 'DENOMINACION DE GRUPO: ' . $this->project->name);
        $sheet->setCellValue('A18', 'Servicio de Asistencia Técnica (SAT): ' . ($this->project->getSat->NucNomSat ?? 'N'));

        // Obtener mes y año actual en español
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $currentMonth = $months[date('n')];
        $currentYear = date('Y');
        $monthYear = $currentMonth . ' / ' . $currentYear;

        $sheet->setCellValue('I18', $monthYear);

        // Aplicar negrita a las celdas de información del proyecto
        $sheet->getStyle('A15:A19')->applyFromArray([
            'font' => [
                'bold' => true,
            ]
        ]);

        // Fecha en la columna I también en negrita
        $sheet->getStyle('I18')->applyFromArray([
            'font' => [
                'bold' => true,
            ]
        ]);

        // Insertar encabezados en la fila 20
        $headings = [
            'Orden',
            'Biblio',
            'Exp.',
            'Apellido y Nombre',
            'N° de Cédula de Identidad',
            'Ingreso',
            'Apellido y Nombre del Cónyuge o concubino',
            'N° de Cédula de Identidad',
            'Ingreso',
            'Otros Ingresos',
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
            'Motivo',
            'Observacion de Consideracion',
            'Califica'
        ];

        foreach ($headings as $index => $heading) {
            $column = chr(65 + $index); // A, B, C, etc.
            $sheet->setCellValue($column . '20', $heading);
        }

        // Aplicar estilo a los encabezados
        $sheet->getStyle('A20:Y20')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9
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

        // Procesar e insertar los datos de postulantes a partir de la fila 21
        $data = $this->postulantes->map(function ($post, $key) {
            $postulante = $post->getPostulante;

            // Buscar cónyuge en los miembros del grupo familiar
            $conyuge = null;
            if ($post->getMembers && $post->getMembers->count() > 0) {
                $conyuge = $post->getMembers->firstWhere('parentesco_id', 1) ??
                    $post->getMembers->firstWhere('parentesco_id', 8);
            }

            // Función ayudante para campos vacíos
            $fe = function ($val) {
                return (trim($val) === '' || $val === null) ? '--------------' : $val;
            };

            return [
                'orden' => $key + 1,
                'biblio' => 1,
                'exp' => $fe($postulante->nexp),
                'apellido_nombre' => $fe(trim(($postulante->last_name ?? '') . ' ' . ($postulante->first_name ?? ''))),
                'cedula' => is_numeric($postulante->cedula ?? '') ?
                    number_format($postulante->cedula, 0, ',', '.') : '--------------',
                'ingreso' => (float) ($postulante->ingreso ?? 0),
                'conyuge_nombre' => $conyuge ?
                    $fe(trim(($conyuge->getPostulante->last_name ?? '') . ' ' . ($conyuge->getPostulante->first_name ?? ''))) : '--------------',
                'conyuge_cedula' => $conyuge && is_numeric($conyuge->getPostulante->cedula ?? '') ?
                    number_format($conyuge->getPostulante->cedula, 0, ',', '.') : '--------------',
                'conyuge_ingreso' => $conyuge ? (float) ($conyuge->getPostulante->ingreso ?? 0) : '--------------',
                'otros_ingresos' => (float) ($this->otrosIngresos[$post->getPostulante->id] ?? ($postulante->otros_ingresos ?? 0)),
                'ingreso_total' => (float) ($this->ingresosTotales[$post->postulante_id] ?? 0),
                'nivel' => $this->niveles[$post->postulante_id] ?? '',
                'cantidad_hijos' => $postulante->cantidad_hijos ?? 0,
                'discap' => $postulante->discapacidad ?? 'N',
                'tercera_edad' => $postulante->tercera_edad ?? 'N',
                'hijo_sosten' => $postulante->hijo_sosten ?? 'N',
                'otra_persona_cargo' => $postulante->otra_persona_a_cargo ?? '',
                'terreno' => $this->project->land_id ? $this->project->getLand->name : 'N',
                'residencia' => $fe($postulante->address),
                'composicion_familiar' => $fe($postulante->composicion_del_grupo),
                'documentos_presentados' => $fe($postulante->documentos_presentados),
                'documentos_faltantes' => $fe($postulante->documentos_faltantes),
                'motivo' => $fe($postulante->motivo ?? ''),
                'observacion_consideracion' => $fe($postulante->observacion_de_consideracion),
                'califica' => $postulante->califica ?? 'N'
            ];
        });

        // Insertar los datos a partir de la fila 21
        $row = 21;
        foreach ($data as $item) {
            $col = 0;
            foreach ($item as $value) {
                $sheet->setCellValue(chr(65 + $col) . $row, $value);
                $col++;
            }
            $row++;
        }
    }

    private function addLogo($sheet)
    {
        try {
            $logoPath = public_path('img/logofull.png');

            // Crear el objeto Drawing
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo del Ministerio');

            // Verificar si el archivo de logo existe
            if (file_exists($logoPath)) {
                $drawing->setPath($logoPath);
            } else {
                throw new \Exception("El archivo de imagen no existe en la ruta especificada.");
            }

            // Establecer tamaño de la imagen
            $drawing->setHeight(400);
            $drawing->setWidth(1000);

            // Centrar la imagen
            $columnCount = 25;
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX((($columnCount * 22) - 300) / 2);

            // Añadir al worksheet
            $drawing->setWorksheet($sheet);
        } catch (\Exception $e) {
            Log::error('Error al agregar el logo: ' . $e->getMessage());
        }
    }
}
