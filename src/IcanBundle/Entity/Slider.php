<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Slider
 *
 * @ORM\Table(name="slider")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\SliderRepository")
 */
class Slider {

    /**
     * @var integer
     *
     * @ORM\Column(name="slider_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sliderId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @var string
     *
     * @ORM\Column(name="formadeabrir", type="string", length=255, nullable=true)
     */
    private $formadeabrir;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cantclick", type="integer", nullable=true)
     */
    private $cantclick;
    
    /**
     * @var string
     *
     * @ORM\Column(name="posicion", type="integer", nullable=true)
     */
    private $posicion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechapublicacion", type="datetime", nullable=true)
     */
    private $fechapublicacion;    
  

    /**
     * Get sliderId
     *
     * @return integer 
     */
    public function getSliderId() {
        return $this->sliderId;
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Slider
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
     * Set titulo
     *
     * @param string $titulo
     * @return Slider
     */
    public function setTitulo($titulo) {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo() {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Slider
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
     * Set imagen
     *
     * @param string $imagen
     * @return Slider
     */
    public function setImagen($imagen) {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen() {
        return $this->imagen;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     * @return Slider
     */
    public function setEstado($estado) {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean 
     */
    public function getEstado() {
        return $this->estado;
    }
    
    /**
     * Set url
     *
     * @param string $url
     * @return Slider
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * Set formadeabrir
     *
     * @param string $formadeabrir
     * @return Slider
     */
    public function setFormadeabrir($formadeabrir) {
        $this->formadeabrir = $formadeabrir;

        return $this;
    }

    /**
     * Get formadeabrir
     *
     * @return string 
     */
    public function getFormadeabrir() {
        return $this->formadeabrir;
    }
    
    /**
     * Set cantclick
     *
     * @param string $cantclick
     * @return Slider
     */
    public function setCantclick($cantclick) {
        $this->cantclick = $cantclick;

        return $this;
    }

    /**
     * Get cantclick
     *
     * @return string 
     */
    public function getCantclick() {
        return $this->cantclick;
    }
    
    /**
     * Set posicion
     *
     * @param string $posicion
     * @return Slider
     */
    public function setPosicion($posicion) {
        $this->posicion = $posicion;

        return $this;
    }

    /**
     * Get posicion
     *
     * @return string 
     */
    public function getPosicion() {
        return $this->posicion;
    }
    
    /**
     * Set fechapublicacion
     *
     * @param string $fechapublicacion
     * @return Slider
     */
    public function setFechapublicacion($fechapublicacion) {
        $this->fechapublicacion = $fechapublicacion;

        return $this;
    }

    /**
     * Get fechapublicacion
     *
     * @return string 
     */
    public function getFechapublicacion() {
        return $this->fechapublicacion;
    }    

}