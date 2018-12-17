<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DescuentoProductoRepository extends EntityRepository
{

    /**
     * ListarProductos: Lista los productos de un descuento
     * @param int $descuento_id
     *
     * @author Marcel
     */
    public function ListarProductos($descuento_id)
    {
        $consulta = $this->createQueryBuilder('d_p')
            ->leftJoin('d_p.descuento', 'd')
            ->where('d.descuentoId = :descuento_id')
            ->setParameter('descuento_id', $descuento_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * ListarDescuentos: Lista los descuentos de un producto
     * @param int $producto_id
     *
     * @author Marcel
     */
    public function ListarDescuentos($producto_id)
    {
        $consulta = $this->createQueryBuilder('d_p')
            ->leftJoin('d_p.producto', 'p')
            ->where('p.productoId = :producto_id')
            ->setParameter('producto_id', $producto_id)
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

}
