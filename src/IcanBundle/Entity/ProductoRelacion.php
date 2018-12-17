<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoRelacion
 *
 * @ORM\Table(name="producto_relacion")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\ProductoRelacionRepository")
 */
class ProductoRelacion {

    /**
     * @var integer
     *
     * @ORM\Column(name="productorelacion_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $productorelacionId;

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
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="productorelacionado_id", referencedColumnName="producto_id")
     * })
     */
    private $productoRelacion;

    /**
     * Get productorelacionId
     *
     * @return integer 
     */
    public function getProductorelacionId() {
        return $this->productorelacionId;
    }

    /**
     * Set producto
     *
     * @param \IcanBundle\Entity\Producto $producto
     * @return Permisousuario
     */
    public function setProducto(\IcanBundle\Entity\Producto $producto = null) {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \IcanBundle\Entity\Producto
     */
    public function getProducto() {
        return $this->producto;
    }

    /**
     * Set productoRelacion
     *
     * @param \IcanBundle\Entity\Producto $productoRelacion
     * @return Permisousuario
     */
    public function setProductoRelacion(\IcanBundle\Entity\Producto $productoRelacion = null) {
        $this->productoRelacion = $productoRelacion;

        return $this;
    }

    /**
     * Get productoRelacion
     *
     * @return \IcanBundle\Entity\Producto
     */
    public function getProductoRelacion() {
        return $this->productoRelacion;
    }

}