<?php
declare(strict_types=1);

namespace TamerHassan\ScheduleList;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'Schedule List',
            'description' => 'Command that lists scheduled tasks',
            'author' => 'Tamer Hassan',
            'icon' => 'icon-leaf'
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('schedule.list', 'TamerHassan\ScheduleList\Console\ListScheduler');
    }
}
