<?php
// AdoPET/editar_perfil.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Editar Perfil';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Fa�a login para editar seu perfil.', 'warning');
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'] ?: null;
    $telefone = $_POST['telefone'] ?: null;
    $descricao = ($_SESSION['user_type'] == 'ONG') ? ($_POST['descricao'] ?: null) : null;

    if ($_SESSION['user_type'] == 'ONG') {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, endereco = ?, telefone = ?, descricao = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nome, $endereco, $telefone, $descricao, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, endereco = ?, telefone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $endereco, $telefone, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $nome;
        set_flash_message('Perfil atualizado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        set_flash_message('Erro ao atualizar perfil: ' . $stmt->error, 'danger');
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

?>
<section class="form-section">
    <h2>Editar Perfil</h2>
    <form method="POST" action="editar_perfil.php">
        <label for="nome">Nome/Nome da ONG:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

        <label for="email">E-mail (n�o edit�vel):</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>

        <label for="endereco">Endere�o:</label>
        <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?>">

        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>" pattern="[0-9]{10,11}" placeholder="Ex: 47988887777">
        <small class="help-text">Apenas n�meros, com DDD.</small>

        <?php if ($usuario['tipo_usuario'] == 'ONG'): ?>
            <label for="descricao">Descri��o da ONG:</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($usuario['descricao'] ?? ''); ?></textarea>
        <?php endif; ?>

        <button type="submit" class="btn-primary">Atualizar Perfil</button>
    </form>
</section>
<script src="https://unpkg.com/imask"></script>
<script>
    var phoneInput = document.getElementById('telefone');
    var phoneMask = IMask(phoneInput, {
        mask: [
            {
                mask: '(00) 0000-0000',
                maxLength: 14
            },
            {
                mask: '(00) 00000-0000',
                maxLength: 15
            }
        ]
    });
</script>

<?php include 'templates/footer.php'; ?>