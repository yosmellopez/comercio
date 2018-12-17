<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoImagen
 *
 * @ORM\Table(name="producto_imagen")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ProductoImagenRepository")
 */
class ProductoImagen {

    /**
     * @var integer
     *
     * @ORM\Column(name="productoimagen_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $productoimagenId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=false)
     */
    private $imagen;
    
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
     * Get productoimagenId
     *
     * @return integer 
     */
    public function getProductoimagenId() {
        return $this->productoimagenId;
    }
    
    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Producto
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
     * Set producto
     *
     * @param \IcanBundle\Entity\Producto $producto
     * @return Permisousuario
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