// Получаем элементы на странице (кнопки и инпуты)
const btnIncrease = $(".btn-increase");
const btnDecrease = $(".btn-decrease");
const qtyInput = $(".qty-input");

// Функция для обновления корзины
function updateCart(change) {
    console.log("Updating cart with change:", change);  // Логируем изменение

    $.ajax({
        url: ajax_data.ajaxurl,  // Теперь используем ajax_data.ajaxurl
        method: 'POST',
        data: {
            action: 'update_wc_cart',    // Действие на сервере
            product_id: productId,       // ID товара
            quantity_change: change,     // Изменение количества
        },
        success: function(response) {
            if (response.success) {
                console.log("Cart updated:", response.data);
                // Можно обновить интерфейс, например, показать новое количество в корзине
            } else {
                console.error("Failed to update cart:", response.data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error updating cart:", error);
        }
    });
}

// Обработчик нажатия на кнопку увеличения
btnIncrease.on("click", function () {
    let currentQty = parseInt(qtyInput.val(), 10) || 1;  // Текущее количество из инпута
    let newQty = currentQty + 1;  // Увеличиваем на 1

    console.log("Clicked + button: Increasing quantity by 1");
    qtyInput.val(newQty);  // Обновляем инпут
    updateCart(1);  // Отправляем изменение в корзину
});

// Обработчик нажатия на кнопку уменьшения
btnDecrease.on("click", function () {
    let currentQty = parseInt(qtyInput.val(), 10) || 1;  // Текущее количество из инпута
    let newQty = Math.max(1, currentQty - 1);  // Уменьшаем на 1, но не ниже 1

    console.log("Clicked - button: Decreasing quantity by 1");
    qtyInput.val(newQty);  // Обновляем инпут
    updateCart(-1);  // Отправляем изменение в корзину
});

// Обработчик изменения количества вручную в инпуте
qtyInput.on("change", function () {
    let newQty = parseInt(qtyInput.val(), 10) || 1;  // Получаем количество из инпута
    console.log("Quantity changed manually: " + newQty);
    updateCart(newQty - 1);  // Отправляем изменение в корзину (по аналогии с кнопками)
});
