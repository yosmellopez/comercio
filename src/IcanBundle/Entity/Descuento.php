<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Descuento
 *
 * @ORM\Table(name="descuento")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\DescuentoRepository")
 */
class Descuento
{

    /**
     * @var integer
     *
     * @ORM\Column(name="descuento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $descuentoId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=50, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="fechainicio", type="datetime", nullable=true)
     */
    private $fechainicio;

    /**
     * @var string
     *
     * @ORM\Column(name="fechafin", type="datetime", nullable=true)
     */
    private $fechafin;

    /**
     * @var \Porciento
     *
     * @ORM\ManyToOne(targetEntity="Porciento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="porciento_id", referencedColumnName="porciento_id")
     * })
     */
    private $porciento;

    /**
     * Get descuentoId
     *
     * @return integer
     */
    public function getDescuentoId()
    {
        return $this->descuentoId;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Descuento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     * @return Descuento
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return Descuento
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set fechainicio
     *
     * @param string $fechainicio
     * @return Descuento
     */
    public function setFechainicio($fechainicio)
    {
        $this->fechainicio = $fechainicio;

        return $this;
    }

    /**
     * Get fechainicio
     *
     * @return string
     */
    public function getFechainicio()
    {
        return $this->fechainicio;
    }

    /**
     * Set fechafin
     *
     * @param string $fechafin
     * @return Descuento
     */
    public function setFechafin($fechafin)
    {
        $this->fechafin = $fechafin;

        return $this;
    }

    /**
     * Get fechafin
     *
     * @return string
     */
    public function getFechafin()
    {
        return $this->fechafin;
    }

    /**
     * Set porciento
     *
     * @param \IcanBundle\Entity\Porciento $porciento
     * @return Descuento
     */
    public function setPorciento(\IcanBundle\Entity\Porciento $porciento = null)
    {
        $this->porciento = $porciento;

        return $this;
    }

    /**
     * Get porciento
     *
     * @return \IcanBundle\Entity\Porciento
     */
    public function getPorciento()
    {
        return $this->porciento;
    }

}
