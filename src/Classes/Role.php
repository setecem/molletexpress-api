<?php

namespace App\Classes;

use Cavesman\Db;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class Role
{

    /**
     * Get a locale list
     *
     * @return array
     */
    public static function list(): array
    {
        try {
            $roles = Db::getManager()->getRepository(Role::class)->findAll();
            return array_map(fn(\App\Entity\Role $role) => new \App\Model\Role(['group' => $role->group, 'role' => $role->role]), $roles);
        } catch (Exception) {
            return [];
        }

    }

    /**
     * Create a locale file if not exists
     *
     * @param string $item
     * @return bool
     */
    public static function check(string $item): bool
    {

        try {
            $em = Db::getManager();
            $role = $em->getRepository(\App\Entity\Role::class)->findOneBy(['name' => $item, 'deletedOn' => null]);
            if (!$role) {
                $role = new \App\Entity\Role();
                $role->role = \App\Enum\Role::tryFrom($item);
                $em->persist($role);
                $em->flush();
            }
            return true;
        } catch (Exception|ORMException) {
            return false;
        }
    }
}
