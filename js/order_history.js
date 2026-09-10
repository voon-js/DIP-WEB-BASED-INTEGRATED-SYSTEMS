document.addEventListener('DOMContentLoaded', function() {
    // 为所有取消按钮添加事件监听
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const orderId = row.dataset.orderId;
            const orderTime = parseInt(row.dataset.orderTime);
            const currentTime = Math.floor(Date.now() / 1000);
            const timeDiff = currentTime - orderTime;

            if (this.classList.contains('disabled')) {
                alert('Cancel Fail, Time limit (1min) exceeded');
                return;
            }

            if (timeDiff > 60) {
                alert('Cancel Fail, Time limit (1min) exceeded');
                this.classList.add('disabled');
                return;
            }

            if (confirm('Are you sure you want to cancel this order?')) {
                cancelOrder(orderId, this);
            }
        });
    });

    // 取消订单的函数
    function cancelOrder(orderId, buttonElement) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 更新状态显示
                const row = buttonElement.closest('tr');
                row.querySelector('.order-status').textContent = 'cancelled';
                buttonElement.replaceWith('Cancelled');
            } else {
                alert('Error: ' + (data.message || 'Failed to cancel order'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
});

