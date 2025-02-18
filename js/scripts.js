// Shopping Cart Functionality
let cart = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load cart from localStorage
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartCount();
    }

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const card = e.target.closest('.card');
            const product = {
                name: card.querySelector('.card-title').textContent,
                price: card.querySelector('.card-text strong').textContent,
                image: card.querySelector('img').src,
                quantity: 1
            };
            
            addToCart(product);
            updateCartCount();
            
            // Show success message
            showNotification('Product added to cart!');
        });
    });
});

function addToCart(product) {
    // Check if product already exists in cart
    const existingProduct = cart.find(item => item.name === product.name);
    if (existingProduct) {
        existingProduct.quantity += 1;
    } else {
        cart.push(product);
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
}

function updateCartCount() {
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartElement = document.querySelector('.fa-shopping-cart').parentElement;
    cartElement.innerHTML = `<i class="fas fa-shopping-cart"></i> Cart (${cartCount})`;
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success notification';
    notification.innerHTML = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Newsletter Form
const newsletterForm = document.querySelector('form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;
        if (email) {
            showNotification('Thank you for subscribing!');
            this.reset();
        }
    });
});

// Initialize carousel
$(document).ready(function() {
    $('.carousel').carousel({
        interval: 3000
    });
});

// Form validation and submission
$('.contact-form').on('submit', function(e) {
    e.preventDefault();
    
    // Basic form validation
    let isValid = true;
    $(this).find('input, textarea').each(function() {
        if (!$(this).val()) {
            isValid = false;
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    if (isValid) {
        showNotification('Message sent successfully!');
        this.reset();
    }
});

// Smooth scroll
$('a[href*="#"]').on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({
        scrollTop: $($(this).attr('href')).offset().top - 100
    }, 500);
});