<?php
// AdoPET/contato.php
$page_title = 'Entre em Contato';
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'templates/header.php';
?>
<section class="static-page-content">
    <h1>Entre em Contato</h1>
    <p>Tem alguma d�vida, sugest�o ou precisa de ajuda? Entre em contato conosco! Estamos aqui para ajudar voc� a encontrar o seu novo amigo ou a doar um animal com responsabilidade.</p>

    <div class="contact-info">
        <p><strong>E-mail:</strong> <a href="mailto:contato@adopet.com">contato@adopet.com</a></p>
        <p><strong>Telefone:</strong> (47) 3481-7900 (hor�rio comercial)</p>
        <p><strong>Endere�o:</strong> R. Paulo Malschitzki, 200 - Zona Industrial Norte, Joinville/SC</p>
    </div>

    <h2>Envie sua Mensagem</h2>
    <form action="#" method="POST" class="contact-form">
        <label for="nome">Seu Nome:</label>
        <input type="text" id="nome" name="nome" required>
        <label for="email">Seu E-mail:</label>
        <input type="email" id="email" name="email" required>
        <label for="mensagem">Sua Mensagem:</label>
        <textarea id="mensagem" name="mensagem" rows="6" required></textarea>
        <button type="submit" class="btn-primary">Enviar Mensagem</button>
    </form>
</section>
<?php include 'templates/footer.php'; ?>