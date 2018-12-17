<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\NewsletterRepository")
 */
class Newsletter {

    /**
     * @var integer
     *
     * @ORM\Column(name="newsletter_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $newsletterId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;         
    

    /**
     * Get newsletterId
     *
     * @return integer 
     */
    public function getNewsletterId() {
        return $this->newsletterId;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return Newsletter
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param bool $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Set fecha
     *
     * @param boolean $fecha
     * @return Newsletter
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return boolean 
     */
    public function getFecha() {
        return $this->fecha;
    }
    
    
}