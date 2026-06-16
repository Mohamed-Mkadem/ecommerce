<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>

</head>

<body class="antialiased text-lg text-primary px-4 ">

    <div class="">
        <div class="flex justify-between items-center gap-4 mb-4">
            <img src="{{ asset('logo-gold.png') }}" class="w-32 h-auto" alt="Logo">

            <div class="text-primary text-base">
                <p><span class="font-semibold">Adresse :</span> Jardins El Menzah</p>
                <p><span class="font-semibold"> Tel 1: </span> 29144054</p>
                <p><span class="font-semibold"> Tel 2: </span> 29144980</p>
                <p><span class="font-semibold"> Site Web :</span> https://boughanmipatisserie.com</p>
                <p><span class="font-semibold">Livreur :</span> {{ $order->shipper->name }}</p>
            </div>
        </div>
        <div class="mb-4 text-base">
            <h2 class="font-bold text-2xl text-gold">Client : </h2>
            <p><span class="font-semibold">Nom :</span> {{ $order->client_name }}</p>
            <p>
                <span class="font-semibold">Téléphone :</span>
                {{ $order->phone }}
                @if (!is_null($order->phone2))
                    / {{ $order->phone2 }}
                @endif
            </p>
            <p><span class="font-semibold">Adresse :</span>
                {{ $order->address . ' ' . $order->locality->translate('fr')->name . ' ' . $order->city->translate('fr')->name . ' ' . $order->state->translate('fr')->name }}
            </p>
            <p><span class="font-semibold">Numéro de la commande :</span> {{ $order->id }}</p>


        </div>
        <div class="mb-4 text-base">
            <h2 class="font-bold text-2xl text-gold">Désignation : </h2>
            <p>
                {{ $order->products->map(function ($product) {
                        return $product->pivot->quantity . ' ' . $product->shipping_name;
                    })->implode(' + ') }}
            </p>


        </div>
        <div class="text-base flex justify-end">
            <div>
                <p>Montant : </p>
                <p class="text-2xl font-bold text-primary">{{ number_format($order->amount / 1000, 3, ',', ' ') }}
                    DT</p>
            </div>


        </div>
        <h1 class="text-gold font-semibold text-center text-4xl ">مع حلويات البوغانمي الخير ما يوفاش</h1>
    </div>



</body>

</html>
