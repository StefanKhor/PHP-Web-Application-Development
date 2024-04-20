$(document).ready(function() {
    $(".sidebar").hover(
        function() {
        $(".content").addClass("shifted");
        // console.log('done')
        },
        function() {
        $(".content").removeClass("shifted");
        }
    );
    }
);