// Cache DOM elements
const locationInput = document.getElementById("location");
const minPriceInput = document.getElementById("min_price");
const maxPriceInput = document.getElementById("max_price");
const typeInput = document.getElementById("house_type");
const resultsDiv = document.getElementById("results");

// Debounce function to limit API calls
function debounce(func, delay = 300) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// Format price with currency
function formatPrice(price) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        maximumFractionDigits: 0
    }).format(price);
}

// Fetch properties from server
async function fetchProperties() {
    const params = new URLSearchParams({
        location: locationInput.value.trim(),
        min_price: minPriceInput.value,
        max_price: maxPriceInput.value,
        house_type: typeInput.value
    });

    // Show loading state
    resultsDiv.innerHTML = '<div class="loading"></div>';

    try {
        const response = await fetch(`search.php?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.text();
        resultsDiv.innerHTML = data;
        
        // Add fade-in animation
        resultsDiv.style.opacity = '0';
        setTimeout(() => {
            resultsDiv.style.transition = 'opacity 0.3s ease';
            resultsDiv.style.opacity = '1';
        }, 10);
        
    } catch (error) {
        console.error('Error fetching properties:', error);
        resultsDiv.innerHTML = `
            <div class="no-results">
                <div class="no-results-icon">⚠️</div>
                <h3>Error Loading Properties</h3>
                <p>Please try again later or refresh the page.</p>
            </div>
        `;
    }
}

// Debounced version of fetchProperties for text inputs
const debouncedFetch = debounce(fetchProperties, 400);

// Event listeners with debouncing for better performance
if (locationInput) {
    locationInput.addEventListener("input", debouncedFetch);
}

if (minPriceInput) {
    minPriceInput.addEventListener("input", debouncedFetch);
}

if (maxPriceInput) {
    maxPriceInput.addEventListener("input", debouncedFetch);
}

if (typeInput) {
    typeInput.addEventListener("change", fetchProperties); // No debounce for select
}

// Load all properties initially
if (resultsDiv) {
    fetchProperties();
}

// Confirm delete with better UX
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-btn')) {
        if (!confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});

// Form validation for add/edit pages
const propertyForm = document.querySelector('form[method="POST"]');
if (propertyForm) {
    propertyForm.addEventListener('submit', function(e) {
        const title = this.querySelector('input[name="title"]');
        const location = this.querySelector('input[name="location"]');
        const price = this.querySelector('input[name="price"]');
        
        if (title && title.value.trim().length < 3) {
            e.preventDefault();
            alert('Title must be at least 3 characters long');
            title.focus();
            return false;
        }
        
        if (location && location.value.trim().length < 2) {
            e.preventDefault();
            alert('Location must be at least 2 characters long');
            location.focus();
            return false;
        }
        
        if (price && parseFloat(price.value) <= 0) {
            e.preventDefault();
            alert('Price must be greater than 0');
            price.focus();
            return false;
        }
    });
}

// Image preview for file upload
const imageInput = document.querySelector('input[type="file"][name="image"]');
if (imageInput) {
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size should not exceed 5MB');
                this.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload only JPG, JPEG, or PNG images');
                this.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'image-preview';
                    preview.style.marginTop = '1rem';
                    imageInput.parentElement.appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         style="max-width: 100%; max-height: 200px; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);" 
                         alt="Image preview">
                    <p style="margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">Preview: ${file.name}</p>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
}

// Add smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Handle success messages fade out
window.addEventListener('load', function() {
    const successMsg = document.querySelector('.success-message');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.transition = 'opacity 0.5s ease';
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }, 5000);
    }
});
