<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DescuentoRepository extends EntityRepository
{

    /**
     * ListarDescuentosDePorciento: Lista los promociones de un porciento
     * @param int $porciento_id
     *
     * @author Marcel
     */
    public function ListarDescuentosDePorciento($porciento_id)
    {
        $consulta = $this->createQueryBuilder('d')
            ->leftJoin('d.porciento', 'pr')
            ->where('pr.porcientoId = :porciento_id')
            ->setParameter('porciento_id', $porciento_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarDescuentos: Lista los producto
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarDescuentos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('d');
        $consulta->leftJoin('d.porciento', 'po');

        if ($sSearch != "") {
            $consulta
                ->andWhere('d.nombre LIKE :nombre OR d.codigo LIKE :codigo')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('codigo', "%${sSearch}%");
        }

        if ($iSortCol_0 == "valor") {
            $consulta->orderBy("po.valor", $sSortDir_0);
        } else {
            $consulta->orderBy("d.$iSortCol_0", $sSortDir_0);
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
    public function TotalDescuentos($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(d.descuentoId) FROM IcanBundle\Entity\Descuento d ';
        $join = ' LEFT JOIN d.porciento po ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= ' WHERE d.nombre LIKE :nombre OR d.codigo LIKE :codigo ';
            else
                $where .= ' AND d.nombre LIKE :nombre OR d.codigo LIKE :codigo ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch    
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_codigo = substr_count($consulta, ':codigo');
        if ($esta_query_codigo == 1)
            $query->setParameter('codigo', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
