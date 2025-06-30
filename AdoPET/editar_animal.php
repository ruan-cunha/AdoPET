<?php
// AdoPET/editar_animal.php
require_once 'db.php';
$page_title = 'Editar Animal';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para editar um animal.', 'warning');
    header('Location: login.php');
    exit();
}

$animal_id = $_GET['id'] ?? 0;
if (!$animal_id) {
    header('Location: dashboard.php');
    exit();
}

$conn = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM animais WHERE id = ? AND id_usuario = ?");
$stmt->bind_param("ii", $animal_id, $user_id);
$stmt->execute();
$animal = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$animal) {
    set_flash_message('Animal não encontrado ou você não tem permissão para editá-lo.', 'danger');
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'] ?: null;
    $idade = $_POST['idade'] ?: null;
    $genero = $_POST['genero'];
    $porte = $_POST['porte'];
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $disponivel = isset($_POST['disponivel']) ? 1 : 0;
    $descricao = $_POST['descricao'];

    $foto_url = $animal['foto_url']; 
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            
            if ($foto_url !== 'default_animal.jpg' && file_exists('uploads/' . $foto_url)) {
                unlink('uploads/' . $foto_url);
            }
            
            $filename = uniqid('animal_', true) . '.' . $file_extension;
            $upload_path = 'uploads/' . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $foto_url = $filename;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE animais SET nome = ?, especie = ?, raca = ?, idade = ?, genero = ?, porte = ?, castrado = ?, vacinado = ?, vermifugado = ?, disponivel = ?, descricao = ?, foto_url = ? WHERE id = ?");
    $stmt->bind_param("sssisiiiiissi", $nome, $especie, $raca, $idade, $genero, $porte, $castrado, $vacinado, $vermifugado, $disponivel, $descricao, $foto_url, $animal_id);
    
    if ($stmt->execute()) {
        set_flash_message('Animal atualizado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        set_flash_message('Erro ao atualizar animal: ' . $stmt->error, 'danger');
    }
    $stmt->close();
}
$conn->close();
?>
<section class="form-section">
    <h2>Editar Animal: <?php echo htmlspecialchars($animal['nome']); ?></h2>
    <form method="POST" action="editar_animal.php?id=<?php echo $animal['id']; ?>" enctype="multipart/form-data">
        <label for="nome">Nome do Animal:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($animal['nome']); ?>" required>

        <label for="especie">Espécie:</label>
        <select name="especie" id="especie" required>
            <option value="Cachorro" <?php if($animal['especie'] == 'Cachorro') echo 'selected'; ?>>Cachorro</option>
            <option value="Gato" <?php if($animal['especie'] == 'Gato') echo 'selected'; ?>>Gato</option>
            <option value="Outros" <?php if($animal['especie'] == 'Outros') echo 'selected'; ?>>Outros</option>
        </select>
        
        <div class="checkbox-group">
            <label><input type="checkbox" name="castrado" <?php if($animal['castrado']) echo 'checked'; ?>> Castrado</label>
            <label><input type="checkbox" name="vacinado" <?php if($animal['vacinado']) echo 'checked'; ?>> Vacinado</label>
            <label><input type="checkbox" name="vermifugado" <?php if($animal['vermifugado']) echo 'checked'; ?>> Vermifugado</label>
            <label><input type="checkbox" name="disponivel" <?php if($animal['disponivel']) echo 'checked'; ?>> Disponível para Adoção</label>
        </div>

        <label for="descricao">Descrição e Personalidade:</label>
        <textarea id="descricao" name="descricao" rows="6" required><?php echo htmlspecialchars($animal['descricao']); ?></textarea>

        <label for="foto">Alterar Foto do Animal:</label>
        <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto atual" style="max-width: 150px; display: block; margin-bottom: 10px;">
        <input type="file" id="foto" name="foto" accept="image/*">
        <small>Deixe em branco para manter a foto atual.</small>

        <button type="submit" class="btn-primary">Atualizar Animal</button>
    </form>
</section>
<?php include 'templates/footer.php'; ?>