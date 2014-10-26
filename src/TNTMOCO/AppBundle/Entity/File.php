<?php 

namespace TNTMOCO\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;



/**
 * Class Image
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=2)
 * @ORM\DiscriminatorMap({"PU" = "ImageFile", "DL" = "TextFile", "PP" = "TextFile", "CN" = "PdfFile"})
 * @ORM\HasLifecycleCallbacks  
 */
abstract class File 
{
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", length=4)
	 */
	protected $ext;
	
	/**
	 * File type	  	 
	 * @var string	 
	 */	 
	protected $type;
	
	
	/**	 	 	 
	 * @ORM\Column(type="string", length=70, nullable=true)
	 */
	protected $location;
		
	
	/**	 	 	 
	 * @ORM\Column(type="datetime")
	 */
	protected $datetime;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="imageFiles")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 **/
	protected $user;
	
	
	protected $file;
		
	
	public function __construct($type){
		$this->type = $type;
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
	 * Set location
	 *
	 * @param string $location
	 * @return ImageFile
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	
		return $this;
	}
	
	/**
	 * Get location
	 *
	 * @return string
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	/**
	 * Set datetime
	 *
	 * @param \DateTime $datetime
	 * @return ImageFile
	 */
	public function setDatetime($datetime)
	{
		$this->datetime = $datetime;
	
		return $this;
	}
	
	/**
	 * Get datetime
	 *
	 * @return \DateTime
	 */
	public function getDatetime()
	{
		return $this->datetime;
	}
	
	/**
	 * Set user
	 *
	 * @param \TNTMOCO\AppBundle\Entity\User $user
	 * @return ImageFile
	 */
	public function setUser(\TNTMOCO\AppBundle\Entity\User $user = null)
	{
		$this->user = $user;
	
		return $this;
	}
	
	/**
	 * Get user
	 *
	 * @return \TNTMOCO\AppBundle\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	 * Set ext
	 *
	 * @param string $ext
	 * @return ImageFile
	 */
	public function setExt($ext)
	{
		$this->ext = $ext;
	
		return $this;
	}
	
	/**
	 * Get ext
	 *
	 * @return integer
	 */
	public function getExt()
	{
		return $this->ext;
	}
		
	
	/**
     * Called before saving the entity
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
    	if (null !== $this->file) {    		
    		if(!is_dir($this->getUploadRootDir())){    						
    			mkdir($this->getUploadRootDir(), 0777, true);
    		}    		
    		
    		$this->ext = $this->file->guessExtension();    		
    	}
    }
    
    /**
     * Called before entity removal
     *
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
    	if ($file = $this->getAbsolutePath()) {
    		unlink($file);
    	}
    }
    
    public function upload()
    {
    	// the file property can be empty if the field is not required
    	if (null === $this->file) {
    		return;
    	}
    
    	// use the original file name here but you should
    	// sanitize it at least to avoid any security issues
    
    	// move takes the target directory and then the
    	// target filename to move to
    	$this->file->move(
    		$this->getUploadRootDir(),    	
    		$this->id . '.' .$this->ext
    	);
    
    	// set the ext property to the filename where you've saved the file
    	//$this->ext = $this->file->getClientOriginalName();
    
    	// clean up the file property as you won't need it anymore
    	$this->file = null;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
    	$this->file = $file;
    }
    
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
    	return $this->file;
    }        
    
    public function getAbsolutePath()
    {
    	return null === $this->ext
    	? null
    	: $this->getUploadRootDir() . '/' . $this->id . '.' . $this->ext;
    }
    
    public function getWebPath()
    {
    	/*
    	if( !is_file($this->getAbsolutePath()) ){
    		$config = Config::getInstance();
    		$noPhoto = ($this->getUserid()->getUsergender() == 1) ? $config['users']['noImage']['female']: $config['users']['noImage']['male'];
    		return $noPhoto;
    	}
    	*/
    	 
    	return $this->getUploadDir() . '/' . $this->id . '.' . $this->ext;
    }
    
    public function getUploadRootDir()
    {
    	// the absolute directory ext where uploaded
    	// documents should be saved
    	return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir();
    }
    
    abstract function getUploadDir(); 
    
    
   
}
