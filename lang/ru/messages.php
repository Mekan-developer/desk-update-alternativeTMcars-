<?php

return [
    'created'   => 'Создано успешно',
    'updated'   => 'Сохранено',
    'deleted'   => 'Удалено',
    'published' => 'Опубликовано',
    'unpublished' => 'Снято с публикации',
    'admin_only' => 'Только администратор',

    'listing_approved' => 'Объявление одобрено',
    'listing_rejected' => 'Объявление отклонено',
    'listing_boosted'  => 'Объявление поднято',
    'boost_interval_not_passed' => 'Нельзя поднять — интервал ещё не прошёл',
    'tariff_limit_exceeded' => 'Лимит тарифа исчерпан',
    'tariff_assigned' => 'Тариф назначен',

    'video_approved' => 'Ролик одобрен',
    'video_rejected' => 'Ролик отклонён',

    'review_approved' => 'Отзыв одобрен',
    'review_rejected' => 'Отзыв отклонён',

    'category_max_level' => 'Нельзя создать категорию глубже 3 уровней',
    'category_parent_invalid' => 'Категория не может быть родителем сама для себя или своего потомка',
    'category_has_listings' => 'Нельзя удалить — категория (или её подкатегории) используется в объявлениях',

    'user_blocked'   => 'Пользователь заблокирован',
    'user_unblocked' => 'Пользователь разблокирован',
    'user_blocked_message' => 'Ваш аккаунт заблокирован. Обратитесь в поддержку',

    'sms_code_text'         => 'Ваш код подтверждения: :code',
    'sms_code_sent'         => 'Код отправлен',
    'sms_code_invalid'      => 'Неверный или просроченный код',
    'sms_too_many_attempts' => 'Слишком много попыток. Запросите новый код',
    'sms_resend_wait'       => 'Повторная отправка будет доступна через :seconds сек.',
    'auth_success'          => 'Вход выполнен',
    'logged_out'            => 'Вы вышли из аккаунта',
    'phone_changed'         => 'Номер телефона изменён',
    'phone_already_registered' => 'Номер уже зарегистрирован',
    'phone_format_invalid'     => 'Неверный формат номера: ожидается +993 и 8 цифр',

    'push_listing_approved_title' => 'Объявление одобрено',
    'push_listing_approved_body'  => 'Ваше объявление «:title» успешно прошло модерацию',
    'push_listing_rejected_title' => 'Объявление отклонено',
    'push_listing_rejected_body'  => 'Ваше объявление «:title» было отклонено',

    'manager_permission_updated' => 'Права менеджера обновлены',
    'localization_updated'       => 'Настройки локализации сохранены',
    'sms_test_sent'              => 'Тестовое SMS отправлено',
    'sms_test_failed'            => 'Не удалось отправить тестовое SMS',
];
