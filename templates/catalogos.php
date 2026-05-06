<?php
session_start();

// 🔐 Validación (ejemplo)
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Acceso denegado";
    exit;
}

$path = __DIR__ . '/../private/Docs/catalogos/CATALOGO_FIBRASAN_2026.pdf';

if (!file_exists($path)) {
    http_response_code(404);
    echo "Archivo no encontrado";
    exit;
}

// 📄 Forzar apertura en navegador
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Aviso_de_Privacidad_Lerma.pdf"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit;