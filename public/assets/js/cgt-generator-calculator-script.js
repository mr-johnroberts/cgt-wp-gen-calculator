document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cgt-generator-form');
    const squareFootageInputs = document.querySelectorAll('.square-footage-input');
    const totalWattageDisplay = document.getElementById('cgt-total-wattage');
    const steps = document.querySelectorAll('.cgt-form-step');
    const nextBtns = document.querySelectorAll('.cgt-next-btn');
    const prevBtns = document.querySelectorAll('.cgt-prev-btn');
    const generatorsList = document.getElementById('generators-list');
    const descriptionContainer = document.createElement('p');
    descriptionContainer.style.textAlign = 'center';
    descriptionContainer.style.marginBottom = '20px';
    generatorsList.before(descriptionContainer);
    const loader = document.createElement('div');
    loader.className = 'loader';
    loader.style.display = 'none';
    generatorsList.before(loader);
    let currentStep = 0;
    let allProducts = [];
    let currentPage = 0;
    const itemsPerPage = 10; // Display 10 items per page for two columns

    function updateTotalWattage() {
        let totalWattage = 0;

        document.querySelectorAll('.item-quantity').forEach(input => {
            const quantity = parseInt(input.value) || 0;
            const wattage = parseInt(input.previousElementSibling.dataset.wattage) || 0;
            totalWattage += quantity * wattage;
        });

        squareFootageInputs.forEach(input => {
            const squareFootage = parseInt(input.value) || 0;
            totalWattage += squareFootage * 3; // Assuming 3 watts per square foot
        });

        const totalKw = (totalWattage / 1000).toFixed(2);
        totalWattageDisplay.textContent = `Total Wattage: ${totalKw} kW`;
        totalWattageDisplay.dataset.totalKw = totalKw;
    }

    function syncSquareFootageInputs(value) {
        squareFootageInputs.forEach(input => {
            input.value = value;
        });
        updateTotalWattage();
    }

    function attachInputListeners() {
        document.querySelectorAll('.item-quantity').forEach(input => {
            input.addEventListener('input', updateTotalWattage);
        });

        squareFootageInputs.forEach(input => {
            input.addEventListener('input', function() {
                syncSquareFootageInputs(input.value);
            });
        });
    }

    function addNewItem(event) {
        event.preventDefault();
        const itemContainer = event.target.closest('.item-addition-area');
        const itemNameInput = itemContainer.querySelector('.new-item-name');
        const itemWattageInput = itemContainer.querySelector('.new-item-wattage');

        if (!itemNameInput.value || !itemWattageInput.value) {
            alert('Please enter both an appliance name and wattage amount!');
            return;
        }

        const newItemRow = document.createElement('div');
        newItemRow.className = 'sub-category-input-group';
        newItemRow.innerHTML = `<input type="text" value="${itemNameInput.value}" readonly>
                                 <input type="number" value="${itemWattageInput.value}" class="item-wattage" readonly data-wattage="${itemWattageInput.value}">
                                 <input type="number" class="item-quantity" placeholder="Quantity eg. 1,2,etc.">
                                 <button type="button" class="remove-item-btn">Remove</button>`;

        newItemRow.querySelector('.remove-item-btn').addEventListener('click', function() {
            this.parentElement.remove();
            updateTotalWattage();
        });

        newItemRow.querySelector('.item-quantity').addEventListener('input', updateTotalWattage);

        itemContainer.before(newItemRow);
        itemNameInput.value = '';
        itemWattageInput.value = '';

        updateTotalWattage();
    }

    document.querySelectorAll('.add-item-btn').forEach(button => {
        button.addEventListener('click', addNewItem);
    });

    function navigateSteps(direction) {
        const newIndex = currentStep + direction;
        if (newIndex >= 0 && newIndex < steps.length) {
            steps[currentStep].style.display = 'none';
            steps[newIndex].style.display = 'block';
            currentStep = newIndex;

            if (newIndex === steps.length - 1) {
                fetchGenerators(parseFloat(totalWattageDisplay.dataset.totalKw));
            }
        }
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => navigateSteps(1));
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => navigateSteps(-1));
    });

    attachInputListeners();
    updateTotalWattage();
    steps[currentStep].style.display = 'block';

    function fetchGenerators(totalKw) {
        loader.style.display = 'block';
        const requestUrl = `${cgtCalculatorVars.ajax_url}?action=fetch_generators&total_wattage=${totalKw}&nonce=${cgtCalculatorVars.nonce}`;
        console.log('Request URL:', requestUrl); // Log the request URL
        fetch(requestUrl, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            loader.style.display = 'none';
            if (data.success) {
                allProducts = data.data.products;
                displayGenerators(totalKw);
            } else {
                console.error('Failed to fetch generators:', data.message);
                displayNoGenerators();
            }
        })
        .catch(error => {
            loader.style.display = 'none';
            console.error('Failed to fetch generators:', error);
            displayNoGenerators();
        });
    }

    function displayGenerators(totalKw) {
        generatorsList.innerHTML = '';
        const rangeStart = Math.floor(totalKw / 10) * 10;
        const rangeEnd = rangeStart + 9.9;
    
        let filteredProducts = allProducts.filter(product => {
            if (product.name.match(/(\d+(\.\d+)?)(kW)/)) {
                const generatorWattage = parseFloat(product.name.match(/(\d+(\.\d+)?)(kW)/)[1]);
                return generatorWattage >= rangeStart && generatorWattage <= rangeEnd;
            }
            return false;
        });
    
        if (filteredProducts.length > 0) {
            descriptionContainer.textContent = `Below is the list of generators that fall within the range of your Total Wattage for your property.`;
            paginateProducts(filteredProducts, 0);
        } else {
            // Sort products by how close they are to the total wattage
            filteredProducts = allProducts.sort((a, b) => {
                const aMatch = a.name.match(/(\d+(\.\d+)?)(kW)/);
                const bMatch = b.name.match(/(\d+(\.\d+)?)(kW)/);
                const aWattage = aMatch ? parseFloat(aMatch[1]) : Infinity;
                const bWattage = bMatch ? parseFloat(bMatch[1]) : Infinity;
                return Math.abs(aWattage - totalKw) - Math.abs(bWattage - totalKw);
            }).slice(0, 5); // Select the top 5 closest matches
    
            descriptionContainer.textContent = `No exact match found. Below is the closest list of generators that fall within the range of your Total Wattage for your property. Select the one that meets your demand.`;
            paginateProducts(filteredProducts, 0);
        }
    }    

    function displayNoGenerators() {
        generatorsList.innerHTML = '<p style="text-align: center; color: red;">Failed to fetch generators. Please try again later.</p>';
    }

    function paginateProducts(products, page) {
        generatorsList.innerHTML = '';
        const start = page * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedProducts = products.slice(start, end);

        paginatedProducts.forEach(product => {
            const generatorItem = document.createElement('div');
            generatorItem.className = 'generator-item';
            generatorItem.innerHTML = `<img src="${product.images[0].src}" alt="${product.name}">
                                       <h2>${product.name}</h2>
                                       <p>${product.price_html}</p>
                                       <a href="${product.permalink}" class="button">Request Quote</a>`;
            generatorsList.appendChild(generatorItem);
        });

        const totalPages = Math.ceil(products.length / itemsPerPage);
        updatePaginationControls(page, totalPages);
    }

    function updatePaginationControls(currentPage, totalPages) {
        const paginationContainer = document.querySelector('.pagination-controls');
        paginationContainer.innerHTML = '';

        if (currentPage > 0) {
            const prevButton = document.createElement('button');
            prevButton.innerHTML = 'Previous';
            prevButton.className = 'pagination-prev';
            prevButton.addEventListener('click', () => paginateProducts(allProducts, currentPage - 1));
            paginationContainer.appendChild(prevButton);
        }

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.innerHTML = i;
            pageButton.className = 'pagination-button';
            if (i === currentPage + 1) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', () => paginateProducts(allProducts, i - 1));
            paginationContainer.appendChild(pageButton);
        }

        if (currentPage < totalPages - 1) {
            const nextButton = document.createElement('button');
            nextButton.innerHTML = 'Next page';
            nextButton.className = 'pagination-next';
            nextButton.addEventListener('click', () => paginateProducts(allProducts, currentPage + 1));
            paginationContainer.appendChild(nextButton);
        }
    }
});
