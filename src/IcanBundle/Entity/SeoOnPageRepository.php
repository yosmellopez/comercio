<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SeoOnPageRepository extends EntityRepository
{

    /**
     * BuscarPorUrl: Devuelve la pagina de la url
     * @param string $url url
     *
     * @author Marcel
     */
    public function BuscarPorUrl($url)
    {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarPaginas: Lista los pagina
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPaginas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('p');

        if ($sSearch != "")
            $consulta->andWhere('p.titulo LIKE :titulo OR p.descripcion LIKE :descripcion')
                ->setParameter('titulo', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");

        $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalPaginas: Total de pagina de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalPaginas($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.paginaId) FROM IcanBundle\Entity\SeoOnPage p ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE p.titulo LIKE :titulo OR p.descripcion LIKE :descripcion ';
            else
                $where .= 'AND p.titulo LIKE :titulo OR p.descripcion LIKE :descripcion  ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_titulo = substr_count($consulta, ':titulo');
        if ($esta_query_titulo == 1)
            $query->setParameter('titulo', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}