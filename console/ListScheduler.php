<?php
declare(strict_types=1);

namespace TamerHassan\ScheduleList\Console;

use TamerHassan\ScheduleList\Classes\ScheduleEvent;
use TamerHassan\ScheduleList\Classes\ScheduleList;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListScheduler extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'schedule:list';

    /**
     * @var string The console command description.
     */
    protected $description = 'List Scheduled Tasks';


    protected $scheduleList;

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(ScheduleList $scheduleList)
    {
        $this->scheduleList = $scheduleList;
        $events = $this->scheduleList->all();
        if (count($events) === 0) {
            $this->info('No tasks scheduled');
            return;
        }
        if ($this->option('cron')) {
            $this->outputCronStyle($events);
            return;
        }
        $this->outputTableStyle($events, $this->output);
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['cron', null, InputOption::VALUE_NONE, 'Show output cron style', null],
        ];
    }

    /**
     * @param array|ScheduleEvent[] $events
     */
    protected function outputCronStyle($events)
    {
        foreach ($events as $event) {
            $this->line($event->getExpression() . ' ' . $event->getFullCommand());
        }
    }
    /**
     * @param array|ScheduleEvent[] $events
     */
    protected function outputTableStyle($events, $output)
    {
        $isVerbosityNormal = $this->output->getVerbosity() === OutputInterface::VERBOSITY_NORMAL;
        $rows = [];
        foreach ($events as $event) {
            $rows[] = [
                'expression' => $event->getExpression(),
                'last run at' => $event->getPreviousRunDate()->format('Y-m-d H:i:s'),
                'next run at' => $event->getNextRunDate()->format('Y-m-d H:i:s'),
                'command' => $isVerbosityNormal ? $event->getShortCommand() : $event->getFullCommand(),
                'description' => $event->getDescription(),
            ];
        }
        $headers = array_keys($rows[0]);

        $table = new Table($output);
        $table->setHeaders($headers)->setRows($rows);
        $table->render();
    }

}
