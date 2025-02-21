<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
    </head>
    <body class="">
        @vite('resources/js/app.js')
    </body>
    <script>
        setTimeout(() => {
            window.Echo.channel('rooms')
            .listen('RoomUpdated', (e) => {
                console.log("Event dispatched in server");
                console.log(e);
            }) 
        }, 200)

    </script>
</html>
