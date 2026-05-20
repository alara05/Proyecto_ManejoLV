<?php

namespace App\Services;

use Illuminate\Support\Collection;

class NotificacionPdfGenerator
{
    public function generate(Collection $notificaciones): string
    {
        $commands = [];
        $commands[] = '0.96 0.97 0.99 rg 0 0 595 842 re f';
        $commands[] = '0.08 0.12 0.18 rg 0 730 595 112 re f';
        $commands[] = $this->text('REPORTE DE NOTIFICACIONES', 48, 785, 22, true);
        $commands[] = $this->text('Generado: ' . now()->format('d/m/Y H:i'), 48, 755, 12);

        $y = 700;

        if ($notificaciones->isEmpty()) {
            $commands[] = '0 g';
            $commands[] = $this->text('No existen notificaciones para exportar.', 48, $y, 12);
        }

        foreach ($notificaciones->take(18) as $notificacion) {
            $data = $notificacion->data ?? [];
            $estado = $notificacion->read_at ? 'Leida' : 'Pendiente';

            $commands[] = '0 g';
            $commands[] = $this->text(($data['titulo'] ?? 'Notificacion') . ' - ' . $estado, 48, $y, 12, true);
            $commands[] = $this->text($this->limit($data['mensaje'] ?? 'Sin detalle.', 88), 48, $y - 18, 10);
            $commands[] = $this->text('Fecha: ' . $notificacion->created_at->format('d/m/Y H:i'), 48, $y - 34, 9);
            $commands[] = '0.85 0.87 0.91 RG 48 ' . ($y - 48) . ' 499 1 re S';

            $y -= 66;

            if ($y < 80) {
                break;
            }
        }

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

    private function limit(string $text, int $length): string
    {
        return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
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
