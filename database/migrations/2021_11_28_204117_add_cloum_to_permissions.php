<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloumToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('descriptions')->nullable()->comment('descriptions for this permission.');
            $table->integer("parent_id")->after("id")->default(0)->comment("permission parent id");
            $table->string("display_name",120)->after("name")->comment("take an alias for name");
            $table->string("route_name")->after("descriptions")->nullable()->comment("route for the menu");
            $table->string("icons_name")->after("route_name")->nullable()->comment("icon for the menu");
            $table->tinyInteger('sort')->after('icons_name')->default(0)->comment("sort number for menus");
            $table->tinyInteger('type')->after("parent_id")->default(1)->comment('Permission type: 2 menus, 1 button');
            $table->tinyInteger("discarded")->after("guard_name")->default(0)->comment("Discard this permission [0-Not,1-Yes]");
            $table->softDeletes()->after('discarded')->comment('soft delete this permission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('descriptions');
            $table->dropColumn('parent_id');
            $table->dropColumn('display_name');
            $table->dropColumn('route_name');
            $table->dropColumn('icons_name');
            $table->dropColumn('sort');
            $table->dropColumn('type');
            $table->dropColumn('discarded');
            $table->dropColumn('deleted_at');
        });
    }
}
