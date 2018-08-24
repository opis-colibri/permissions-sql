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

namespace Opis\Colibri\Modules\PermissionsSQL;

use Opis\ORM\{
    Entity, IEntityMapper, IDataMapper, IMappableEntity
};
use Opis\Colibri\Modules\Permissions\{
    IRole,
    IPermission,
    IPermissionRepository
};
use function Opis\Colibri\Functions\{
    make, uuid4
};

class Role extends Entity implements IRole, IMappableEntity
{
    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    /**
     * @param string $name
     * @return Role
     */
    public function setName(string $name): IRole
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return $this->orm()->getColumn('description');
    }

    /**
     * @param string $description
     * @return Role
     */
    public function setDescription(string $description): IRole
    {
        $this->orm()->setColumn('description', $description);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function permissions(): iterable
    {
        return $this->orm()->getColumn('permissions');
    }

    /**
     * @inheritDoc
     */
    public function setPermissions(iterable $permissions): IRole
    {
        $this->orm()->setColumn('permissions', $permissions);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isUserCreated(): bool
    {
        return $this->orm()->getColumn('is_user_created');
    }

    /**
     * @param bool $value
     * @return Role
     */
    public function setIsUserCreated(bool $value): IRole
    {
        $this->orm()->setColumn('is_user_created', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->cast([
            'permissions' => 'json',
            'is_user_created' => 'boolean',
        ]);

        $mapper->setter('permissions', function (iterable $permissions) {
            $names = [];
            foreach ($permissions as $permission) {
                if ($permission instanceof IPermission) {
                    $names[$permission->name()] = $permission->name();
                } elseif (is_string($permission)) {
                    $names[$permission] = $permission;
                }
            }
            return array_values($names);
        });

        $mapper->getter('permissions', function (array $names) {
            return make(IPermissionRepository::class)->getMultipleByName($names);
        });

        $mapper->primaryKeyGenerator(function (IDataMapper $data) {
            return uuid4('');
        });
    }
}