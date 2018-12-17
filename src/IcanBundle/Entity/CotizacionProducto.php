<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CotizacionProducto
 *
 * @ORM\Table(name="cotizacion_producto")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\CotizacionProductoRepository")
 */
class CotizacionProducto
{

    /**
     * @var integer
     *
     * @ORM\Column(name="cotizacionproducto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $cotizacionproductoId;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="descuento", type="integer", nullable=false)
     */
    private $descuento;

    /**
     * @var \Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cotizacion_id", referencedColumnName="cotizacion_id")
     * })
     */
    private $cotizacion;

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
     * Get cotizacionproductoId
     *
     * @return integer
     */
    public function getCotizacionProductoId()
    {
        return $this->cotizacionproductoId;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return CotizacionProducto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * @return int
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * @param int $descuento
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    }

    /**
     * Set cotizacion
     *
     * @param \IcanBundle\Entity\Cotizacion $cotizacion
     * @return Permisousuario
     */
    public function setCotizacion(\IcanBundle\Entity\Cotizacion $cotizacion = null)
    {
        $this->cotizacion = $cotizacion;

        return $this;
    }

    /**
     * Get cotizacion
     *
     * @return \IcanBundle\Entity\Cotizacion
     */
    public function getCotizacion()
    {
        return $this->cotizacion;
    }

    /**
     * Set producto
     *
     * @param \IcanBundle\Entity\Producto $producto
     * @return PermisoRol
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
