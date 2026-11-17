<?php
if (($actionName ?? null) === 'mark-contact-read') {
    if (!valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_message'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } else {
        $messageId = filter_var($_POST['message_id'] ?? null, FILTER_VALIDATE_INT);
        if ($messageId === false || $messageId === null || $messageId < 1) {
            $_SESSION['flash_message'] = 'Tin nhắn liên hệ không hợp lệ.';
        } else {
            $db->execute(
                'UPDATE contact_messages SET is_read = 1 WHERE message_id = ?',
                [$messageId]
            );
            $_SESSION['flash_message'] = 'Đã đánh dấu tin nhắn là đã đọc.';
        }
    }

    redirect(admin_url('?page=contact'));
}

$pageTitle = 'Tin nhắn liên hệ - Trang quản lý';
$search = trim((string)get_param('q', ''));
$readFilter = (string)get_param('status', '');
$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)';
    $searchLike = '%' . $search . '%';
    array_push($params, $searchLike, $searchLike, $searchLike, $searchLike, $searchLike);
}
if ($readFilter === 'unread' || $readFilter === 'read') {
    $conditions[] = 'COALESCE(is_read, 0) = ?';
    $params[] = $readFilter === 'read' ? 1 : 0;
} else {
    $readFilter = '';
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$pageNumber = max(1, (int)get_param('p', 1));
$pageSize = 25;
$count = $db->fetchOne("SELECT COUNT(*) AS total FROM contact_messages $where", $params);
$totalMessages = (int)($count['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalMessages / $pageSize));
$pageNumber = min($pageNumber, $totalPages);
$messages = $db->fetchAll(
    "SELECT message_id, name, email, phone, subject, message, is_read, created_at
     FROM contact_messages $where
     ORDER BY created_at DESC, message_id DESC
     LIMIT $pageSize OFFSET " . (($pageNumber - 1) * $pageSize),
    $params
);
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);