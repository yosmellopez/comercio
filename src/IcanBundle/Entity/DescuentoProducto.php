<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DescuentoProducto
 *
 * @ORM\Table(name="descuento_producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\DescuentoProductoRepository")
 */
class DescuentoProducto
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Descuento
     *
     * @ORM\ManyToOne(targetEntity="Descuento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="descuento_id", referencedColumnName="descuento_id")
     * })
     */
    private $descuento;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descuento
     *
     * @param \IcanBundle\Entity\Descuento $descuento
     * @return PermisoRol
     */
    public function setDescuento(\IcanBundle\Entity\Descuento $descuento = null)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return \IcanBundle\Entity\Descuento
     */
    public function getDescuento()
    {
        return $this->descuento;
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
