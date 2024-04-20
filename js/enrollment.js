const dialogModals = document.querySelectorAll("[dialog-modal]");
const openButtons = document.querySelectorAll('[open-modal]');

openButtons.forEach((openButton) => {
    openButton.addEventListener("click", () => {
        const sectionId = openButton.getAttribute("open-modal");
        const dialogModal = document.querySelector(`.dialog-${sectionId}`);
        dialogModal.showModal();
    });
});

dialogModals.forEach((dialogModal) => {
    const closeModal = dialogModal.querySelector('[close-modal]');
    closeModal.addEventListener("click", () => {
        dialogModal.close();
    });
});