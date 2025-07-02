<?php
// AdoPET/adicionar_animal.php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once 'db.php';
$page_title = 'Adicionar Novo Animal';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Faça login para adicionar um animal.', 'warning');
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Bloco de depuração da Foto ---
    $foto_url = 'default_animal.jpg'; // Começa com a imagem padrão
    $upload_success = false;

    // Verifica se um arquivo foi enviado e se não há erro inicial
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp_name = $_FILES['foto']['tmp_name'];
        $foto_name = basename($_FILES['foto']['name']); // Usa basename para segurança
        $file_extension = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid('animal_', true) . '.' . $file_extension;
            $upload_path = 'uploads/' . $filename;

            // Tenta mover o arquivo e verifica o resultado
            if (move_uploaded_file($foto_tmp_name, $upload_path)) {
                $foto_url = $filename; // Atualiza a URL da foto se o upload der certo
                $upload_success = true;
            } else {
                // Erro mais comum: permissões de escrita na pasta 'uploads'
                set_flash_message('Erro crítico: Falha ao mover o arquivo para a pasta "uploads". Verifique as permissões de escrita da pasta.', 'danger');
            }
        } else {
            set_flash_message('Erro: Tipo de arquivo de imagem não permitido. Use apenas: png, jpg, jpeg, gif.', 'danger');
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Se houve um erro de upload, mostra qual foi
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => 'O arquivo excede o limite definido em upload_max_filesize no php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'O arquivo excede o limite definido no formulário HTML.',
            UPLOAD_ERR_PARTIAL    => 'O upload do arquivo foi feito apenas parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'Faltando uma pasta temporária.',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever o arquivo no disco.',
            UPLOAD_ERR_EXTENSION  => 'Uma extensão do PHP interrompeu o upload do arquivo.',
        ];
        $error_code = $_FILES['foto']['error'];
        $error_message = $upload_errors[$error_code] ?? 'Ocorreu um erro desconhecido no upload.';
        set_flash_message('Erro no Upload: ' . $error_message, 'danger');
    }
    // --- Fim do Bloco de depuração ---

    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'] ?: 'Não definida';
    $idade = $_POST['idade'] ?: null;
    $genero = $_POST['genero'];
    $porte = $_POST['porte'];
    $castrado = isset($_POST['castrado']) ? 1 : 0;
    $vacinado = isset($_POST['vacinado']) ? 1 : 0;
    $vermifugado = isset($_POST['vermifugado']) ? 1 : 0;
    $descricao = $_POST['descricao'];
    $id_usuario = $_SESSION['user_id'];

    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO animais (nome, especie, raca, idade, genero, porte, castrado, vacinado, vermifugado, descricao, foto_url, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    // Correção na ordem dos tipos de dados
    $stmt->bind_param("sssisssiissi", $nome, $especie, $raca, $idade, $genero, $porte, $castrado, $vacinado, $vermifugado, $descricao, $foto_url, $id_usuario);
    
    if ($stmt->execute()) {
        set_flash_message('Animal cadastrado com sucesso!', 'success');
        header('Location: dashboard.php');
        exit();
    } else {
        error_log('Erro no DB: ' . $stmt->error); 
        set_flash_message('Erro ao cadastrar animal no banco de dados: ' . $stmt->error, 'danger');
    }
    $stmt->close();
    $conn->close();
    
    // Se o código chegou aqui, algo deu errado. Redireciona de volta.
    header('Location: adicionar_animal.php');
    exit();
}

include 'templates/header.php';
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
        
        <label for="raca">Raça:</label>
        <input type="text" id="raca" name="raca" placeholder="Ex: SRD (Sem Raça Definida)">
        
        <label for="idade">Idade (anos):</label>
        <input type="number" id="idade" name="idade" min="0" placeholder="Ex: 2">
        
        <label for="genero">Gênero:</label>
        <select name="genero" id="genero" required>
            <option value="">Selecione</option>
            <option value="Macho">Macho</option>
            <option value="Fêmea">Fêmea</option>
        </select>
        
        <label for="porte">Porte:</label>
        <select name="porte" id="porte" required>
            <option value="">Selecione</option>
            <option value="Pequeno">Pequeno</option>
            <option value="Medio">Médio</option>
            <option value="Grande">Grande</option>
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