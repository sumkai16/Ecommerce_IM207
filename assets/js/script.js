(function () {

    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.getAttribute('data-productid');
            const quantity = this.getAttribute('data-quantity') || 1;

            const formData = new FormData();
            formData.append('productId', productId);
            formData.append('quantity', quantity);

            // Dynamically build the fetch URL based on current location
            const basePath = window.location.pathname.split('/').slice(0,2).join('/');
            const fetchUrl = basePath + '/classes/cart-process.php';

            fetch(fetchUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', text);
                        throw e;
                    }
                });
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Product added to cart successfully!');
                    // Update cart count badge dynamically
                    const cartBadge = document.querySelector('.icon-link .badge');
                    if (cartBadge && data.cartCount !== undefined) {
                        cartBadge.textContent = data.cartCount;
                    }
                } else {
                    alert('Failed to add product to cart.');
                }
            })
            .catch(error => {
                
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
})();