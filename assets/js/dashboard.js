document.addEventListener('DOMContentLoaded', function() {
    // Health Metrics Chart
    const healthCtx = document.getElementById('healthChart');
    if (healthCtx) {
        const healthChart = new Chart(healthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Weight (kg)',
                    data: [75, 74, 72, 71, 70, 69],
                    backgroundColor: 'rgba(255, 69, 0, 0.2)',
                    borderColor: 'rgba(255, 69, 0, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y'
                }, {
                    label: 'BMI',
                    data: [26.5, 26.1, 25.4, 25.1, 24.7, 24.4],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'BMI'
                        }
                    }
                }
            }
        });
    }
    
    // Weight & BMI Tracking Chart
    const weightBmiCtx = document.getElementById('weightBmiChart');
    if (weightBmiCtx) {
        const weightBmiChart = new Chart(weightBmiCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Weight (kg)',
                    data: [78, 77, 76, 75, 74, 72, 71, 70, 69, 68, 67, 66],
                    backgroundColor: 'rgba(255, 69, 0, 0.2)',
                    borderColor: 'rgba(255, 69, 0, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y'
                }, {
                    label: 'BMI',
                    data: [27.5, 27.2, 26.8, 26.5, 26.1, 25.4, 25.1, 24.7, 24.4, 24.0, 23.7, 23.3],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'BMI'
                        }
                    }
                }
            }
        });
    }
    
    // Workout Frequency Chart
    const workoutFrequencyCtx = document.getElementById('workoutFrequencyChart');
    if (workoutFrequencyCtx) {
        const workoutFrequencyChart = new Chart(workoutFrequencyCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Workout Sessions',
                    data: [3, 4, 5, 4],
                    backgroundColor: 'rgba(255, 69, 0, 0.7)',
                    borderColor: 'rgba(255, 69, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Sessions'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // Workout Types Distribution Chart
    const workoutTypesCtx = document.getElementById('workoutTypesChart');
    if (workoutTypesCtx) {
        const workoutTypesChart = new Chart(workoutTypesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cardio', 'Strength Training', 'Yoga', 'HIIT', 'Other'],
                datasets: [{
                    label: 'Workout Types',
                    data: [30, 25, 15, 20, 10],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    }
    
    // Handle plan selection in registration modal
    const registrationModal = document.getElementById('registrationModal');
    if (registrationModal) {
        registrationModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const plan = button.getAttribute('data-plan');
            const selectedPlanElement = document.getElementById('selectedPlan');
            const planInput = document.getElementById('planInput');
            
            if (selectedPlanElement && planInput && plan) {
                selectedPlanElement.textContent = plan;
                planInput.value = plan;
            }
        });
    }
    
    // Password validation
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const editProfileForm = document.querySelector('#editProfileModal form');
    
    if (editProfileForm && newPassword && confirmPassword) {
        editProfileForm.addEventListener('submit', function(event) {
            if (newPassword.value !== '' && newPassword.value !== confirmPassword.value) {
                event.preventDefault();
                alert('New passwords do not match!');
                return false;
            }
        });
    }
    
    // BMI calculation
    const heightInput = document.getElementById('height');
    const weightInput = document.getElementById('weight');
    const bmiDisplay = document.querySelector('.card-title[data-bmi]');
    
    function calculateBMI(weight, height) {
        // Height in meters (convert from cm)
        const heightInMeters = height / 100;
        // BMI formula: weight (kg) / (height (m) * height (m))
        const bmi = weight / (heightInMeters * heightInMeters);
        return bmi.toFixed(1);
    }
    
    function getBMICategory(bmi) {
        if (bmi < 18.5) {
            return "Underweight";
        } else if (bmi >= 18.5 && bmi < 25) {
            return "Normal weight";
        } else if (bmi >= 25 && bmi < 30) {
            return "Overweight";
        } else {
            return "Obese";
        }
    }
    
    if (heightInput && weightInput) {
        [heightInput, weightInput].forEach(input => {
            input.addEventListener('change', function() {
                if (heightInput.value && weightInput.value) {
                    const bmi = calculateBMI(weightInput.value, heightInput.value);
                    if (bmiDisplay) {
                        bmiDisplay.textContent = bmi;
                        bmiDisplay.nextElementSibling.textContent = getBMICategory(bmi);
                    }
                }
            });
        });
    }
    
    // AJAX data loading for charts
    function loadChartData() {
        fetch('backend/track.php?action=get_health_data')
            .then(response => response.json())
            .then(data => {
                // Process and update charts with the data
                console.log('Health data loaded:', data);
                // Update charts with the data (implementation depends on your data structure)
            })
            .catch(error => console.error('Error loading health data:', error));
            
        fetch('backend/track.php?action=get_workouts')
            .then(response => response.json())
            .then(data => {
                console.log('Workout data loaded:', data);
                // Update workout charts
            })
            .catch(error => console.error('Error loading workout data:', error));
    }
    
    // Call the function to load data
    loadChartData();
    
    // Tab change event
    const dashboardTabs = document.getElementById('dashboardTabs');
    if (dashboardTabs) {
        dashboardTabs.addEventListener('shown.bs.tab', function (event) {
            // Redraw charts when tab is shown to fix rendering issues
            window.dispatchEvent(new Event('resize'));
        });
    }
});