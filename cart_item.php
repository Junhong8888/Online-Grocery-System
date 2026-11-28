<?php
session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$isAjax = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productName = trim($_POST['productName'] ?? '');
    $productQty  = (int) ($_POST['productQty'] ?? 1);

    if ($productName !== '' && $productQty !== 0) {
        $found = false;

        foreach ($_SESSION['cart'] as $i => $item) {
            if (is_array($item) && isset($item['name'], $item['quantity'])) {
                if (strtolower(trim((string) $item['name'])) === strtolower($productName)) {
                    $newQty = (int) $item['quantity'] + $productQty;

                    if ($newQty <= 0) {
                        unset($_SESSION['cart'][$i]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    } else {
                        $_SESSION['cart'][$i]['quantity'] = $newQty;
                    }

                    $found = true;
                    break;
                }
            }
        }

        // Only create a new item when adding positive quantity
        if (!$found && $productQty > 0) {
            $_SESSION['cart'][] = [
                "name"     => $productName,
                "quantity" => $productQty,
            ];
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['cart' => $_SESSION['cart']]);
        exit;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<?php require "_head.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-4.1.0.min.js" integrity="sha256-hjRvPkFLO1JGZFMXGrzdpG6TlsHDyjp4PQeqLWRcMzk=" crossorigin="anonymous"></script>
</head>
<body>

    <figure>
        <img src="" alt="Apple" style="display: block;width:100px;height:100px;">
        <figcaption>Apple</figcaption>
        <form action="" method="post">
            <input type="hidden" name="productName" value="apple">
            <input type="hidden" name="productQty" value="1">
            <input type="submit" value="Add Apple">
            <input type="button" value="Remove Apple" id="rm_app">
        </form>
    </figure>

    <figure>
        <img src="" alt="Banana" style="display: block;width:100px;height:100px;">
        <figcaption>Banana</figcaption>
        <form action="" method="post">
            <input type="hidden" name="productName" value="banana">
            <input type="hidden" name="productQty" value="1">
            <input type="submit" value="Add Banana">
            <input type="submit" value="Remove Banana">
        </form>
    </figure>

    <ul id="cart-list">
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <?php if (is_array($item) && isset($item['name'], $item['quantity'])): ?>
                    <li>
                        <?php echo htmlspecialchars((string) $item['name']) . ", " . (int) $item['quantity']; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <script>
        function renderCart(cartItems) {
            const $list = $("#cart-list");
            $list.empty();

            if (!Array.isArray(cartItems)) return;

            cartItems.forEach(function (item) {
                if (item && item.name !== undefined && item.quantity !== undefined) {
                    const $li = $("<li>").text(`${item.name}, ${item.quantity}`);
                    $list.append($li);
                }
            });
        }

        $("#rm_app").on("click", function () {
            const data = new URLSearchParams();
            data.append("productName", "apple");
            data.append("productQty", -1);

            fetch("cart_item.php", {
                method: "POST",
                headers: {
                    "Accept": "application/json"
                },
                body: data
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("HTTP error: " + response.status);
                }
                return response.json();
            })
            .then(function (result) {
                renderCart(result.cart);
            })
            .catch(function (err) {
                console.error("Fetch error:", err);
            });
        });
    </script>
</body>
</html>