<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TerminoRepository extends EntityRepository
{

    /**
     * DevolverTerminos: Devuelve la pagina de terminos y condiciones
     *
     * @author Marcel
     */
    public function DevolverTerminos()
    {
        $criteria = array();
        return $this->findOneBy($criteria);
    }
}
