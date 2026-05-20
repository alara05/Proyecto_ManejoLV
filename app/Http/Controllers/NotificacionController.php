<?php

namespace App\Http\Controllers;

use App\Services\NotificacionPdfGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(Request $request): View
    {
        $notificaciones = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida(Request $request, string $notificacion): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notificacion)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('notificaciones.index'));
    }

    public function exportarPdf(Request $request, NotificacionPdfGenerator $generator): Response
    {
        $notificaciones = $request->user()
            ->notifications()
            ->latest()
            ->limit(18)
            ->get();

        return response($generator->generate($notificaciones), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="notificaciones-' . now()->format('Ymd-His') . '.pdf"',
        ]);
    }
}
