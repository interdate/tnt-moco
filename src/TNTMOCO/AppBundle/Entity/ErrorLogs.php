<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErrorLogs
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ErrorLogs
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
     * @ORM\ManyToOne(targetEntity="Log")
     * @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     */
    private $log;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=255)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;


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
     * Set log
     *
     * @param \TNTMOCO\AppBundle\Entity\Log $log
     * @return ErrorLogs
     */
    public function setLog(\TNTMOCO\AppBundle\Entity\Log $log = null)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return \TNTMOCO\AppBundle\Entity\Log 
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return ErrorLogs
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ErrorLogs
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
}
