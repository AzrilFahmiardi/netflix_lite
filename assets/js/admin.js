// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Tab switching functionality
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.getElementById(tabId).classList.add('active');
    
    document.querySelectorAll('.sidebar-menu li').forEach(item => {
        item.classList.remove('active');
    });
    
    if (event && event.currentTarget) {
        event.currentTarget.parentElement.classList.add('active');
    }
    
    window.location.hash = tabId;
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
    document.body.classList.add('modal-open');
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Edit genre function
function editGenre(id, name, description) {
    document.getElementById('edit_genre_id').value = id;
    document.getElementById('edit_genre_name').value = name;
    document.getElementById('edit_genre_description').value = description;
    openModal('edit-genre-modal');
}

// Edit movie function AJAX
function editMovie(id) {
    const container = document.getElementById('edit-movie-form-container');
    container.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading movie data...</div>';
    openModal('edit-movie-modal');
    
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `components/get_movie_form.php?id=${id}`, true);
    xhr.onload = function() {
        if (this.status === 200) {
            container.innerHTML = this.responseText;
        } else {
            container.innerHTML = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Error loading movie data</div>';
        }
    };
    xhr.onerror = function() {
        container.innerHTML = '<div class="alert error"><i class="fas fa-exclamation-circle"></i> Network error occurred</div>';
    };
    xhr.send();
}

// Close modals 
window.onclick = function(event) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById(hash)) {
        showTab(hash);
        
        document.querySelectorAll('.sidebar-menu li').forEach(item => {
            const link = item.querySelector('a');
            if (link && link.getAttribute('onclick') && link.getAttribute('onclick').includes(hash)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
});
