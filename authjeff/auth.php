<?php
// Lista de códigos válidos
$codes = [
    "J3ff3r3S0urC3s", // Exemplo de código válido
    "OUTRO_CODIGO",   // Adicione mais códigos conforme necessário
    "MAIS_UM_CODIGO"
];

// Arquivo onde os códigos usados e IPs serão registrados
$usedCodesFile = 'used_codes_ips.json';

// Verifica se o arquivo existe; caso contrário, cria
if (!file_exists($usedCodesFile)) {
    file_put_contents($usedCodesFile, json_encode([])); // Cria um JSON vazio
}

// Carrega os códigos usados e os IPs autorizados
$usedCodesData = json_decode(file_get_contents($usedCodesFile), true);

// Obtém o IP do servidor que está fazendo a requisição
$serverIp = $_SERVER['REMOTE_ADDR'];

// Verifica se o código foi enviado via parâmetro GET
if (!isset($_GET['code'])) {
    http_response_code(400); // Código 400 = Bad Request
    echo "Erro: Nenhum código foi enviado.";
    exit;
}

// Código enviado via GET
$code = $_GET['code'];

// Verifica se o código está na lista de válidos
if (!in_array($code, $codes)) {
    http_response_code(403); // Código 403 = Forbidden
    echo "Erro: Código inválido.";
    exit;
}

// Verifica se o código já foi usado
if (isset($usedCodesData[$code])) {
    // Código já utilizado, verifica se o IP é o mesmo
    if ($usedCodesData[$code] !== $serverIp) {
        http_response_code(403); // Código 403 = Forbidden
        echo "Erro: Código já utilizado por outro IP.";
        exit;
    }
    // Código usado, mas o IP é autorizado
    http_response_code(200); // Código 200 = OK
    echo "Sucesso: IP já autorizado para este código.";
    exit;
}

// Se o código é válido e ainda não foi usado, associa o IP ao código
$usedCodesData[$code] = $serverIp;

// Salva os dados atualizados no arquivo
file_put_contents($usedCodesFile, json_encode($usedCodesData, JSON_PRETTY_PRINT));

// Retorna uma mensagem de sucesso
http_response_code(200); // Código 200 = OK
echo "Sucesso: Código válido e IP autorizado.";
