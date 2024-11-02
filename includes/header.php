<header>
    <div class="container">
        <div class="logo">
            <a href="/NTUmami/index.php">
                <img src="/NTUmami/assets/images/logo.png" alt="NTUmami Logo">
                <span>NTUmami</span>
            </a>
        </div>

        <!-- Desktop Navigation Links -->
        <nav class="nav-links">
            <ul>
                <li><a href="/NTUmami/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/NTUmami/pages/menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
                <li><a href="/NTUmami/pages/locations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>">Locations</a></li>
                <li><a href="/NTUmami/pages/about-us.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="/NTUmami/pages/orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">My Orders</a></li>
            </ul>
        </nav>

        <!-- User Actions (Cart and Login in Desktop, Login hidden in Mobile) -->
        <div class="user-actions">
            <a href="/NTUmami/pages/cart.php" class="cart-icon"><i class="fa fa-shopping-cart"></i> <span class="cart-count">0</span></a>
            <?php
                if (!isset($_SESSION['user_id'])) {
                    echo '<a href="/NTUmami/pages/login.php" class="user-icon"><i class="fa fa-user"></i>Login</a>';
                } else {
                    echo '<a href="/NTUmami/controllers/logout.php" class="user-icon"><i class="fa fa-user"></i>Logout</a>';
                }
            ?>
        </div>
    </div>
    
</header>

<!-- Bottom Navigation for Mobile -->
<nav class="bottom-nav">
    <ul>
        <li><a href="/NTUmami/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fa fa-home"></i><span>Home</span></a></li>
        <li><a href="/NTUmami/pages/menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>"><i class="fa fa-utensils"></i><span>Menu</span></a></li>
        <li><a href="/NTUmami/pages/locations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>"><i class="fa fa-map-marker-alt"></i><span>Locations</span></a></li>
        <li><a href="/NTUmami/pages/orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fa fa-list"></i><span>My Orders</span></a></li>
        <li><a href="/NTUmami/pages/profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"><i class="fa fa-user"></i><span>Account</span></a></li>

        <!-- More button with sliding panel -->
        <li class="more-nav">
            <div class="more-button"><i class="fa fa-ellipsis-h"></i><span>More</span></div>
        </li>
    </ul>
</nav>

<!-- More Panel -->
<div class="more-panel">
    <span class="close-more">&times;</span>
    <div class="more-panel-item">
        <a href="/NTUmami/pages/about.php"><i class="fa fa-info-circle"></i>About Us</a>
    </div>
    <?php
        if (isset($_SESSION['user_id'])) {
            echo '
            <div class="more-panel-item">
                <a href="/NTUmami/controllers/logout.php" class="user-icon"><i class="fa fa-user"></i>Logout</a>
            </div>
            ';
        }
    ?>
</div>

<!-- Dark overlay for when More panel is active -->
<div class="dark-overlay"></div>