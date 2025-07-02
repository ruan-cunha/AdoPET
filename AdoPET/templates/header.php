<?php
// AdoPET/templates/header.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdoPET - <?php echo $page_title ?? 'Bem-vindo'; ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="icon" href="static/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="index.php">AdoPET</a></div>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="animais.php">Animais Disponíveis</a></li>
                <li><a href="sobre.php">Sobre Nós</a></li>
                <li><a href="contato.php">Contato</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <a href="dashboard.php" class="user-name">Olá, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</a>
                        <ul class="dropdown-content">
                            <li><a href="dashboard.php">Meu Painel</a></li>
                            <li><a href="editar_perfil.php">Editar Perfil</a></li>
                            <li><a href="logout.php">Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="cadastro.php" class="btn-nav">Cadastre-se</a></li>
                    <li><a href="login.php" class="btn-nav">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <ul class="flashes">
                <li class="<?php echo $_SESSION['flash_message']['type']; ?>">
                    <?php echo $_SESSION['flash_message']['message']; ?>
                </li>
            </ul>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>