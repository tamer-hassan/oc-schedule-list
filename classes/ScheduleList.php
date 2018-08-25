<?php

declare(strict_types=1);

namespace TamerHassan\ScheduleList\Classes;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use October\Rain\Foundation\Console\Kernel as ConsoleKernel;

class ScheduleList
{
    /**
     * @var Schedule
     */
    private $schedule;
    /**
     * @var ConsoleKernel
     */
    private $consoleKernel;
    /**
     * @param Schedule $schedule
     * @param ConsoleKernel $consoleKernel
     */
    public function __construct(Schedule $schedule, ConsoleKernel $consoleKernel)
    {
        $this->schedule = $schedule;
        $this->consoleKernel = $consoleKernel;
    }
    /**
     * @return array|ScheduleEvent[]
     */
    public function all(): array
    {
        $events = [];
        /** @var Event $event */
        foreach ($this->schedule->events() as $event) {
            $fullCommand = $event->buildCommand();
            $scheduleEvent = new ScheduleEvent(
                $event->getExpression(),
                $event->timezone,
                $fullCommand,
                $event->description ?? ''
            );
            if (empty($scheduleEvent->getDescription())) {
                try {
                    $scheduleEvent->setDescription($this->getDescriptionFromCommand($scheduleEvent->getCommandName()));
                } catch (\InvalidArgumentException $invArgExc) {
                    $scheduleEvent->setDescription("Maybe an anonymous function call");
                }
            }
            $events[] = $scheduleEvent;
        }
        return $events;
    }
    /**
     * Parse the description from the Command instead of the scheduled event
     *
     * @param string $commandName
     * @return string
     */
    private function getDescriptionFromCommand(string $commandName): string
    {
        $commands = $this->consoleKernel->all();
        if (!isset($commands[$commandName])) {
            return '';
        }
        try {
            $className = get_class($commands[$commandName]);
            $reflection = new \ReflectionClass($className);
            return (string)$reflection->getDefaultProperties()['description'];
        } catch (\ReflectionException $exception) {
            return '';
        }
    }
}
