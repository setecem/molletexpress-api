<?php

namespace App\Middleware\Api;

use App\Entity\EmployeeRole;
use App\Enum\RoleGroup;
use App\Model\Auth;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Http\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Exception;

final class Role
{
    public static function check(...$permissions): ?JsonResponse
    {
        try {
            /** @var \App\Entity\Role[] $roles */
            $roles = array_map(function (EmployeeRole $employeeRole) {
                $role = new \App\Model\Role();
                $role->role = $employeeRole->role;
                $role->group = $employeeRole->group;
                return $role;
            }, Auth::getEmployee()->roles);

            foreach ($permissions as $permission) {
                if (!str_contains($permission, '|'))
                    continue;
                list($groupName, $roleName) = explode('|', $permission);

                try {
                    $currentRole = self::validate($roleName, $groupName);

                    if (!Config::get('params.role.check', true))
                        continue;

                    if (!array_find($roles, fn(\App\Model\Role $r) => $r->role === $currentRole->role && $r->group === $currentRole->group))
                        return new JsonResponse(['message' => 'role.error.forbidden', 'role' => $permissions], 403);
                } catch (Exception $e) {
                    return new JsonResponse(['message' => 'role.error.add', 'exception' => $e->getMessage()], 500);
                }
            }
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'error.fatal', 'exception' => $e->getMessage()], 500);
        }

        return null;
    }

    /**
     * @param string $roleName
     * @param string $groupName
     * @return \App\Entity\Role
     */
    private static function validate(string $roleName, string $groupName): \App\Entity\Role
    {

        try {
            $role = \App\Enum\Role::tryFrom($roleName);
            $group = RoleGroup::tryFrom($groupName);

            $item = \App\Entity\Role::findOneBy(['role' => $role, 'group' => $group]);
            if (!$item) {
                $em = Db::getManager();
                $item = new \App\Entity\Role();
                $item->role = $role;
                $item->group = $group;
                $em->persist($item);
                $em->flush();
            }
            return $item;
        } catch (Exception|ORMException) {
            return new \App\Entity\Role();
        }

    }

    public static function try(...$permissions): bool
    {
        try {
            /** @var \App\Entity\Role[]|ArrayCollection $roles */
            $roles = Auth::getEmployee()->roles ?? [];
            foreach ($permissions as $permission) {
                if (!str_contains($permission, '|'))
                    continue;
                list($groupName, $roleName) = explode('|', $permission);

                $currentRole = self::validate($roleName, $groupName);

                if (!Config::get('params.role.check', true))
                    continue;

                if (!in_array($currentRole, array_map(fn(\App\Model\Role $r) => $r->entity(), $roles), true))
                    return false;

            }
            return true;
        } catch (Exception|ORMException) {
            return false;
        }
    }
}


