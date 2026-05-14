<?php
ob_start();
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener la API KEY desde .env
define('RESEND_API_KEY', $_ENV['RESEND_API_KEY']);

function resend_send(array $payload): array
{
    $ch = curl_init('https://api.resend.com/emails');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . RESEND_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
    ]);

    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        return ['ok' => false, 'error' => curl_error($ch)];
    }

    curl_close($ch);

    return [
        'ok' => $code >= 200 && $code < 300,
        'code' => $code,
        'body' => $body
    ];
}

function build_xlsx_b64(array $post): string
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Pedido');

    $fields = [
        'Nombre'            => 'nombre',
        'Teléfono'          => 'telefono',
        'Correo'            => 'correo',
        'Tipo de Tejido'    => 'tejido',
        'Tipo de Hilatura'  => 'hilatura',
        'Composición'       => 'composicion',
        'Peso (Grs/m²)'     => 'peso',
        'Ancho (Cm)'        => 'ancho',
        'Engomado'          => 'engomado',
        'Corte de Orilla'   => 'corte_orilla',
        'Tipo de Acabado'   => 'acabado',
        'Cuellos y Puños'   => 'cuellos',
        'Liso o Estampado'  => 'liso_estampado',
        'Cantidad Requerida' => 'cantidad',
        'Con Lycra'         => 'con_lycra',
        'Observaciones'     => 'observaciones',
        'Agente Comercial'  => '',
    ];

    // ── Estilos reutilizables ──────────────────────────
    $styleTitulo = [
        'font' => [
            'bold' => true,
            'size' => 14,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => 'solid',
            'startColor' => ['rgb' => '1A1A1A'],
        ],
        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
    ];

    $styleHeader = [
        'font' => [
            'bold' => true,
            'size' => 11,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => 'solid',
            'startColor' => ['rgb' => '2AA8A0'],
        ],
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
        'borders' => [
            'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']],
        ],
    ];

    $styleValorPar = [
        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F5F3EF']],
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'wrapText' => true],
        'borders' => [
            'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E8E4DC']],
        ],
    ];

    $styleValorImpar = [
        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => 'left', 'vertical' => 'center', 'wrapText' => true],
        'borders' => [
            'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E8E4DC']],
        ],
    ];

    // ── Fila 1: Encabezados de columna ─────────────────
    $sheet->setCellValue('A1', 'Campo');
    $sheet->setCellValue('B1', 'Valor');
    $sheet->getStyle('A1:B1')->applyFromArray($styleHeader);
    $sheet->getRowDimension(1)->setRowHeight(22);

    // ── Filas de datos (fecha como primer campo) ────────
    $sheet->setCellValue('A2', 'Fecha de envío');
    $sheet->setCellValue('B2', date('d/m/Y H:i'));
    $sheet->getStyle('A2')->applyFromArray(array_merge($styleValorImpar, [
        'font' => ['bold' => true, 'color' => ['rgb' => '2AA8A0']],
    ]));
    $sheet->getStyle('B2')->applyFromArray($styleValorImpar);
    $sheet->getRowDimension(2)->setRowHeight(18);

    // ── Freeze encabezados ─────────────────────────────
    $sheet->freezePane('A2');

    $row = 3;
    foreach ($fields as $label => $key) {
        $value = $key ? (trim($post[$key] ?? '') ?: '—') : '';
        $sheet->setCellValue('A' . $row, $label);
        $sheet->setCellValue('B' . $row, $value);

        $style = ($row % 2 === 0) ? $styleValorPar : $styleValorImpar;

        // Columna A siempre con fondo teal suave en el label
        $sheet->getStyle('A' . $row)->applyFromArray(array_merge($style, [
            'font' => ['bold' => true, 'color' => ['rgb' => '2AA8A0']],
        ]));
        $sheet->getStyle('B' . $row)->applyFromArray($style);
        $sheet->getRowDimension($row)->setRowHeight(18);

        $row++;
    }

    // ── Anchos de columna ──────────────────────────────
    $sheet->getColumnDimension('A')->setWidth(28);
    $sheet->getColumnDimension('B')->setWidth(48);

    // ── Freeze encabezados ─────────────────────────────
    $sheet->freezePane('A3');

    $tmp = tempnam(sys_get_temp_dir(), 'fib_') . '.xlsx';
    $writer = new XlsxWriter($spreadsheet);
    $writer->save($tmp);

    $b64 = base64_encode(file_get_contents($tmp));
    @unlink($tmp);
    return $b64;
}

// ─────────────────────────────────────────────
// PROCESAR POST
// ─────────────────────────────────────────────
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Evitar que el navegador cachee la respuesta JSON
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');


    $required = [
        'nombre',
        'telefono',
        'correo',
        'cantidad',
        'tejido',
        'acabado',
        'hilatura',
        'composicion',
        'liso_estampado',
        'engomado',
        'corte_orilla',
        'con_lycra',
        'ancho',
        'peso',
        'cuellos'
    ];
    $missing = [];
    foreach ($required as $f) {
        if (empty(trim($_POST[$f] ?? ''))) $missing[] = $f;
    }

    if ($missing) {
        ob_clean(); // ← AGREGAR
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Por favor completa todos los campos obligatorios.']);
        exit;
    } else {

        $labels = [
            'nombre' => 'Nombre',
            'telefono' => 'Teléfono',
            'correo' => 'Correo',
            'cantidad' => 'Cantidad requerida',
            'tejido' => 'Tipo de tejido',
            'acabado' => 'Tipo de acabado',
            'hilatura' => 'Tipo de hilatura',
            'composicion' => 'Composición',
            'liso_estampado' => 'Liso o estampado',
            'engomado' => 'Engomado',
            'corte_orilla' => 'Corte de orilla',
            'con_lycra' => 'Con lycra',
            'ancho' => 'Ancho solicitado (cm)',
            'peso' => 'Peso solicitado (grs/m²)',
            'cuellos' => 'Cuellos y puños',
            'observaciones' => 'Observaciones',
        ];

        // ── Excel ──────────────────────────────────────────
        $xlsRows = [
            ['Solicitud de Pedido — Fibrasan · ' . date('d/m/Y H:i'), ''],
            ['Campo', 'Valor'],
        ];
        foreach ($labels as $key => $label) {
            $xlsRows[] = [$label, trim($_POST[$key] ?? '') ?: '—'];
        }

        // Solo esta línea, nada más
        $xlsB64 = build_xlsx_b64($_POST);
        $xlsName = 'pedido_' . date('Ymd_His') . '.xlsx';

        // ── HTML del correo ────────────────────────────────
        $tableRows = '';
        $i = 0;
        foreach ($labels as $key => $label) {
            $val = htmlspecialchars(trim($_POST[$key] ?? '') ?: '—');
            $bg = $i % 2 === 0 ? '#ffffff' : '#f5f3ef';
            $tableRows .= "<tr>
                    <td
                        style='padding:9px 14px;font-weight:600;font-size:13px;background:{$bg};border:1px solid #e8e4dc;color:#333'>
                        "
                . htmlspecialchars($label) . "</td>
                    <td style='padding:9px 14px;font-size:13px;background:{$bg};border:1px solid #e8e4dc;color:#555'>
                        {$val}</td>
                </tr>";
            $i++;
        }

        $html = "
<div style='font-family:Arial,sans-serif;max-width:620px;margin:0 auto;background:#f5f3ef;padding:16px'>
    <div style='background:#1a1a1a;padding:20px 24px;border-radius:12px 12px 0 0'>
        <p style='margin:0;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:#888'>Fibrasan · Pedidos</p>
        <h1 style='margin:6px 0 0;font-size:20px;font-weight:300;color:#fff'>
            Nueva solicitud de <span style='color:#2aa8a0'>pedido</span>
        </h1>
    </div>
    <div style='background:#fff;border:1px solid #e8e4dc;border-top:none;border-radius:0 0 12px 12px;padding:0'>

        <div style='background:#2aa8a0;padding:10px 16px;display:flex'>
            <span style='color:#fff;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;width:45%'>Campo</span>
            <span style='color:#fff;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase'>Valor</span>
        </div>";

        $i = 0;
        foreach ($labels as $key => $label) {
            $val = htmlspecialchars(trim($_POST[$key] ?? '') ?: '—');
            $bg = $i % 2 === 0 ? '#ffffff' : '#f5f3ef';
            $html .= "
        <div style='display:flex;flex-wrap:wrap;background:{$bg};border-bottom:1px solid #e8e4dc'>
            <div style='padding:10px 16px;font-weight:700;font-size:13px;color:#333;width:45%;box-sizing:border-box;min-width:120px'>"
                . htmlspecialchars($label) . "</div>
            <div style='padding:10px 16px;font-size:13px;color:#555;flex:1;min-width:120px;word-break:break-word'>{$val}</div>
        </div>";
            $i++;
        }

        $html .= "
        <div style='padding:14px 16px'>
            <p style='font-size:12px;color:#aaa;margin:0'>El Excel con todos los datos está adjunto a este correo.</p>
        </div>
    </div>
</div>";

        $asunto = 'Nueva solicitud de pedido — ' . trim($_POST['nombre']);

        // ── Envío: UN solo correo con ambos destinatarios ──
        $res = resend_send([
            'from' => 'onboarding@resend.dev',
            // 'to' => ['marketing@fibrasan.com.mx'],
            'to' => ['soyjiomartinez@gmail.com'],
            'reply_to' => trim($_POST['correo']),
            'subject' => $asunto,
            'html' => $html,
            'attachments' => [
                ['filename' => $xlsName, 'content' => $xlsB64],
            ],
        ]);


        $success = $res['ok'];
        if (!$success) {
            $error = 'Error al enviar el correo: ' . $res['code'] . ' - ' . ($res['body'] ?? 'Sin respuesta');
        }
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'error' => $error
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Pedido — Fibrasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f3ef]">

    <section class="min-h-screen py-16 px-4">

        <!-- Header -->
        <div class="max-w-4xl mx-auto mb-12">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-px w-8 bg-[#1a1a1a]"></div>
                <span class="text-[11px] tracking-[0.2em] uppercase font-medium text-[#888]">Fibrasan ·
                    Pedidos</span>
            </div>
            <h1 class="text-[42px] leading-none font-light text-[#1a1a1a] tracking-tight mb-3">
                Solicitud de <em class="not-italic text-[#2aa8a0]">pedido</em>
            </h1>
            <p class="text-[15px] text-[#666] max-w-md leading-relaxed">
                Completa el formulario y nuestro equipo de ventas te contactará a la brevedad con
                disponibilidad y
                cotización.
            </p>
        </div>

        <?php if ($success): ?>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white border border-[#e8e4dc] rounded-2xl p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-[#2aa8a0]/10 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-[#2aa8a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-2xl font-light text-[#1a1a1a] mb-3">Solicitud enviada</h2>
                <p class="text-[15px] text-[#666]">Te contactaremos pronto con todos los detalles.</p>
            </div>
        </div>

        <?php else: ?>

        <?php if ($error): ?>
        <div class="max-w-4xl mx-auto mb-6">
            <div
                class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-sm text-red-700 flex items-center gap-3">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
        <?php endif; ?>

        <form id="pedidoForm" method="POST" action="" novalidate class="max-w-4xl mx-auto">
            <div class="bg-white border border-[#e8e4dc] rounded-2xl overflow-hidden">

                <!-- 01 Contacto -->
                <div class="p-8 border-b border-[#f0ece4]">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] tracking-[0.15em] uppercase text-[#999] font-medium">
                                01</p>
                            <h2 class="text-[15px] font-medium text-[#1a1a1a]">Datos de contacto</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <?php
                            $inputs = [
                                ['type' => 'text',  'name' => 'nombre',   'label' => 'Nombre',            'ph' => 'Tu nombre completo'],
                                ['type' => 'tel',   'name' => 'telefono', 'label' => 'Teléfono',           'ph' => '10 dígitos'],
                                ['type' => 'email', 'name' => 'correo',   'label' => 'Correo electrónico', 'ph' => 'correo@ejemplo.com'],
                            ];
                            foreach ($inputs as $inp): ?>
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                <?= $inp['label'] ?> <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <input type="<?= $inp['type'] ?>" name="<?= $inp['name'] ?>"
                                value="<?= htmlspecialchars($_POST[$inp['name']] ?? '') ?>"
                                placeholder="<?= $inp['ph'] ?>"
                                class="w-full px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] placeholder-[#bbb] outline-none focus:border-[#2aa8a0] transition-all"
                                required>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 02 Especificaciones -->
                <div class="p-8 border-b border-[#f0ece4]">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] tracking-[0.15em] uppercase text-[#999] font-medium">
                                02</p>
                            <h2 class="text-[15px] font-medium text-[#1a1a1a]">Especificaciones del
                                pedido</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                        <!-- Cantidad -->
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                Cantidad requerida <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <div class="relative">
                                <select name="cantidad"
                                    class="w-full appearance-none px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] outline-none focus:border-[#2aa8a0] transition-all pr-10 cursor-pointer"
                                    required>
                                    <option value="" disabled <?= empty($_POST['cantidad']) ? 'selected' : '' ?>>
                                        Selecciona…</option>
                                    <?php foreach (['350kg', '400kg', '450kg', '500kg', '550kg', '600kg', '650kg', '700kg', 'Más de 700kg'] as $c): ?>
                                    <option value="<?= $c ?>"
                                        <?= (($_POST['cantidad'] ?? '') === $c) ? 'selected' : '' ?>>
                                        <?= $c ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-4 h-4 text-[#aaa]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <?php
                            $selects = [
                                ['name' => 'tejido',        'label' => 'Tipo de tejido',   'opts' => ['Jersey', 'Interlock', 'Piqué', 'Rib', 'French Terry', 'Jacquard', 'Felpa', 'Cardigan', 'Wafles']],
                                ['name' => 'acabado',       'label' => 'Tipo de acabado',  'opts' => ['Abierto', 'Tubular', 'Body size']],
                                ['name' => 'hilatura',      'label' => 'Tipo de hilatura', 'opts' => ['Open end', 'Cardado', 'Peinado', 'Heather (jaspe)', 'Aspen (avena)']],
                                ['name' => 'composicion',   'label' => 'Composición',      'opts' => ['50% Polyester 50% Algodón', '100% Algodón', '100% Polyester', '90% Algodón 10% Polyester', '99% Algodón 1% Polyester']],
                                ['name' => 'liso_estampado', 'label' => 'Liso o estampado', 'opts' => ['Liso', 'Estampado']],
                            ];
                            foreach ($selects as $s): ?>
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                <?= $s['label'] ?> <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <div class="relative">
                                <select name="<?= $s['name'] ?>"
                                    class="w-full appearance-none px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] outline-none focus:border-[#2aa8a0] transition-all pr-10 cursor-pointer"
                                    required>
                                    <option value="" disabled <?= empty($_POST[$s['name']]) ? 'selected' : '' ?>>
                                        Selecciona…</option>
                                    <?php foreach ($s['opts'] as $opt): ?>
                                    <option value="<?= $opt ?>"
                                        <?= (($_POST[$s['name']] ?? '') === $opt) ? 'selected' : '' ?>>
                                        <?= $opt ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-4 h-4 text-[#aaa]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 03 Acabados -->
                <div class="p-8 border-b border-[#f0ece4]">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] tracking-[0.15em] uppercase text-[#999] font-medium">
                                03</p>
                            <h2 class="text-[15px] font-medium text-[#1a1a1a]">Acabados y detalles</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                        <?php
                            $bools = [
                                ['name' => 'engomado',     'label' => 'Engomado'],
                                ['name' => 'corte_orilla', 'label' => 'Corte de orilla'],
                                ['name' => 'con_lycra',    'label' => 'Con lycra'],
                                ['name' => 'cuellos',      'label' => 'Cuellos y puños', 'hint' => 'Indica medidas en observaciones.'],
                            ];
                            foreach ($bools as $bs): ?>
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                <?= $bs['label'] ?> <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <div class="relative">
                                <select name="<?= $bs['name'] ?>"
                                    class="w-full appearance-none px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] outline-none focus:border-[#2aa8a0] transition-all pr-10 cursor-pointer"
                                    required>
                                    <option value="" disabled <?= empty($_POST[$bs['name']]) ? 'selected' : '' ?>>—
                                    </option>
                                    <option value="Sí" <?= (($_POST[$bs['name']] ?? '') === 'Sí') ? 'selected' : '' ?>>
                                        Sí</option>
                                    <option value="No" <?= (($_POST[$bs['name']] ?? '') === 'No') ? 'selected' : '' ?>>
                                        No</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-4 h-4 text-[#aaa]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <?php if (!empty($bs['hint'])): ?>
                            <p class="text-[11px] text-[#aaa] mt-1.5 leading-snug"><?= $bs['hint'] ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 04 Medidas -->
                <div class="p-8 border-b border-[#f0ece4]">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] tracking-[0.15em] uppercase text-[#999] font-medium">
                                04</p>
                            <h2 class="text-[15px] font-medium text-[#1a1a1a]">Medidas</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-lg">
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                Ancho solicitado (cm) <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <input type="number" name="ancho" value="<?= htmlspecialchars($_POST['ancho'] ?? '') ?>"
                                min="1" step="0.1" placeholder="ej. 160"
                                class="w-full px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] placeholder-[#bbb] outline-none focus:border-[#2aa8a0] transition-all"
                                required>
                        </div>
                        <div>
                            <label class="block text-[12px] font-medium text-[#555] mb-2 tracking-wide">
                                Peso solicitado (grs/m²) <span class="text-[#2aa8a0]">*</span>
                            </label>
                            <input type="number" name="peso" value="<?= htmlspecialchars($_POST['peso'] ?? '') ?>"
                                min="1" step="0.1" placeholder="ej. 220"
                                class="w-full px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] placeholder-[#bbb] outline-none focus:border-[#2aa8a0] transition-all"
                                required>
                        </div>
                    </div>
                </div>

                <!-- 05 Observaciones -->
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] tracking-[0.15em] uppercase text-[#999] font-medium">
                                05</p>
                            <h2 class="text-[15px] font-medium text-[#1a1a1a]">Observaciones</h2>
                        </div>
                    </div>
                    <textarea name="observaciones" rows="4"
                        placeholder="Medidas de cuellos/puños, colores, referencias, cualquier detalle adicional…"
                        class="w-full px-4 py-3 bg-[#f8f6f2] border border-[#e8e4dc] rounded-xl text-[14px] text-[#1a1a1a] placeholder-[#bbb] outline-none focus:border-[#2aa8a0] transition-all resize-none"><?= htmlspecialchars($_POST['observaciones'] ?? '') ?></textarea>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-between flex-wrap gap-4">
                <p class="text-[12px] text-[#aaa]"><span class="text-[#2aa8a0]">*</span> Campos
                    obligatorios</p>
                <button id="submitBtn" type="submit"
                    class="inline-flex items-center gap-3 px-8 py-3.5 bg-[#1a1a1a] text-white text-[14px] font-medium rounded-xl">

                    <span id="btnText">Enviar solicitud</span>

                    <svg id="loader" class="w-4 h-4 animate-spin hidden" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" fill="none" />
                    </svg>
                </button>
            </div>

        </form>

        <?php endif; ?>

    </section>

    <script>
    (function() {
        // Esperar al DOM
        window.addEventListener('load', function() {
            var form = document.getElementById('pedidoForm');
            if (!form) return;

            form.onsubmit = async function(e) {
                e.preventDefault();
                e.stopPropagation();

                var btn = document.getElementById('submitBtn');
                var btnText = document.getElementById('btnText');
                var loader = document.getElementById('loader');

                btn.disabled = true;
                loader.classList.remove('hidden');
                btnText.textContent = 'Enviando...';

                var formData = new FormData(form);

                try {
                    var response = await fetch('templates/pedidos.php', {
                        method: 'POST',
                        body: formData
                    });

                    var text = await response.text();
                    console.log('RAW:', text);

                    var data = JSON.parse(text);

                    if (data.success) {
                        form.parentNode.innerHTML =
                            '<div style="background:#fff;border:1px solid #e8e4dc;border-radius:16px;padding:48px;text-align:center;max-width:896px;margin:0 auto"><h2 style="font-size:24px;color:#1a1a1a;font-weight:300;margin-bottom:12px">Solicitud enviada correctamente</h2><p style="color:#666;font-size:15px">Te contactaremos pronto con disponibilidad y cotización.</p></div>';
                    } else {
                        alert(data.error || 'Error al procesar');
                        btn.disabled = false;
                        loader.classList.add('hidden');
                        btnText.textContent = 'Enviar solicitud';
                    }
                } catch (err) {
                    alert('Error: ' + err.message +
                        '\nAbre DevTools > Network y dime qué aparece en la respuesta'
                    );
                    btn.disabled = false;
                    loader.classList.add('hidden');
                    btnText.textContent = 'Enviar solicitud';
                }

                return false;
            };
        });
    })();
    </script>

</body>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pedidoForm');
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const loader = document.getElementById('loader');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // ← Esto es clave

        // Desactivar botón
        btn.disabled = true;
        loader.classList.remove('hidden');
        btnText.textContent = 'Enviando...';

        const formData = new FormData(form);

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            // Primero lee el texto crudo para debuggear si algo falla
            const text = await response.text();
            console.log('Respuesta raw:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('El servidor no devolvió JSON válido');
            }

            if (data.success) {
                // Reemplazar el formulario por mensaje de éxito
                const successHTML = `
                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white border border-[#e8e4dc] rounded-2xl p-12 text-center">
                            <div class="w-16 h-16 rounded-full bg-[#2aa8a0]/10 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-8 h-8 text-[#2aa8a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-light text-[#1a1a1a] mb-3">Solicitud enviada correctamente</h2>
                            <p class="text-[15px] text-[#666]">Te contactaremos pronto con disponibilidad y cotización.</p>
                        </div>
                    </div>
                `;

                // Reemplazar todo el contenido del formulario por el éxito
                form.outerHTML = successHTML;

            } else {
                alert(data.error || 'Ocurrió un error al procesar la solicitud');
            }

        } catch (err) {
            console.error(err);
            alert('Error de conexión o el servidor respondió mal: ' + err.message);
        } finally {
            // Restaurar botón (solo si no se reemplazó el form)
            if (document.getElementById('submitBtn')) {
                btn.disabled = false;
                loader.classList.add('hidden');
                btnText.textContent = 'Enviar solicitud';
            }
        }
    });
});
</script>

</html>