<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="box-sizing: border-box; background-color: #ffffff; color: #000000; height: 100%; line-height: 1.4; margin: 0; width: 100% !important;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff;">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; margin: auto; text-align: center; border-collapse: collapse;">
                <!-- Header -->
                <tr>
                    <td class="header" style="padding: 0; margin: 0;">
                        <img src="https://www.muvh.gov.py/sitio/wp-content/uploads/2023/08/MINISTERIO-DE-URBANISMO_VIVIENDA_HABITAT-Curvas-01-6.png"
                             alt="Ministerio de Urbanismo, Vivienda y Hábitat"
                             style="display: block; width: 100%; max-width: 600px; height: auto; margin: 0; padding: 0; border: none;">
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; padding: 0 20px;">
                        <table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff; margin: auto; border: 1px solid #ddd; border-radius: 8px;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell" style="color: #000000; padding: 30px;">
                                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                                    {{ $subcopy ?? '' }}
                                </td>
                            </tr>

                            <!-- Footer dentro del inner-body -->
                            <tr>
                                <td class="footer" style="text-align: center; background-color: #ffffff; padding: 20px; border-top: 1px solid #dddddd;">
                                    <p style="margin: 0; color: #000000; font-weight: bold;">
                                        &copy; {{ date('Y') }} SIPP - Sistema informático de postulación de proyectos
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
