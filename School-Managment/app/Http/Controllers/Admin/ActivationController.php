<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountActivation;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ActivationController extends Controller
{
    /**
     * Students who haven't activated their account, with the status of their link.
     */
    public function index(AccountActivation $activation)
    {
        $students = User::students()
            ->where('is_registered', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $status = $activation->statusFor($students);

        return view('admin.activations.index', compact('students', 'status'));
    }

    /**
     * Send a new activation email to every pending student.
     * Students who got one less than a minute ago are skipped.
     */
    public function resendAll(AccountActivation $activation)
    {
        $students = User::students()->where('is_registered', false)->get();

        if ($students->isEmpty()) {
            return back()->with('success', __('No hay alumnos pendientes de activar su cuenta.'));
        }

        $sent = 0;
        $skipped = 0;

        foreach ($students as $student) {
            try {
                $activation->send($student) ? $sent++ : $skipped++;
            } catch (TransportExceptionInterface $e) {
                report($e);

                // The mail server is failing: stop instead of waiting for each student
                $message = __('No se pudo enviar el email a :email. Revisa la configuración del correo.', ['email' => $student->email]);

                return back()->with('error', $sent ? $message.' '.__('Antes del error se enviaron :count.', ['count' => $sent]) : $message);
            }
        }

        $message = trans_choice('Enviado :count email de activación.|Enviados :count emails de activación.', $sent);
        if ($skipped) {
            $message .= ' '.trans_choice(':count alumno omitido: se le envió uno hace menos de un minuto.|:count alumnos omitidos: se les envió uno hace menos de un minuto.', $skipped);
        }

        return back()->with('success', $message);
    }
}
