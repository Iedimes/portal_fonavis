<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; margin: 0;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="700" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #dddddd; padding: 40px; box-shadow: 0 0 8px rgba(0,0,0,0.1); border-radius: 8px;">
                    <tr>
                        <td align="center" style="padding-bottom: 30px;">
                            <img src="https://www.muvh.gov.py/sitio/wp-content/uploads/2023/08/MINISTERIO-DE-URBANISMO_VIVIENDA_HABITAT-Curvas-01-6.png" alt="MUVH" height="70">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h2 style="text-align: center; font-size: 24px; color: #333; margin-top: 0;">Hola, {{ strtoupper($user->name) }}</h2>
                            <p style="text-align: center; font-size: 16px; color: #555;">Recibimos una solicitud para restablecer tu contraseña.</p>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}" style="background-color: #007bff; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-size: 16px; display: inline-block;">
                                    Restablecer contraseña
                                </a>
                            </div>

                            <p style="text-align: center; font-size: 15px; color: #555;">Este enlace expirará en 60 minutos.</p>
                            <p style="text-align: center; font-size: 15px; color: #555;">Si no hiciste esta solicitud, podés ignorar este mensaje.</p>

                            <p style="text-align: center; font-size: 14px; color: #777; word-break: break-word; margin-top: 20px;">
                                Si el botón anterior no funciona, copiá y pegá el siguiente enlace en tu navegador:<br>
                                <a href="{{ $url }}">{{ $url }}</a>
                            </p>

                            <hr style="margin: 40px 0; border: none; border-top: 1px solid #ddd;">

                            <p style="text-align: center; font-size: 15px; color: #444;">Saludos,<br><strong>Equipo del SIPP</strong></p>
                            <p style="font-size: 13px; text-align: center; color: #aaa;">© {{ date('Y') }} SIPP - Sistema informático de postulación de proyectos</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
