<?php

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RejectionReason
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RejectionReason
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
     * @ORM\Column(name="name", type="string", length=40)
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="PdfFile", mappedBy="rejectionReason")
     */
    private $pdfFiles;


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
     * @return RejectionReason
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
     * Constructor
     */
    public function __construct()
    {
        $this->pdfFiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add pdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles
     * @return RejectionReason
     */
    public function addPdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles)
    {
        $this->pdfFiles[] = $pdfFiles;

        return $this;
    }

    /**
     * Remove pdfFiles
     *
     * @param \TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles
     */
    public function removePdfFile(\TNTMOCO\AppBundle\Entity\PdfFile $pdfFiles)
    {
        $this->pdfFiles->removeElement($pdfFiles);
    }

    /**
     * Get pdfFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPdfFiles()
    {
        return $this->pdfFiles;
    }
}
