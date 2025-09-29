<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return config('tokenable.database.connection');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('tokenable.database.table', 'auth_tokens'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index('idx_name')->comment('Token Name');
            $table->string('platform')->default('default')->index('idx_platform')->comment('Platform Type');
            $table->morphs('tokenable', 'idx_tokenable');
            $table->string('token_driver', 128)->nullable()->index('idx_token_driver')->comment('Token Driver');
            $table->string('access_token', 128)->nullable()->index('idx_access_token')->comment('Access Token');
            $table->string('refresh_token', 128)->nullable()->index('idx_refresh_token')->comment('Refresh Token');
            $table->longText('scopes')->comment('Token Scopes');
            $table->datetime('access_token_expire_at')->index('idx_access_token_expire_at')->comment('Token Expiration Time');
            $table->datetime('refresh_token_available_at')->index('idx_refresh_token_available_at')->comment('Refresh Token Available Time');
            $table->datetime('refresh_token_expire_at')->index('idx_refresh_token_expire_at')->comment('Refresh Token Expiration Time');
            $table->datetime('created_at')->nullable()->comment('Created Time');
            $table->datetime('updated_at')->nullable()->comment('Updated Time');
            $table->datetime('deleted_at')->index('idx_deleted_at')->nullable()->comment('Deleted Time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('tokenable.database.table', 'auth_tokens'));
    }
};
