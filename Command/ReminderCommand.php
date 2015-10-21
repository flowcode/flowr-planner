<?php

namespace Flower\PlannerBundle\Command;

/**
 *
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class ReminderCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('flower:reminder:cron')
                ->setDescription('Init reminder cron');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start reminder cron ");
        $reminderService = $this->getContainer()->get("flower.core.service.reminder");
        $reminderService->run();
        $output->writeln("Done.");
    }
}
