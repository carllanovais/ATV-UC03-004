<?php
$mensagem = "";
$dados = [];

// Entrada
$capital = filter_input(INPUT_GET, "capitalBuscada", FILTER_SANITIZE_SPECIAL_CHARS);

if (!isset($capital) || strlen($capital) < 2) {
    $mensagem = "Capital inválida!";
} else {
    $url = "https://restcountries.com/v3.1/capital/" . urlencode($capital);

    $configuracoes = [
        "http" => [
            "method" => "GET",
            "header" => "Content-Type: application/json"
        ]
    ];

    $context = stream_context_create($configuracoes);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        $mensagem = "Erro ao acessar a API.";
    } else {
        $data = json_decode($response, true);

        if (isset($data['status']) || empty($data)) {
            $mensagem = "Capital não encontrada.";
        } else {
            $pais = $data[0];

            $dados['nome'] = $pais['name']['common'] ?? '';
            $dados['populacao'] = number_format($pais['population'] ?? 0);
            $dados['regiao'] = $pais['region'] ?? '';
            $dados['bandeira'] = $pais['flags']['png'] ?? '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado - País por Capital</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Resultado da Busca</h2>

    <div id="resultado">
        <span id="error"><?= $mensagem ?></span>

        <div>
            <label>Nome do País:</label>
            <input type="text" value="<?= $dados['nome'] ?? '' ?>" disabled>
        </div>

        <div>
            <label>População:</label>
            <input type="text" value="<?= $dados['populacao'] ?? '' ?>" disabled>
        </div>

        <div>
            <label>Região:</label>
            <input type="text" value="<?= $dados['regiao'] ?? '' ?>" disabled>
        </div>

        <div>
            <label>Bandeira:</label><br>
            <?php if (!empty($dados['bandeira'])): ?>
                <img src="<?= $dados['bandeira'] ?>" alt="Bandeira de <?= $dados['nome'] ?>">
            <?php endif; ?>
        </div>
    </div>

    <br><a href="index.html"> Voltar</a>

</body>
</html>
