<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\UsuarioRepository")
 */
class Usuario implements \Serializable, UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $usuarioId;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=false)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado", type="boolean", nullable=true)
     */
    private $habilitado;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fecharegistro", type="datetime", nullable=true)
     */
    private $fecharegistro;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fechaultimologin", type="datetime", nullable=true)
     */
    private $fechaultimologin;

    /**
     * @var \Rol
     *
     * @ORM\ManyToOne(targetEntity="Rol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rol_id", referencedColumnName="rol_id")
     * })
     */
    private $rol;


    /**
     * Usuario constructor.
     * @param string $nombre
     * @param string $apellidos
     * @param string $email
     * @param string $password
     */
    public function __construct($nombre, $apellidos, $email, $password) {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get usuarioId
     *
     * @return integer
     */
    public function getUsuarioId() {
        return $this->usuarioId;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
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
     * Set apellidos
     *
     * @param string $apellidos
     * @return Usuario
     */
    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos() {
        return $this->apellidos;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setContrasenna($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getContrasenna() {
        return $this->password;
    }

    /**
     * Set habilitado
     *
     * @param boolean $habilitado
     * @return Usuario
     */
    public function setHabilitado($habilitado) {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return boolean
     */
    public function getHabilitado() {
        return $this->habilitado;
    }

    /**
     * Set fecharegistro
     *
     * @param string $fecharegistro
     * @return Usuario
     */
    public function setFecharegistro($fecharegistro) {
        $this->fecharegistro = $fecharegistro;

        return $this;
    }

    /**
     * Get fecharegistro
     *
     * @return string
     */
    public function getFecharegistro() {
        return $this->fecharegistro;
    }

    /**
     * Set fechaultimologin
     *
     * @param string $fechaultimologin
     * @return Usuario
     */
    public function setFechaultimologin($fechaultimologin) {
        $this->fechaultimologin = $fechaultimologin;

        return $this;
    }

    /**
     * Get fechaultimologin
     *
     * @return string
     */
    public function getFechaultimologin() {
        return $this->fechaultimologin;
    }

    /**
     * Set rol
     *
     * @param \IcanBundle\Entity\Rol $rol
     * @return Usuario
     */
    public function setRol(\IcanBundle\Entity\Rol $rol = null) {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return \IcanBundle\Entity\Rol
     */
    public function getRol() {
        return $this->rol;
    }

    public function __toString() {
        return $this->getNombre();
    }

    public function getNombreCompleto() {
        return $this->nombre . " " . $this->apellidos;
    }

    /*
     * Implementation of UserInterface
     */

    /**
     * Set usuarioid
     *
     * @param string $usuario_id
     */
    public function setUsuarioId($usuario_id) {
        $this->usuarioId = $usuario_id;
    }

    public function equals(UserInterface $user) {
        return $user->getUsername() == $this->getNick();
    }

    public function eraseCredentials() {

    }

    public function getPassword() {
        return $this->password;
    }

    public function getRoles() {
        if ($this->rol->getRolId() == 1)
            return array('ROLE_ADMIN');
        else
            return array('ROLE_USER');
    }

    public function getSalt() {
        return false;
    }

    public function getUsername() {

        return $this->email;
    }

    public function serialize() {
        return serialize(array(
            $this->usuarioId
        ));
    }

    public function unserialize($serialized) {
        $arr = unserialize($serialized);
        $this->setUsuarioId($arr[0]);
    }
}