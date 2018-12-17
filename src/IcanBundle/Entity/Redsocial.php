<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Redsocial
 *
 * @ORM\Table(name="redsocial")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\RedsocialRepository")
 */
class Redsocial {

    /**
     * @var integer
     *
     * @ORM\Column(name="redsocial_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $redsocialId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=false)
     */
    private $facebook;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarfacebook", type="boolean", nullable=true)
     */
    private $activarfacebook;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="youtube", type="string", length=255, nullable=false)
     */
    private $youtube;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activaryoutube", type="boolean", nullable=true)
     */
    private $activaryoutube;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=false)
     */
    private $twitter;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activartwitter", type="boolean", nullable=true)
     */
    private $activartwitter;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="flickr", type="string", length=255, nullable=false)
     */
    private $flickr;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarflickr", type="boolean", nullable=true)
     */
    private $activarflickr; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="instagram", type="string", length=255, nullable=false)
     */
    private $instagram;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarinstagram", type="boolean", nullable=true)
     */
    private $activarinstagram; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=255, nullable=false)
     */
    private $linkedin;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarlinkedin", type="boolean", nullable=true)
     */
    private $activarlinkedin; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="soundcloud", type="string", length=255, nullable=false)
     */
    private $soundcloud;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarsoundcloud", type="boolean", nullable=true)
     */
    private $activarsoundcloud; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="tumblr", type="string", length=255, nullable=false)
     */
    private $tumblr;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activartumblr", type="boolean", nullable=true)
     */
    private $activartumblr; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="vimeo", type="string", length=255, nullable=false)
     */
    private $vimeo;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activarvimeo", type="boolean", nullable=true)
     */
    private $activarvimeo; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="google", type="string", length=255, nullable=false)
     */
    private $google;
   

    /**
     * @var boolean
     *
     * @ORM\Column(name="activargoogle", type="boolean", nullable=true)
     */
    private $activargoogle;

    /**
     * Get redsocialId
     *
     * @return integer 
     */
    public function getRedsocialId() {
        return $this->redsocialId;
    }
        
    /**
     * Set facebook
     *
     * @param string $facebook
     * @return Redsocial
     */
    public function setFacebook($facebook) {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook() {
        return $this->facebook;
    }

    
    /**
     * Set activarfacebook
     *
     * @param boolean $activarfacebook
     * @return Redsocial
     */
    public function setActivarfacebook($activarfacebook) {
        $this->activarfacebook = $activarfacebook;

        return $this;
    }

    /**
     * Get activarfacebook
     *
     * @return boolean 
     */
    public function getActivarfacebook() {
        return $this->activarfacebook;
    }
    
    
    /**
     * Set youtube
     *
     * @param string $youtube
     * @return Redsocial
     */
    public function setYoutube($youtube) {
        $this->youtube = $youtube;

        return $this;
    }

    /**
     * Get youtube
     *
     * @return string 
     */
    public function getYoutube() {
        return $this->youtube;
    }

    
    /**
     * Set activaryoutube
     *
     * @param boolean $activaryoutube
     * @return Redsocial
     */
    public function setActivaryoutube($activaryoutube) {
        $this->activaryoutube = $activaryoutube;

        return $this;
    }

    /**
     * Get activaryoutube
     *
     * @return boolean 
     */
    public function getActivaryoutube() {
        return $this->activaryoutube;
    }
    
    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Redsocial
     */
    public function setTwitter($twitter) {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter() {
        return $this->twitter;
    }

    
    /**
     * Set activartwitter
     *
     * @param boolean $activartwitter
     * @return Redsocial
     */
    public function setActivartwitter($activartwitter) {
        $this->activartwitter = $activartwitter;

        return $this;
    }

    /**
     * Get activartwitter
     *
     * @return boolean 
     */
    public function getActivartwitter() {
        return $this->activartwitter;
    }
    
    
    /**
     * Set flickr
     *
     * @param string $flickr
     * @return Redsocial
     */
    public function setFlickr($flickr) {
        $this->flickr = $flickr;

        return $this;
    }

    /**
     * Get flickr
     *
     * @return string 
     */
    public function getFlickr() {
        return $this->flickr;
    }

    
    /**
     * Set activarflickr
     *
     * @param boolean $activarflickr
     * @return Redsocial
     */
    public function setActivarflickr($activarflickr) {
        $this->activarflickr = $activarflickr;

        return $this;
    }

    /**
     * Get activarflickr
     *
     * @return boolean 
     */
    public function getActivarflickr() {
        return $this->activarflickr;
    }
    
    
    /**
     * Set instagram
     *
     * @param string $instagram
     * @return Redsocial
     */
    public function setInstagram($instagram) {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * Get instagram
     *
     * @return string 
     */
    public function getInstagram() {
        return $this->instagram;
    }

    
    /**
     * Set activarinstagram
     *
     * @param boolean $activarinstagram
     * @return Redsocial
     */
    public function setActivarinstagram($activarinstagram) {
        $this->activarinstagram = $activarinstagram;

        return $this;
    }

    /**
     * Get activarinstagram
     *
     * @return boolean 
     */
    public function getActivarinstagram() {
        return $this->activarinstagram;
    }
    
    /**
     * Set linkedin
     *
     * @param string $linkedin
     * @return Redsocial
     */
    public function setLinkedin($linkedin) {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * Get linkedin
     *
     * @return string 
     */
    public function getLinkedin() {
        return $this->linkedin;
    }

    
    /**
     * Set activarlinkedin
     *
     * @param boolean $activarlinkedin
     * @return Redsocial
     */
    public function setActivarlinkedin($activarlinkedin) {
        $this->activarlinkedin = $activarlinkedin;

        return $this;
    }

    /**
     * Get activarlinkedin
     *
     * @return boolean 
     */
    public function getActivarlinkedin() {
        return $this->activarlinkedin;
    }
    
    
    /**
     * Set soundcloud
     *
     * @param string $soundcloud
     * @return Redsocial
     */
    public function setSoundcloud($soundcloud) {
        $this->soundcloud = $soundcloud;

        return $this;
    }

    /**
     * Get soundcloud
     *
     * @return string 
     */
    public function getSoundcloud() {
        return $this->soundcloud;
    }

    
    /**
     * Set activarsoundcloud
     *
     * @param boolean $activarsoundcloud
     * @return Redsocial
     */
    public function setActivarsoundcloud($activarsoundcloud) {
        $this->activarsoundcloud = $activarsoundcloud;

        return $this;
    }

    /**
     * Get activarsoundcloud
     *
     * @return boolean 
     */
    public function getActivarsoundcloud() {
        return $this->activarsoundcloud;
    }
    
    
    
    /**
     * Set tumblr
     *
     * @param string $tumblr
     * @return Redsocial
     */
    public function setTumblr($tumblr) {
        $this->tumblr = $tumblr;

        return $this;
    }

    /**
     * Get tumblr
     *
     * @return string 
     */
    public function getTumblr() {
        return $this->tumblr;
    }

    
    /**
     * Set activartumblr
     *
     * @param boolean $activartumblr
     * @return Redsocial
     */
    public function setActivartumblr($activartumblr) {
        $this->activartumblr = $activartumblr;

        return $this;
    }

    /**
     * Get activartumblr
     *
     * @return boolean 
     */
    public function getActivartumblr() {
        return $this->activartumblr;
    }
    
    
    
    /**
     * Set vimeo
     *
     * @param string $vimeo
     * @return Redsocial
     */
    public function setVimeo($vimeo) {
        $this->vimeo = $vimeo;

        return $this;
    }

    /**
     * Get vimeo
     *
     * @return string 
     */
    public function getVimeo() {
        return $this->vimeo;
    }

    
    /**
     * Set activarvimeo
     *
     * @param boolean $activarvimeo
     * @return Redsocial
     */
    public function setActivarvimeo($activarvimeo) {
        $this->activarvimeo = $activarvimeo;

        return $this;
    }

    /**
     * Get activarvimeo
     *
     * @return boolean 
     */
    public function getActivarvimeo() {
        return $this->activarvimeo;
    }
    
    /**
     * Set google
     *
     * @param string $google
     * @return Redsocial
     */
    public function setGoogle($google) {
        $this->google = $google;

        return $this;
    }

    /**
     * Get google
     *
     * @return string 
     */
    public function getGoogle() {
        return $this->google;
    }

    
    /**
     * Set activargoogle
     *
     * @param boolean $activargoogle
     * @return Redsocial
     */
    public function setActivargoogle($activargoogle) {
        $this->activargoogle = $activargoogle;

        return $this;
    }

    /**
     * Get activargoogle
     *
     * @return boolean 
     */
    public function getActivargoogle() {
        return $this->activargoogle;
    }
}