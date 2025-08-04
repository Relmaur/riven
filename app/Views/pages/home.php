<?php

use Core\Session; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
</head>

<body>
    <header></header>
    <main>
        <?php $user_name = Session::isAuthenticated() ? Session::get('user_name') : null;
        if ($user_name): ?>
            <div class="welcome-message"><?php echo $user_name ? "Welcome back, $user_name!" : ''; ?></div>
        <?php endif; ?>
        <h1><?php echo $pageTitle; ?></h1>
        <p><?php echo $pageContent; ?></p>
    </main>
    <footer></footer>
</body>

</html>