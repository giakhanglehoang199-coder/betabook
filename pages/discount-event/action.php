<?php
$coupons = $db->fetchAll(
    'SELECT coupon_code, description, expires_at, image_path
     FROM home_coupons
     WHERE is_active = 1 AND expires_at >= CURDATE()
     ORDER BY sort_order ASC, coupon_id ASC'
);

$events = $db->fetchAll(
    'SELECT event_date, title, description, image_path
     FROM home_events
     WHERE is_active = 1
     ORDER BY sort_order ASC, event_id ASC'
);

$sessionUserId = filter_var($_SESSION['user']['user_id'] ?? null, FILTER_VALIDATE_INT);
$isLoggedIn = $sessionUserId !== false && $sessionUserId !== null && $sessionUserId > 0;

$pageTitle = 'Mã giảm & Sự kiện';
$bodyClass = 'discount-event-page';
$extraCss = [asset('css/discount-event.css') . '?v=' . filemtime(__DIR__ . '/../../assets/css/discount-event.css')];