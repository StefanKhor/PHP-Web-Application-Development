const toggleDetail = document.getElementById("toggleDetail");
const courseDetail = document.getElementById("hiddenDetail");

toggleDetail.addEventListener("click", function(e) {
    e.preventDefault();
    if (courseDetail.style.display === "none") {
        courseDetail.style.display = "block";
        toggleDetail.innerHTML = "Hide Description";
    } else {
        courseDetail.style.display = "none";
        toggleDetail.innerHTML = "Course Description";
    }
});

if(document.getElementById('toggleFeedback')){    
    const hiddenAnnouncement = document.getElementById("hiddenAnnouncement");
    const toggleFeedback = document.getElementById("toggleFeedback");
    const hiddenFeedback = document.getElementById("hiddenFeedback");
    const toggleCertificate = document.getElementById("toggleCertificate");
    const hiddenCertificate = document.getElementById("hiddenCertificate");
    const togglePrintCertificate = document.getElementById("printCertificate");
    const divShowingCertificate = document.querySelector(".divShowingCertificate");



    togglePrintCertificate.addEventListener("click",function(e){
        var openWindow = window.open("", "title",  'left=0,top=0,toolbar=0,scrollbars=0,status=0');
        openWindow.document.write(divShowingCertificate.innerHTML);
        openWindow.document.close();
        openWindow.focus();
        openWindow.print();
        openWindow.close();
    })

    toggleFeedback.addEventListener("click", function(e) {
        if(hiddenFeedback.style.display == "none"){
            hiddenFeedback.style.display = "block";
            hiddenAnnouncement.style.display = "none";
            toggleDetail.style.display = "none";
            toggleCertificate.style.display = "none";
            toggleFeedback.innerHTML = "Announcement";
        }
        else {
            hiddenFeedback.style.display = "none";
            hiddenAnnouncement.style.display = "block";
            toggleDetail.style.display = "block";
            toggleCertificate.style.display = "block";
            toggleFeedback.innerHTML = "Feedback";
        }
    })

    toggleCertificate.addEventListener("click", function(e) {
        if(hiddenCertificate.style.display == "none"){
            hiddenCertificate.style.display = "block";
            hiddenAnnouncement.style.display = "none";
            toggleDetail.style.display = "none";
            toggleFeedback.style.display = "none";
            toggleCertificate.innerHTML = "Announcement";
        }
        else {
            hiddenCertificate.style.display = "none";
            hiddenAnnouncement.style.display = "block";
            toggleDetail.style.display = "block";
            toggleFeedback.style.display = "block";
            toggleCertificate.innerHTML = "Certificate";
        }
    })
}

if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}