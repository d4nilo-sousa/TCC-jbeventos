<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evento Atualizado</title>

    <style type="text/css">
        /* Cores de Destaque para ATUALIZAÇÃO: AZUL/CIANO */
        :root {
            --update-color: #0ea5e9;
            /* Ciano/Azul vibrante (sky-500) */
            --update-dark: #0369a1;
            --update-light: #e0f2fe;
            --gray-text: #4b5563;
            --dark-text: #1f2937;
        }

        /* Reset básico e tipografia */
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

        /* Estilos do Conteúdo Principal */
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

        /* Estilos do Botão Principal - Cor AZUL/CIANO para Update */
        .button-link {
            text-decoration: none;
            display: inline-block;
            font-weight: 700;
            line-height: 1.5;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid #0369a1;
            background-color: #0ea5e9;
            /* Cor AZUL/CIANO */
            color: #ffffff !important;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 17px;
            letter-spacing: 0.5px;
        }

        /* Cor dos ícones e tags de destaque - AZUL/CIANO */
        .icon-color {
            color: #0ea5e9;
        }

        .event-card-tag {
            background-color: #0ea5e9;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 9999px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
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
                        <td align="left" class="main-content"
                            style="background-color:#ffffff;border-radius:12px;padding:30px;box-shadow:0 10px 20px -5px rgba(0,0,0,0.08);border: 1px solid #e5e7eb;">

                            <h1
                                style="color:var(--dark-text);margin-top:0;font-size:28px;font-weight:800;line-height:1.2;">
                                Olá, {{ $user->name }} 👋
                            </h1>
                            <p style="font-size:17px;color:var(--gray-text);margin-bottom:20px;line-height:1.6;">
                                O evento <span style="font-weight: 600;">{{ $event->event_name }}</span> foi <span
                                    style="font-weight: 600;">atualizado</span>! 🔄
                            </p>

                            <div style="border-top:1px solid #f3f4f6;padding-top:0px;margin-bottom:30px;"></div>

                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0"
                                class="event-card"
                                style="border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.05);background-color:#ffffff;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('events.show', $event->id) }}"
                                            style="display:block;text-decoration:none;">

                                            <div
                                                style="height:200px;background-color:#e5e7eb;overflow:hidden;position:relative;border-top-left-radius:16px;border-top-right-radius:16px;">


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
                                                    <table role="presentation" width="100%" height="200"
                                                        border="0" cellspacing="0" cellpadding="0">
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
                                                    <span class="event-card-tag"
                                                        style="background-color:#dc2626; color:#ffffff; font-size:12px; font-weight:700;
           padding:6px 12px; border-radius:9999px; box-shadow:0 2px 5px rgba(0,0,0,0.2);
           text-transform: uppercase;">
                                                        {{ $event->event_type === 'course' ? 'CURSO' : ($event->event_type === 'general' ? 'GERAL' : '') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div style="padding:24px;text-align:left;">

                                                <h3
                                                    style="font-size:22px;
           font-weight:800;
           color:var(--dark-text);
           margin-top:0;
           margin-bottom:12px;
           line-height:1.3;
           max-width:520px;              /* largura máxima fixa */
           word-wrap:break-word;         /* quebra palavras longas */
           overflow-wrap:break-word;     /* compatibilidade */
           white-space:normal;           /* garante quebra automática */
           ">
                                                    {{ $event->event_name }}
                                                </h3>

                                                <table role="presentation" border="0" cellspacing="0"
                                                    cellpadding="0" style="margin-bottom:20px;">
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

                                                    <table role="presentation" width="100%" border="0"
                                                        cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td width="24" valign="top" style="line-height:1;">
                                                                <span class="icon-color"
                                                                    style="color:#0ea5e9;font-size:20px;">📍</span>
                                                            </td>
                                                            <td valign="top" style="padding-left:8px;">
                                                                <span
                                                                    style="display:block;font-weight: 600;">Local:</span>
                                                                <span
                                                                    style="display:block;">{{ $event->event_location }}</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <table role="presentation" width="100%" border="0"
                                                        cellspacing="0" cellpadding="0" style="margin-top:10px;">
                                                        <tr>
                                                            <td width="24" valign="top" style="line-height:1;">
                                                                <span class="icon-color"
                                                                    style="color:#0ea5e9;font-size:20px;">🗓️</span>
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
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            @if (!empty($changedFields))
                                <div style="margin-top:30px; border-top:1px solid #f3f4f6; padding-top:20px;">
                                    <h4
                                        style="font-size:18px; font-weight:700; color:#dc2626; margin-top:0; margin-bottom:10px;
           border-left: 4px solid #dc2626; padding-left: 8px;">

                                        Detalhes da Atualização
                                    </h4>
                                    <ul style="font-size:15px; color:var(--gray-text); padding-left:20px; margin:0;">
    @foreach ($changedFields as $field => $value)
        @php
            $label = [
                'event_name' => 'Nome do Evento',
                'event_scheduled_at' => 'Data e Hora',
                'event_location' => 'Local do Evento',
                'courses' => 'Cursos Associados',
            ][$field] ?? ucfirst(str_replace('_', ' ', $field));
        @endphp

        <li style="margin-bottom:6px;">
            <span style="font-weight:600; color:var(--dark-text);">
                {{ $label }}
            </span>
            foi alterado.

            @if ($field === 'courses' && isset($oldCourses, $newCourses))
                @php
                    $oldCourses = collect($oldCourses);
                    $newCourses = collect($newCourses);
                    $addedCourses = $newCourses->diff($oldCourses);
                    $removedCourses = $oldCourses->diff($newCourses);
                @endphp

                @if ($addedCourses->count() || $removedCourses->count())
                    <ul style="margin-top:6px; padding-left:18px; list-style-type:none; font-size:14px;">
                        @foreach ($addedCourses as $course)
                            <li style="margin:3px 0;">
                                <span style="color:#16a34a;">🟢</span>
                                <span>{{ $course->course_name ?? $course }}</span>
                            </li>
                        @endforeach

                        @foreach ($removedCourses as $course)
                            <li style="margin:3px 0;">
                                <span style="color:#dc2626;">🔴</span>
                                <span>{{ $course->course_name ?? $course }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </li>
    @endforeach
</ul>
                                </div>
                            @endif

                            <table role="presentation" width="100%" border="0" cellspacing="0"
                                cellpadding="0" style="margin-top:30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('events.show', $event->id) }}" class="button-link"
                                            style="background-color:#dc2626; color:#ffffff !important; font-weight:700;
           padding:14px 32px; border-radius:8px; display:inline-block; text-decoration:none;
           font-size:17px; border:1px solid #b91c1c;">
                                            Ver Novidades &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin-top:40px;font-size:13px;color:#6b7280;text-align:center;line-height:1.6;">
                                Verifique as alterações importantes!<br>
                                Se você não consegue clicar no botão acima, copie e cole o seguinte link no seu
                                navegador:
                                <a href="{{ route('events.show', $event->id) }}"
                                    style="color:#dc2626;
           text-decoration:underline;
           overflow-wrap:anywhere;
           word-wrap:anywhere;
           word-break:normal;
           white-space:normal;">
                                    {{ route('events.show', $event->id) }}
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
