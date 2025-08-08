<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon de Vente #{{ $bonVente->n_bv }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            margin: auto;
            padding: 10px;
        }

        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
            display: flex;
            justify-content: space-between; /* Push title to left and logo area to right */
            align-items: center; /* Vertically align items */
        }

        .logo-area {
            display: flex;
            align-items: center; /* Align logo and text vertically */
        }

        .logo {
            width: 70px; /* Smaller logo size */
            height: auto;
        }

        .boutique-name {
            font-size: 24px; /* Slightly smaller boutique name */
            font-weight: bold;
            margin: 0;
            color: tomato;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: 0;
            color: #222;
        }

        .subtitle {
            font-size: 16px;
            text-align: center;
            color: #666;
            margin: 5px 0 0;
        }

        .info-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 50px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            margin-bottom: 50px;
        }

        .products-table th, .products-table td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .products-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .summary-section {
            width: 300px;
            float: right;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .summary-label {
            font-weight: bold;
        }

        .summary-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            margin-top: 8px;
        }

        .clear-fix::after {
            content: "";
            display: table;
            clear: both;
            margin-top: 20px;
        }

        .signatures {
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 14px;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="logo-area">
            <img src="{{ asset('logo.png') }}" alt="DecaPlast Logo" class="logo"/>
            <h1 class="boutique-name">DecaPlast</h1>
        </div>
        <div>
            <div class="title">Bon de Vente</div>
            <div class="subtitle">Référence: #{{ $bonVente->n_bv }}</div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Client :</span>
            <span class="info-value">{{ $bonVente->client->nom_client }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date :</span>
            <span class="info-value">{{ $dateFormatted }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">État du Paiement :</span>
            <span >
                <span {{ $bonVente->etat_bv }}">
                    {{ $bonVente->etat_bv }}
                </span>
            </span>
        </div>
    </div>

    <h3 class="section-title">Détails des Produits</h3>
    <table class="products-table">
        <thead>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bonVente->produits as $product)
            <tr>
                <td>{{ $product->nom_prod }}</td>
                <td>{{ $product->pivot->qte_vente }}</td>
                <td>{{ number_format($product->pivot->prix_vente, 2) }} DA</td>
                <td>{{ number_format($product->pivot->qte_vente * $product->pivot->prix_vente, 2) }} DA</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="clear-fix">
        <div class="summary-section">
            <div class="summary-row">
                <span class="summary-label">Montant Total:</span>
                <span>{{ number_format($totalAmount, 2) }} DA</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Montant Versé:</span>
                <span>{{ number_format($montantVerse, 2) }} DA</span>
            </div>
            <div class="summary-total summary-row">
                <span class="summary-label">Reste à Payer:</span>
                <span>{{ number_format($resteAPayer, 2) }} DA</span>
            </div>
        </div>
    </div>


</div>

</body>
</html>
