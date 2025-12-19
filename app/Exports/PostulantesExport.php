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

    public function __construct($project, $postulantes)
    {
        $this->project = $project;
        $this->postulantes = $postulantes;
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

                    // Centrar columnas numéricas (datos empiezan en fila 16)
                    $sheet->getStyle('A21:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B21:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C21:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E21:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H21:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('M21:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    private function addProjectInfo($sheet)
    {
        // Insertar imagen del logo (ajusta la URL según corresponda)
        $this->addLogo($sheet);

        // Dirección General Social en la fila 10
        $sheet->mergeCells('A10:V10');
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
        $sheet->mergeCells('A11:V11');
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
        $sheet->mergeCells('A12:V12');
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
        $sheet->mergeCells('A13:V13');
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

        $currentMonth = $months[date('n')]; // Obtener el mes actual
        $currentYear = date('Y'); // Obtener el año actual
        $monthYear = $currentMonth . ' / ' . $currentYear; // Formato "Mes / Año"

        $sheet->setCellValue('I18', $monthYear); // Colocar en la columna I al lado de A18

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

        foreach ($headings as $index => $heading) {
            $column = chr(65 + $index); // A, B, C, etc.
            $sheet->setCellValue($column . '20', $heading);
        }

        // Aplicar estilo a los encabezados
        $sheet->getStyle('A20:V20')->applyFromArray([
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

        // Procesar y insertar los datos de postulantes a partir de la fila 21
        $data = $this->postulantes->map(function ($post, $key) {
            // $post es un ProjectHasPostulantes, por lo que accedemos a getPostulante
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
                'ingreso' => number_format($postulante->ingreso ?? 0, 0, ',', '.'),
                'conyuge_nombre' => $conyuge ?
                    $fe(trim(($conyuge->getPostulante->last_name ?? '') . ' ' . ($conyuge->getPostulante->first_name ?? ''))) : '--------------',
                'conyuge_cedula' => $conyuge && is_numeric($conyuge->getPostulante->cedula ?? '') ?
                    number_format($conyuge->getPostulante->cedula, 0, ',', '.') : '--------------',
                'conyuge_ingreso' => $conyuge ?
                    number_format($conyuge->getPostulante->ingreso ?? 0, 0, ',', '.') : '--------------',
                'ingreso_total' => number_format(ProjectHasPostulantes::getIngreso($post->postulante_id), 0, ',', '.'),
                'nivel' => ProjectHasPostulantes::getNivel($post->postulante_id),
                'cantidad_hijos' => $postulante->cantidad_hijos ?? 0,
                'discap' => $postulante->discapacidad ?? 'N',
                'tercera_edad' => $postulante->tercera_edad ?? 'N',
                'hijo_sosten' => $postulante->hijo_sosten ?? 'N',
                'otra_persona_cargo' => $postulante->otra_persona_a_cargo ?? 'N',
                'terreno' => $this->project->land_id ? $this->project->getLand->name : 'N',
                'residencia' => $fe($postulante->address),
                'composicion_familiar' => $fe($postulante->composicion_del_grupo),
                'documentos_presentados' => $fe($postulante->documentos_presentados),
                'documentos_faltantes' => $fe($postulante->documentos_faltantes),
                'observacion_consideracion' => $fe($postulante->observacion_de_consideracion)
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
            $drawing->setHeight(400); // Ajusta según sea necesario
            $drawing->setWidth(1000);  // Ajusta según sea necesario

            // Centrar la imagen
            $columnCount = 22; // Por ejemplo, si tienes de A a V
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX((($columnCount * 22) - 300) / 2); // Ajusta el offset X para centrar

            // Añadir al worksheet
            $drawing->setWorksheet($sheet);
        } catch (\Exception $e) {
            Log::error('Error al agregar el logo: ' . $e->getMessage());
        }
    }
}
