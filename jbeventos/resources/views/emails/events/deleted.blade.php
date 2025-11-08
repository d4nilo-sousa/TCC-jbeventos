<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evento Cancelado</title>

    <style type="text/css">
        /* Cores de Destaque para EXCLUSÃO: VERMELHO */
        :root {
            --delete-color: #dc2626;
            --delete-dark: #991b1b;
            --delete-light: #fee2e2;
            --gray-text: #4b5563;
            --dark-text: #1f2937;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f4f7fa;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .main-content {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .button-link {
            text-decoration: none;
            display: inline-block;
            font-weight: 700;
            line-height: 1.5;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid var(--delete-dark);
            background-color: var(--delete-color);
            color: #ffffff !important;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 17px;
            letter-spacing: 0.5px;
        }

        .event-card-tag {
            background-color: var(--delete-color);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 9999px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
        }

        .icon-color {
            color: var(--delete-color);
        }
    </style>
</head>

<body style="margin:0;padding:0;background-color:#f4f7fa;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation"
        style="background-color:#f4f7fa;">
        <tr>
            <td align="center" style="padding: 20px 10px;">

                <table class="container" border="0" cellspacing="0" cellpadding="0" role="presentation"
                    style="max-width:600px;width:100%;">
                    <tr>
                        <td align="left" class="main-content">

                            <h1
                                style="color:var(--dark-text);margin-top:0;font-size:28px;font-weight:800;line-height:1.2;">
                                Olá, {{ $user->name }} 👋
                            </h1>
                            <p style="font-size:17px;color:var(--gray-text);margin-bottom:20px;line-height:1.6;">
                                O evento <span style="font-weight: 600;">{{ $event->event_name }}</span> foi <span
                                    style="font-weight: 600;">cancelado/excluído</span>! ❌
                            </p>

                            <div style="border-top:1px solid #f3f4f6;padding-top:0px;margin-bottom:30px;"></div>

                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"
                                class="event-card"
                                style="border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.05);background-color:#ffffff;">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="height:200px;background-color:#fef2f2;overflow:hidden;position:relative;border-top-left-radius:16px;border-top-right-radius:16px;">

                                            @if ($event->event_image)
                                                @php
                                                    $path = storage_path('app/public/' . $event->event_image);
                                                    $base64 =
                                                        'data:image/jpeg;base64,' .
                                                        base64_encode(file_get_contents($path));
                                                @endphp

                                                <img src="{{ $base64 }}" alt="{{ $event->event_name }}"
                                                    width="600" height="200"
                                                    style="display:block;object-fit:cover;width:100%;height:200px;max-width:100%;">
                                            @else
                                                <table role="presentation" width="100%" height="200" border="0"
                                                    cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td align="center" valign="middle"
                                                            style="background-color: #f3f4f6; color:#0ea5e9; height: 200px; text-align: center;">
                                                            <div style="display: inline-block; text-align: center;">
                                                                <span
                                                                    style="font-size:48px; line-height:1; display:block;">📝</span>
                                                                <p
                                                                    style="margin: 8px 0 0 0; font-size:15px; font-weight:500; color:#4b5563;">
                                                                    Sem Imagem de Capa
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif

                                            <div style="position:absolute;top:12px;right:12px;">
                                                <span class="event-card-tag">
                                                    {{ $event->event_type === 'course' ? 'CURSO' : ($event->event_type === 'general' ? 'GERAL' : '') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div style="padding:24px;text-align:left;">
                                            <h3
                                                style="font-size:22px; font-weight:800; color:var(--dark-text); margin-top:0; margin-bottom:12px;">
                                                {{ $event->event_name }}
                                            </h3>

                                            <table role="presentation" border="0" cellspacing="0" cellpadding="0"
                                                style="margin-bottom:20px;">
                                                <tr>
                                                    @forelse ($event->eventCategories as $category)
                                                        <td style="padding-right:8px;padding-bottom:4px;">
                                                            <span
                                                                style="background-color:#f3f4f6;color:#4b5563;padding:3px 10px;border-radius:9999px;border:1px solid #e5e7eb;font-size:11px;white-space:nowrap;display:inline-block;font-weight:600;">
                                                                {{ $category->category_name }}
                                                            </span>
                                                        </td>
                                                    @empty
                                                        <td style="padding-bottom:4px;">
                                                            <span
                                                                style="background-color:#f3f4f6;color:#4b5563;padding:3px 10px;border-radius:9999px;border:1px solid #e5e7eb;font-size:11px;white-space:nowrap;display:inline-block;font-weight:600;">
                                                                Sem Categoria
                                                            </span>
                                                        </td>
                                                    @endforelse
                                                </tr>
                                            </table>

                                            <div
                                                style="border-top:1px solid #f3f4f6;padding-top:16px;font-size:15px;color:var(--gray-text);line-height:1.6;">
                                                <table role="presentation" width="100%" border="0" cellspacing="0"
                                                    cellpadding="0">
                                                    <tr>
                                                        <td width="24" valign="top" style="line-height:1;">
                                                            <span class="icon-color"
                                                                style="color:#dc2626;font-size:20px;">📍</span>
                                                        </td>
                                                        <td valign="top" style="padding-left:8px;">
                                                            <span style="display:block;font-weight: 600;">Local:</span>
                                                            <span
                                                                style="display:block;">{{ $event->event_location }}</span>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table role="presentation" width="100%" border="0" cellspacing="0"
                                                    cellpadding="0" style="margin-top:10px;">
                                                    <tr>
                                                        <td width="24" valign="top" style="line-height:1;">
                                                            <span class="icon-color"
                                                                style="color:#dc2626;font-size:20px;">🗓️</span>
                                                        </td>
                                                        <td valign="top" style="padding-left:8px;">
                                                            <span style="display:block;font-weight: 600;">Data e
                                                                Hora:</span>
                                                            <span style="display:block;">
                                                                {{ \Carbon\Carbon::parse($event->event_scheduled_at)->isoFormat('dddd, D [de] MMMM [de] YYYY, [às] HH:mm') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="margin-top:30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('events.index') }}" class="button-link">
                                            Ver Eventos &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-top:40px;font-size:13px;color:#6b7280;text-align:center;line-height:1.6;">
                                Se você não consegue clicar no botão acima, copie e cole o seguinte link no seu
                                navegador:<br>
                                <a href="{{ route('events.index') }}"
                                    style="color:#dc2626; text-decoration:underline;">
                                    {{ route('events.index') }}
                                </a>
                                <br><br>
                                &copy; {{ date('Y') }} <span
                                    style="font-weight: 600;">{{ config('app.name') }}</span>. Todos os direitos
                                reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
