<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoriaRepository extends EntityRepository
{

    /**
     * ListarOrdenadas: Lista las categorias ordenadas
     *
     * @author Marcel
     */
    public function ListarOrdenadas()
    {
        $consulta = $this->createQueryBuilder('c')
            ->where('c.estado = 1')
            ->orderBy('c.nombre', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarPadres: Lista los categoria padres ordenados
     *
     * @author Marcel
     */
    public function ListarPadres($estado = "")
    {
        $consulta = $this->createQueryBuilder('c')
            ->where('c.categoriaPadre is null');

        if ($estado != "") {
            $consulta->andWhere('c.estado = 1');
        }

        $consulta->orderBy('c.descripcion', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarHijos: Lista los categoria hijos ordenados
     *
     * @author Marcel
     */
    public function ListarHijos($estado = "")
    {
        $consulta = $this->createQueryBuilder('c')
            ->where('c.categoriaPadre is not null');

        if ($estado != "") {
            $consulta->andWhere('c.estado = 1');
        }

        $consulta->orderBy('c.descripcion', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarPadresActivos: Lista las categorias padres ordenadas y activas
     *
     * @author Marcel
     */
    public function ListarPadresActivos()
    {
        $consulta = $this->createQueryBuilder('c')
            ->where('c.categoriaPadre is null AND c.estado = 1')
            ->orderBy('c.nombre', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarHijosActivos: Lista las categorias hijos ordenadas y activas
     *
     * @author Marcel
     */
    public function ListarHijosActivos()
    {
        $consulta = $this->createQueryBuilder('c')
            ->where('c.categoriaPadre is not null AND c.estado = 1')
            ->orderBy('c.nombre', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarCategoriasDelPadreOrdenadas: Carga las categorias del padre de la BD
     *
     * @param int $categoria_padre_id Padre
     *
     * @author Marcel
     */
    public function ListarCategoriasDelPadreOrdenadas($categoria_padre_id)
    {

        $consulta = $this->createQueryBuilder('c')
            ->leftJoin('c.categoriaPadre', 'c_p')
            ->where('c_p.categoriaId = :categoria_padre_id AND c_p.estado = 1')
            ->setParameter('categoria_padre_id', $categoria_padre_id)
            ->orderBy('c_p.nombre', 'ASC')
            ->getQuery();

        $lista = $consulta->getResult();
        return $lista;
    }

    /**
     * BuscarPorUrl: Devuelve la categoria de la url
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
     * BuscarPorImagen: Devuelve la categoria de la imagen
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
     * BuscarPorNombre: Devuelve la categoria dado el nombre
     * @param string $nombre nombre
     *
     * @author Marcel
     */
    public function BuscarPorNombre($nombre)
    {
        $criteria = array('nombre' => $nombre);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarCategoriasDelPadre: Carga los categoria del padre de la BD
     *
     * @param int $categoria_padre_id Padre
     *
     * @author Marcel
     */
    public function ListarCategoriasDelPadre($categoria_padre_id, $estado = "")
    {

        $consulta = $this->createQueryBuilder('c')
            ->leftJoin('c.categoriaPadre', 'c_p')
            ->where('c_p.categoriaId = :categoria_padre_id')
            ->setParameter('categoria_padre_id', $categoria_padre_id);

        if ($estado != "") {
            $consulta->andWhere('c.estado = 1');
        }

        $consulta->orderBy('c.descripcion', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarCategorias: Lista los categoria
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarCategorias($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('c');

        if ($sSearch != "")
            $consulta->andWhere('c.nombre LIKE :nombre OR c.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");


        $consulta->orderBy("c.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalCategorias: Total de categoria de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalCategorias($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(c.categoriaId) FROM IcanBundle\Entity\Categoria c ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE c.nombre LIKE :nombre OR c.descripcion LIKE :descripcion ';
            else
                $where .= 'AND c.nombre LIKE :nombre OR c.descripcion LIKE :descripcion  ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1)
            $query->setParameter('descripcion', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
