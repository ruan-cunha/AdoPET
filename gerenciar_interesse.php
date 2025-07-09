<?php
require_once 'helpers.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

validate_csrf_token(); 

require_once 'db.php';

$interesse_id = $_POST['interesse_id'];
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

$conn = get_db_connection();

$stmt = $conn->prepare("SELECT ia.id, a.id_usuario, ia.id_animal FROM interesses_adocao ia JOIN animais a ON ia.id_animal = a.id WHERE ia.id = ?");
$stmt->bind_param("i", $interesse_id);
$stmt->execute();
$interesse_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$interesse_info || $interesse_info['id_usuario'] != $user_id) {
    set_flash_message('Você não tem permissão para gerenciar este interesse.', 'danger');
    header('Location: dashboard.php');
    exit();
}

$new_status = '';
$message = '';

switch ($action) {
    case 'aprovar':
        $new_status = 'Aprovado';
        $message = 'Interesse aprovado!';
        break;
    case 'rejeitar':
        $new_status = 'Rejeitado';
        $message = 'Interesse rejeitado.';
        break;
    case 'marcar_adotado':

        $conn->begin_transaction();
        try {

            $stmt_animal = $conn->prepare("UPDATE animais SET disponivel = FALSE WHERE id = ?");
            $stmt_animal->bind_param("i", $interesse_info['id_animal']);
            $stmt_animal->execute();
            $stmt_animal->close();

            $stmt_interesse = $conn->prepare("UPDATE interesses_adocao SET status = 'Adotado' WHERE id = ?");
            $stmt_interesse->bind_param("i", $interesse_id);
            $stmt_interesse->execute();
            $stmt_interesse->close();

            $conn->commit();
            set_flash_message('Animal marcado como adotado e interesse atualizado!', 'success');
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            set_flash_message('Erro ao atualizar o status: ' . $exception->getMessage(), 'danger');
        }
        header('Location: dashboard.php');
        exit();
}

if (!empty($new_status)) {
    $stmt = $conn->prepare("UPDATE interesses_adocao SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $interesse_id);
    if ($stmt->execute()) {
        set_flash_message($message, 'success');
    } else {
        set_flash_message('Erro ao atualizar status.', 'danger');
    }
    $stmt->close();
}

$conn->close();
header('Location: dashboard.php');
exit();