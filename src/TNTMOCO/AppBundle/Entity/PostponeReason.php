<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostponeReason
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TNTMOCO\AppBundle\Entity\PostponeReasonRepository")
 */
class PostponeReason
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_new_delivery_date_required", type="boolean")
     */
    private $isNewDeliveryDateRequired;


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
     * Set name
     *
     * @param string $name
     * @return PostponeReason
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return PostponeReason
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
     * Set isNewDeliveryDateRequired
     *
     * @param boolean $isNewDeliveryDateRequired
     * @return PostponeReason
     */
    public function setIsNewDeliveryDateRequired($isNewDeliveryDateRequired)
    {
        $this->isNewDeliveryDateRequired = $isNewDeliveryDateRequired;

        return $this;
    }

    /**
     * Get isNewDeliveryDateRequired
     *
     * @return boolean 
     */
    public function getIsNewDeliveryDateRequired()
    {
        return $this->isNewDeliveryDateRequired;
    }
}
