document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in (this would normally be done server-side)
    const isLoggedIn = checkLoginStatus();
    
    if (isLoggedIn) {
        document.getElementById('login-required-message').classList.add('d-none');
        document.getElementById('health-tracking-dashboard').classList.remove('d-none');
        
        // Initialize charts
        initializeCharts();
        
        // Set up form handlers
        setupFormHandlers();
    }
    
    // Demo function to check login status
    function checkLoginStatus() {
        // In a real application, this would check session/cookie data
        // For demo purposes, we'll use localStorage
        return localStorage.getItem('fitlife_logged_in') === 'true';
    }
    
    // Initialize progress charts
    function initializeCharts() {
        // Weight Chart
        const weightCtx = document.getElementById('weightChart').getContext('2d');
        const weightChart = new Chart(weightCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Weight (kg)',
                    data: [78, 77, 76, 75.5, 75, 74.5],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 80
                    }
                }
            }
        });
        
        // Body Fat Chart
        const bodyFatCtx = document.getElementById('bodyFatChart').getContext('2d');
        const bodyFatChart = new Chart(bodyFatCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Body Fat (%)',
                    data: [22, 21, 20, 19, 18.5, 18],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 15,
                        max: 25
                    }
                }
            }
        });
        
        // Muscle Mass Chart
        const muscleCtx = document.getElementById('muscleMassChart').getContext('2d');
        const muscleChart = new Chart(muscleCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Muscle Mass (kg)',
                    data: [30, 30.5, 31, 31.5, 32, 32.5],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 25,
                        max: 35
                    }
                }
            }
        });
    }
    
    // Set up form handlers
    function setupFormHandlers() {
        // Update Metrics Form
        const updateMetricsForm = document.getElementById('updateMetricsForm');
        if (updateMetricsForm) {
            updateMetricsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const weight = document.getElementById('weight').value;
                const bodyFat = document.getElementById('bodyFat').value;
                const muscleMass = document.getElementById('muscleMass').value;
                const date = document.getElementById('measurementDate').value;
                
                // In a real application, this would send data to the server
                // For demo purposes, we'll just update the UI
                document.getElementById('current-weight').textContent = weight + ' kg';
                document.getElementById('current-body-fat').textContent = bodyFat + '%';
                document.getElementById('current-muscle-mass').textContent = muscleMass + ' kg';
                
                // Calculate BMI (weight in kg / height in m^2)
                // Assuming height is 1.75m for demo
                const height = 1.75;
                const bmi = (weight / (height * height)).toFixed(1);
                document.getElementById('current-bmi').textContent = bmi;
                
                // Close modal
                const updateMetricsModal = document.getElementById('updateMetricsModal');
                const modal = bootstrap.Modal.getInstance(updateMetricsModal);
                modal.hide();
                
                // Show success message
                alert('Metrics updated successfully!');
            });
        }
        
        // Add Workout Form
        const addWorkoutForm = document.getElementById('addWorkoutForm');
        if (addWorkoutForm) {
            addWorkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const exercise = document.getElementById('exercise').value;
                const sets = document.getElementById('sets').value;
                const reps = document.getElementById('reps').value;
                const weight = document.getElementById('weight').value;
                
                // In a real application, this would send data to the server
                // For demo purposes, we'll just update the UI
                const workoutLog = document.getElementById('workout-log');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${exercise}</td>
                    <td>${sets}</td>
                    <td>${reps}</td>
                    <td>${weight}</td>
                `;
                workoutLog.appendChild(newRow);
                
                // Close modal
                const addWorkoutModal = document.getElementById('addWorkoutModal');
                const modal = bootstrap.Modal.getInstance(addWorkoutModal);
                modal.hide();
                
                // Show success message
                alert('Workout logged successfully!');
            });
        }
        
        // Log Food Form
        const logFoodForm = document.getElementById('logFoodForm');
        if (logFoodForm) {
            logFoodForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const foodName = document.getElementById('foodName').value;
                const calories = parseInt(document.getElementById('calories').value);
                const protein = parseFloat(document.getElementById('protein').value);
                const carbs = parseFloat(document.getElementById('carbs').value);
                const fat = parseFloat(document.getElementById('fat').value);
                
                // In a real application, this would send data to the server
                // For demo purposes, we'll just update the UI
                
                // Update progress bars
                const currentCalories = parseInt(document.getElementById('calorie-progress').textContent.split('/')[0]);
                const calorieGoal = parseInt(document.getElementById('calorie-goal').textContent.split(' ')[0]);
                const newCalories = currentCalories + calories;
                const caloriePercentage = Math.min(Math.round((newCalories / calorieGoal) * 100), 100);
                
                document.getElementById('calorie-progress').style.width = caloriePercentage + '%';
                document.getElementById('calorie-progress').textContent = newCalories + '/' + calorieGoal + ' kcal';
                
                // Update macros
                const proteinGoal = parseInt(document.getElementById('protein-goal').textContent.split('g')[0]);
                const carbsGoal = parseInt(document.getElementById('carbs-goal').textContent.split('g')[0]);
                const fatGoal = parseInt(document.getElementById('fat-goal').textContent.split('g')[0]);
                
                const proteinPercentage = Math.min(Math.round(((protein / proteinGoal) * 100) + 5), 100);
                const carbsPercentage = Math.min(Math.round(((carbs / carbsGoal) * 100) + 5), 100);
                const fatPercentage = Math.min(Math.round(((fat / fatGoal) * 100) + 5), 100);
                
                document.getElementById('protein-progress').style.width = proteinPercentage + '%';
                document.getElementById('protein-progress').textContent = proteinPercentage + '%';
                
                document.getElementById('carbs-progress').style.width = carbsPercentage + '%';
                document.getElementById('carbs-progress').textContent = carbsPercentage + '%';
                
                document.getElementById('fat-progress').style.width = fatPercentage + '%';
                document.getElementById('fat-progress').textContent = fatPercentage + '%';
                
                // Close modal
                const logFoodModal = document.getElementById('logFoodModal');
                const modal = bootstrap.Modal.getInstance(logFoodModal);
                modal.hide();
                
                // Show success message
                alert('Food logged successfully!');
            });
        }
    }
    
    // For demo purposes - login button in the modal
    const loginForm = document.querySelector('#loginModal form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // In a real application, this would validate credentials
            // For demo purposes, we'll just set the user as logged in
            localStorage.setItem('fitlife_logged_in', 'true');
            
            // Close modal
            const loginModal = document.getElementById('loginModal');
            const modal = bootstrap.Modal.getInstance(loginModal);
            modal.hide();
            
            // Refresh page to show dashboard
            window.location.reload();
        });
    }
});