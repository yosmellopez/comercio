<?php

namespace IcanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Compra
 *
 * @ORM\Table(name="compra")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\CompraRepository")
 */
class Compra
{

    /**
     * @var integer
     *
     * @ORM\Column(name="compra_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $compraId;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=false)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha_compra", type="datetime", nullable=true)
     */
    private $fechaCompra;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="usuario_id")
     * })
     */
    private $usuario;

    /**
     * @var \Producto
     *
     * @ORM\ManyToMany(targetEntity="Producto")
     * @ORM\JoinTable(name="compra_productos",
     *     @ORM\JoinColumn(name="compra_id", referencedColumnName="compra_id"),
     *     @ORM\InverseJoinColumn(name="producto_id", referencedColumnName="producto_id")
     * )
     */
    private $productos;

    /**
     * Compra constructor.
     */
    public function __construct() {
        $this->productos = new ArrayCollection();
    }

    /**
     * Get compraId
     *
     * @return integer
     */
    public function getCompraId() {
        return $this->compraId;
    }

    /**
     * Set nombre
     *
     * @param string $monto
     * @return Compra
     */
    public function setMonto($monto) {
        $this->monto = $monto;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getMonto() {
        return $this->monto;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Compra
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
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
     * Set estado
     *
     * @param boolean $estado
     * @return Compra
     */
    public function setEstado($estado) {
        $this->estado = $estado;
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
     * Set fechapublicacion
     *
     * @param string $fechaCompra
     * @return Compra
     */
    public function setFechaCompra($fechaCompra) {
        $this->fechaCompra = $fechaCompra;
    }

    /**
     * Get fechapublicacion
     *
     * @return string
     */
    public function getFechaCompra() {
        return $this->fechaCompra;
    }

    /**
     * @return \Usuario
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * @param \Usuario $usuario
     */
    public function setUsuario(Usuario $usuario) {
        $this->usuario = $usuario;
    }

    /**
     * @return \Producto
     */
    public function getProductos() {
        return $this->productos;
    }

    /**
     * @param \Marca $productos
     */
    public function setProductos($productos) {
        $this->productos = $productos;
    }

}
