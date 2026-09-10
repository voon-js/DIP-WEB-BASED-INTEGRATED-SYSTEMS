$(document).ready(function () {

    // 加按钮点击
    $('.plus').click(function () {
        const id = $(this).data('id');
        const input = $(`.quantity-input[data-id="${id}"]`);
        if (parseInt(input.val()) < 99) {
            input.val(parseInt(input.val()) + 1);
            updateCartItem(id);
        }
    });

    // 减按钮点击
    $('.minus').click(function () {
        const id = $(this).data('id');
        const input = $(`.quantity-input[data-id="${id}"]`);
        if (parseInt(input.val()) > 1) {
            input.val(parseInt(input.val()) - 1);
            updateCartItem(id);
        }
    });

    // 输入框变化
    $('.quantity-input').change(function () {
        const id = $(this).data('id');
        if (parseInt($(this).val()) < 1) {
            $(this).val(1);
        }
        if (parseInt($(this).val()) > 99) {
            $(this).val(99);
        }
        updateCartItem(id);
    });

    // 删除商品
    $('.delete-btn').click(function () {
        const id = $(this).data('id');
    
        // AJAX 请求后端删除
        $.ajax({
            url: 'removeFromCart.php',
            type: 'POST',
            data: { product_id: id },
            success: function (response) {
                console.log(response); // 可选
    
                // 前端 DOM 也移除
                $(`.cart-item[data-id="${id}"]`).remove(); 
                updateTotal();
    
                if ($('.cart-item').length === 0) {
                    $('.cart-items').html('<p class="empty-cart">Your cart is empty</p>');
                }
            },
            error: function () {
                alert("Failed to remove item from cart.");
            }
        });
    });
    

    $('.item-select').change(function () {
        updateTotal();
    });


    $('#checkout-form').submit(function (e) {
        e.preventDefault(); // 阻止表单默认提交
    
        let selectedItems = [];
    
        $('.cart-item').each(function () {
            const checkbox = $(this).find('.item-select');
            if (checkbox.is(':checked')) {
                const id = $(this).data('id');
                const name = $(this).find('h3').text();
                const image = $(this).find('img').attr('src').replace('images/', '');
                const price = parseFloat($(this).find('.item-details p:nth-child(3)').text().replace('Price: RM ', ''));
                const quantity = parseInt($(this).find('.quantity-input').val());
    
                selectedItems.push({ id, name, image, price, quantity });
            }
        });
    
        // 把资料转成 JSON 串传到 hidden input
        $('#cartData').val(JSON.stringify(selectedItems));
    
        // 提交表单
        this.submit();
    });
    

    // 更新购物车项和小计
    function updateCartItem(id) {
        const quantity = parseInt($(`.quantity-input[data-id="${id}"]`).val());
        const price = parseFloat($(`.cart-item[data-id="${id}"] .item-details p:nth-child(3)`).text().replace('Price: RM ', ''));

        // 计算小计
        const subtotal = price * quantity;
        $(`.cart-item[data-id="${id}"] .subtotal span`).text(subtotal.toFixed(2));

        // 计算总计
        updateTotal();
    }

    // 更新总计
    function updateTotal() {
        let total = 0;
        $('.cart-item').each(function () {
            const checkbox = $(this).find('.item-select');
            if (checkbox.is(':checked')) {
                const subtotal = parseFloat($(this).find('.subtotal span').text());
                total += subtotal;
            }
        });
        $('#total-amount').text(total.toFixed(2));
    }
});



