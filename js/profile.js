const uploadPictureInput = document.getElementById("uploadPicture");

uploadPictureInput.addEventListener("change", function(e) {
    document.getElementById("changeProfilePictureForm").submit();
});


const editDIV = document.getElementById("editDIV");
const displayDIV = document.getElementById("displayDIV");
const newPasswordDIV = document.getElementById("newPasswordDIV");

const editButton = document.getElementById("startEditButton");
const changePasswordButton = document.getElementById("changePasswordButton");

const submitEditButton = document.getElementById("submitEdit");
const cancelEditButton = document.getElementById("cancelEdit");

const cancelChangePWDButton = document.getElementById("cancelChangePWD");
const submitChangePWDButton = document.getElementById("submitChangePWD");

const editProfileForm = document.getElementById("editProfileForm");
const newPasswordForm = document.getElementById("newPasswordForm");

const newPassword = document.getElementById("newPassword");
const newPasswordConfirm = document.getElementById("newPasswordConfirm");

if (editButton != null) { // When usertype is student/instructor/training provider
    editButton.addEventListener("click",function(e){
        displayDIV.style.display = "none";
        editDIV.style.display = "block";
    })

    cancelEditButton.addEventListener("click", function(e){
        displayDIV.style.display = "block";
        editDIV.style.display = "none";
        editProfileForm.reset();
    })
}

changePasswordButton.addEventListener("click", function(e) {
    displayDIV.style.display = "none";
    newPasswordDIV.style.display = "block";
})

cancelChangePWDButton.addEventListener("click", function(e) {
    displayDIV.style.display = "block";
    newPasswordDIV.style.display = "none";
    newPasswordForm.reset();
})

function check_password_input(){
    if(newPassword.value != newPasswordConfirm.value){
        newPasswordConfirm.setCustomValidity("Password Don't Match");
    }
    else{
        newPasswordConfirm.setCustomValidity("");
    }
}
newPassword.onchange = check_password_input;
newPasswordConfirm.onkeyup = check_password_input;