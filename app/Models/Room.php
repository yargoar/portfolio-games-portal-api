<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    private int $id;
    private string $name;
    private string $isPrivate;
    private string $status;
    private array $players;
    private array $spectators;
    private string $game;

}
