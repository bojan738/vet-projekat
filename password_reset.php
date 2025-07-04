<!DOCTYPE html>
<html>
<head>
    <title>Izmena lozinke</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
</header>

<h2>Izmena lozinke</h2>

<form action="update_password.php" method="POST" onsubmit="return validatePassword()">
    <label for="current_password">Trenutna lozinka:</label><br>
    <input type="password" id="current_password" name="current_password" required><br>
    <a href="password_reset.php" class="active">zaboravljena lozinka</a><br><br>

    <label for="new_password">Nova lozinka:</label><br>
    <input type="password" id="new_password" name="new_password" required><br><br>

    <label for="confirm_password">Potvrdi novu lozinku:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <button type="submit">Sačuvaj novu lozinku</button>
</form>
</body>
</html>
