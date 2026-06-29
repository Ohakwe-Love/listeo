<?php
    require_once __DIR__ . "/assets/extras/includes/function.inc.php";
    start_secure_session();
    $messages = get_flash_messages();
    $oldName = old_input('name');
    $oldEmail = old_input('email');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up here</title>
    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/images/favicon/site.webmanifest">

    <!-- css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">

    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/ee0082ad61.js" crossorigin="anonymous"></script>

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC:ital,wght@0,100;0,300;0,400;0,500;0,700;0,800;0,900;1,100;1,300;1,400;1,500;1,700;1,800;1,900&family=Allison&family=Bebas+Neue&family=Cedarville+Cursive&family=Dekko&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Lora:ital,wght@0,400..700;1,400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    
    <section class="general_form_container">
        <div class="form_wrapper">
            <div class="form_container">
                <form action="assets/extras/includes/signup.inc.php" method="post">
                    <h1>create account</h1>
                    <div class="social_container">
                        <a href="#" aria-label="Sign up with Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Sign up with Google"><i class="fa-brands fa-google-plus-g"></i></a>
                        <a href="#" aria-label="Sign up with LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                    <span>or use your email for registration</span>
                    <?php foreach ($messages as $type => $items): ?>
                        <?php foreach ($items as $message): ?>
                            <p class="form-alert form-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <input type="text" name="name" placeholder="Name" value="<?php echo h($oldName); ?>" required/>
                    <input type="email" name="email" placeholder="Email" value="<?php echo h($oldEmail); ?>" required />
                    <input type="password" name="password" placeholder="Password" minlength="8" required />
                    <select name="account_type" class="form-select">
                        <option value="user">I want to browse and book</option>
                        <option value="lister">I want to list properties</option>
                    </select>
                    <button type="submit">Sign Up</button>
                </form>
            </div>

            <div class="form_about_container">
                <div class="form_about">
                    <h1>Hello, Friend!</h1>
                    <p>Already have an account with us? Sign in to stay connected.</p>
                    <a href="login.php">log in</a>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/form.js"></script>
</body>
</html>
