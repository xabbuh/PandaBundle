$(document).ready(function() {
    $("input[panda-uploader=v2]").each(function () {
        var authoriseUrl = $(this).attr("authorise-url");
        var browseButtonId = $(this).attr("browse-button-id");
        var cancelButtonId = $(this).attr("cancel-button-id");
        var progressBarId = $(this).attr("progress-bar-id");
        var progressBar = $("#" + progressBarId);
        var allowMultipleFiles = $(this).attr("multiple_files");

        var currentFile = null;

        var uploader = panda.uploader.init({
            "buttonId": browseButtonId,
            "authorizeUrl": authoriseUrl,
            "progressBarId": progressBarId,
            "allowSelectMultipleFiles": allowMultipleFiles,
            "onStart": function(file) {
                progressBar.show();
                currentFile = file;
            },
            "onProgress": function(file, percent) {
                progressBar.text(percent + "%");
            },
            "onCancel": function() {
                progressBar.hide();
            },
            "onComplete": function() {
                progressBar.hide();
            }
        });

        $("#" + cancelButtonId).click(function() {
            uploader.cancel(currentFile);
        });
    });
});
