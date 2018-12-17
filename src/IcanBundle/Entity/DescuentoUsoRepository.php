<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DescuentoUsoRepository extends EntityRepository
{
    /**
     * BuscarUso: Saber si un codigo se ha usado anteriormente
     * @param int $descuento_id
     *
     * @author Marcel
     */
    public function BuscarUso($rut, $email, $descuento_id)
    {
        $consulta = $this->createQueryBuilder('d_u')
            ->leftJoin('d_u.descuento', 'd')
            ->where('d.descuentoId = :descuento_id')
            ->andWhere('d_u.rut = :rut AND d_u.email = :email')
            ->setParameter('descuento_id', $descuento_id)
            ->setParameter('rut', $rut)
            ->setParameter('email', $email)
            ->getQuery();

        $entity = $consulta->getOneOrNullResult();
        return $entity;
    }

    /**
     * ListarUsosDeDescuento: Lista los usos de un descuento
     * @param int $descuento_id
     *
     * @author Marcel
     */
    public function ListarUsosDeDescuento($descuento_id)
    {
        $consulta = $this->createQueryBuilder('d_u')
            ->leftJoin('d_u.descuento', 'd')
            ->where('d.descuentoId = :descuento_id')
            ->setParameter('descuento_id', $descuento_id)
            ->orderBy('d_u.createdAt', "DESC")
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }


    /**
     * ListarUsos: Lista los usos
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarUsos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $descuento_id = "")
    {
        $consulta = $this->createQueryBuilder('d_u')
            ->leftJoin('d_u.descuento', 'd');

        if ($sSearch != "") {
            $consulta
                ->andWhere('d_u.rut LIKE :rut OR d_u.email LIKE :email')
                ->setParameter('rut', "%${sSearch}%")
                ->setParameter('email', "%${sSearch}%");
        }

        if ($descuento_id != "") {
            $consulta->andWhere('d.descuentoId = :descuento_id')
                ->setParameter('descuento_id', $descuento_id);
        }

        $consulta->orderBy("d_u.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalUsos: Total de usos de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalUsos($sSearch, $descuento_id = "")
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(d_u.usoId) FROM IcanBundle\Entity\DescuentoUso d_u ';
        $join = ' LEFT JOIN d_u.descuento d ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE d_u.rut LIKE :rut OR d_u.email LIKE :email ';
            else
                $where .= ' AND d_u.rut LIKE :rut OR d_u.email LIKE :email ';
        }

        if ($descuento_id != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE d.descuentoId = :descuento_id ';
            else
                $where .= ' AND d.descuentoId = :descuento_id ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros
        //$sSearch
        $esta_query_descuento_id = substr_count($consulta, ':descuento_id');
        if ($esta_query_descuento_id == 1)
            $query->setParameter('descuento_id', $descuento_id);

        $esta_query_rut = substr_count($consulta, ':rut');
        if ($esta_query_rut == 1)
            $query->setParameter('rut', "%${sSearch}%");

        $esta_query_email = substr_count($consulta, ':email');
        if ($esta_query_email == 1)
            $query->setParameter('email', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
