function showAll() {
    document.querySelectorAll('.box').forEach(item => {
        item.style.display = 'inline-block';
    });
}

function showBurger() {
    filterByType('Burger');
}

function showSide() {
    filterByType('Side');
}

function showDrink() {
    filterByType('Drink');
}


function filterByType(type) {
    let box = document.getElementsByClassName('box')
    
    Array.from(box).forEach(item => {
        item.style.display = item.dataset.type === type ? 'inline-block' : 'none';
    });
}

// 檢查已停用的產品
function openModel(burgerType) {
    const modal = document.getElementById(burgerType + "Modal");
    if (modal) {
        modal.style.display = "block";
    }
}

// 更新功能以包含已停用的產品
function performSearch() {
    const searchTerm = searchInput.value.toLowerCase();
    
    productBoxes.forEach(box => {
        const productName = box.querySelector('p').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            box.style.display = 'inline-block';
        } else {
            box.style.display = 'none';
        }
    });
}

function closeModel(burgerType) {
    document.getElementById(burgerType+"Modal").style.display = "none";
}

function addToCart(productId) {
    const quantity = document.getElementById('qty' + productId).value || 1;
    
    $.ajax({
        url: "addToCart.php",
        type: "POST",
        data: {
            prod_id: productId,
            quantity: quantity
        },
        success: function(response){
            try {
                const data = JSON.parse(response);
                if (data.status === "success") {
                    alert("Product added to cart");
                    // Update cart counter if you have one
                    if (data.cartCount) {
                        document.getElementById('cart-count').textContent = data.cartCount;
                    }
                } else {
                    alert(data.message || "Failed to add to cart");
                }
            } catch (e) {
                alert("Error processing response");
            }
        },
        error: function(xhr, status, error){
            alert("Error: " + error);
        }
    });
}


function changeQty(productId, delta) {
    const input = document.getElementById('qty' + productId);
    let value = parseInt(input.value) || 1;
    value += delta;

    if (value < 1) value = 1;
    if (value > 99) value = 99;

    input.value = value;
}


document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', () => {
            let value = parseInt(input.value, 10);

            if (isNaN(value) || value < 1) {
                input.value = 1;
            } else if (value > 99) {
                input.value = 99;
            }
        });
    });
});

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const productBoxes = document.querySelectorAll('.box');
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        
        productBoxes.forEach(box => {
            const productName = box.querySelector('p').textContent.toLowerCase();
            if (productName.includes(searchTerm)) {
                box.style.display = 'inline-block';
            } else {
                box.style.display = 'none';
            }
        });
    }
    
    // Search on button click
    searchButton.addEventListener('click', performSearch);
    
    // Search on Enter key press
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

//高到低
function sortHighPrice() {
    let menu = document.querySelector('.menu');
    let products = Array.from(menu.getElementsByClassName('box'));

    products.sort((a, b) => {
        let priceA = parseFloat(a.querySelector('p:nth-of-type(2)').innerText.replace('RM', '').trim());
        let priceB = parseFloat(b.querySelector('p:nth-of-type(2)').innerText.replace('RM', '').trim());
        return priceB - priceA;
    });

    products.forEach(product => menu.appendChild(product));
}


//低到高
function sortLowPrice() {
    let menu = document.querySelector('.menu');
    let products = Array.from(menu.getElementsByClassName('box'));

    products.sort((a, b) => {
        let priceA = parseFloat(a.querySelector('p:nth-of-type(2)').innerText.replace('RM', '').trim());
        let priceB = parseFloat(b.querySelector('p:nth-of-type(2)').innerText.replace('RM', '').trim());
        return priceA - priceB;
    });

    products.forEach(product => menu.appendChild(product));
}





