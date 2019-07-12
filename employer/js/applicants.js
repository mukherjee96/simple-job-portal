const approve = document.getElementById('approve');
const reject = document.getElementById('reject');
const spinner = document.getElementById('spinner');

approve.addEventListener('click', function(e) {
    $('#spinner').modal('show');
});

reject.addEventListener('click', function(e) {
    $('#spinner').modal('show');
});
