<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoView
 *
 * @ORM\Table(name="producto_view")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ProductoViewRepository")
 */
class ProductoView
{
    /**
     * @var integer
     *
     * @ORM\Column(name="productoview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $productoviewId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=50, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;      
    
    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="producto_id")
     * })
     */
    private $producto;
        

    /**
     * Get productoviewId
     *
     * @return integer 
     */
    public function getProductoViewId()
    {
        return $this->productoviewId;
    }
    
    /**
     * Set ip
     *
     * @param string $ip
     * @return ProductoView
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set fecha
     *
     * @param string $fecha
     * @return ProductoView
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return string 
     */
    public function getFecha()
    {
        return $this->fecha;
    }
    
            
    /**
     * Set producto
     *
     * @param \IcanBundle\Entity\Producto $producto
     * @return Producto
     */
    public function setProducto(\IcanBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;
    
        return $this;
    }

    /**
     * Get producto
     *
     * @return \IcanBundle\Entity\Producto
     */
    public function getProducto()
    {
        return $this->producto;
    }   
    
}