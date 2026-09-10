$(document).ready(function () {
    const DELIVERY_FEE = 10.00;
    let selectedOption = null;

    // 更新总额（SST + Delivery + Total）
    function updateTotals() {
        let subtotal = 0;

        $('.subtotal span').each(function () {
            subtotal += parseFloat($(this).text());
        });

        $('#item-total').text(subtotal.toFixed(2));

        let deliveryFee = $('#delivery-fee-box').is(':visible') ? DELIVERY_FEE : 0;
        let sst = subtotal * 0.06;
        let total = subtotal + deliveryFee + sst;

        $('#sst-amount').text(sst.toFixed(2));
        $('#total-amount').text(total.toFixed(2));
    }

    // 更新单项商品（小计）
    function updateCartItem(id) {
        const quantity = parseInt($(`.quantity-input[data-id="${id}"]`).val());
        const price = parseFloat($(`.cart-item[data-id="${id}"] .item-details p:nth-child(3)`).text().replace('Price: RM ', ''));

        const subtotal = price * quantity;
        $(`.cart-item[data-id="${id}"] .subtotal span`).text(subtotal.toFixed(2));

        updateTotals(); // 更新总额
    }

    // 加数量
    $('.plus').click(function () {
        const id = $(this).data('id');
        const input = $(`.quantity-input[data-id="${id}"]`);
        if (parseInt(input.val()) < 99) {
            input.val(parseInt(input.val()) + 1);
            updateCartItem(id);
        }
    });

    // 减数量
    $('.minus').click(function () {
        const id = $(this).data('id');
        const input = $(`.quantity-input[data-id="${id}"]`);
        if (parseInt(input.val()) > 1) {
            input.val(parseInt(input.val()) - 1);
            updateCartItem(id);
        }
    });

    // 直接改数量
    $('.quantity-input').change(function () {
        const id = $(this).data('id');
        let val = parseInt($(this).val());
        if (val < 1) $(this).val(1);
        if (val > 99) $(this).val(99);
        updateCartItem(id);
    });

    // 删除商品
    $('.delete-btn').click(function () {
        const id = $(this).data('id');

        // 如果只剩一个商品，阻止删除
        if ($('.cart-item').length === 1) {
            alert("You cannot delete the last item .");
            return;
        }

        $(`.cart-item[data-id="${id}"]`).remove();
        updateTotals(); // 更新总额

        // 如果没有商品，显示空购物车信息
        if ($('.cart-item').length === 0) {
            $('.cart-items').html('<p class="empty-cart">Your cart is empty</p>');
        }
    });

    // 配送地址
    $('#btn-delivery').click(function () {
        $('#address-box').html(`
            <label for="delivery-address">Enter your delivery address:</label><br>
            <input type="text" id="delivery-address" name="delivery-address" placeholder="e.g. No 12, Jalan ABC" style="width:60%; padding:8px; margin-top:5px; display:block; margin-left:auto; margin-right:auto;">
        `).show();

        $('#delivery-fee-box').show();
        updateTotals();
    });

    // 自取地址
    $('#btn-pickup').click(function () {
        const address = $(this).data('address');
        $('#address-box').html(`<p><strong>Pickup Address:</strong> ${address}</p>`).show();

        $('#delivery-fee-box').hide();
        updateTotals();
    });

    // 选 Delivery
    $('#btn-delivery').click(function () {
        selectedOption = 'delivery';
        const userAddress = $('#address-box').data('user-address');
        
        $('#address-box').html(`
            <label for="delivery-address">Enter your delivery address:</label><br>
            <input type="text" id="delivery-address" placeholder="e.g. No 12, Jalan ABC" 
                   value="${userAddress || ''}" 
                   style="width:60%; padding:8px; margin:5px auto; display:block;">
            <button id="save-address-btn" class="confirm-btn">Save Address</button>
        `).show();
        
        $('#delivery-fee-box').show();
        updateTotals();
    });
    
    // 保存地址
    $(document).on('click', '#save-address-btn', function () {
        const address = $('#delivery-address').val().trim();
        if (!address) {
            alert('Please enter a valid address.');
            return;
        }
        
        if (address.length < 5) {  
            alert('Address is too short. Please enter a complete address.');
            return;
        }
    
        $.ajax({
            url: 'saveAddress.php',
            method: 'POST',
            data: { address: address },
            dataType: 'json', 
            success: function (data) {
                if (data && data.success) {
                    alert('Address saved successfully!');
                    $('#address-box').data('user-address', address);
                } else {
                    const errorMsg = data && data.error ? data.error : 'Failed to save address';
                    alert('Error: ' + errorMsg);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        });
    });

    $('#btn-delivery').click(function () {
        const userAddress = $('#address-box').data('user-address');
        $('#delivery-address').val(userAddress || ''); // 如果地址为空，清空输入框
        $('#address-box').show();
        $('#delivery-fee-box').show();
        updateTotals();
    });

    // 选 Pickup
    $('#btn-pickup').click(function () {
        selectedOption = 'pickup'; // 标记为已选
        const address = $(this).data('address');
        $('#address-box').html(`<p><strong>Pickup Address:</strong> ${address}</p>`).show();

        $('#delivery-fee-box').hide();
        updateTotals();
    });

    // 保存地址
    $(document).on('click', '#save-address-btn', function () {
        const address = $('#delivery-address').val().trim();
        if (!address) {
            alert('Please enter a valid address.');
            return;
        }

        $.ajax({
            url: 'saveAddress.php',
            method: 'POST',
            data: { address: address },
            dataType: 'json', 
            success: function (data) {
                if (data && data.success) {
                    alert('Address saved successfully!');
                    // 更新前端显示的地址
                    $('#address-box').data('user-address', address);
                } else {
                    const errorMsg = data && data.error ? data.error : 'Failed to save address';
                    alert('Error: ' + errorMsg);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Network error. Please try again.');
            }
        });
    });

    // 如果没选 delivery/pickup，就不让付款
    $('#checkout-btn').click(function () {
        if (!selectedOption) {
            alert("Please select Delivery or Pick Up before proceeding to payment.");
            return;
        }

        // 如果选择的是 Delivery，检查地址是否已输入
    if (selectedOption === 'delivery') {
        const address = $('#delivery-address').val().trim();
        if (!address) {
            alert("Please enter a delivery address before proceeding to payment.");
            return;
        }
    }

        $('#paymentModal').fadeIn();
    });

    // 初始载入也算一次
    updateTotals();

    $('#confirmPaymentBtn').click(function () {
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        if (!paymentMethod) {
            alert("Please select a payment method before confirming.");
            return;
        }
    
        if (paymentMethod === 'tng') {
            $('#paymentModal').fadeOut(); // 先关掉付款方式 modal
            $('#tngQRModal').fadeIn(); // 弹出 Touch 'n Go 的 QR modal
        } else {
            proceedPayment(); // 非TNG，直接继续流程
        }
    });
    
    // 用户按下 TNG 的 Confirm 按钮后再继续付款流程
    $('#tngConfirmBtn').click(function () {
        $('#tngQRModal').fadeOut();
        proceedPayment();
    });
    
    // 封装统一付款流程
    function proceedPayment() {
        $('#paymentModal').fadeOut(); // 关闭支付方式选择弹窗
        $('#processingModal').fadeIn();
    
        setTimeout(function () {
            $('#processingModal').fadeOut(function () {
                $('#completeModal').fadeIn();
            });
        }, 3000);
    }
    

    //回去home.php
    $('#completeConfirmBtn').click(function () {
        let paidIds = [];
        let orderItems = [];

        // 获取选中的支付方式
    const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        // 收集订单项信息
        $('.cart-item').each(function () {
            const id = $(this).data('id');
            const quantity = parseInt($(this).find('.quantity-input').val());
            const price = parseFloat($(this).find('.item-details p:nth-child(3)').text().replace('Price: RM ', ''));
            
            paidIds.push(id);
            orderItems.push({
                product_id: id,
                quantity: quantity,
                price: price
            });
        });
    
        // 收集订单信息
        const orderData = {
            items: orderItems,
            delivery_option: selectedOption,
            address: selectedOption === 'delivery' ? $('#delivery-address').val() : $('#btn-pickup').data('address'),
            subtotal: parseFloat($('#item-total').text()),
            sst: parseFloat($('#sst-amount').text()),
            delivery_fee: selectedOption === 'delivery' ? DELIVERY_FEE : 0,
            total: parseFloat($('#total-amount').text())
        };
    
        // 发送订单数据到服务器
        $.ajax({
            url: 'saveOrder.php',
            method: 'POST',
            data: { 
                orderData: JSON.stringify(orderData),
                paidItems: paidIds,
                paymentMethod: paymentMethod
            },
            success: function (response) {
                console.log('Order saved:', response);
                // 清空购物车并跳转
                window.location.href = 'home.php';
            },
            error: function (xhr, status, error) {
                console.error('Error saving order:', error);
                alert('Error saving order. Please try again.');
            }
        });
    });

});


// 不给开Payment
function closePaymentModal() {
    $('#paymentModal').fadeOut();
}


function closePaymentModal() {
    $('#paymentModal').fadeOut(); // 关闭付款弹窗
}

