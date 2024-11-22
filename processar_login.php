<?php
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

// Capturar os dados do formulário
$email = $_POST['email'];
$senha = $_POST['senha'];

// Verificar se o e-mail está registrado
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Verificar senha
    $usuario = $result->fetch_assoc();
    if (password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header("Location: dashboard.php");
        exit;
    } else {
        die("Senha incorreta.");
    }
} else {
    die("E-mail não registrado.");
}

// Fechar conexão
$stmt->close();
$conn->close();
?>
