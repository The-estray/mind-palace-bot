<?php

class Keyboard
{
    public static function start(int $count): array
    {
        if ($count === 0) {
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '➕ Добавить первую мысль', 'callback_data' => 'add_quote']],
                ]
            ];
        } else {
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '➕ Добавить мысль', 'callback_data' => 'add_quote']],
                    [['text' => '🎲 Случайная мысль', 'callback_data' => 'random_quote']],
                    [['text' => '👁‍🗨 Заглянуть в чертоги', 'callback_data' => 'open_chambers']],
                ]
            ];
        }

        return $keyboard;
    }

    public static function chooseCategory(array $cats): array
    {
        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($cats as $cat) {
            $keyboard['inline_keyboard'][] = [
                ['text' => '📁 ' . $cat['title'], 'callback_data' => 'save_to_cat_' . $cat['id']]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '➕ Новая папка', 'callback_data' => 'new_category'],
            ['text' => '📥 Без папки', 'callback_data' => 'save_to_cat_0'],
        ];

        $keyboard['inline_keyboard'][] = [
            ['text' => '❌ Отмена', 'callback_data' => 'cancel_action']
        ];

        return $keyboard;
    }

    public static function randomQuote(): array
    {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🎲 Еще мысль', 'callback_data' => 'random_quote']],
                [['text' => '➕ Добавить мысль', 'callback_data' => 'add_quote']],
                [['text' => '👁‍🗨 Заглянуть в чертоги', 'callback_data' => 'open_chambers']],
            ]
        ];
        return $keyboard;
    }

    public static function cancelAction(): array
    {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '❌ Отмена', 'callback_data' => 'cancel_action']],
            ]
        ];
        return $keyboard;
    }

    public static function openChambers(array $cats, int $countWithoutCat): array
    {
        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($cats as $cat) {
            $keyboard['inline_keyboard'][] = [
                ['text' => $cat['title'] . '(' . $cat['quotes_count'] . ')', 'callback_data' => 'view_cat_' . $cat['id']]
            ];
        }

        if ($countWithoutCat > 0) {
            $keyboard['inline_keyboard'][] = [
                ['text' => '📥 Без папки (' . $countWithoutCat . ')', 'callback_data' => 'view_cat_0'],
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '⬅️ Назад', 'callback_data' => 'cancel_action'],
        ];


        return $keyboard;
    }
}
