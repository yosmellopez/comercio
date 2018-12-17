<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UsuarioRepository extends EntityRepository
{

    /**
     * AutenticarLogin: Chequear el login
     * @param string $email Email
     * @param string $pass Pass
     * @author Marcel
     */
    public function AutenticarLogin($email, $pass)
    {
        $email = strtolower($email);
        $consulta = $this->createQueryBuilder('u')
            ->where('u.email = :email AND u.password = :pass')
            ->setParameter('email', $email)
            ->setParameter('pass', $pass)
            ->getQuery();

        $usuario = $consulta->getOneOrNullResult();
        return $usuario;
    }

    /**
     * BuscarUsuarioPorEmail: Devuelve el usuario al que le corresponde el email
     * @param string $email Email
     *
     * @author Marcel
     */
    public function BuscarUsuarioPorEmail($email)
    {
        $criteria = array('email' => $email);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarUsuarios: Lista los usuarios
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarUsuarios($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('u')
            ->leftJoin('u.rol', 'r');


        if ($sSearch != "") {
            $consulta->andWhere('u.email LIKE :email OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos OR r.nombre LIKE :rol')
                ->setParameter('email', "%${sSearch}%")
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('apellidos', "%${sSearch}%")
                ->setParameter('rol', "%${sSearch}%");
        }

        if ($iSortCol_0 == "perfil") {
            $consulta->orderBy("r.nombre", $sSortDir_0);
        } else {
            $consulta->orderBy("u.$iSortCol_0", $sSortDir_0);
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;

    }

    /**
     * TotalUsuarios: Total de usuarios de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalUsuarios($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(u.usuarioId) FROM IcanBundle\Entity\Usuario u ';
        $join = 'JOIN u.rol r ';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE u.email LIKE :email OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos OR r.nombre LIKE :rol';
            else
                $where .= 'AND u.email LIKE :email OR u.nombre LIKE :nombre OR u.apellidos LIKE :apellidos OR r.nombre LIKE :rol';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch        
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1)
            $query->setParameter('nombre', "%${sSearch}%");

        $esta_query_email = substr_count($consulta, ':email');
        if ($esta_query_email == 1)
            $query->setParameter('email', "%${sSearch}%");

        $esta_query_apellidos = substr_count($consulta, ':apellidos');
        if ($esta_query_apellidos == 1)
            $query->setParameter('apellidos', "%${sSearch}%");

        $esta_query_rol = substr_count($consulta, ':rol');
        if ($esta_query_rol == 1)
            $query->setParameter('rol', "%${sSearch}%");

        $total = $query->getSingleScalarResult();
        return $total;
    }


    /**
     * ListarUsuariosNoRol: Carga todos los usuarios que no son del rol de la BD
     * @param int $rol_id Id del rol
     *
     * @author Marcel
     */
    public function ListarUsuariosNoRol($rol_id)
    {
        $consulta = $this->createQueryBuilder('u')
            ->leftJoin('u.rol', 'r')
            ->where('r.rolId <> :rol_id')
            ->setParameter('rol_id', $rol_id)
            ->getQuery();

        $usuarios = $consulta->getResult();
        return $usuarios;
    }

    /**
     * ListarUsuariosRol: Carga todos los usuarios del rol de la BD
     * @param int $rol_id Id del rol
     *
     * @author Marcel
     */
    public function ListarUsuariosRol($rol_id)
    {
        $consulta = $this->createQueryBuilder('u')
            ->leftJoin('u.rol', 'r')
            ->where('r.rolId = :rol_id')
            ->setParameter('rol_id', $rol_id)
            ->getQuery();

        $usuarios = $consulta->getResult();
        return $usuarios;
    }

}