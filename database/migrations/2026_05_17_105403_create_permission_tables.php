<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = config('permission.teams', false);

        /** @var array<string, string> $tableNames */
        $tableNames = config('permission.table_names', []);

        /** @var array<string, string> $columnNames */
        $columnNames = config('permission.column_names', []);

        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';

        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        throw_if(
            empty($tableNames),
            Exception::class,
            'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
        );

        throw_if(
            $teams && empty($columnNames['team_foreign_key'] ?? null),
            Exception::class,
            'Error: team_foreign_key on config/permission.php not loaded.'
        );

        /*
        |--------------------------------------------------------------------------
        | Permissions Table
        |--------------------------------------------------------------------------
        */

        Schema::create($tableNames['permissions'], static function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('name');

            $table->string('guard_name');

            $table->timestamps();

            $table->unique(
                ['name', 'guard_name'],
                'permissions_name_guard_name_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Roles Table
        |--------------------------------------------------------------------------
        */

        Schema::create($tableNames['roles'], static function (
            Blueprint $table
        ) use (
            $teams,
            $columnNames
        ) {

            $table->bigIncrements('id');

            if ($teams || config('permission.testing')) {

                $table->unsignedBigInteger(
                    $columnNames['team_foreign_key']
                )->nullable();

                $table->index(
                    $columnNames['team_foreign_key'],
                    'roles_team_foreign_key_index'
                );
            }

            $table->string('name');

            $table->string('guard_name');

            $table->timestamps();

            if ($teams || config('permission.testing')) {

                $table->unique([
                    $columnNames['team_foreign_key'],
                    'name',
                    'guard_name',
                ], 'roles_team_name_guard_name_unique');
            } else {

                $table->unique(
                    ['name', 'guard_name'],
                    'roles_name_guard_name_unique'
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Model Has Permissions
        |--------------------------------------------------------------------------
        */

        Schema::create($tableNames['model_has_permissions'], static function (
            Blueprint $table
        ) use (
            $tableNames,
            $columnNames,
            $pivotPermission,
            $teams
        ) {

            /*
            |--------------------------------------------------------------------------
            | Permission Relation
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger($pivotPermission);

            /*
            |--------------------------------------------------------------------------
            | Morph Relation
            |--------------------------------------------------------------------------
            */

            $table->string('model_type');

            // SUPPORT ULID
            $table->ulid($columnNames['model_morph_key']);

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                [
                    $columnNames['model_morph_key'],
                    'model_type',
                ],
                'model_has_permissions_model_id_model_type_index'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Teams Support
            |--------------------------------------------------------------------------
            */

            if ($teams) {

                $table->unsignedBigInteger(
                    $columnNames['team_foreign_key']
                );

                $table->index(
                    $columnNames['team_foreign_key'],
                    'model_has_permissions_team_foreign_key_index'
                );

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotPermission,
                    $columnNames['model_morph_key'],
                    'model_type',
                ], 'model_has_permissions_primary');
            } else {

                $table->primary([
                    $pivotPermission,
                    $columnNames['model_morph_key'],
                    'model_type',
                ], 'model_has_permissions_primary');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Model Has Roles
        |--------------------------------------------------------------------------
        */

        Schema::create($tableNames['model_has_roles'], static function (
            Blueprint $table
        ) use (
            $tableNames,
            $columnNames,
            $pivotRole,
            $teams
        ) {

            /*
            |--------------------------------------------------------------------------
            | Role Relation
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger($pivotRole);

            /*
            |--------------------------------------------------------------------------
            | Morph Relation
            |--------------------------------------------------------------------------
            */

            $table->string('model_type');

            // SUPPORT ULID
            $table->ulid($columnNames['model_morph_key']);

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index(
                [
                    $columnNames['model_morph_key'],
                    'model_type',
                ],
                'model_has_roles_model_id_model_type_index'
            );

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Teams Support
            |--------------------------------------------------------------------------
            */

            if ($teams) {

                $table->unsignedBigInteger(
                    $columnNames['team_foreign_key']
                );

                $table->index(
                    $columnNames['team_foreign_key'],
                    'model_has_roles_team_foreign_key_index'
                );

                $table->primary([
                    $columnNames['team_foreign_key'],
                    $pivotRole,
                    $columnNames['model_morph_key'],
                    'model_type',
                ], 'model_has_roles_primary');
            } else {

                $table->primary([
                    $pivotRole,
                    $columnNames['model_morph_key'],
                    'model_type',
                ], 'model_has_roles_primary');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Role Has Permissions
        |--------------------------------------------------------------------------
        */

        Schema::create($tableNames['role_has_permissions'], static function (
            Blueprint $table
        ) use (
            $tableNames,
            $pivotRole,
            $pivotPermission
        ) {

            $table->unsignedBigInteger($pivotPermission);

            $table->unsignedBigInteger($pivotRole);

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Composite Primary Key
            |--------------------------------------------------------------------------
            */

            $table->primary(
                [$pivotPermission, $pivotRole],
                'role_has_permissions_primary'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app('cache')
            ->store(
                config('permission.cache.store') !== 'default'
                    ? config('permission.cache.store')
                    : null
            )
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /** @var array<string, string> $tableNames */
        $tableNames = config('permission.table_names', []);

        if (empty($tableNames)) {
            return;
        }

        Schema::dropIfExists($tableNames['role_has_permissions']);

        Schema::dropIfExists($tableNames['model_has_roles']);

        Schema::dropIfExists($tableNames['model_has_permissions']);

        Schema::dropIfExists($tableNames['roles']);

        Schema::dropIfExists($tableNames['permissions']);
    }
};
