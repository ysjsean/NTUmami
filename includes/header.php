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
                <li><a href="/NTUmami/pages/about-us.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about-us.php' ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="/NTUmami/pages/myorders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">My Orders</a></li>
            </ul>
        </nav>

        <!-- User Actions (Cart and Login/Username Dropdown) -->
        <div class="user-actions">
            <a href="/NTUmami/pages/cart.php" class="cart-icon">
                <i class="fa fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $_SESSION['cart_count'] ?? 0; ?></span>
            </a>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <!-- If user is not logged in, show Login button -->
                <a href="/NTUmami/pages/login.php" class="user-icon"><i class="fa fa-user"></i> Login</a>
            <?php else: ?>
                <!-- If user is logged in, show username with dropdown -->
                <div class="dropdown">
                    <button class="dropbtn">
                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="/NTUmami/pages/profile.php"><i class="fa fa-id-card"></i> View Profile</a>
                        <a href="/NTUmami/controllers/logout.php"><i class="fa fa-sign-out-alt"></i> Log Out</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>


<!-- Bottom Navigation for Mobile -->
<nav class="bottom-nav">
    <ul>
        <li><a href="/NTUmami/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fa fa-home"></i><span>Home</span></a></li>
        <li><a href="/NTUmami/pages/menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>"><i class="fa fa-utensils"></i><span>Menu</span></a></li>
        <li><a href="/NTUmami/pages/locations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>"><i class="fa fa-map-marker-alt"></i><span>Locations</span></a></li>
        <li><a href="/NTUmami/pages/myorders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fa fa-list"></i><span>My Orders</span></a></li>
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
        <a href="/NTUmami/pages/about-us.php"><i class="fa fa-info-circle"></i>About Us</a>
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