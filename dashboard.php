<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Configurações pra conectar
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'sistema_atendimento';

// Conexão com o bd
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão com bd
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar os dados do usuário lá no bd
$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nome, cpf_cnpj, whatsapp, telefone, endereco, DATE_FORMAT(data_nascimento, '%d/%m/%Y') as data_nascimento FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'];
    $comentarios = $_POST['comentarios'];
    $pagamento = $_POST['pagamento'];
    $data_retorno = $_POST['data_retorno'];

    // Inserir no banco de dados
    $sql = "INSERT INTO historico_atendimentos (usuario_id, data_registro, data_retorno, descricao, comentarios, pagamento)
            VALUES (?, NOW(), ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql);
    $stmt_insert->bind_param("issss", $usuario_id, $data_retorno, $descricao, $comentarios, $pagamento);

    if ($stmt_insert->execute()) {
        echo "<script>alert('Atendimento registrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao registrar atendimento: {$stmt_insert->error}');</script>";
    }

    $stmt_insert->close();
}

// Fechar a conexão
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/home.modulo.css" />
</head>
<body>
    <header class="header">
      <span>Informações</span>
      <label for="pesquisar">
        Pesquise
        <input type="text" />
        <button>ir</button>
      </label>
    </header>
    <div class="sub-container">
      <main class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>
        <div class="nome">
          <span>Nome Completo: <?php echo htmlspecialchars($usuario['nome']); ?></span>
        </div>
        <section class="cpf">
          <span>CPF ou CNPJ: <?php echo htmlspecialchars($usuario['cpf_cnpj']); ?></span>
        </section>
        <div class="tc">
          <section class="cpf">
            <span>WhatsApp: <?php echo htmlspecialchars($usuario['whatsapp']); ?></span>
          </section>
          <section class="cpf">
            <span>Telefone: <?php echo htmlspecialchars($usuario['telefone']); ?></span>
          </section>
        </div>
        <div class="tc">
          <section class="cpf">
            <span>Endereço: <?php echo htmlspecialchars($usuario['endereco']); ?></span>
          </section>
          <section class="cpf">
            <span>Data de Nascimento: <?php echo htmlspecialchars($usuario['data_nascimento']); ?></span>
          </section>
          <a href="complemento.php">Completar cadastro</a>
          <a href="sair.php">Sair</a>
          <a href="historico.php">Acessar histórico</a>
        </div>
      </main>
      <main class="container-dscr">
        <h1>Descrição de atendimento</h1>
        <form method="POST" action="">
          <section class="Descriçao">
            <textarea name="descricao" id="descricao" placeholder="Descreva o atendimento..." required></textarea>
          </section>
          <h1>Comente sobre o atendimento</h1>
          <section class="Descriçao">
            <textarea name="comentarios" id="comentarios" placeholder="Adicione seus comentários..." required></textarea>
          </section>
          <h1>Data de Retorno</h1>
          <section class="Descriçao">
            <input type="date" name="data_retorno" id="data_retorno" required>
          </section>
          <h1>Método de Pagamento</h1>
          <section class="Descriçao">
            <select name="pagamento" id="pagamento" required>
              <option value="" disabled selected>Selecione</option>
              <option value="Cartão de Crédito">Cartão de Crédito</option>
              <option value="Boleto Bancário">Boleto Bancário</option>
              <option value="Pix">Pix</option>
              <option value="Dinheiro">Dinheiro</option>
            </select>
          </section>
          <button type="submit" class="dsc">Registrar Atendimento</button>
        </form>
      </main>
    </div>
</body>
</html>
