<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | LumenMart POS</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/">LumenMart POS</a>
        <nav aria-label="Main navigation">
            <a href="/">Home</a>
            <a href="/about">About</a>
            <?php if (session()->get('user_id') !== null): ?>
                <a href="/products">Products</a>
                <a href="/sales/new">Record Sale</a>
                <a href="/sales">Sales History</a>
                <a href="/customers">Customers</a>
                <a href="/users">Users</a>
                <form class="nav-form" method="post" action="/logout"><?= csrf_field() ?><button type="submit">Log Out</button></form>
            <?php else: ?>
                <a href="/login">Log In</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
<?php if ($message = session()->getFlashdata('success')): ?>
    <div class="alert success"><?= esc($message) ?></div>
<?php endif; ?>
<?php if ($message = session()->getFlashdata('error')): ?>
    <div class="alert error"><?= esc($message) ?></div>
<?php endif; ?>
