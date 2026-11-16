ALTER TABLE home_coupons
    ADD COLUMN discount_type ENUM('percentage', 'fixed', 'free_shipping') NOT NULL DEFAULT 'percentage' AFTER expires_at,
    ADD COLUMN discount_value DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER discount_type,
    ADD COLUMN minimum_order DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER discount_value,
    ADD COLUMN minimum_quantity INT UNSIGNED NOT NULL DEFAULT 0 AFTER minimum_order;

ALTER TABLE orders
    ADD COLUMN coupon_code VARCHAR(50) DEFAULT NULL AFTER discount_amount;

UPDATE home_coupons
SET discount_type = CASE coupon_code
        WHEN 'SACH50K' THEN 'fixed'
        WHEN 'FREESHIP' THEN 'free_shipping'
        WHEN 'BETA50K' THEN 'fixed'
        ELSE 'percentage'
    END,
    discount_value = CASE coupon_code
        WHEN 'BETA10' THEN 10
        WHEN 'SACH50K' THEN 50000
        WHEN 'COMBO20' THEN 20
        WHEN 'HSSV15' THEN 15
        WHEN 'BETA50K' THEN 50000
        ELSE 0
    END,
    minimum_order = CASE coupon_code
        WHEN 'SACH50K' THEN 300000
        ELSE 0
    END,
    minimum_quantity = CASE coupon_code
        WHEN 'COMBO20' THEN 3
        ELSE 0
    END
WHERE coupon_code IN ('BETA10', 'SACH50K', 'FREESHIP', 'COMBO20', 'HSSV15', 'BETA50K');
