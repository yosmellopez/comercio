<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class WebpayConfigRepository extends EntityRepository
{

    /**
     * DevolverConfig: Devuelve configuracion webpay
     *
     * @author Marcel
     */
    public function DevolverConfig()
    {
        $criteria = array();
        return $this->findOneBy($criteria);
    }
}