<?php
// AdoPET/dashboard.php
require_once 'db.php';
session_start();
header('Content-Type: text/html; charset=utf-8');
$page_title = 'Meu Painel - AdoPET';

function set_flash_message($message, $type) {
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

include 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    set_flash_message('Por favor, fa�a login para acessar esta p�gina.', 'warning');
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

$conn = get_db_connection();

$stmt_perfil = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt_perfil->bind_param("i", $user_id);
$stmt_perfil->execute();
$perfil_usuario = $stmt_perfil->get_result()->fetch_assoc();
$stmt_perfil->close();


$meus_animais = [];
$interesses_recebidos = [];
$meus_interesses_enviados = [];

if ($user_type == 'ONG' || $user_type == 'Pessoa Fisica') {
    $stmt_animais = $conn->prepare("SELECT * FROM animais WHERE id_usuario = ? ORDER BY data_cadastro DESC");
    $stmt_animais->bind_param("i", $user_id);
    $stmt_animais->execute();
    $meus_animais = $stmt_animais->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_animais->close();

    $sql_interesses = "
        SELECT ia.*, a.nome as nome_animal, u.nome as nome_interessado, u.email as email_interessado, u.telefone as telefone_interessado
        FROM interesses_adocao ia
        JOIN animais a ON ia.id_animal = a.id
        JOIN usuarios u ON ia.id_interessado = u.id
        WHERE a.id_usuario = ?
        ORDER BY ia.data_interesse DESC";
    $stmt_interesses = $conn->prepare($sql_interesses);
    $stmt_interesses->bind_param("i", $user_id);
    $stmt_interesses->execute();
    $interesses_recebidos = $stmt_interesses->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_interesses->close();
}

if ($user_type == 'Pessoa Fisica') {
    $sql_enviados = "
        SELECT ia.*, a.nome as nome_animal, a.id as id_animal, u.nome as nome_doador
        FROM interesses_adocao ia
        JOIN animais a ON ia.id_animal = a.id
        JOIN usuarios u ON a.id_usuario = u.id
        WHERE ia.id_interessado = ?
        ORDER BY ia.data_interesse DESC";
    $stmt_enviados = $conn->prepare($sql_enviados);
    $stmt_enviados->bind_param("i", $user_id);
    $stmt_enviados->execute();
    $meus_interesses_enviados = $stmt_enviados->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_enviados->close();
}

$conn->close();
?>

<section class="dashboard">
    <h1>Ol�, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>

    <div class="profile-info-summary">
        <p><strong>Tipo de Usu�rio:</strong> <?php echo htmlspecialchars($perfil_usuario['tipo_usuario']); ?></p>
        <p><strong>E-mail:</strong> <?php echo htmlspecialchars($perfil_usuario['email']); ?></p>
        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($perfil_usuario['telefone'] ?: 'N�o informado'); ?></p>
        <p><strong>Endere�o:</strong> <?php echo htmlspecialchars($perfil_usuario['endereco'] ?: 'N�o informado'); ?></p>
        <?php if ($perfil_usuario['tipo_usuario'] == 'ONG'): ?>
            <p><strong>Descri��o da ONG:</strong> <?php echo htmlspecialchars($perfil_usuario['descricao'] ?: 'N�o informada'); ?></p>
        <?php endif; ?>
        <a href="editar_perfil.php" class="btn-secondary">Editar Perfil</a>
    </div>

    <?php if ($user_type == 'ONG' || $user_type == 'Pessoa Fisica'): ?>
        <hr>
        <h2>Meus Animais Cadastrados</h2>
        <div class="dashboard-actions">
            <a href="adicionar_animal.php" class="btn-primary">Adicionar Novo Animal</a>
        </div>
        <div class="galeria-animais">
            <?php if (!empty($meus_animais)): ?>
                <?php foreach ($meus_animais as $animal): ?>
                    <div class="animal-card <?php echo !$animal['disponivel'] ? 'card-adotado' : ''; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($animal['foto_url']); ?>" alt="Foto do <?php echo htmlspecialchars($animal['nome']); ?>">
                        <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                        <p><?php echo htmlspecialchars($animal['especie']); ?> - <?php echo $animal['disponivel'] ? '<span class="status-badge status-disponivel">Dispon�vel</span>' : '<span class="status-badge status-adotado">Adotado</span>'; ?></p>
                        <div class="card-actions">
                            <a href="animal_detalhes.php?id=<?php echo $animal['id']; ?>" class="btn-small btn-secondary">Ver</a>
                            <a href="editar_animal.php?id=<?php echo $animal['id']; ?>" class="btn-small btn-edit">Editar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="info-message">Voc� ainda n�o cadastrou nenhum animal.</p>
            <?php endif; ?>
        </div>

        <hr>
        <h2>Interesses Recebidos</h2>
        <?php if (!empty($interesses_recebidos)): ?>
            <div class="interesses-lista">
                <?php foreach ($interesses_recebidos as $interesse): ?>
                    <div class="interesse-card interesse-status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>">
                        <h4>Interesse em "<?php echo htmlspecialchars($interesse['nome_animal']); ?>"</h4>
                        <p><strong>De:</strong> <?php echo htmlspecialchars($interesse['nome_interessado']); ?> (<?php echo htmlspecialchars($interesse['email_interessado']); ?>)</p>
                        <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>"><?php echo htmlspecialchars($interesse['status']); ?></span></p>
                        <p><strong>Mensagem:</strong> <?php echo nl2br(htmlspecialchars($interesse['mensagem_interessado'] ?: 'Nenhuma mensagem.')); ?></p>
                        
                        <?php if ($interesse['status'] == 'Pendente'): ?>
                        <div class="interest-actions">
                            <form action="gerenciar_interesse.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                <input type="hidden" name="action" value="aprovar">
                                <button type="submit" class="btn-small btn-approve">Aprovar</button>
                            </form>
                             <form action="gerenciar_interesse.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                <input type="hidden" name="action" value="rejeitar">
                                <button type="submit" class="btn-small btn-reject">Rejeitar</button>
                            </form>
                        </div>
                        <?php elseif ($interesse['status'] == 'Aprovado'): ?>
                            <form action="gerenciar_interesse.php" method="POST">
                                <input type="hidden" name="interesse_id" value="<?php echo $interesse['id']; ?>">
                                <input type="hidden" name="action" value="marcar_adotado">
                                <button type="submit" class="btn-small btn-adopt">Marcar como Adotado</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="info-message">Nenhum interesse recebido ainda.</p>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($user_type == 'Pessoa Fisica'): ?>
        <hr>
        <h2>Meus Interesses Enviados</h2>
        <?php if (!empty($meus_interesses_enviados)): ?>
            <div class="interesses-lista">
                <?php foreach ($meus_interesses_enviados as $interesse): ?>
                    <div class="interesse-card interesse-status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>">
                        <h4>Interesse em "<?php echo htmlspecialchars($interesse['nome_animal']); ?>"</h4>
                         <p><strong>Doador:</strong> <?php echo htmlspecialchars($interesse['nome_doador']); ?></p>
                        <p><strong>Seu Status:</strong> <span class="status-badge status-<?php echo strtolower(str_replace(' ', '_', $interesse['status'])); ?>"><?php echo htmlspecialchars($interesse['status']); ?></span></p>
                        <p><strong>Sua Mensagem:</strong> <?php echo nl2br(htmlspecialchars($interesse['mensagem_interessado'] ?: 'Nenhuma mensagem.')); ?></p>
                        <a href="animal_detalhes.php?id=<?php echo $interesse['id_animal']; ?>" class="btn-small btn-secondary">Ver Animal</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="info-message">Voc� ainda n�o manifestou interesse em nenhum animal.</p>
        <?php endif; ?>
    <?php endif; ?>

</section>

<?php include 'templates/footer.php'; ?>