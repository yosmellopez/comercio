<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Porciento
 *
 * @ORM\Table(name="porciento")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\PorcientoRepository")
 */
class Porciento
{

    /**
     * @var integer
     *
     * @ORM\Column(name="porciento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $porcientoId;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor", type="integer", nullable=false)
     */
    private $valor;

    /**
     * Get porcientoId
     *
     * @return integer
     */
    public function getPorcientoId()
    {
        return $this->porcientoId;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     * @return Porciento
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return integer
     */
    public function getValor()
    {
        return $this->valor;
    }

}
