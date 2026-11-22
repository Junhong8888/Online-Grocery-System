<?php
session_start();

// Initialize only once per session, not on every page load
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productName = trim($_POST['productName'] ?? '');
    $productQty  = (int) ($_POST['productQty'] ?? 1);

    if ($productName !== '' && $productQty !== 0) {
        $found = false;

        foreach ($_SESSION['cart'] as $i => $item) {
            if (is_array($item) && isset($item['name'], $item['quantity'])) {
                if (strtolower(trim((string) $item['name'])) === strtolower($productName)) {
                    $_SESSION['cart'][$i]['quantity'] = (int) $item['quantity'] + $productQty;
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            // Append with = (assignment operator) and [] (next index)
            $_SESSION['cart'][] = [
                "name"     => $productName,
                "quantity" => $productQty,
            ];
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>
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
        <img src="img/profile.png" alt="Apple" style="display: block;width:100px;height:100px;">
        <figcaption>Apple</figcaption>
        <form action="" method="post">
            <input type="hidden" name="productName" value="apple">
            <input type="hidden" name="productQty" value="1">
            <input type="submit" value="Add Apple">
            <input type="submit" value="Remove Apple" id="rm_app">
        </form>
    </figure>

    <figure>
        <img src="img/profile.png" alt="Banana" style="display: block;width:100px;height:100px;">
        <figcaption>Banana</figcaption>
        <form action="" method="post">
            <input type="hidden" name="productName" value="banana">
            <input type="hidden" name="productQty" value="1">
            <input type="submit" value="Add Banana">
            <input type="submit" value="Remove Banana">
        </form>
    </figure>
    <pre><?php var_dump($_SESSION); ?></pre>
    <?php if (!empty($_SESSION['cart'])): ?>
        <ul>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <?php if (is_array($item) && isset($item['name'], $item['quantity'])): ?>
                    <li>
                        <?php echo htmlspecialchars((string) $item['name']) . ", " . (int) $item['quantity']; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <script>
        $("#rm_app").click(function (e) {
            e.preventDefault();

            const data = new URLSearchParams();
            data.append("productName", "apple");
            data.append("productQty", -1);

            fetch("cart_item.php", {
                method: "POST",
                body: data
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error("HTTP error: " + response.status);
                }
                return response.text(); // <-- Call response.text() as a function
            })
            .then(function (text) {
                console.log("Server response:", text);
            })
            .catch(function (err) {
                console.error("Fetch error:", err);
            });
        });
    </script>
</body>
</html>