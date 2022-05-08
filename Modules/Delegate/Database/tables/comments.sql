CREATE TABLE `comments`( 
    `id` int(11) UNSIGNED NOT NULL,
    `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `order_id` int(50) UNSIGNED NOT NULL,
    `user_id` int(50) UNSIGNED NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT