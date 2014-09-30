<?php

namespace D4D\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use D4D\AppBundle\Services\Messenger\Config;

/**
 * Images
 */
class Images
{
    /**
     * @var integer
     */
    private $imgid;

    /**
     * @var boolean
     */
    private $imgmain;

    /**
     * @var boolean
     */
    private $imgvalidated;
    
    /**
     * @var \D4D\AppBundle\Entity\Users
     */
    private $userid;
    
    /**
     * @var boolean
     */
    private $homepage;
    
    
    
    
    
    
    
    
    
    
    /**
     * Image ext
     *
     * @var string     
     */
    protected $ext;
    
    
    /**
     * Image file
     *
     * @var File          
     */
    protected $file;
    
    
    
    
    
    
    
    


    /**
     * Get imgid
     *
     * @return integer 
     */
    public function getImgid()
    {
        return $this->imgid;
    }

    /**
     * Set imgmain
     *
     * @param boolean $imgmain
     * @return Images
     */
    public function setImgmain($imgmain)
    {
        $this->imgmain = $imgmain;

        return $this;
    }

    /**
     * Get imgmain
     *
     * @return boolean 
     */
    public function getImgmain()
    {
        return $this->imgmain;
    }

    /**
     * Set imgvalidated
     *
     * @param boolean $imgvalidated
     * @return Images
     */
    public function setImgvalidated($imgvalidated)
    {
        $this->imgvalidated = $imgvalidated;

        return $this;
    }

    /**
     * Get imgvalidated
     *
     * @return boolean 
     */
    public function getImgvalidated()
    {
        return $this->imgvalidated;
    }

    /**
     * Set userid
     *
     * @param \D4D\AppBundle\Entity\Users $userid
     * @return Images
     */
    public function setUserid(\D4D\AppBundle\Entity\Users $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \D4D\AppBundle\Entity\Users 
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set homepage
     *
     * @param boolean $homepage
     * @return Images
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * Get homepage
     *
     * @return boolean 
     */
    public function getHomepage()
    {
        return $this->homepage;
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
    		// do whatever you want to generate a unique name
    		$filename = sha1(uniqid(mt_rand(), true));
    		//$this->ext = $filename.'.'.$this->file->guessExtension();
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
    
    
    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
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
    			//$this->ext
    			$this->imgid . '.' .$this->ext
    	);
    
    	// set the ext property to the filename where you've saved the file
    	//$this->ext = $this->file->getClientOriginalName();
    
    	// clean up the file property as you won't need it anymore
    	$this->file = null;
    }   
    
    
    /**
     * Set ext
     *
     * @param string $ext
     * @return Image
     */
    public function setExt($ext)
    {
    	$this->ext = $ext;
    
    	return $this;
    }
    
    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
    	return $this->ext;
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
    	: $this->getUploadRootDir() . '/' . $this->imgid . '.' . $this->ext;
    }
    
    public function getWebPath()
    {
    	if( !is_file($this->getAbsolutePath()) ){
    		$config = Config::getInstance();
    		$noPhoto = ($this->getUserid()->getUsergender() == 1) ? $config['users']['noImage']['female']: $config['users']['noImage']['male'];
    		return $noPhoto;
    	}
    	
    	return $this->getUploadDir() . '/' . $this->imgid . '.' . $this->ext;
    }
    
    protected function getUploadRootDir()
    {
    	// the absolute directory ext where uploaded
    	// documents should be saved
    	return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
    	// get rid of the __DIR__ so it doesn't screw up
    	// when displaying uploaded doc/image in the view.
    	return '/uploads/userpics';
    }
    
    
    
    
    
    
    
    
    
    
    
    
}
