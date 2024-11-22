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

// Capturar o ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Mensagem de sucesso ou erro
$mensagem = "";

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf_cnpj = $_POST['cpf_cnpj'];
    $whatsapp = $_POST['whatsapp'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $data_nascimento = $_POST['data_nascimento'];

    // Atualizar os dados no banco de dados
    $sql = "UPDATE usuarios SET cpf_cnpj = ?, whatsapp = ?, telefone = ?, endereco = ?, data_nascimento = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $cpf_cnpj, $whatsapp, $telefone, $endereco, $data_nascimento, $usuario_id);

    if ($stmt->execute()) {
        $mensagem = "Dados atualizados com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar os dados: " . $stmt->error;
    }

    $stmt->close();
}

// Buscar os dados do usuário para exibir no formulário
$sql = "SELECT cpf_cnpj, whatsapp, telefone, endereco, data_nascimento FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("Usuário não encontrado.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Complementar Cadastro</title>
    <link
      rel="stylesheet"
      type="text/css"
      href="./css/stylesheet.css"
      media="screen"
    />
    <link
      rel="stylesheet"
      type="text/css"
      href="./css/complemento.module.css"
      media="screen"
    />
  </head>
  <body>
    <div class="container">
      <form class="form-container-complemento" method="POST" action="">
        <h1>Complementar Cadastro</h1>
        <?php if (!empty($mensagem)) { echo "<p>$mensagem</p>"; } ?>
        <div class="input-forms-complemento">
          <input
            type="number"
            placeholder="CPF ou CNPJ"
            id="cpf"
            name="cpf_cnpj"
            value="<?php echo htmlspecialchars($usuario['cpf_cnpj']); ?>"
            required
          />
          <input
            type="tel"
            placeholder="WhatsApp"
            id="celuar"
            name="whatsapp"
            value="<?php echo htmlspecialchars($usuario['whatsapp']); ?>"
            required
          />
          <input
            type="tel"
            placeholder="Telefone"
            id="tel"
            name="telefone"
            value="<?php echo htmlspecialchars($usuario['telefone']); ?>"
            required
          />
          <input
            type="text"
            placeholder="Endereço"
            id="end"
            name="endereco"
            value="<?php echo htmlspecialchars($usuario['endereco']); ?>"
            required
          />
          <input
            type="date"
            placeholder="Data de Nascimento"
            id="dtn"
            name="data_nascimento"
            value="<?php echo htmlspecialchars($usuario['data_nascimento']); ?>"
            required
          />
        </div>
        <div class="input-btn">
          <input type="submit" value="Finalizar" />
          <span>ou</span>
          <a href="dashboard.php">Cancelar</a>
        </div>
      </form>
    </div>
  </body>
</html>
