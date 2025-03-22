function showMessageModal(message, type) {
    const modal = document.getElementById("modal");
    const modalMessage = document.getElementById("modal-message");
    const closeModal = document.getElementById("close-modal");

    modalMessage.textContent = message;
    modal.style.display = "block";

    closeModal.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
}

document.querySelectorAll('.wave-group input').forEach(input => {
    input.addEventListener('focus', () => {
        let chars = input.nextElementSibling.querySelectorAll('.label-char');
        chars.forEach((char, index) => {
            char.style.animation = `wave 0.6s ease-in-out ${index * 0.1}s`;
        });
    });

    input.addEventListener('blur', () => {
        let chars = input.nextElementSibling.querySelectorAll('.label-char');
        chars.forEach(char => {
            char.style.animation = 'none';
        });
    });
});

