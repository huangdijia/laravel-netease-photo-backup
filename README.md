# laravel-netease-photo-backup

## 安装

```base
composer require huangdijia/laravel-netease-photo-backup
```

## 使用

```bash
php artisan netease:photo-backup your-photo-nickname [savepath]
```

## 执行结果

```bash
正在备份 your-photo-nickname 相册 from http://photo.163.com/your-photo-nickname

正在抓取相册 新专辑－一生最爱
 19/19 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 第一滩(大二)
 43/43 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 编号20081002
 25/25 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 2008第一滩
 21/21 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 瓜瓜
 12/12 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 家乡风景 
 132/132 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

抓取失败：18
正在抓取相册 我的照片
 22/22 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 大学相片
 28/28 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 高中相片
 33/33 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 初中相片
 25/25 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 小学相片
 19/19 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 第一滩(大一)
 13/13 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

正在抓取相册 外婆生日
 6/6 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
```

## 前提

> 相册必须改为 `公开模式`！