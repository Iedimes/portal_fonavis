<?php

namespace App\Traits;

trait CleansUtf8Encoding
{
    /**
     * Limpia y normaliza texto con problemas de encoding (mojibake, doble codificación UTF-8, etc.)
     * Si la cadena ya es UTF-8 válida y limpia, la mantiene intacta (quitando espacios en blanco finales).
     * Si viene con doble codificación (Ã‘, etc.), la repara a su carácter original (Ñ, etc.).
     */
    public static function fixUtf8($string)
    {
        if ($string === null || !is_string($string) || $string === '') {
            return $string;
        }

        $string = rtrim($string);

        // Desenrollar hasta 8 capas de codificación UTF-8 repetida
        for ($i = 0; $i < 8; $i++) {
            if (preg_match('/[\xC2-\xF4][\x80-\xBF]/', $string)) {
                $converted = @mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
                if ($converted !== false && mb_check_encoding($converted, 'UTF-8') && preg_match('/[\x80-\xFF]/', $converted)) {
                    $string = $converted;
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        // Mapeo exhaustivo de patrones comunes de mojibake latinoamericano
        $replacements = [
            'Ã‘' => 'Ñ',
            'Ã±' => 'ñ',
            'Ã ' => 'Á',
            'Ã¡' => 'á',
            'Ã‰' => 'É',
            'Ã©' => 'é',
            'ÃÍ' => 'Í',
            'Ã­' => 'í',
            'Ã“' => 'Ó',
            'Ã³' => 'ó',
            'ÃŠ' => 'Ú',
            'Ãº' => 'ú',
            'Ãœ' => 'Ü',
            'Ã¼' => 'ü',
            'Âº' => 'º',
            'Â°' => '°',
            'Âª' => 'ª',
            'Â´' => '´',
            'Â'  => '',
            'â€œ' => '“',
            'â€' => '”',
            'â€“' => '–',
            'â€”' => '—',
        ];

        return strtr($string, $replacements);
    }
}
