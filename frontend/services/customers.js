var CustomerService = {



loadCustomers: function() {
    RestClient.get('customers/report', function(response) {
        const tbody = document.querySelector('#customer-details tbody');
        tbody.innerHTML = '';
        
        response.forEach(function(customer) {
            console.log("Customer details: ", customer);
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center">
                    <button type="button" onclick="CustomerService.showDetails(${customer.customer_id});" class="btn btn-success">Details</button>
                </td>
                <td>${customer.customer_full_name}</td>
                <td>${customer.total_amount}</td>
            `;
            tbody.appendChild(row);
        });
    });
},
    showDetails: function(customerId) {
        console.log("Fetching details for customer:", customerId); // DEBUG
        RestClient.get('rentals/customer/' + customerId, function(response) {
            console.log("Raw response:", response); // DEBUG
            console.log("Response type:", typeof response); // DEBUG
            console.log("Is array:", Array.isArray(response)); // DEBUG
            
            const modalBody = document.querySelector('#customer-details-modal .modal-body');
            
            // Check if response is valid
            if (!response || !Array.isArray(response)) {
                console.error("Invalid response - not an array:", response);
                modalBody.innerHTML = '<p>No rental data found for this customer.</p>';
                new bootstrap.Modal(document.getElementById('customer-details-modal')).show();
                return;
            }
            
            // Create table structure
            modalBody.innerHTML = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Rental Date</th>
                            <th>Film Title</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            `;
            
            const tbody = modalBody.querySelector('tbody');
            let total = 0;
            
            response.forEach(function(rental, index) {
                total += parseFloat(rental.payment_amount);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${rental.rental_date}</td>
                    <td>${rental.film_title}</td>
                    <td>$${parseFloat(rental.payment_amount).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
            
            // Add total row
            const totalRow = document.createElement('tr');
            totalRow.innerHTML = `
                <td colspan="3"><strong>Total Bill</strong></td>
                <td><strong>$${total.toFixed(2)}</strong></td>
            `;
            tbody.appendChild(totalRow);
            
            // Show modal
            new bootstrap.Modal(document.getElementById('customer-details-modal')).show();
        }, function(error) {
            console.error("Error fetching rental details:", error); // DEBUG
        });
    }
}
