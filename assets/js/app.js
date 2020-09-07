let currentUser = document.getElementById('currentUserData').dataset.currentUserId;

if (!currentUser || 0 === parseInt(currentUser)) {
    window.currentUser = null;
} else {
    window.currentUser = currentUser;
}
