$(document).ready(
    function () {

        $('#logOutputModal').on('hidden.bs.modal', function (e) {
            $(this).removeData('bs.modal');
        });

    }
);