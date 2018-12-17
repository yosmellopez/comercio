<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DescuentoUso
 *
 * @ORM\Table(name="descuento_uso")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\DescuentoUsoRepository")
 */
class DescuentoUso
{

    /**
     * @var integer
     *
     * @ORM\Column(name="uso_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $usoId;

    /**
     * @var string
     *
     * @ORM\Column(name="rut", type="string", length=50, nullable=false)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

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
     * Get usoId
     *
     * @return integer
     */
    public function getUsoId()
    {
        return $this->usoId;
    }

    /**
     * Set rut
     *
     * @param string $rut
     * @return DescuentoUso
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
     * Set email
     *
     * @param string $email
     * @return DescuentoUso
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     * @return DescuentoUso
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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

}
