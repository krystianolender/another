<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="author" content="Krystian Olender">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="web/css/bootstrap.min.css" rel="stylesheet">
        <link href="web/css/templates.css" rel="stylesheet">

        <title>Another</title>
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">

                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <?php if (!$this->auth->isLoggedIn()): ?>
                            <li><?php echo $this->Url->toHtml('Rejestracja', 'user', 'register'); ?></li>
                            <li><?php echo $this->Url->toHtml('Logowanie', 'user', 'login'); ?></li>
                        <?php else: ?>
                            <li><?php echo $this->Url->toHtml('Wyświetlanie', 'user', 'view'); ?></li>
                            <li><?php echo $this->Url->toHtml('Lista', 'user', 'index'); ?></li>
                            <li><?php echo $this->Url->toHtml('Wylogowywanie', 'user', 'logout'); ?></li>
                            <li><?php echo $this->Url->toHtml($loggedUser['first_name'] . ' ' . $loggedUser['last_name'], 'user', 'edit'); ?></li>
                        <?php endif; ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
        <div class="template">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-info"><?php echo $message ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="container">
                <?php include($viewContent); ?>
            </div><!-- /.container -->
        </div>

        <script src="web/javascript/jquery.min.js"></script>
        <script src="web/javascript/bootstrap.min.js"></script>
    </body>
</html>