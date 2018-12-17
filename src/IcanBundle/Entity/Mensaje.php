<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mensaje
 *
 * @ORM\Table(name="mensaje")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\MensajeRepository")
 */
class Mensaje {

    /**
     * @var integer
     *
     * @ORM\Column(name="mensaje_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $mensajeId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;  
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50, nullable=false)
     */
    private $telefono;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="asunto", type="string", length=255, nullable=false)
     */
    private $asunto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;   

    /**
     * @var boolean
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;         
    

    /**
     * Get mensajeId
     *
     * @return integer 
     */
    public function getMensajeId() {
        return $this->mensajeId;
    }
        
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Mensaje
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }
        
    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Mensaje
     */
    public function setTelefono($telefono) {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono() {
        return $this->telefono;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return Mensaje
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
     * Set asunto
     *
     * @param string $asunto
     * @return Mensaje
     */
    public function setAsunto($asunto) {
        $this->asunto = $asunto;

        return $this;
    }

    /**
     * Get asunto
     *
     * @return string 
     */
    public function getAsunto() {
        return $this->asunto;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Mensaje
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }    

    /**
     * Set fecha
     *
     * @param boolean $fecha
     * @return Mensaje
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