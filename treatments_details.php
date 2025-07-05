<!DOCTYPE html>
<html>
<head>
    <title>Zakazani tretmani</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="treatments_info.php">Tretmani</a></li>
            <li><a href="electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Detalji tretmana</h2>
<p><strong>Životinja:</strong> Rex (25kg, 60cm)</p>
<p><strong>Vlasnik:</strong> Marko Marković</p>
<p><strong>Datum:</strong> 2025-06-10 u 10h</p>
<p><strong>Napomene o životinji:</strong> Pas ima alergiju na određenu hranu.</p>

<h3>Veterinarova beleška</h3>
<form method="post" action="save_note.php">
    <textarea name="note" rows="5">Pas je pregledan, terapija je propisana.</textarea>
    <input type="hidden" name="id" value="123">
    <button type="submit">Sačuvaj napomenu</button>
</form>

</body>
</html>
