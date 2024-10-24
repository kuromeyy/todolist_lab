<?php
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">To-Do List</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto"> 
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'profile') ? 'active' : '' ?>" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'index') ? 'active' : '' ?>" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'add_task') ? 'active' : '' ?>" href="add_task.php">Tambah Task</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
