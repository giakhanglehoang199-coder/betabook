-- Add the contact-message inbox for administrators. Safe to run repeatedly.
CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_messages_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admin_menu_items (
    menu_key,
    label,
    page_key,
    active_pages,
    icon_class,
    is_external,
    sort_order,
    is_active
)
VALUES (
    'contact',
    'Tin nhắn liên hệ',
    'contact',
    'contact',
    'bi bi-envelope',
    0,
    26,
    1
)
ON DUPLICATE KEY UPDATE
    label = VALUES(label),
    page_key = VALUES(page_key),
    active_pages = VALUES(active_pages),
    icon_class = VALUES(icon_class),
    is_external = VALUES(is_external),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active);

INSERT IGNORE INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'admin'
  AND m.menu_key = 'contact';
