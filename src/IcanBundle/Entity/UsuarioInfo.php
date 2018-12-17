<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioInfo
 *
 * @ORM\Table(name="usuario_info")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\UsuarioInfoRepository")
 */
class UsuarioInfo
{

    /**
     * @var integer
     *
     * @ORM\Column(name="info_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $infoId;

    /**
     * @var string
     *
     * @ORM\Column(name="rut", type="string", length=50, nullable=false)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50, nullable=false)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=50, nullable=false)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=50, nullable=false)
     */
    private $numero;

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
     * Get infoId
     *
     * @return integer
     */
    public function getInfoId()
    {
        return $this->infoId;
    }

    /**
     * Set rut
     *
     * @param string $rut
     * @return UsuarioInfo
     */
    public function setRut($rut)
    {
        $this->rut = $rut;

        return $this;
    }

    /**
     * Get rut
     *
     * @return string
     */
    public function getRut()
    {
        return $this->rut;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Cotizacion
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set calle
     *
     * @param string $calle
     * @return UsuarioInfo
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return UsuarioInfo
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set usuario
     *
     * @param \IcanBundle\Entity\Usuario $usuario
     * @return Usuario
     */
    public function setUsuario(\IcanBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \IcanBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
