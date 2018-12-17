<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SliderRepository extends EntityRepository
{

    /**
     * BuscarPorImagen: Devuelve el slider de la imagen
     * @param string $imagen imagen
     *
     * @author Marcel
     */
    public function BuscarPorImagen($imagen)
    {
        $criteria = array('imagen' => $imagen);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarSliderPortada: Lista los sliders para la portada
     *
     * @author Marcel
     */
    public function ListarSliderPortada($fecha_actual)
    {
        $consulta = $this->createQueryBuilder('s')
            ->where('s.estado = 1');

        if ($fecha_actual != "") {
            $consulta->andWhere("s.fechapublicacion <= :fecha")
                ->setParameter('fecha', $fecha_actual);
        }

        $lista = $consulta->orderBy('s.posicion', 'DESC')->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarSliderOrdenados: Lista los sliders ordenados
     *
     * @author Marcel
     */
    public function ListarSliderOrdenados($sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('s')
            ->orderBy('s.posicion', $sSortDir_0);

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarSliderMayorPosision: Lista los sliders mayor que una posicion
     *
     * @author Marcel
     */
    public function ListarSliderMayorPosision($pos)
    {
        $consulta = $this->createQueryBuilder('s')
            ->where('s.posicion > :pos')
            ->setParameter('pos', $pos);

        $lista = $consulta->orderBy('s.posicion', 'ASC')
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarSliders: Lista los slider
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSliders($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('s');

        if ($sSearch != "")
            $consulta->andWhere('s.nombre LIKE :nombre OR s.titulo LIKE :titulo OR s.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('titulo', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");

        $consulta->orderBy("s.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalSliders: Total de slider de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalSliders($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(s.sliderId) FROM IcanBundle\Entity\Slider s ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE s.nombre LIKE :nombre OR s.descripcion LIKE :descripcion OR s.titulo LIKE :titulo ';
            else
                $where .= 'AND s.nombre LIKE :nombre OR s.descripcion LIKE :descripcion OR s.titulo LIKE :titulo  ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");
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
