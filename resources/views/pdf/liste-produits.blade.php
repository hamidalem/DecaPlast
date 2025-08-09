<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Produits</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
            text-align: center;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0 0;
        }
        .info-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info-item {
            font-size: 14px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .products-table th, .products-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .products-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: right;
            color: #666;
        }

        .logo-area {
            display: flex;
            align-items: start; /* Align logo and text vertically */
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

    </style>
</head>
<body>
<div class="header">
    <div class="logo-area">
        <img src="/public/logo.png" alt="DecaPlast Logo" class="logo"/>
        <h1 class="boutique-name">DecaPlast</h1>
    </div>
    <h1 class="title">Liste des Produits</h1>
    <div class="subtitle">Généré le {{ $dateFormatted }}</div>
</div>

<div class="info-section">
    <div class="info-item">Total Produits: {{ $totalProduits }}</div>
</div>

<table class="products-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Catégorie</th>

    </tr>
    </thead>
    <tbody>
    @foreach($produits as $index => $produit)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $produit->nom_prod }}</td>
            <td>{{ $produit->desc_prod ?? 'N/A' }}</td>
            <td>{{ $produit->categorie->nom_categ ?? 'Non catégorisé' }}</td>

        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    Généré par DecaPlast - © {{ date('Y') }}
</div>
</body>
</html>
