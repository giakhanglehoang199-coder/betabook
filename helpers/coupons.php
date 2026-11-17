<?php

function coupon_fetch_active(string $code): array|false {
    return db()->fetchOne(
        'SELECT coupon_id, coupon_code, description, expires_at, discount_type, discount_value,
                minimum_order, minimum_quantity
         FROM home_coupons
         WHERE coupon_code = ? AND is_active = 1 AND expires_at >= CURDATE()',
        [$code]
    );
}

function coupon_validation_error(array $coupon, float $subtotal, int $quantity): ?string {
    if (!in_array($coupon['discount_type'] ?? null, ['percentage', 'fixed', 'free_shipping'], true)) {
        return 'Mã giảm giá chưa được cấu hình hợp lệ.';
    }

    $discountValue = (float)($coupon['discount_value'] ?? 0);
    if (($coupon['discount_type'] !== 'free_shipping' && $discountValue <= 0)
        || ($coupon['discount_type'] === 'percentage' && $discountValue > 100)) {
        return 'Mã giảm giá chưa được cấu hình hợp lệ.';
    }

    if ($subtotal < (float)($coupon['minimum_order'] ?? 0)
        || $quantity < (int)($coupon['minimum_quantity'] ?? 0)) {
        return 'Giỏ hàng chưa đáp ứng điều kiện áp dụng mã giảm giá này.';
    }

    return null;
}

function coupon_discount_amount(array $coupon, float $subtotal): float {
    if ($coupon['discount_type'] === 'percentage') {
        return min($subtotal, round($subtotal * (float)$coupon['discount_value'] / 100));
    }
    if ($coupon['discount_type'] === 'fixed') {
        return min($subtotal, (float)$coupon['discount_value']);
    }

    return 0.0;
}
