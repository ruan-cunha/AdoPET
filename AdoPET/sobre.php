<?php
// AdoPET/sobre.php
$page_title = 'Sobre a AdoPET';
session_start();
header('Content-Type: text/html; charset=utf-8');
include 'templates/header.php';
?>
<section class="static-page-content">
    <h1>Sobre a AdoPET</h1>
    <p>Bem-vindo � AdoPET, sua plataforma dedicada a conectar animais necessitados com lares amorosos e respons�veis. Acreditamos que todo pet merece uma segunda chance e um lar cheio de carinho.</p>

    <h2>Nossa Miss�o</h2>
    <p>Nossa miss�o � simplificar o processo de ado��o, tornando-o acess�vel e seguro para todos. Queremos facilitar a vida de ONGs, protetores independentes e pessoas f�sicas que desejam doar animais, ao mesmo tempo em que proporcionamos aos futuros tutores uma maneira f�cil e intuitiva de encontrar seu novo melhor amigo.</p>

    <h2>Nossos Valores</h2>
    <ul>
        <li><strong>Amor e Respeito:</strong> Todo animal � digno de amor, respeito e cuidado.</li>
        <li><strong>Transpar�ncia:</strong> Promovemos um processo de ado��o claro e honesto.</li>
        <li><strong>Responsabilidade:</strong> Encorajamos a ado��o respons�vel e a posse consciente.</li>
        <li><strong>Comunidade:</strong> Constru�mos uma comunidade de pessoas apaixonadas por animais.</li>
    </ul>

    <p>Junte-se � fam�lia AdoPET e ajude-nos a criar um futuro mais feliz para milh�es de animais!</p>
</section>
<?php include 'templates/footer.php'; ?>