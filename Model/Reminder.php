<?php

namespace Flower\PlannerBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
/**
 * Reminder
 */
abstract class Reminder
{
    public static $TYPE_EMAIL = 1;
    public static $TYPE_NOTIFICATION = 2;
    public static $TYPE_SMS = 3;
    public static $UNITY_MINUTES = 1;
    public static $UNITY_HOUR = 2;
    public static $UNITY_DAY = 3;
    public static $UNITY_WEEK = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"search","api", "full"})
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="unity", type="integer")
     * @Groups({"search","api", "full"})
     */
    protected $unity;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer")
     * @Groups({"search","api", "full"})
     */
    protected $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     * @Groups({"search","api", "full"})
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Groups({"search","api", "full"})
     */
    protected $date;
    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Planner\Event", inversedBy="reminders")
     * @JoinColumn(name="event_reminder_id", referencedColumnName="id")
     * */
    protected $event;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;
    /**
     * @ORM\ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     * @Groups({"search","api", "full"})
     */
     protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set unity
     *
     * @param integer $unity
     * @return Reminder
     */
    public function setUnity($unity)
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * Get unity
     *
     * @return integer 
     */
    public function getUnity()
    {
        return $this->unity;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return Reminder
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Reminder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Reminder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set event
     *
     * @param \Flower\ModelBundle\Entity\Planner\Event $event
     * @return Reminder
     */
    public function setEvent(\Flower\ModelBundle\Entity\Planner\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Flower\ModelBundle\Entity\Planner\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function getPrefixForInterval(){
        if($this->unity == Reminder::$UNITY_DAY || $this->unity == Reminder::$UNITY_WEEK){
            return 'P';
        }else{
            return 'PT';
        }
    }
    public function getUnityForInterval(){
        switch ($this->unity) {
            case Reminder::$UNITY_MINUTES:
                return 'M';
                break;
            case Reminder::$UNITY_HOUR:
                return 'H';
                break;
            case Reminder::$UNITY_DAY:
                return 'D';
                break;
            case Reminder::$UNITY_WEEK:
                return 'W';
                break;

            default:
                return 'D';
                break;
        }
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Reminder
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Reminder
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return Reminder
     */
    public function setUser(\Flower\ModelBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
