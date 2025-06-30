<?php
// AdoPET/animal_detalhes.php
require_once 'db.php';

$animal_id = $_GET['id'] ?? 0;
if (!$animal_id) {
    header("Location: animais.php");
    exit();
}

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT a.*, u.nome as nome_doador, u.email as email_doador, u.telefone as telefone_doador 
                        FROM animais a 
                        JOIN usuarios u ON a.id_usuario = u.id 
                        WHERE a.id = ?");
$stmt->bind_param("i", $animal_id);
$stmt->execute();
$result = $stmt->get_result();
$animal = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$animal) {
    $_SESSION['flash_message'] = ['message' => 'Animal não encontrado.', 'type' => 'danger'];
    header("Location: animais.php");
    exit();
}

$page_title = htmlspecialchars($animal['nome']) . ' - Detalhes';
include 'templates/header.php';
?>

<section class="animal-detalhes">
    <div class="detalhes-container">
        <div class="detalhes-imagem">
            <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">
        </div>
        <div class="detalhes-info">
            <h1><?php echo htmlspecialchars($animal['nome']); ?></h1>
            <p><strong>Espécie:</strong> <?php echo htmlspecialchars($animal['especie']); ?></p>
            <p><strong>Raça:</strong> <?php echo htmlspecialchars($animal['raca'] ?: 'Não informada'); ?></p>
            <p><strong>Idade:</strong> <?php echo htmlspecialchars($animal['idade']); ?> anos</p>
            <p><strong>Gênero:</strong> <?php echo htmlspecialchars($animal['genero']); ?></p>
            <p><strong>Porte:</strong> <?php echo htmlspecialchars($animal['porte']); ?></p>
            <p><strong>Castrado:</strong> <?php echo $animal['castrado'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Vacinado:</strong> <?php echo $animal['vacinado'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Vermifugado:</strong> <?php echo $animal['vermifugado'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Status:</strong> <?php echo $animal['disponivel'] ? 'Disponível para Adoção' : 'Adotado!'; ?></p>
            <p><strong>Sobre mim:</strong> <?php echo nl2br(htmlspecialchars($animal['descricao'])); ?></p>

            <h3>Contato para Adoção:</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p><strong>Doador(a):</strong> <?php echo htmlspecialchars($animal['nome_doador']); ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($animal['email_doador']); ?>"><?php echo htmlspecialchars($animal['email_doador']); ?></a></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($animal['telefone_doador']); ?></p>

                <?php if ($animal['disponivel'] && $_SESSION['user_type'] == 'Pessoa Fisica' && $_SESSION['user_id'] != $animal['id_usuario']): ?>
                    <div class="manifestar-interesse-form">
                        <h4>Manifestar Interesse:</h4>
                        <form action="manifestar_interesse.php" method="POST">
                            <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                            <label for="mensagem">Deixe uma mensagem para o doador:</label>
                            <textarea id="mensagem" name="mensagem" rows="4" placeholder="Conte um pouco sobre você e por que gostaria de adotar este animal." required></textarea>
                            <button type="submit" class="btn-primary">Enviar Interesse</button>
                        </form>
                    </div>
                <?php elseif ($_SESSION['user_id'] == $animal['id_usuario']): ?>
                    <p class="info-message">Este é um dos seus animais. Acesse o <a href="dashboard.php">painel</a> para gerenciá-lo.</p>
                <?php endif; ?>

            <?php else: ?>
                <p>Faça <a href="login.php">login</a> para ver os dados de contato do doador e manifestar interesse.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>