<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Muestra el formulario de contacto de soporte.
     */
    public function index()
    {
        return view('support.contact');
    }

    /**
     * Procesa el envío del formulario a Telegram.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        $userName = $user ? $user->name : 'Usuario Desconocido';
        $userEmail = $user ? $user->email : 'Sin email';

        // Escapar caracteres especiales de Markdown para prevenir output injection en Telegram
        $escapeMarkdown = fn(string $s): string => str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'],
            $s
        );

        $safeSubject = $escapeMarkdown($request->subject);
        $safeMessage = $escapeMarkdown($request->message);

        // Construir el mensaje Markdown para Telegram
        $text = "🚨 *Nuevo Ticket de Soporte (DX Portal)*\n\n";
        $text .= "👤 *De:* {$userName} ({$userEmail})\n";
        $text .= "📌 *Asunto:* {$safeSubject}\n\n";
        $text .= "📝 *Mensaje:*\n{$safeMessage}";

        $token = config('services.telegram-bot-api.token');
        $chatId = config('services.telegram-bot-api.chat_id') ?? '2795962'; // Fallback a Oskar si no hay chat_id

        if (!$token) {
            Log::error('Intento de envío a soporte fallido: Token de Telegram no configurado.');
            return redirect()->back()->with('error', 'Error del sistema: Canal de soporte no disponible.');
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                Log::info("Ticket de soporte enviado por {$userName}.");
                return redirect()->back()->with('success', 'Mensaje enviado a Oskar correctamente.');
            } else {
                Log::error("Error enviando ticket a Telegram: " . $response->body());
                return redirect()->back()->with('error', 'No se pudo contactar con Soporte IT. Inténtalo más tarde.');
            }
        } catch (\Exception $e) {
            Log::error("Excepción enviando ticket a Telegram: " . $e->getMessage());
            return redirect()->back()->with('error', 'Fallo de conexión con el servicio de soporte.');
        }
    }
}
