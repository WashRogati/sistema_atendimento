<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Configurações do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'sistema_atendimento';

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar o histórico do usuário
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT id, DATE_FORMAT(data_registro, '%d/%m/%Y') as data_registro, 
        DATE_FORMAT(data_retorno, '%d/%m/%Y') as data_retorno, 
        descricao, comentarios, pagamento 
        FROM historico_atendimentos 
        WHERE usuario_id = ? 
        ORDER BY data_registro DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Fechar a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Atendimentos</title>
    <link rel="stylesheet" href="./css/historico.css" />
</head>
<body>
    <header class="header">
        <h1>Histórico de Atendimento</h1>
    </header>
    <main class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-content">
                        <p><strong>Data do Registro:</strong> <?php echo htmlspecialchars($row['data_registro']); ?></p>
                        <p><strong>Data de Retorno:</strong> <?php echo htmlspecialchars($row['data_retorno']); ?></p>
                        <p><strong>Pagamento:</strong> <?php echo htmlspecialchars($row['pagamento']); ?></p>
                        <p><strong>Descrição de Atendimento:</strong></p>
                        <p><?php echo htmlspecialchars($row['descricao']); ?></p>
                        <p><strong>Comentários:</strong></p>
                        <p><?php echo htmlspecialchars($row['comentarios']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card">
                <p>Sem registros de atendimento.</p>
            </div>
        <?php endif; ?>
        <div class="btn-container">
            <a href="dashboard.php" class="btn">Voltar ao Dashboard</a>
        </div>
    </main>
</body>
</html>
