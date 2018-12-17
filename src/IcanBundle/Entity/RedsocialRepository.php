<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RedsocialRepository extends EntityRepository {

    /**
     * DevolverRedSocial: Devuelve la red social     *
     *
     * @author Marcel
     */
    public function DevolverRedSocial() {
        $criteria = array();
        return $this->findOneBy($criteria);
    }

    /**
     * ListarRedessociales: Lista los redsocial
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     * 
     * @author Marcel
     */
    public function ListarRedessociales($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0) {
        $consulta = $this->createQueryBuilder('r');

        if ($sSearch != "")
            $consulta->andWhere('r.nombre LIKE :nombre OR r.link LIKE :link')
                    ->setParameter('nombre', "%${sSearch}%")
                    ->setParameter('link', "%${sSearch}%");

        if ($iSortCol_0 == 0)
            $consulta->orderBy('r.nombre', $sSortDir_0);
        if ($iSortCol_0 == 2)
            $consulta->orderBy('r.link', $sSortDir_0);
        if ($iSortCol_0 == 1)
            $consulta->orderBy('r.estado', $sSortDir_0);

        $lista = $consulta->setFirstResult($start)
                        ->setMaxResults($limit)
                        ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalRedessociales: Total de redsocial de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalRedessociales($sSearch) {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(r.redsocialId) FROM IcanBundle\Entity\Redsocial r ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE r.link LIKE :link OR r.nombre LIKE :nombre ';
            else
                $where .= 'AND r.link LIKE :link OR r.nombre LIKE :nombre  ';
        }

        $consulta.=$join;
        $consulta.=$where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch        
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_link = substr_count($consulta, ':link');
        if ($esta_query_link == 1)
            $query->setParameter('link', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
