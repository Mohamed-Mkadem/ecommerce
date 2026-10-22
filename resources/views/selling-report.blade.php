<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .title-holder {
            margin-bottom: 20px;
        }

        .title {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 16px;
        }

        .wrapper-title {
            font-size: 20px;
            font-weight: bold;
        }

        .item {
            font-size: 14px;
            margin: 3px 0;
        }


        .wrapper {
            border-bottom: #bebebe32 1px solid;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="title-holder">
        <div class="title">Rapport De Ventes</div>
        <div class="subtitle">Période : {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
        </div>
    </div>

    @foreach ($rows->groupBy('wrapper_name') as $wrapperName => $products)
        <div class="wrapper">

            <div class="wrapper-title">{{ $wrapperName }}</div>
            @foreach ($products as $product)
                <div class="item">
                    Produit : {{ $product->product_name }} | Qté Totale : {{ $product->total_quantity }} | N° de
                    commandes :
                    {{ $product->number_of_orders }}
                </div>
            @endforeach
        </div>
    @endforeach
</body>

</html>
