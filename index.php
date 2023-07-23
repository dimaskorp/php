<?php

// Часть 1: Работа с внешним API и highload блоками Bitrix

// Функция для получения информации о товарах из API
function fetchProductsFromAPI($url)
{
    $xml = file_get_contents($url); // считывает весь файл в строку
    $data = simplexml_load_string($xml); // преобразует строку XML в объект

    $products = [];

    foreach ($data->shop->offers->offer as $offer) {
        $product = [
            'id' => (string)$offer['id'],
            'url' => (string)$offer->url,
            'price' => (float)$offer->price,
            'picture' => [],
            'name' => (string)$offer->name,
            'code' => generateProductCode((string)$offer->name),
        ];

        foreach ($offer->picture as $picture) {
            $product['picture'][] = (string)$picture;
        }

        $products[] = $product;
    }

    return $products;
}

// Функция для генерации символьного кода товара на основе его названия
function generateProductCode($name)
{
    // Реализуется логика генерации символьного кода товара с помощью средств Bitrix
    // Например, можно использовать функцию CUtil::translit для транслитерации названия товара

    return ''; // Вернуть сгенерированный символьный код товара
}

// Функция для сохранения товаров в highload блок
function saveProductsToHighloadBlock($products)
{
    // Реализуется сохранение товаров в highload блок с помощью средств Bitrix
    // Например, можно использовать класс CIBlockElement для создания элементов highload блока
}

// Вызов функций для получения информации о товарах и их сохранения
$products = fetchProductsFromAPI('https://www.galacentre.ru/download/yml/MSK-posuda.xml');
saveProductsToHighloadBlock($products);


// Часть 2: Работа с JS (jQuery)

?>

<!-- Верстка для публичной части -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Последние добавленные товары</title>
    <link rel="stylesheet" href="modal.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h1>Последние добавленные товары</h1>

<?php
// Вывод последних 10 добавленных товаров
$products_10 = array_slice($products, -10);
foreach ($products_10 as $product) {
    echo '<div>';
    echo '<img src="' . $product['picture'][0] . '" alt="' . $product['name'] . '">';
    echo '<h3>' . $product['name'] . '</h3>';
    echo '<p>Цена: ' . $product['price'] . '</p>';
    echo '<button onclick="openOrderModal(\'' . $product['name'] . '\')">Заказать</button>';
    echo '</div>';
}
?>

<!-- Модальное окно для заказа -->
<div class="popup-fade" id="order-modal" style="display: none;">
    <div class="popup">
        <a class="popup-close" href="#">Закрыть</a>
        <h2>Оформление заказа</h2>
        <p>Товар: <span id="product-name"></span></p>
        <form id="order-form" method="post">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required>
            <br><br>
            <label for="phone">Телефон:</label>
            <input type="text" id="phone" name="phone" required>
            <br><br>
            <input type="submit" value="Отправить">
        </form>
    </div>
</div>
<script>

    // Функция для открытия модального окна с формой заказа
    function openOrderModal(productName) {
        $('#product-name').text(productName);
        $("#order-modal").show();
        // Клик по ссылке "Закрыть".
        $('.popup-close').click(function() {
            $(this).parents('.popup-fade').fadeOut();
            return false;
        });
    }

        // Обработчик отправки формы заказа
    $('#order-form').submit(function (e) {
        e.preventDefault();

        // Получение данных из формы
        let name = $('#name').val();
        let phone = $('#phone').val();
        let productName = $('#product-name').text();

        // Отправка данных на сервер
        $.ajax({
            url: 'process_order.php',
            method: 'POST',
            data: {
                name: name,
                phone: phone,
                productName: productName
            },
            success: function (response) {
                // Обработка успешного ответа от сервера
                alert('Заказ успешно оформлен!');
                $('#order-modal').hide();
            },
            error: function () {
                // Обработка ошибки
                alert('Ошибка при оформлении заказа.');
            }
        });
    });
</script>
</body>
</html>
