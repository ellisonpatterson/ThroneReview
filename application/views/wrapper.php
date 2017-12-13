<!doctype html>
<html lang="en">
    <head>
        <title><?php echo (isset($title) ? $title . ' | Throne Review' : 'Throne Review'); ?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="csrf-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
        <meta name="csrf-value" content="<?php echo $this->security->get_csrf_hash(); ?>">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/global.css">
    </head>

    <body>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">Throne Review</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav ml-auto">
                    <?php if ($this->user->isLoggedIn()) { ?>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle py-0" type="button" id="userMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="avatar-s rounded-circle align-text-top" src="<?php echo $this->provider->getUserAvatar(); ?>"></img>
                                <span class="nav-link d-inline-block"><?php echo $this->user->name; ?></span>
                            </button>

                            <div class="dropdown-menu" aria-labelledby="userMenu">
                                <a id="trigger-logout" class="dropdown-item" href="<?php echo base_url('logout'); ?>">Log-Out</a>
                                <form type="post" action="<?php echo base_url('logout'); ?>" id="logout-form" style="display: none;"></form>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if (!$this->user->isLoggedIn()) { ?>
                        <li class="nav-item">
                                <a class="nav-link" data-toggle="modal" href="<?php echo base_url(); ?>login/">Login</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>

        <main role="main" class="d-flex h-100 flex-column">
            <div class="container-fluid d-flex h-100 flex-column <?php echo (isset($containerClasses) ? $containerClasses : ''); ?>">
                <?php echo $content; ?>
            </div>
        </main>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.2/js/star-rating.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/global.js"></script>

        <?php echo (isset($scripts) ? $scripts : ''); ?>
    </body>
</html>