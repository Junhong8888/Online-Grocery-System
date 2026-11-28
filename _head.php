<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-4.1.0.min.js" integrity="sha256-hjRvPkFLO1JGZFMXGrzdpG6TlsHDyjp4PQeqLWRcMzk=" crossorigin="anonymous"></script>
    <script src="js/app.js" defer></script>
    <title>Willem Mart</title>
</head>
<body>
   <header>
        <nav>
            <a href="index.php" id="logo">Willem Mart</a>
            <input type="search" name="searchBar" id="searchBar" placeholder="Search products...">
            <a id="modalBtn" class="button"><img src="images/cart.png" alt="cart" id="cart"></a>

            
            <!-- Shopping Cart Modal  -->
            <div id="simpleModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>My Cart</h4>
                        <a class="closeBtn"><img src="images/cancel.png" alt="close"></a>
                    </div>
                    <div class="modal-body">
                        <div class="cart_item">
                            <img src="" alt="product">
                            <p id="prod_desc">TOMATO 2KG -PEK</p>
                            <p id="prod_price">RM10.50/Each</p>
                            <div id="prod_input">
                                <div id="option">
                                    <input type="number" name="prod_qty" id="prod_qty">
                                    <img src="images/delete.png" alt="delete" id="del">
                                </div>
                                <span>RM20.50</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span>Total Payment: <span>RM15</span></span>
                        <button>Checkout</button>
                    </div>
                </div>
            </div>


            <!-- Authenticate Button -->
            <div class="auth-buttons">
                <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'): ?>
                    <a href="admin_members.php" class="btn-login">Manage Members</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="btn-login">Profile</a>
                    <a href="logout.php" class="btn-register">Log Out</a>
                <?php else: ?>
                    <a href="login.php" id="loginBtn" class="btn-login">Log In</a>
                    <a href="register.php" id="registerBtn" class="btn-register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
