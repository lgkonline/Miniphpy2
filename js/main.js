var ApiUri = "API";

$.ajax({
    url: ApiUri,
    data: { action: "config" },
    type: "GET",
    dataType: "json",
    success: function(config) {
        console.log(config);
        
        $("#bundles").loadTemplate($("#tpl-bundle"), config.inputs);
        tplLoaded();
    }
});

function tplLoaded() {
    $(".minify-btn").click(function() {
        var inputID = $(this).attr("value");
        var bundle = $(this).closest(".bundle");
        
        startLoading();
        
        $.ajax({
            url: ApiUri,
            data: { action: "minify", inputID: inputID},
            type: "GET",
            dataType: "json",
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
                bundle.find(".bundle-error").removeClass("hide");
            },
            complete: function() {
                finishLoading();
            }
        });
    });
}

function startLoading() {
    $("#loading, #loading-wrapper").fadeIn();
}

function finishLoading() {
    $("#loading, #loading-wrapper").fadeOut();
}