<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cart.css">
    <title>Document</title>
</head>
<body>
    <div class="page_backdrop" aria-hidden="true"></div>

    <aside id="cart">
        <header class="cart_head">
            <h4>My Cart</h4>
            <a href="#"><img src="img/cancel.png" alt="close"></a>
        </header>

        <div class="cart_content">
            <div class="cart_item">
                <img src="img/profile.png" alt="profile">
                <p id="prod_desc">TOMATO 2KG -PEK</p>
                <p id="prod_price">RM10.50/Each</p>
                <div id="prod_input">
                    <div id="option">
                        <input type="number" name="prod_qty" id="prod_qty">
                        <img src="img/delete.png" alt="delete" id="del">
                    </div>
                    <span>RM20.50</span>
                </div>
            </div>

        </div>
        
        <footer class="cart_foot">
            <span>Total Payment: <span>RM15</span></span>
            <button>Checkout</button>
        </footer>
    </aside>
</body>
</html>