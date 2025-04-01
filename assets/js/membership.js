document.addEventListener('DOMContentLoaded', function() {
    // Set selected plan in registration modal
    const planButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#registrationModal"]');
    planButtons.forEach(button => {
        button.addEventListener('click', function() {
            const plan = this.getAttribute('data-plan');
            const price = this.getAttribute('data-price');
            document.getElementById('selectedPlan').value = plan;
            document.getElementById('selectedPrice').value = price;
            document.getElementById('registrationModalLabel').textContent = plan + ' Membership Registration';
        });
    });
    
    // Toggle medical details textarea
    const medicalConditionRadios = document.querySelectorAll('input[name="medicalCondition"]');
    const medicalDetailsContainer = document.getElementById('medicalDetailsContainer');
    
    medicalConditionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'yes') {
                medicalDetailsContainer.classList.remove('d-none');
                document.getElementById('medicalDetails').setAttribute('required', 'required');
            } else {
                medicalDetailsContainer.classList.add('d-none');
                document.getElementById('medicalDetails').removeAttribute('required');
            }
        });
    });
    
    // Limit class selection to 3
    const classCheckboxes = document.querySelectorAll('.class-checkbox');
    const classError = document.getElementById('classError');
    
    classCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedClasses = document.querySelectorAll('.class-checkbox:checked');
            
            if (checkedClasses.length > 3) {
                this.checked = false;
                classError.classList.remove('d-none');
            } else {
                classError.classList.add('d-none');
            }
        });
    });
    
    // Password confirmation validation
    const registrationForm = document.getElementById('registrationForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            // Check if passwords match
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                confirmPassword.focus();
                return false;
            }
            
            // Validate registration code if provided
            const registrationCode = document.getElementById('registrationCode');
            if (registrationCode.value.trim() !== '') {
                // Validate registration code format (e.g., FIT-XXXX-XXXX)
                const codeRegex = /^FIT-\d{4}-\d{4}$/;
                
                if (!codeRegex.test(registrationCode.value)) {
                    e.preventDefault();
                    alert('Invalid registration code format. Please use format: FIT-XXXX-XXXX');
                    registrationCode.focus();
                    return false;
                }
            }
            
            // Ensure medical condition is selected
            const medicalYes = document.getElementById('medicalConditionYes');
            const medicalNo = document.getElementById('medicalConditionNo');
            
            if (!medicalYes.checked && !medicalNo.checked) {
                e.preventDefault();
                alert('Please indicate whether you have any medical conditions.');
                return false;
            }
            
            // If medical condition is yes, ensure details are provided
            if (medicalYes.checked) {
                const medicalDetails = document.getElementById('medicalDetails');
                if (medicalDetails.value.trim() === '') {
                    e.preventDefault();
                    alert('Please provide details about your medical conditions.');
                    medicalDetails.focus();
                    return false;
                }
            }
            
            // Ensure terms are agreed to
            const termsAgree = document.getElementById('termsAgree');
            if (!termsAgree.checked) {
                e.preventDefault();
                alert('You must agree to the Terms and Conditions to register.');
                termsAgree.focus();
                return false;
            }
            
            return true;
        });
    }
});