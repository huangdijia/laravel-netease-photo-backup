<?php

namespace Huangdijia\Netease\Commands;

use function GuzzleHttp\json_decode;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PhotoBackupCommand extends Command
{
    protected $signature   = 'netease:photo-backup {nickname : 相册昵称} {savepath? : 备份路径}';
    protected $description = '一键备份网易相册';

    private $baseUrl = 'http://photo.163.com/';

    public function handle()
    {
        $nickname  = $this->argument('nickname');
        $savepath  = $this->argument('savepath') ?: storage_path('app/netease/');
        $albumRoot = rtrim($savepath, '/') . '/' . $nickname;

        $this->info("正在备份 {$nickname} 相册 from {$this->baseUrl}{$nickname} to {$albumRoot}");

        if (!File::isDirectory($savepath)) {
            File::makeDirectory($savepath);
            $this->info("创建目录 {$savepath}");
        }

        if (!File::isDirectory($albumRoot)) {
            File::makeDirectory($albumRoot);
            $this->info("创建目录 {$albumRoot}");
        }

        $albumUrl = $this->getAlbumUrl($nickname);
        $albums   = $this->parseAlbumList($albumUrl);

        collect($albums)->transform(function ($album) { // 获取每个相册照片
            $album['items'] = $this->parseAlbumItems($album['purl']);
            return $album;
        })->each(function ($album) use ($albumRoot) {
            $albumPath  = rtrim($albumRoot, '/') . "/" . trim($album['name']) . "/";
            $faildCount = 0;

            // 建立相册目录
            if (!File::isDirectory($albumPath)) {
                File::makeDirectory($albumPath);
            }

            if (!count($album['items'])) {
                $this->warn("相册 {$album['name']} 为空或已加密");
                return;
            }

            // 下载图片
            collect($album['items'])->tap(function ($items) use ($album, $albumPath) {
                $this->info("正在抓取相册 {$album['name']} to {$albumPath}");
                $this->output->progressStart($items->count());
            })->each(function ($item) use ($albumPath, &$faildCount) {
                $filename = $item['desc'] ?: basename($item['ourl']);
                $filepath = rtrim($albumPath, '/') . '/' . $filename;

                $this->output->progressAdvance();

                try {
                    $client = new Client(['verify' => false]); //忽略SSL错误
                    $client->get($item['ourl'], ['save_to' => $filepath]);
                } catch (\Exception $e) {
                    $faildCount++;
                    if (is_file($filepath)) {
                        unlink($filepath);
                    }

                    info($item['ourl'] . ' 抓取失败，错误：' . $e->getMessage());
                }
            })->tap(function () use ($faildCount) {
                $this->output->progressFinish();
                $faildCount && $this->warn("抓取失败：{$faildCount}");
            });

        });
    }

    private function getAlbumUrl($nickname = '')
    {
        $url     = $this->baseUrl . $nickname;
        $content = file_get_contents($url);

        preg_match('/albumUrl\s*:\s*\'([^\']+)/', $content, $matches);

        if (!$matches) {
            throw new \Exception("获取相册配置失败", 1);
        }

        return $matches[1];
    }

    private function parseAlbumList($albumUrl)
    {
        try {
            $content = file_get_contents($albumUrl);
        } catch (\Exception $e) {
            $this->warn("获取相册配置失败，错误：" . $e->getMessage());
            return [];
        }

        $content = mb_convert_encoding($content, "UTF-8", "GBK");
        // $content = preg_replace('/^var[^=]+=[^=]+=/', '', $content);
        [$null, $content] = explode('=[', $content, 2);
        $content          = '[' . $content;
        $content          = trim($content, ';');
        $matches          = [$content, $content];

        if (!$matches) {
            $this->warn("解析相册配置失败");
            return [];
        }

        $matches[1] = str_replace("'", '"', $matches[1]);
        $matches[1] = str_replace("\\", "\\\\", $matches[1]);
        $matches[1] = preg_replace('/(\{|,)(\w+):/', '\\1"\\2":', $matches[1]);

        $albums = json_decode($matches[1], true);

        return $albums;
    }

    private function parseAlbumItems($pUrl)
    {
        if (!$pUrl) {
            return [];
        }

        if (!preg_match('/^https?:/', $pUrl)) {
            $pUrl = 'http://' . $pUrl;
        }

        try {
            $content = file_get_contents($pUrl);
        } catch (\Exception $e) {
            $this->warn("获取相片配置失败，错误：" . $e->getMessage());
            return [];
        }
        $content = mb_convert_encoding($content, "UTF-8", "GBK");
        $matches = explode('=[', $content, 2);

        if (!$matches) {
            $this->warn("解析相片配置失败");
            return [];
        }

        $matches[1] = '[' . $matches[1];
        $matches[1] = trim($matches[1], ';');
        $matches[1] = str_replace("'", '"', $matches[1]);
        $matches[1] = preg_replace('/(\w+):/', '"\\1":', $matches[1]);

        $items = json_decode($matches[1], true);

        return collect($items)->transform(function ($item) {
            $item['ourl'] = $this->transformPhotoUrl($item['ourl']);
            $item['murl'] = $this->transformPhotoUrl($item['murl']);
            $item['surl'] = $this->transformPhotoUrl($item['surl']);
            $item['turl'] = $this->transformPhotoUrl($item['turl']);
            $item['qurl'] = $this->transformPhotoUrl($item['qurl']);
            $item['desc'] = trim($item['desc']);

            return $item;
        })->toArray();
    }

    private function transformPhotoUrl($path)
    {
        [$num, $path] = explode('/', $path, 2);
        return sprintf('http://img%d.ph.126.net/%s', $num, $path);
    }
}
