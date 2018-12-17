<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PromocionRepository extends EntityRepository
{


    /**
     * ListarPromocionesDePorciento: Lista los promociones de un porciento
     * @param int $porciento_id
     *
     * @author Marcel
     */
    public function ListarPromocionesDePorciento($porciento_id)
    {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.porciento', 'pr')
            ->where('pr.porcientoId = :porciento_id')
            ->setParameter('porciento_id', $porciento_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarPromociones: Lista los producto
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPromociones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('p');
        $consulta->leftJoin('p.porciento', 'po');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR po.valor LIKE :valor OR p.codigo LIKE :codigo OR p.fechainicio LIKE :fechainicio OR p.fechafin LIKE :fechafin')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('valor', "%${sSearch}%")
                ->setParameter('codigo', "%${sSearch}%")
                ->setParameter('fechainicio', "%${sSearch}%")
                ->setParameter('fechafin', "%${sSearch}%");
        }

        if ($iSortCol_0 == "valor") {
            $consulta->orderBy("po.valor", $sSortDir_0);
        } else {
            $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalProductos: Total de producto de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalPromociones($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.promocionId) FROM IcanBundle\Entity\Promocion p ';
        $join = ' LEFT JOIN p.porciento po ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE p.nombre LIKE :nombre OR po.valor LIKE :valor OR p.codigo LIKE :codigo OR p.fechainicio LIKE :fechainicio OR p.fechafin LIKE :fechafin ';
            else
                $where .= ' AND p.nombre LIKE :nombre OR po.valor LIKE :valor OR p.codigo LIKE :codigo OR p.fechainicio LIKE :fechainicio OR p.fechafin LIKE :fechafin ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch    
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_valor = substr_count($consulta, ':valor');
        if ($esta_query_valor == 1)
            $query->setParameter('valor', "%${sSearch}%");

        $esta_query_codigo = substr_count($consulta, ':codigo');
        if ($esta_query_codigo == 1)
            $query->setParameter('codigo', "%${sSearch}%");

        $esta_query_fechainicio = substr_count($consulta, ':fechainicio');
        if ($esta_query_fechainicio == 1)
            $query->setParameter('fechainicio', "%${sSearch}%");

        $esta_query_fechafin = substr_count($consulta, ':fechafin');
        if ($esta_query_fechafin == 1)
            $query->setParameter('fechafin', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

    /**
     * ListarPromocionPortada: Lista los sliders para la portada
     *
     * @author Marcel
     */
    public function ListarPromocionPortada($fecha_actual)
    {
        $consulta = $this->createQueryBuilder('p')
            ->leftJoin('p.porciento', 'po')
            ->where('p.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechainicio <= :fecha1")
                ->andWhere("p.fechafin >= :fecha2")
                ->setParameter('fecha1', $fecha_actual)
                ->setParameter('fecha2', $fecha_actual);
        }

        $lista = $consulta->orderBy('po.valor', 'DESC')->getQuery()->getResult();
        return $lista;
    }

}
