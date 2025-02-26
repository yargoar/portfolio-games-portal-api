<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('{channelName}', function ($user, $channelName) {
    // Lógica para verificar se o usuário tem permissão para acessar o canal
    // Aqui você pode dividir o nome do canal e decidir de forma dinâmica
    // se o usuário tem permissão para esse tipo de canal.


    return true;
});


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
