<?php

namespace Flower\PlannerBundle\Service;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Flower\ModelBundle\Entity\Planner\Reminder;
use DateInterval;
use DateTime;
/**
 * Description of ReminderService
 *
 * @author Francisco Memoli <fmemoli@flowcode.com.ar>
 */
class ReminderService implements ContainerAwareInterface
{

    /**
     * @var Container
     */
    private $container;
    /**
     * @var entity manager
     */
    private $em;
    /**
     * @var connection
     */
    private $conn;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get("doctrine.orm.entity_manager");
        $this->conn = $this->em->getConnection();
    }
    /**
     * This methos search all the pendings reminders for this minute and send notifications.
     * @author Francisco Memoli <fmemoli@flowcode.com.ar>
     * @date   2015-09-24
     * @return void
     */
    public function run(){
        $em = $this->em;
        $batch = 100;
        $this->entityNameNotification = $em->getClassMetadata("FlowerModelBundle:Planner\Reminder")->getName();
        $dateFrom = new DateTime();
        $countReminders = $em->getRepository('FlowerModelBundle:Planner\Reminder')->countAllPendingReminders($dateFrom);
        $pages = ceil($countReminders/$batch);
        $this->disableLogging();
        for ($i=0; $i < ($pages); $i++) { 
            $offset =($i*$batch);//not necessary for now.
            $redminders = $em->getRepository('FlowerModelBundle:Planner\Reminder')->getAllPendingReminders($dateFrom,$batch,0);
            foreach ($redminders as $reminder) {
                $date = clone $reminder->getDate();
                $date->add(new DateInterval("PT1S"));
                $reminder->setDate($date);
                $notification = $this->container->get("flower.core.service.notification")->getNotificateReminder($reminder);
                $em->persist($notification);
            }
            $this->flushAndClear();
        }
        $this->reEnableLogging();
        $this->finish();
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging() {
        $config = $this->em->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
     * Re-enable Doctrine logging
     */
    protected function reEnableLogging() {
        $config = $this->em->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }

    /**
     * Do ending process tasks.
     *
     */
    public function finish() {
        $this->flushAndClear();

        $this->reEnableLogging();
    }

    /**
     * Flush and clear the entity manager
     */
    protected function flushAndClear() {
        $this->em->flush();
        $this->em->clear($this->entityNameNotification);
    }
}
