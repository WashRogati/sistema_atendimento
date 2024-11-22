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
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

// Inserir os dados no banco de dados
$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha);

try {
    $stmt->execute();
    echo "Cadastro realizado com sucesso!";
    header("Location: login.html");
    exit;
} catch (mysqli_sql_exception $e) {
    if ($conn->errno == 1062) {
        // Trata duplicidade (chave única violada)
        echo "Erro: O e-mail informado já está cadastrado.";
    } else {
        echo "Erro no cadastro: " . $e->getMessage();
    }
}

// Fechar conexão
$stmt->close();
$conn->close();
?>
