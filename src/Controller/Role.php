<?php

namespace App\Controller;

use App\Enum\RoleGroup;
use Cavesman\Console;
use Cavesman\Db;
use Cavesman\Http\JsonResponse;
use Exception;

class Role
{
    /**
     * List of all available locales
     *
     * @return JsonResponse
     */
    public static function list(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Entity\Role::findBy(['active' => true]));
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * Sync roles from RoleGroup enum to the role table
     */
    public static function syncRoles(): void
    {
        try {
            $em = Db::getManager();
            $created = 0;
            $existing = 0;

            foreach (RoleGroup::all() as $groupName => $roles) {
                $group = RoleGroup::from($groupName);
                foreach ($roles as $role) {
                    $entity = $em->getRepository(\App\Entity\Role::class)->findOneBy([
                        'role' => $role,
                        'group' => $group,
                    ]);

                    if (!$entity) {
                        $entity = new \App\Entity\Role();
                        $entity->role = $role;
                        $entity->group = $group;
                        $entity->active = true;
                        $em->persist($entity);
                        $created++;
                    } else {
                        $existing++;
                    }
                }
            }

            $em->flush();
            Console::output("Roles sincronizados: $created creados, $existing ya existían.");
        } catch (Exception $e) {
            Console::output('Error: ' . $e->getMessage());
        }
    }
}
