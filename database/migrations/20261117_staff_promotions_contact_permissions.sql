-- Allow Staff to manage discount events and review contact messages.
INSERT INTO admin_menu_permissions (role_id, menu_id, can_view)
SELECT r.role_id, m.menu_id, 1
FROM roles r
CROSS JOIN admin_menu_items m
WHERE r.role_key = 'staff'
  AND m.menu_key IN ('promotions', 'contact')
ON DUPLICATE KEY UPDATE can_view = 1;
