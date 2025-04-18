<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Factories\LineClientFactory;
use GuzzleHttp\Client;

class InitLineMenuCommand extends Command
{
    protected $signature = 'line:menu:init';
    protected $description = '初始化 LINE Rich Menu 並設定 Postback 行為';

    public function handle()
    {
        $client = LineClientFactory::messaging();

        $richMenu = [
            'size' => [
                'width' => 2500,
                'height' => 843
            ],
            'selected' => true,
            'name' => 'LLM Switcher Menu',
            'chatBarText' => '切換回答引擎',
            'areas' => [
                [
                    'bounds' => [
                        'x' => 0,
                        'y' => 0,
                        'width' => 1250,
                        'height' => 843
                    ],
                    'action' => [
                        'type' => 'postback',
                        'label' => 'OpenAI',
                        'data' => 'openai', // <<< 改這裡
                    ]
                ],
                [
                    'bounds' => [
                        'x' => 1251,
                        'y' => 0,
                        'width' => 1250,
                        'height' => 843
                    ],
                    'action' => [
                        'type' => 'postback',
                        'label' => 'Gemini',
                        'data' => 'gemini', // <<< 改這裡
                    ]
                ]
            ]
        ];

        $menuResponse = $client->createRichMenu($richMenu);
        $menuId = method_exists($menuResponse, 'getRichMenuId') ? $menuResponse->getRichMenuId() : null;

        if (!$menuId) {
            $this->error('建立 Rich Menu 失敗，回傳內容不含 richMenuId');
            return;
        }

        $this->info("Rich Menu created: $menuId");

        $imagePath = storage_path('app/public/richmenu.jpg');
        if (!file_exists($imagePath)) {
            $this->error('找不到圖片 richmenu.jpg，請放在 storage/app/public 下');
            return;
        }

        $http = new Client();
        $res = $http->post("https://api-data.line.me/v2/bot/richmenu/{$menuId}/content", [
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.line.channel_access_token'),
                'Content-Type' => 'image/jpeg'
            ],
            'body' => fopen($imagePath, 'r')
        ]);

        if ($res->getStatusCode() === 200) {
            $this->info('圖片上傳成功');
        } else {
            $this->error('圖片上傳失敗');
        }

        $client->setDefaultRichMenu($menuId);
        $this->info('已設為預設 Rich Menu');
    }
}
