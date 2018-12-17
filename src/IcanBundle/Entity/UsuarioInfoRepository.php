<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UsuarioInfoRepository extends EntityRepository {

    /**
     * BuscarInfoDeUsuario: Devuelve la informacion de un usuario de la BD
     * @param int $usuario_id
     *
     * @author Marcel
     */
    public function BuscarInfoDeUsuario($usuario_id) {
        $consulta = $this->createQueryBuilder('u_i')
                ->leftJoin('u_i.usuario', 'u')
                ->where('u.usuarioId = :usuario_id')
                ->setParameter('usuario_id', $usuario_id)
                ->getQuery();

        $entity = $consulta->getOneOrNullResult();
        return $entity;
    }

}