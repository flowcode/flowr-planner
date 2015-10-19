<?php

namespace Flower\PlannerBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;


/**
 * Event
 */
abstract class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"search"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"search"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255,nullable=true)
     * @Groups({"search"})
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"search"})
     */
    protected $description;

    /**
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\Clients\Contact")
     * @JoinTable(name="event_contact",
     *      joinColumns={@JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contact_id", referencedColumnName="id")}
     *      )
     **/
    protected $contacts;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     * @Groups({"search"})
     */
    protected $startDate;
    /**
     * @var DateTime
     *
     * @ORM\Column(name="endDate", type="datetime",nullable=true)
     * @Groups({"search"})
     */
    protected $endDate;
    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float",nullable=true)
     */
    protected $latitude;
        /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float",nullable=true)
     */
    protected $longitude;
    /**
     * @OneToMany(targetEntity="Reminder", mappedBy="event", cascade={"persist","remove"})
     * */
    protected $reminders;

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
     * @ManyToMany(targetEntity="\Flower\ModelBundle\Entity\User\User", inversedBy="events")
     * @JoinTable(name="user_event")
     */
    protected $users;
    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User", inversedBy="events")
     * @JoinColumn(name="user_event_id", referencedColumnName="id")
     * @Groups({"search"})
     * */
    protected $owner;

    /**
     * Add users
     *
     * @param \Flower\ModelBundle\Entity\User\User $users
     * @return Event
     */
    public function addUser(\Flower\ModelBundle\Entity\User\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Flower\ModelBundle\Entity\User\User $users
     */
    public function removeUser(\Flower\ModelBundle\Entity\User\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
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
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Event
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->reminders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Event
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Event
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Add contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     * @return Event
     */
    public function addContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Flower\ModelBundle\Entity\Clients\Contact $contacts
     */
    public function removeContact(\Flower\ModelBundle\Entity\Clients\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set owner
     *
     * @param \Flower\ModelBundle\Entity\User\User $owner
     * @return Event
     */
    public function setOwner(\Flower\ModelBundle\Entity\User\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Flower\ModelBundle\Entity\User\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Event
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Event
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add reminders
     *
     * @param \Flower\ModelBundle\Entity\Planner\Reminder $reminders
     * @return Event
     */
    public function addReminder(\Flower\ModelBundle\Entity\Planner\Reminder $reminder)
    {
        $reminder->setEvent($this);
        $this->reminders[] = $reminder;

        return $this;
    }
    public function updateRedminders(){
        foreach ($this->getReminders() as $reminder) {
            $eventDate = clone $this->getStartDate();
            $date = $eventDate->sub(new \DateInterval($reminder->getPrefixForInterval().$reminder->getAmount().$reminder->getUnityForInterval()));
            $reminder->setDate($date);
        }
    }
    /**
     * Remove reminders
     *
     * @param \Flower\ModelBundle\Entity\Planner\Reminder $reminders
     */
    public function removeReminder(\Flower\ModelBundle\Entity\Planner\Reminder $reminders)
    {
        $reminders->setEvent(null);
        $this->reminders->removeElement($reminders);
    }
    /**
     * Set reminders
     *
     * @param \Array $reminders
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setReminders($reminders)
    {
        $this->reminders = $reminders;
        return $this;
    }
    /**
     * Get reminders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReminders()
    {
        return $this->reminders;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Event
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
     * @return Event
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
}
