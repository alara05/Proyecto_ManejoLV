<?php

namespace App\Services;

use App\Models\Boleto;

class BoletoPdfGenerator
{
    public function generate(Boleto $boleto): string
    {
        $commands = [];
        $commands[] = '0.96 0.97 0.99 rg 0 0 595 842 re f';
        $commands[] = '0.08 0.12 0.18 rg 0 730 595 112 re f';
        $commands[] = $this->text('BOLETO DE VIAJE', 48, 785, 24, true);
        $commands[] = $this->text('Codigo: ' . $boleto->codigo, 48, 755, 13);

        $commands[] = '0 g';
        $commands[] = $this->text('Pasajero', 48, 690, 11, true);
        $commands[] = $this->text($boleto->pasajero_nombre . ' / ' . $boleto->pasajero_cedula, 48, 672, 13);
        $commands[] = $this->text('Ruta', 48, 636, 11, true);
        $commands[] = $this->text($boleto->salida->frecuencia->origen->nombre . ' a ' . $boleto->salida->frecuencia->destino->nombre, 48, 618, 15);
        $commands[] = $this->text('Fecha y hora', 48, 582, 11, true);
        $commands[] = $this->text($boleto->salida->fecha->format('d/m/Y') . ' ' . substr((string) $boleto->salida->hora_salida, 0, 5), 48, 564, 13);
        $commands[] = $this->text('Bus y asiento', 330, 582, 11, true);
        $commands[] = $this->text('Bus ' . $boleto->salida->bus->numero . ' - asiento ' . $boleto->asiento->numero . ' / ' . $boleto->asiento->tipoAsiento->nombre, 330, 564, 13);
        $commands[] = $this->text('Precio final', 330, 636, 11, true);
        $commands[] = $this->text('$' . number_format((float) $boleto->precio, 2) . ' / ' . ucfirst(str_replace('_', ' ', $boleto->estado)), 330, 618, 15);

        $commands[] = '0.9 0.92 0.95 RG 48 330 499 150 re S';
        $commands[] = $this->text('Codigo de barras', 48, 445, 11, true);
        $commands[] = $this->barcode($boleto->codigo, 60, 360, 64);
        $commands[] = $this->text($boleto->codigo, 60, 342, 12);
        $commands[] = $this->text('Presenta este boleto para validar el acceso al bus.', 48, 285, 12);

        $stream = implode("\n", $commands);
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream",
        ];

        return $this->buildPdf($objects);
    }

    private function text(string $text, int $x, int $y, int $size, bool $bold = false): string
    {
        $fontSize = $bold ? $size + 1 : $size;

        return 'BT /F1 ' . $fontSize . ' Tf ' . $x . ' ' . $y . ' Td (' . $this->escape($text) . ') Tj ET';
    }

    private function barcode(string $code, int $x, int $y, int $height): string
    {
        $patterns = [
            '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn', '*' => 'nwnnwnwnn',
        ];

        $encoded = '*' . strtoupper($code) . '*';
        $cursor = $x;
        $commands = ['0 g'];

        foreach (str_split($encoded) as $character) {
            $pattern = $patterns[$character] ?? $patterns['-'];
            foreach (str_split($pattern) as $index => $widthType) {
                $width = $widthType === 'w' ? 2.8 : 1.0;
                if ($index % 2 === 0) {
                    $commands[] = sprintf('%.2F %d %.2F %d re f', $cursor, $y, $width, $height);
                }
                $cursor += $width;
            }
            $cursor += 1.0;
        }

        return implode("\n", $commands);
    }

    private function escape(string $text): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii ?: $text);
    }

    private function buildPdf(array $objects): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }
}
