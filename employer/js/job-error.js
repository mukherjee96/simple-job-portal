const jobError = document.getElementById('jobNotAdded');
jobError.style.display = 'block';
setTimeout(() => {
    jobError.style.display = 'none';
}, 5000);