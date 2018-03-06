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

use function Opis\Colibri\Functions\schema;
use Opis\Colibri\Installer as AbstractInstaller;
use Opis\Database\Schema\CreateTable;

class Installer extends AbstractInstaller
{
    /**
     * @throws \Exception
     */
    public function install()
    {
        schema()->create('roles', function(CreateTable $table){
            $table->string('id', 32)->notNull()->primary();
            $table->string('name', 64)->notNull()->unique();
            $table->string('description', 255)->notNull();
            $table->boolean('is_user_created')->defaultValue(true)->notNull();
            $table->binary('permissions')->notNull();
        });
    }

    /**
     * @throws \Exception
     */
    public function uninstall()
    {
        schema()->drop('roles');
    }
}