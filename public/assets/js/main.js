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