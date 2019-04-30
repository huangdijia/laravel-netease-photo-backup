<?php

namespace Huangdijia\Netease;

use Illuminate\Support\ServiceProvider;

class PhotoBackupProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\PhotoBackupCommand::class,
            ]);
        }
    }
}
