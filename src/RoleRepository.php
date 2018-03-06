<?php
/* ===========================================================================
 * Copyright 2018 The Opis Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace OpisColibri\SqlPermissionEntities;

use function Opis\Colibri\Functions\{
    entity, entityManager
};
use OpisColibri\Permissions\{
    IRole,
    IRoleRepository
};

class RoleRepository implements IRoleRepository
{
    /** @var  null|Role[] */
    private $roles;

    /** @var string[] */
    private $names = [];

    /** @var string[] */
    private $ids = [];

    /**
     * Create role
     *
     * @return IRole|Role
     */
    public function create(): IRole
    {
        /** @var Role $role */
        $role = entityManager()->create(Role::class);
        return $role;
    }

    /**
     * Get all roles
     *
     * @return iterable|IRole[]|Role[]
     */
    public function getAll(): iterable
    {
        if ($this->roles === null) {
            $ids = [];
            $names = [];
            /** @var IRole[] $roles */
            $roles = array_values(entity(Role::class)->all());
            foreach ($roles as $key => $role) {
                $names[$role->name()] = $key;
                $ids[$role->id()] = $key;
            }
            $this->ids = $ids;
            $this->names = $names;
            $this->roles = $roles;
        }
        return $this->roles;
    }

    /**
     * @inheritDoc
     */
    public function getById(string $id): ?IRole
    {
        if ($this->roles === null) {
            $this->getAll();
        }

        if (!isset($this->ids[$id])) {
            return null;
        }

        return $this->roles[$this->ids[$id]];
    }

    /**
     * Get a role by its name
     *
     * @param string $role
     * @return null|IRole
     */
    public function getByName(string $role): ?IRole
    {
        if (null === $this->roles) {
            $this->getAll();
        }

        if (!isset($this->names[$role])) {
            return null;
        }

        return $this->roles[$this->names[$role]];
    }

    /**
     * Get roles by their names
     *
     * @param string[] $roles
     * @return iterable|IRole[]
     */
    public function getMultipleByName(array $roles): iterable
    {
        if (null === $this->roles) {
            $this->getAll();
        }

        $results = [];

        foreach ($roles as $role) {
            if (isset($this->names[$role])) {
                $results[] = $this->roles[$this->names[$role]];
            }
        }

        return $results;
    }

    /**
     * Save role
     *
     * @param IRole|Role $role
     * @return bool
     */
    public function save(IRole $role): bool
    {
        $this->clearCache();
        return entityManager()->save($role);
    }

    /**
     * Delete role
     *
     * @param IRole|Role $role
     * @return bool
     */
    public function delete(IRole $role): bool
    {
        $this->clearCache();
        return entityManager()->delete($role);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $id): bool
    {
        return (bool)$this->deleteMultipleById([$id]);
    }

    /**
     * @inheritDoc
     */
    public function deleteByName(string $name): bool
    {
        return (bool)$this->deleteMultipleByName([$name]);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleById(array $ids): int
    {
        $this->clearCache();

        return entity(Role::class)
            ->where('id')->in($ids)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteMultipleByName(array $names): int
    {
        $this->clearCache();

        return (bool)entity(Role::class)
            ->where('name')->in($names)
            ->delete();
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        $this->roles = $this->names = $this->ids = null;
    }
}