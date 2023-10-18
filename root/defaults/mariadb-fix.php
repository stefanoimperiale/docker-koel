<?php

use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('playlist_song', static function (Blueprint $tableP): void {
            if (DB::getDriverName() == 'mysql') {
              DB::statement( 'alter table playlist_song drop constraint  `playlist_song_song_id_foreign`;' );
            } else if (DB::getDriverName() !== 'sqlite') {
               $tableP->dropForeign('playlist_song_song_id_foreign');
            }
            $tableP->string('song_id', 36)->change();

            Schema::table('interactions', static function (Blueprint $tableI): void {
                if (DB::getDriverName() == 'mysql') {
                    DB::statement( 'alter table interactions drop constraint  `interactions_song_id_foreign`;' );
                } else if (DB::getDriverName() !== 'sqlite') {
                   $tableI->dropForeign('interactions_song_id_foreign');
                }
                $tableI->string('song_id', 36)->change();

                Schema::table('songs', static function (Blueprint $table): void {
                    $table->string('id', 36)->change();
                });

                $tableI->foreign('song_id')->references('id')->on('songs')->cascadeOnDelete()->cascadeOnUpdate();
              });
            $tableP->foreign('song_id')->references('id')->on('songs')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Song::all()->each(static function (Song $song): void {
            $song->id = Str::uuid();
            $song->save();
        });
    }
};
