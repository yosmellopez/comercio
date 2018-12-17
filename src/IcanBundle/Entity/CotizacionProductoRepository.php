<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CotizacionProductoRepository extends EntityRepository
{

    /**
     * ListarProductos: Lista los productos de una cotizacion
     * @param int $cotizacion_id
     *
     * @author Marcel
     */
    public function ListarProductos($cotizacion_id)
    {
        $consulta = $this->createQueryBuilder('c_p')
            ->leftJoin('c_p.cotizacion', 'c')
            ->where('c.cotizacionId = :cotizacion_id')
            ->setParameter('cotizacion_id', $cotizacion_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarCotizaciones: Lista las cotizaciones de un producto
     * @param int $producto_id
     *
     * @author Marcel
     */
    public function ListarCotizaciones($producto_id)
    {
        $consulta = $this->createQueryBuilder('c_p')
            ->leftJoin('c_p.producto', 'p')
            // ->leftJoin('c_p.productocaracteristica', 'p_c')
            // ->leftJoin('p_c.producto', 'p1')
            // ->where('p.productoId = :producto_id OR p1.productoId = :producto2_id')
            ->where('p.productoId = :producto_id')
            ->setParameter('producto_id', $producto_id)
            // ->setParameter('producto2_id', $producto_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }


    /**
     * CantidadTotal: Cantidad total del productos cotizados
     * @param string $cotizacion_id
     *
     * @author Marcel
     */
    public function CantidadTotal($cotizacion_id)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT SUM(c_p.cantidad) FROM IcanBundle\Entity\CotizacionProducto c_p ';
        $join = ' LEFT JOIN c_p.cotizacion c ';
        $where = ' WHERE c.cotizacionId LIKE :cotizacion_id ';

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_cotizacion_id = substr_count($consulta, ':cotizacion_id');
        if ($esta_query_cotizacion_id == 1) {
            $query->setParameter('cotizacion_id', $cotizacion_id);
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
