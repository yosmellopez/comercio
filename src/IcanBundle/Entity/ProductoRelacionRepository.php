<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductoRelacionRepository extends EntityRepository
{

    /**
     * ListarRelacionadosPortada: Lista los productos relacionados para el frontend
     * @param int $producto_id
     *
     * @author Marcel
     */
    public function ListarRelacionadosPortada($producto_id, $fecha_actual)
    {
        $consulta = $this->createQueryBuilder('p_r')
            ->leftJoin('p_r.producto', 'p')
            ->leftJoin('p_r.productoRelacion', 'pr')
            ->andWhere('p.productoId = :producto_id OR pr.productoId = :producto1_id')
            ->andWhere('p.estado = 1 OR pr.estado = 1')
            ->setParameter('producto_id', $producto_id)
            ->setParameter('producto1_id', $producto_id);

        if ($fecha_actual != "") {
            $consulta->andWhere("p.fechapublicacion <= :fecha OR pr.fechapublicacion <= :fecha1")
                ->setParameter('fecha', $fecha_actual)
                ->setParameter('fecha1', $fecha_actual);
        }

        $lista = $consulta->orderBy('p.fechapublicacion', 'DESC')
            ->addOrderBy('pr.fechapublicacion', 'DESC')
            ->getQuery()
            ->getResult();
        return $lista;
    }

    /**
     * ListarRelacionados: Lista los productos relacionados
     * @param int $producto_id
     *
     * @author Marcel
     */
    public function ListarRelacionados($producto_id)
    {
        $consulta = $this->createQueryBuilder('vr')
            ->leftJoin('vr.producto', 'v')
            ->leftJoin('vr.productoRelacion', 'v1')
            ->where('v.productoId = :producto_id OR v1.productoId = :producto1_id')
            ->setParameter('producto_id', $producto_id)
            ->setParameter('producto1_id', $producto_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarProductosRelacionado: Lista las relaciones con el producto
     * @param int $producto_id
     *
     * @author Marcel
     */
    public function ListarProductosRelacionado($producto_id)
    {
        $consulta = $this->createQueryBuilder('vr')
            ->leftJoin('vr.productoRelacion', 'v')
            ->where('v.productoId = :producto_id')
            ->setParameter('producto_id', $producto_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * BuscarRelacion: Busca una relacion
     *
     *
     * @author Marcel
     */
    public function BuscarRelacion($producto_id, $producto_relacion_id)
    {
        $consulta = $this->createQueryBuilder('p_r')
            ->leftJoin('p_r.producto', 'p')
            ->leftJoin('p_r.productoRelacion', 'p_r1')
            ->where('p.productoId = :producto_id AND p_r1.productoId = :producto_relacion_id')
            ->setParameter('producto_id', $producto_id)
            ->setParameter('producto_relacion_id', $producto_relacion_id)
            ->getQuery();

        $entity = $consulta->getOneOrNullResult();
        return $entity;
    }

}
