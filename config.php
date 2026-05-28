<?php
// ============================================================
// config.php — Configurações globais do site Toca do Coelho
// ============================================================

session_start();

// Auto-detect base path
$config_dir = str_replace('\\', '/', __DIR__);
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$base_path = str_replace($doc_root, '', $config_dir);

define('SITE_NAME', 'Toca do Coelho');
define('SITE_URL', $base_path); 

// Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'toca_do_coelho');
define('DB_USER', 'root');
define('DB_PASS', '12345678');
//define('DB_PASS', 'Carlosstc123$');

define('WHATSAPP_NUMBER', '5569992829089'); // Substitua pelo número real
define('WHATSAPP_MSG_DEFAULT', 'Olá, gostaria de mais informações!');

// Informações de contato
define('CONTACT_PHONE', '+55 (69) 99282-9089');
define('CONTACT_EMAIL', 'contato@tocadocoelho.com.br');
define('CONTACT_ADDRESS', 'Rua Sete de Setembro, 1234 - Centro, Porto Velho - RO');

// Helpers
function whatsapp_link(string $msg = WHATSAPP_MSG_DEFAULT): string {
    return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . urlencode($msg);
}

function asset(string $path): string {
    return SITE_URL . '/assets/' . ltrim($path, '/');
}

function page(string $path): string {
    return SITE_URL . '/pages/' . ltrim($path, '/');
}
