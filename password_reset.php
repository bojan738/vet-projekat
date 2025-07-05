<!DOCTYPE html>
<html lang="en">
<head>
    <title>Izmena lozinke</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="login-page">

<header>
    <div class="logo">🐾 PetCare</div>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Izmena lozinke</h2>

        <form action="update_password.php" method="POST" onsubmit="return validatePassword()">
            <div class="form-group">
                <label for="current_password" class="form-label">Trenutna lozinka:</label>
                <input type="password" id="current_password" name="current_password" required class="form-input">
            </div>

            <p class="register-link"><a href="password_forgot.php">Zaboravljena lozinka</a></p>

            <div class="form-group">
                <label for="new_password" class="form-label">Nova lozinka:</label>
                <input type="password" id="new_password" name="new_password" required class="form-input">
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Potvrdi novu lozinku:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-input">
            </div>

            <button type="submit" class="cta-button">Sačuvaj novu lozinku</button>
            <p class="register-link"><a href="user_information.php">Nazad</a></p>
        </form>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

</body>
</html>