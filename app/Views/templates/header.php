<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistemas CIX</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/fontawesome-free/css/all.min.css') ?>">

    <!-- AdminLTE style -->
    <link rel="stylesheet" href="<?= base_url('theme/dist/css/adminlte.min.css')?>">

    <!-- Bootstrap 4.5.2 CSS (necessário para tabs funcionarem) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CDN (última versão estável) -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

  </head>
  <body class="hold-transition sidebar-mini">
    <div class="wrapper">
      <?php include_once('navbar.php') ?>
      <?php if (session()->get('logged_in')): ?>
        <?php include_once('sidebar.php') ?>
      <?php endif; ?>

