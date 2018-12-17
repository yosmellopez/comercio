<?php

namespace IcanBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Token
 *
 * @ORM\Table(name="token")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\TokenRepository")
 */
class Token
{

    /**
     * @var integer
     *
     * @ORM\Column(name="token_id", type="string", nullable=false, length=500)
     * @ORM\Id
     */
    private $tokenId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha_token", type="datetime", nullable=true)
     */
    private $fechaToken;


    /**
     * @var string
     *
     * @ORM\Column(name="fecha_expira", type="datetime", nullable=true)
     */
    private $fechaExpira;

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
     * @return int
     */
    public function getTokenId() {
        return $this->tokenId;
    }

    /**
     * @param int $tokenId
     */
    public function setTokenId($tokenId) {
        $this->tokenId = $tokenId;
    }

    /**
     * @return bool
     */
    public function isEstado() {
        return $this->estado;
    }

    /**
     * @param bool $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * @return string
     */
    public function getFechaToken() {
        return $this->fechaToken;
    }

    /**
     * @param string $fechaToken
     */
    public function setFechaToken($fechaToken) {
        $this->fechaToken = $fechaToken;
    }

    /**
     * @return string
     */
    public function getFechaExpira() {
        return $this->fechaExpira;
    }

    /**
     * @param string $fechaExpira
     */
    public function setFechaExpira($fechaExpira) {
        $this->fechaExpira = $fechaExpira;
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
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }
}
