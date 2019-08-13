const tricks = document.getElementById('tricks');

if (tricks) {
    tricks.addEventListener('click', e => {
        if (e.target.className === 'btn btn-danger delete-trick') {
            if (confirm('Are you sure?')) {
                const id = e.target.getAttribute('data-id');

                fetch(`/blog/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    });
}

$(window).scroll(function() {
    if($(window).scrollTop() == 0){
        $('#scrollToTop').fadeOut("fast");
    } else {
        if($('#scrollToTop').length == 0){
            $('body').append('<div id="scrollToTop">'+
                '<a href="#">Retour en haut</a>'+
                '</div>');
        }
        $('#scrollToTop').fadeIn("fast");
    }
});

function showMedia() {
    var x = document.getElementById("showMediaBtn");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}