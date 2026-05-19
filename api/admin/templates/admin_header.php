<?php 
// No guard here, the pages themselves will have the guard.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Artisania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Artisania Admin</a>
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <a class="nav-link px-3" href="../logout.php">Sign out</a>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#dashboard-top">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#users-section">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products-section">Validate Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php#reviews-section">Moderate Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php#transactions-section">Track Transactions</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
