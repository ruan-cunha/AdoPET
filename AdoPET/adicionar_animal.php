<?php
// AdoPET/adicionar_animal.php
require_once 'db.php';
$page_title = 'Adicionar Novo Animal';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para adicionar um animal.', 'warning');
    header('Location: login.php');
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
    $descricao = $_POST['descricao'];
    $id_usuario = $_SESSION['user_id'];

    $foto_url = 'default_animal.jpg';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid('animal_', true) . '.' . $file_extension;
            $upload_path = 'uploads/' . $filename;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $foto_url = $filename;
            } else {
                 set_flash_message('Erro ao mover o arquivo de upload.', 'danger');
            }
        } else {
             set_flash_message('Tipo de arquivo de imagem não permitido.', 'danger');
        }
    }

    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO animais (nome, especie, raca, idade, genero, porte, castrado, vacinado, vermifugado, descricao, foto_url, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisiiisssi", $nome, $especie, $raca, $idade, $genero, $porte, $castrado, $vacinado, $vermifugado, $descricao, $foto_url, $id_usuario);
    
    if ($stmt->execute()) {
        set_flash_message('Animal cadastrado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        set_flash_message('Erro ao cadastrar animal: ' . $stmt->error, 'danger');
    }
    $stmt->close();
    $conn->close();
}

?>
<section class="form-section">
    <h2>Adicionar Novo Animal</h2>
    <form method="POST" action="adicionar_animal.php" enctype="multipart/form-data">
        <label for="nome">Nome do Animal:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="especie">Espécie:</label>
        <select name="especie" id="especie" required>
            <option value="">Selecione</option>
            <option value="Cachorro">Cachorro</option>
            <option value="Gato">Gato</option>
            <option value="Outros">Outros</option>
        </select>
        
        <div class="checkbox-group">
            <label><input type="checkbox" name="castrado"> Castrado</label>
            <label><input type="checkbox" name="vacinado"> Vacinado</label>
            <label><input type="checkbox" name="vermifugado"> Vermifugado</label>
        </div>

        <label for="descricao">Descrição e Personalidade:</label>
        <textarea id="descricao" name="descricao" rows="6" required></textarea>

        <label for="foto">Foto do Animal:</label>
        <input type="file" id="foto" name="foto" accept="image/*">
        <small>Tipos permitidos: PNG, JPG, JPEG, GIF.</small>

        <button type="submit" class="btn-primary">Cadastrar Animal</button>
    </form>
</section>
<?php include 'templates/footer.php'; ?>